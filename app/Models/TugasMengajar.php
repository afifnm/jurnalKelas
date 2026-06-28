<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TugasMengajar extends Model
{
    protected $table = 'tugas_mengajar';

    protected $fillable = [
        'tahun_ajaran_id', 'guru_id', 'mapel_id', 'kelas_id', 'jumlah_jam',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id')->withTrashed();
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class)->withTrashed();
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class)->withTrashed();
    }
}
