<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_lampiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_id')->constrained('jurnal')->cascadeOnDelete();
            $table->string('path');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_lampiran');
    }
};
