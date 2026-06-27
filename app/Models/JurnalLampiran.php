<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class JurnalLampiran extends Model
{
    protected $table = 'jurnal_lampiran';

    protected $fillable = ['jurnal_id', 'path', 'keterangan'];

    protected $appends = ['url'];

    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(Jurnal::class);
    }

    public function getUrlAttribute(): string
    {
        return URL::signedRoute('lampiran.show', ['path' => $this->path], now()->addHours(2));
    }
}
