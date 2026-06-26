<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $table = 'sekolah';

    protected $fillable = [
        'nama', 'nama_yayasan', 'npsn', 'alamat',
        'kepala_sekolah', 'logo', 'telepon', 'email', 'website',
    ];
}
