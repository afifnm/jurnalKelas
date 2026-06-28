<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jadwal extends Model
{
    use SoftDeletes;

    protected $table = 'jadwal';

    protected $fillable = [
        'guru_id', 'kelas_id', 'mapel_id', 'tahun_ajaran_id',
        'hari', 'jam_mulai', 'jam_selesai',
    ];

    protected function casts(): array
    {
        return ['hari' => 'integer'];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id')->withTrashed();
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class)->withTrashed();
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class)->withTrashed();
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class)->withTrashed();
    }

    public function jurnal(): HasMany
    {
        return $this->hasMany(Jurnal::class);
    }

    public function getNamaHariAttribute(): string
    {
        return match ($this->hari) {
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu',
            4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu',
            default => '-',
        };
    }

    public static function getNamaHariList(): array
    {
        return [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
    }
}
