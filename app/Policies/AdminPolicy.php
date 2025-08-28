<?php

namespace App\Policies;

use App\Models\User;

class AdminPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the user can access admin features.
     */
    public function accessAdmin(User $user): bool
    {
        return $user->role === 'admin' && $user->is_active;
    }

    /**
     * Determine if the user can manage users.
     */
    public function manageUsers(User $user): bool
    {
        return $user->role === 'admin' && $user->is_active;
    }

    /**
     * Determine if the user can manage products.
     */
    public function manageProducts(User $user): bool
    {
        return $user->role === 'admin' && $user->is_active;
    }

    /**
     * Determine if the user can manage categories.
     */
    public function manageCategories(User $user): bool
    {
        return $user->role === 'admin' && $user->is_active;
    }

    /**
     * Determine if the user can manage orders.
     */
    public function manageOrders(User $user): bool
    {
        return $user->role === 'admin' && $user->is_active;
    }

    /**
     * Determine if the user can view reports.
     */
    public function viewReports(User $user): bool
    {
        return $user->role === 'admin' && $user->is_active;
    }
}
