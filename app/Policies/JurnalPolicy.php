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
            return $jurnal->guru_id === $user->id && $jurnal->isEditableByGuru();
        }
        return false;
    }

    public function delete(User $user, Jurnal $jurnal): bool
    {
        if ($user->hasRole('guru')) {
            return $jurnal->guru_id === $user->id && $jurnal->status === 'draft';
        }
        return $user->hasRole('admin');
    }

    public function submit(User $user, Jurnal $jurnal): bool
    {
        return $user->hasRole('guru')
            && $jurnal->guru_id === $user->id
            && $jurnal->isEditableByGuru();
    }

    public function validate(User $user, Jurnal $jurnal): bool
    {
        return $user->hasAnyRole(['ks', 'admin'])
            && $jurnal->status === 'submitted';
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
