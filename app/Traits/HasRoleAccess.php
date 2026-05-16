<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasRoleAccess
{
    /**
     * Check if current user can access this resource
     */
    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Admin bisa akses semua
        if ($user->isAdmin()) {
            return true;
        }

        // Pemilik hanya bisa akses laporan
        if ($user->isPemilik()) {
            return static::canAccessByPemilik();
        }

        return false;
    }

    /**
     * Check if pemilik can access this resource
     * Override this method in each resource
     */
    protected static function canAccessByPemilik(): bool
    {
        return false; // Default: tidak bisa akses
    }

    /**
     * Check if current user can create
     */
    public static function canCreate(): bool
    {
        $user = Auth::user();
        
        // Hanya admin yang bisa create
        return $user && $user->isAdmin();
    }

    /**
     * Check if current user can edit
     */
    public static function canEdit($record): bool
    {
        $user = Auth::user();
        
        // Hanya admin yang bisa edit
        return $user && $user->isAdmin();
    }

    /**
     * Check if current user can delete
     */
    public static function canDelete($record): bool
    {
        $user = Auth::user();
        
        // Hanya admin yang bisa delete
        return $user && $user->isAdmin();
    }

    /**
     * Check if current user can view
     */
    public static function canView($record): bool
    {
        return static::canAccess();
    }
}
