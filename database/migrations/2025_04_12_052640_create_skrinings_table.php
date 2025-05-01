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
            $table->string('ID_Pasien');
            $table->string('ID_Pengguna');
            $table->string('Unit_Pelayanan');
            $table->date('Tanggal_Skrining');
            $table->string('ID_DaftarPertanyaan');
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
