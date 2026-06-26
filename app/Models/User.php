<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'nama', 'username', 'email', 'password', 'no_hp', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'guru_id');
    }

    public function jurnal(): HasMany
    {
        return $this->hasMany(Jurnal::class, 'guru_id');
    }

    public function kinerja(): HasMany
    {
        return $this->hasMany(KinerjaGuru::class, 'guru_id');
    }

    public function validasiJurnal(): HasMany
    {
        return $this->hasMany(Jurnal::class, 'validated_by');
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }
}
