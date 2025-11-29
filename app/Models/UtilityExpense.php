<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilityExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'billing_period',
        'due_date',
        'paid_date',
        'status',
        'notes',
        'receipt_file',
        'created_by',
        'is_void',
        'void_reason',
        'voided_at',
        'voided_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
        'is_void' => 'boolean',
        'voided_at' => 'datetime',
    ];

    /**
     * Get the user who created this expense
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for pending expenses
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid expenses
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for overdue expenses
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Scope for non-void expenses
     */
    public function scopeNotVoid($query)
    {
        return $query->where('is_void', false);
    }

    /**
     * Scope for void expenses
     */
    public function scopeVoided($query)
    {
        return $query->where('is_void', true);
    }

    /**
     * Get the user who voided this expense
     */
    public function voidedBy()
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    /**
     * Void this expense
     */
    public function void($reason, $userId)
    {
        $this->update([
            'is_void' => true,
            'void_reason' => $reason,
            'voided_at' => now(),
            'voided_by' => $userId,
        ]);
    }
}
