<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0)->index();
            $table->string('nama_pasien', 150);
            $table->string('telpon', 20);
            $table->string('dokter', 150);
            $table->string('layanan', 100);
            $table->date('tanggal')->index();
            $table->string('jam', 10);
            $table->string('jaminan', 50)->default('Umum / Mandiri')->nullable();
            $table->text('catatan')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['menunggu', 'terkonfirmasi', 'dibatalkan', 'selesai'])
                  ->default('menunggu')->index();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};
