<?php

namespace App\Services;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditTrailService
{
    /**
     * Log an action in the audit trail
     */
    public static function log($action, $model, $oldValues = [], $newValues = [])
    {
        // Skip logging if no changes
        if (empty($oldValues) && empty($newValues)) {
            return;
        }
        
        // Only log if there are actual changes
        $hasChanges = false;
        foreach ($newValues as $key => $value) {
            $oldValue = $oldValues[$key] ?? null;
            if ($oldValue != $value) {
                $hasChanges = true;
                break;
            }
        }
        
        if (!$hasChanges && !empty($oldValues)) {
            return;
        }
        
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'method' => Request::method()
        ]);
    }
    
    /**
     * Log a create action
     */
    public static function logCreate($model, $values = [])
    {
        static::log('create', $model, [], $values);
    }
    
    /**
     * Log an update action
     */
    public static function logUpdate($model, $oldValues = [], $newValues = [])
    {
        static::log('update', $model, $oldValues, $newValues);
    }
    
    /**
     * Log a delete action
     */
    public static function logDelete($model, $values = [])
    {
        static::log('delete', $model, $values, []);
    }
    
    /**
     * Log a restore action (for soft deletes)
     */
    public static function logRestore($model, $values = [])
    {
        static::log('restore', $model, [], $values);
    }
}