<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function index(User $user): bool
    {
        return $user->can('products.index');
    }

    public function show(User $user, Product $product): bool
    {
        return $user->can('products.show');
    }

    public function create(User $user): bool
    {
        return $user->can('products.store');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->can('products.update');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->can('products.destroy');
    }
}
