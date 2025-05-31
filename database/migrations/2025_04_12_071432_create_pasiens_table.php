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
        Schema::create('pasiens', function (Blueprint $table) {
            $table->id();
            $table->string('NIK');
            $table->string('Nama_Pasien');
            $table->date('Tanggal_Lahir');
            $table->string('Kategori');
            $table->string('Jenis_Kelamin');
            $table->string('Alamat');
            $table->string('Wilayah');
            $table->string('No_telp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasiens');
    }
};
