<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::VIEW_ALL_ORDERS);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ?Order $order): bool
    {

        return  $user->hasPermissionTo(Permissions::VIEW_ORDER); // : response()->json(['error' => 'Order not found'], 404); ;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permissions::CREATE_ORDER);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        return $user->hasPermissionTo(Permissions::UPDATE_ORDER);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return $order? $user->hasPermissionTo(Permissions::DELETE_ORDER) : response()->json(['error' => 'Order not found'], 404);;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        //
    }
}
