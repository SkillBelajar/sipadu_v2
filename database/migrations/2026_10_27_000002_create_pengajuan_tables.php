<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_id')->constrained('layanan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->timestamps();
        });

        Schema::create('dokumen_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan')->cascadeOnDelete();
            $table->foreignId('syarat_id')->constrained('syarat_layanan')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('status')->default('pending');
            $table->text('catatan_verifikator')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_pengajuan');
        Schema::dropIfExists('pengajuan');
    }
};
