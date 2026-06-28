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
        'guru_id', 'kelas_id', 'mapel_id', 'tahun_ajaran_id', 'jam_pelajaran_id'
    ];

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

    public function jamPelajaran(): BelongsTo
    {
        return $this->belongsTo(JamPelajaran::class);
    }

    public function jurnal(): HasMany
    {
        return $this->hasMany(Jurnal::class);
    }

    // Accessors for backward compatibility and ease of access
    public function getHariAttribute()
    {
        return $this->jamPelajaran->hari ?? null;
    }

    public function getJamMulaiAttribute()
    {
        return $this->jamPelajaran->jam_mulai ?? null;
    }

    public function getJamSelesaiAttribute()
    {
        return $this->jamPelajaran->jam_selesai ?? null;
    }

    public function getJamKeAttribute()
    {
        return $this->jamPelajaran->jam_ke ?? null;
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
