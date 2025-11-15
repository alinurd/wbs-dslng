<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Gate;

class PermissionHelper
{
    /**
     * Check if user has any of the given permissions
     */
    public static function check($permission, $abort = true, $message = 'Unauthorized')
    {
        if (auth()->guest()) {
            if ($abort) abort(403, 'Not authenticated');
            return false;
        }

        $hasPermission = auth()->user()->can($permission);

        if (!$hasPermission && $abort) {
            abort(403, $message);
        }

        return $hasPermission;
    }

    /**
     * Check multiple permissions (OR condition)
     */
    public static function any($permissions, $abort = true, $message = 'Unauthorized')
    {
        if (auth()->guest()) {
            if ($abort) abort(403, 'Not authenticated');
            return false;
        }

        $hasAnyPermission = auth()->user()->canany($permissions);

        if (!$hasAnyPermission && $abort) {
            abort(403, $message);
        }

        return $hasAnyPermission;
    }

    /**
     * Check all permissions (AND condition)
     */
    public static function all($permissions, $abort = true, $message = 'Unauthorized')
    {
        if (auth()->guest()) {
            if ($abort) abort(403, 'Not authenticated');
            return false;
        }

        $hasAllPermissions = auth()->user()->canall($permissions);

        if (!$hasAllPermissions && $abort) {
            abort(403, $message);
        }

        return $hasAllPermissions;
    }

    /**
     * Get user permissions for a specific module
     */
    public static function modulePermissions($module = '')
    {
        if (auth()->guest()) {
            return [
                'authenticated' => false,
                'module' => $module,
                'can' => [
                    'view' => false,
                    'create' => false,
                    'edit' => false,
                    'delete' => false,
                    'manage' => false,
                ]
            ];
        }

        $user = auth()->user();
        
        return [
            'authenticated' => true,
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'module' => $module,
            'can' => [
                'view' => $user->can($module . '.view'),
                'create' => $user->can($module . '.create'),
                'edit' => $user->can($module . '.edit'),
                'delete' => $user->can($module . '.delete'),
                'manage' => $user->can($module . '.manage'),
            ]
        ];
    }

    /**
     * Shortcut for common CRUD permissions
     */
    public static function crud($module, $operations = ['view', 'create', 'edit', 'delete'])
    {
        $permissions = [];
        foreach ($operations as $operation) {
            $permissions[] = $module . '.' . $operation;
        }
        
        return self::any($permissions, false);
    }

    /**
     * Shortcut for common CRUD permissions
     */
    public static function getUsers($id=null)
    {
        $user=auth()->user();
        if(!$id){
            auth()->user($id);
        }
        return $user;
    }
}