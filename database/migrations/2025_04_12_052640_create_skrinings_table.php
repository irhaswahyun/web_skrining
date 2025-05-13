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
        Schema::create('skrinings', function (Blueprint $table) {
            $table->id();
            $table->string('Nama_Petugas');
            $table->string('NIK_Pasien');
            $table->string('Nama_Pasien');
            $table->date('Tanggal_Skrining');
            $table->string('ID_penyakit_pertanyaan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skrinings');
    }
};
