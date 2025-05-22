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
        Schema::table('form_skrinings', function (Blueprint $table) {
            // Menambahkan kolom 'kategori' setelah 'nama_skrining'
            // Anda bisa menyesuaikan tipe data dan atribut (nullable, default) sesuai kebutuhan
            $table->string('kategori')->nullable()->after('nama_skrining');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_skrinings', function (Blueprint $table) {
            // Menghapus kolom 'kategori' jika migrasi di-rollback
            $table->dropColumn('kategori');
        });
    }
};
