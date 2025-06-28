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
        Schema::create('diagnosas', function (Blueprint $table) { // Pastikan nama tabel di sini sama dengan yang dicari Laravel
            $table->id(); // Kolom ID otomatis (bigIncrements)
            $table->foreignId('skrining_id')->constrained('skrinings')->onDelete('cascade'); // Foreign key ke tabel 'skrining'
            $table->string('jenis_penyakit')->nullable(); // Sesuaikan tipe data dan nullable sesuai kebutuhan
            $table->text('hasil_utama')->nullable();
            $table->text('rekomendasi_tindak_lanjut')->nullable();
            $table->text('detail_diagnosa')->nullable();
            $table->text('catatan')->nullable();
            $table->date('tanggal_diagnosa'); // Atau $table->timestamp('tanggal_diagnosa'); jika ini timestamp

            $table->timestamps(); // Ini akan membuat kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosas');
    }
};
