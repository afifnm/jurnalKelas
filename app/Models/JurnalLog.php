<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalLog extends Model
{
    protected $table = 'jurnal_log';

    public $timestamps = false;

    protected $fillable = ['jurnal_id', 'user_id', 'aksi', 'keterangan'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(Jurnal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
