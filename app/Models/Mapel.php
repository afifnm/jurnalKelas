<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mapel extends Model
{
    use SoftDeletes;

    protected $table = 'mapel';

    protected $fillable = ['nama', 'kode'];

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function jurnal(): HasMany
    {
        return $this->hasMany(Jurnal::class);
    }
}
