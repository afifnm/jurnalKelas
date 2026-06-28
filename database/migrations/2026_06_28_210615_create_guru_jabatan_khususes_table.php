<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_jabatan_khusus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->integer('jumlah_jam')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_jabatan_khusus');
    }
};
