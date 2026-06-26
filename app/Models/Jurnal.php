<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jurnal extends Model
{
    use SoftDeletes;

    protected $table = 'jurnal';

    protected $fillable = [
        'jadwal_id', 'guru_id', 'kelas_id', 'mapel_id', 'tahun_ajaran_id',
        'tanggal', 'jam_masuk_aktual', 'jam_keluar_aktual',
        'materi', 'metode_pembelajaran', 'kendala', 'tindak_lanjut', 'catatan',
        'status', 'is_terlambat', 'menit_terlambat',
        'validated_by', 'validated_at', 'catatan_validasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal'      => 'date',
            'validated_at' => 'datetime',
            'is_terlambat' => 'boolean',
            'menit_terlambat' => 'integer',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function lampiran(): HasMany
    {
        return $this->hasMany(JurnalLampiran::class);
    }

    public function log(): HasMany
    {
        return $this->hasMany(JurnalLog::class);
    }

    public function isEditableByGuru(): bool
    {
        return $this->status === 'draft';
    }

    public function getBadgeColorAttribute(): string
    {
        return 'badge-draft';
    }

    public function getStatusLabelAttribute(): string
    {
        return 'Terisi';
    }
}
