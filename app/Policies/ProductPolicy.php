<?php

namespace App\Policies;

use App\Constants\Permissions;
use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo(Permissions::VIEW_ALL_PRODUCTS);
    }

    public function view(User $user, Product $product)
    {
        $isAllow = $user->hasPermissionTo(Permissions::VIEW_PRODUCT);
        return $isAllow;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo(Permissions::CREATE_PRODUCT);
    }

    public function update(User $user, Product $product)
    {
        return $user->hasPermissionTo(Permissions::UPDATE_PRODUCT);
    }

    public function delete(User $user, Product $product)
    {
        return $user->hasPermissionTo(Permissions::DELETE_PRODUCT);
    }
}
