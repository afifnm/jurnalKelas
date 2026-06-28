<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruJabatanKhusus extends Model
{
    protected $table = 'guru_jabatan_khusus';

    protected $fillable = [
        'guru_id',
        'tahun_ajaran_id',
        'jumlah_jam',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id')->withTrashed();
    }
}
