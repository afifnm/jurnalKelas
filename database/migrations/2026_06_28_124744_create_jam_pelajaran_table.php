<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jam_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('hari');    // 1=Senin ... 7=Minggu
            $table->tinyInteger('jam_ke'); // 1, 2, 3, ... 8
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();
            $table->unique(['hari', 'jam_ke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jam_pelajaran');
    }
};
