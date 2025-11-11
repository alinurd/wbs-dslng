<?php

use App\Helpers\PermissionHelper;

if (!function_exists('can')) {
    function can($permission, $abort = true, $message = 'Unauthorized')
    {
        return PermissionHelper::check($permission, $abort, $message);
    }
}

if (!function_exists('can_any')) {
    function can_any($permissions, $abort = true, $message = 'Unauthorized')
    {
        return PermissionHelper::any($permissions, $abort, $message);
    }
}

if (!function_exists('can_all')) {
    function can_all($permissions, $abort = true, $message = 'Unauthorized')
    {
        return PermissionHelper::all($permissions, $abort, $message);
    }
}

if (!function_exists('module_permissions')) {
    function module_permissions($module = '')
    {
        return PermissionHelper::modulePermissions($module);
    }
}

if (!function_exists('can_crud')) {
    function can_crud($module, $operations = ['view', 'create', 'edit', 'delete'])
    {
        return PermissionHelper::crud($module, $operations);
    }
}