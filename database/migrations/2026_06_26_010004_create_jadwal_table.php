<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('mapel')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->tinyInteger('hari'); // 1=Senin, 2=Selasa, ..., 7=Minggu
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['guru_id', 'hari']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
