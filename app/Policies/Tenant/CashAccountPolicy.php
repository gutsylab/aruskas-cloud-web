<?php

namespace App\Policies\Tenant;

use App\Models\Tenant\User;

class CashAccountPolicy
{
    static const viewAny = 'viewAny';
    static const view = 'view';
    static const create = 'create';
    static const update = 'update';
    static const archive = 'archive';
    static const restore = 'restore';
    static const delete = 'delete';


    public function viewAny(User $user): bool
    {
        return $user->hasPermission(self::viewAny);
    }
    public function view(User $user): bool
    {
        return $user->hasPermission(self::view);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(self::create);
    }

    public function update(User $user): bool
    {
        return $user->hasPermission(self::update);
    }

    public function archive(User $user): bool
    {
        return $user->hasPermission(self::archive);
    }

    public function restore(User $user): bool
    {
        return $user->hasPermission(self::restore);
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission(self::delete);
    }
}
