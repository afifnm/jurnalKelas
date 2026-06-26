<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KinerjaGuru extends Model
{
    protected $table = 'kinerja_guru';

    protected $fillable = [
        'guru_id', 'periode', 'total_jadwal', 'total_terisi',
        'persen_kepatuhan', 'total_terlambat', 'rata_keterlambatan_menit',
        'total_validated', 'skor_kinerja',
    ];

    protected function casts(): array
    {
        return [
            'persen_kepatuhan'       => 'float',
            'rata_keterlambatan_menit' => 'float',
            'skor_kinerja'           => 'float',
        ];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function getSkorWarnAttribute(): string
    {
        if ($this->skor_kinerja >= 80) return 'text-green-600 dark:text-green-400';
        if ($this->skor_kinerja >= 60) return 'text-amber-600 dark:text-amber-400';
        return 'text-red-600 dark:text-red-400';
    }
}
