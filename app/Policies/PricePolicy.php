<?php

namespace App\Policies;

use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PricePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function index(User $user): bool
    {
         return $user->can('price.index');
    }

    /**
     * Determine whether the user can create models.
     */
    public function store(User $user): bool
    {
         return $user->can('price.store');
    }




}
