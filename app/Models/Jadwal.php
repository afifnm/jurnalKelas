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
        return $this->belongsTo(User::class, 'guru_id')
            ->withoutGlobalScope(\Illuminate\Database\Eloquent\SoftDeletingScope::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class)
            ->withoutGlobalScope(\Illuminate\Database\Eloquent\SoftDeletingScope::class);
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class)
            ->withoutGlobalScope(\Illuminate\Database\Eloquent\SoftDeletingScope::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
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

    /**
     * Kelompokkan jadwal berdasarkan aturan bisnis: 1 guru + 1 mapel + 1 kelas + 1 hari = 1 sesi jurnal.
     * JP yang guru, mapel dan kelasnya sama di hari yang sama selalu digabung jadi satu grup,
     * tidak peduli berurutan atau tidak.
     */
    public static function grupkanBerurutan(\Illuminate\Support\Collection $jadwal): \Illuminate\Support\Collection
    {
        $groups = collect();
        
        $grouped = $jadwal->groupBy(function ($j) {
            return $j->hari . '|' . $j->kelas_id . '|' . $j->mapel_id . '|' . $j->guru_id;
        });

        foreach ($grouped as $group) {
            // Urutkan berdasarkan jam_mulai agar jadwal pertama dan terakhir benar
            $sortedGroup = $group->sortBy(fn($j) => $j->jamPelajaran->jam_mulai)->values();
            $groups->push([
                'jadwal' => $sortedGroup,
                'ids' => $sortedGroup->pluck('id')->toArray()
            ]);
        }
        
        // Urutkan groups berdasarkan jam mulai dari jadwal pertama
        return $groups->sortBy(fn($g) => $g['jadwal']->first()->jamPelajaran->jam_mulai)->values();
    }
}
