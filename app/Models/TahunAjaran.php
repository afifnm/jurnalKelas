<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';

    protected $fillable = ['nama', 'semester', 'is_aktif'];

    protected function casts(): array
    {
        return ['is_aktif' => 'boolean'];
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function jurnal(): HasMany
    {
        return $this->hasMany(Jurnal::class);
    }

    public static function aktif(): ?self
    {
        return self::where('is_aktif', true)->first();
    }

    public function getLabelAttribute(): string
    {
        return "{$this->nama} - {$this->semester}";
    }
}
