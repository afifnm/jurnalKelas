<?php

namespace App\Policies;

use App\Models\Jurnal;
use App\Models\User;

class JurnalPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Jurnal $jurnal): bool
    {
        if ($user->hasRole('guru')) {
            return $jurnal->guru_id === $user->id;
        }
        return $user->hasAnyRole(['admin', 'ks']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('guru');
    }

    public function update(User $user, Jurnal $jurnal): bool
    {
        if ($user->hasRole('guru')) {
            return $jurnal->guru_id === $user->id;
        }
        return false;
    }

    public function delete(User $user, Jurnal $jurnal): bool
    {
        if ($user->hasRole('guru')) {
            return $jurnal->guru_id === $user->id;
        }
        return $user->hasRole('admin');
    }

    public function restore(User $user, Jurnal $jurnal): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Jurnal $jurnal): bool
    {
        return $user->hasRole('admin');
    }
}
