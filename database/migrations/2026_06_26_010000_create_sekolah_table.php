<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->string('nama_yayasan')->nullable();
            $table->string('npsn', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kepala_sekolah')->nullable();
            $table->string('logo')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolah');
    }
};
