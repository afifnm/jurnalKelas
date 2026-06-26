<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kinerja_guru', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->string('periode', 7); // YYYY-MM
            $table->integer('total_jadwal')->default(0);
            $table->integer('total_terisi')->default(0);
            $table->decimal('persen_kepatuhan', 5, 2)->default(0);
            $table->integer('total_terlambat')->default(0);
            $table->decimal('rata_keterlambatan_menit', 8, 2)->default(0);
            $table->integer('total_validated')->default(0);
            $table->decimal('skor_kinerja', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['guru_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kinerja_guru');
    }
};
