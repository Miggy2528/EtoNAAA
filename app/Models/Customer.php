<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\OrderStatus;


class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'phone',
        'address',
        'photo',
        'status',
        'role',
        'email_verified_at',
        'last_login_at',
        'account_holder',
        'account_number',
        'bank_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the customer's orders
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the customer's payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the customer's notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(CustomerNotification::class);
    }

    /**
     * Get the customer's authentication logs
     */
    public function authLogs(): HasMany
    {
        return $this->hasMany(CustomerAuthLog::class);
    }

    /**
     * Get unread notifications
     */
    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(CustomerNotification::class)->where('is_read', false);
    }

    /**
     * Get recent orders (last 5)
     */
    public function recentOrders(): HasMany
    {
        return $this->hasMany(Order::class)->latest()->limit(5);
    }

    /**
     * Get pending orders
     */
    public function pendingOrders(): HasMany
    {
        return $this->hasMany(Order::class)->where('order_status', OrderStatus::PENDING->value);
    }

    /**
     * Get completed orders
     */
    public function completedOrders(): HasMany
    {
        return $this->hasMany(Order::class)->where('order_status', OrderStatus::COMPLETE->value);
    }

    /**
     * Check if customer is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if customer is suspended
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Role checking methods
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Get customer's full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->name ?? 'Unknown Customer';
    }

    /**
     * Get customer's display name (username or email)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->username ?? $this->email ?? 'Unknown Customer';
    }

    /**
     * Search scope
     */
    public function scopeSearch($query, $value): void
    {
        $query->where('name', 'like', "%{$value}%")
            ->orWhere('email', 'like', "%{$value}%")
            ->orWhere('username', 'like', "%{$value}%")
            ->orWhere('phone', 'like', "%{$value}%");
    }

    /**
     * Active customers scope
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }


}