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
        Schema::table('skrinings', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu jika ada
           
            // Kemudian hapus kolomnya
            $table->dropColumn('id_daftar_penyakit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skrinings', function (Blueprint $table) {
             $table->unsignedBigInteger('id_daftar_penyakit')->nullable();
            $table->foreign('id_daftar_penyakit')->references('id')->on('daftar_penyakits')->onDelete('set null');
        });
    }
};
