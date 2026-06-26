<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->nullable()->constrained('jadwal')->nullOnDelete();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('mapel')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk_aktual')->nullable();
            $table->time('jam_keluar_aktual')->nullable();
            $table->text('materi');
            $table->string('metode_pembelajaran')->nullable();
            $table->text('kendala')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'submitted', 'validated', 'revisi'])->default('draft');
            $table->boolean('is_terlambat')->default(false);
            $table->integer('menit_terlambat')->default(0);
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->text('catatan_validasi')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['guru_id', 'tanggal']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal');
    }
};
