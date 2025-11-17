<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditTrail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was affected
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include audits for a specific model
     */
    public function scopeForModel($query, $model)
    {
        return $query->where('model_type', get_class($model))
                     ->where('model_id', $model->id);
    }

    /**
     * Scope a query to only include audits by a specific user
     */
    public function scopeByUser($query, $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope a query to only include specific actions
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Get formatted changes for display
     */
    public function getFormattedChangesAttribute()
    {
        $changes = [];
        
        foreach ($this->new_values as $key => $value) {
            $oldValue = $this->old_values[$key] ?? null;
            if ($oldValue != $value) {
                $changes[] = [
                    'field' => $key,
                    'old' => $oldValue,
                    'new' => $value
                ];
            }
        }
        
        return $changes;
    }
}