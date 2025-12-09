<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Services\AdminNotificationService;
use App\Services\CustomerNotificationService;
use App\Services\StaffNotificationService;
use App\Models\CustomerNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'customer_id',
        'customer_name',
        'receiver_name',
        'customer_email',
        'order_date',
        'order_status',
        'total_products',
        'sub_total',
        'vat',
        'total',
        'invoice_no',
        'tracking_number',
        'payment_type',
        'pay',
        'due',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'estimated_delivery',
        'delivery_notes',
        'delivery_address',
        'contact_phone',
        'gcash_reference',        
        'proof_of_payment',
        'city',
        'postal_code',
        'barangay',
        'street_name',
        'building',
        'house_no',
    ];

    protected $casts = [
        'order_date'    => 'date',
        'cancelled_at'  => 'datetime',
        'estimated_delivery' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'order_status'  => OrderStatus::class,
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Set order_date with correct timezone when creating
        static::creating(function ($order) {
            if (empty($order->order_date)) {
                $order->order_date = now()->timezone('Asia/Manila')->format('Y-m-d');
            }
        });

        // Create notification when a new order is created
        static::created(function ($order) {
            if ($order->order_status === OrderStatus::PENDING) {
                // Ensure customer relationship is loaded before creating notification
                if (!$order->relationLoaded('customer')) {
                    $order->load('customer');
                }
                
                // Create admin notification
                $adminNotificationService = app(AdminNotificationService::class);
                $adminNotificationService->createPendingOrderNotification($order);
                
                // Create staff notification
                $staffNotificationService = app(StaffNotificationService::class);
                $staffNotificationService->createPendingOrderNotification($order);
            }
        });

        // Create customer notifications when order status changes
        static::updated(function ($order) {
            $originalStatus = $order->getOriginal('order_status');
            $newStatus = $order->order_status;

            // Helper function to normalize status values
            $normalizeStatus = function($status) {
                // Handle enum objects
                if ($status instanceof OrderStatus) {
                    return $status->value;
                }
                // Handle both string and integer values
                if ($status === 0 || $status === '0' || $status === 'pending') return 'pending';
                if ($status === 1 || $status === '1' || $status === 'complete') return 'complete';
                if ($status === 2 || $status === '2' || $status === 'cancelled') return 'cancelled';
                return $status;
            };

            $originalStatusNormalized = $normalizeStatus($originalStatus);
            $newStatusNormalized = $normalizeStatus($newStatus);

            // Skip if status hasn't actually changed
            if ($originalStatusNormalized === $newStatusNormalized) {
                return;
            }

            // Ensure customer relationship is loaded before creating notifications
            if (!$order->relationLoaded('customer')) {
                $order->load('customer');
            }

            $customerNotificationService = app(CustomerNotificationService::class);

            // If status changed to complete - reduce product stock
            if ($newStatusNormalized === 'complete' && $originalStatusNormalized !== 'complete') {
                // Ensure details relationship is loaded
                if (!$order->relationLoaded('details')) {
                    $order->load('details.product');
                }

                // Reduce stock for each product in the order
                foreach ($order->details as $detail) {
                    if ($detail->product) {
                        $detail->product->decrement('quantity', $detail->quantity);
                        
                        // Create inventory movement record
                        \App\Models\InventoryMovement::create([
                            'product_id' => $detail->product_id,
                            'type' => 'out',
                            'quantity' => $detail->quantity,
                        ]);
                    }
                }

                // Send order completed notification
                $customerNotificationService->createOrderCompletedNotification($order);
            }

            // If status changed from pending to complete (approved)
            if ($originalStatusNormalized === 'pending' && $newStatusNormalized === 'complete') {
                // Already handled above in the 'complete' status check
            }

            // If status changed to cancelled (admin cancelled)
            if ($originalStatusNormalized !== 'cancelled' && $newStatusNormalized === 'cancelled') {
                $customerNotificationService->createOrderCancelledNotification($order);
            }
        });
    }

    /**
     * Get the customer that owns the order
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the order details
     */
    public function details(): HasMany
    {
        return $this->hasMany(OrderDetails::class);
    }

    /**
     * Get the payments for this order
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the user who cancelled the order
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Check if order is pending
     */
    public function isPending(): bool
    {
        return $this->order_status === OrderStatus::PENDING;
    }

    /**
     * Check if order is completed
     */
    public function isCompleted(): bool
    {
        return $this->order_status === OrderStatus::COMPLETE;
    }

    /**
     * Check if order is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->order_status === OrderStatus::CANCELLED;
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return $this->isPending() && !$this->isCancelled();
    }

    /**
     * Cancel the order
     */
    public function cancel(string $reason, ?User $cancelledBy = null): void
    {
        $this->update([
            'order_status' => OrderStatus::CANCELLED,
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
            'cancelled_by' => $cancelledBy?->id,
        ]);

        // Ensure customer relationship is loaded before creating notification
        if (!$this->relationLoaded('customer')) {
            $this->load('customer');
        }

        // Create admin notification for cancelled order
        $adminNotificationService = app(AdminNotificationService::class);
        $adminNotificationService->createCancelledOrderNotification($this, $cancelledBy);
        
        // Create staff notification for cancelled order
        $staffNotificationService = app(StaffNotificationService::class);
        $staffNotificationService->createCancelledOrderNotification($this, $cancelledBy);
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->completed()->sum('amount');
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalanceAttribute(): float
    {
        return $this->total - $this->total_paid;
    }

    /**
     * Check if order is fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->remaining_balance <= 0;
    }

    /**
     * Search scope
     */
    public function scopeSearch($query, $value): void
    {
        $query->where('invoice_no', 'like', "%{$value}%")
            ->orWhere('tracking_number', 'like', "%{$value}%")
            ->orWhere('order_status', 'like', "%{$value}%")
            ->orWhere('payment_type', 'like', "%{$value}%");
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('order_status', OrderStatus::PENDING);
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('order_status', OrderStatus::COMPLETE);
    }

    /**
     * Scope for cancelled orders
     */
    public function scopeCancelled($query)
    {
        return $query->where('order_status', OrderStatus::CANCELLED);
    }
}
