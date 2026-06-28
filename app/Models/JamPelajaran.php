<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JamPelajaran extends Model
{
    use SoftDeletes;

    protected $table = 'jam_pelajaran';

    protected $fillable = ['hari', 'jam_ke', 'jam_mulai', 'jam_selesai'];

    protected $casts = [
        'hari'   => 'integer',
        'jam_ke' => 'integer',
    ];

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }
}
