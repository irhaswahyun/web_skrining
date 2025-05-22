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
                 // Menambahkan unique constraint gabungan pada NIK_Pasien dan id_form_skrining
                 // Ini memastikan setiap pasien hanya bisa melakukan satu jenis skrining satu kali
                 $table->unique(['NIK_Pasien', 'id_form_skrining'], 'unique_skrining_per_patient');
             });
         }

         /**
          * Reverse the migrations.
          */
         public function down(): void
         {
             Schema::table('skrinings', function (Blueprint $table) {
                 // Menghapus unique constraint jika migrasi di-rollback
                 $table->dropUnique('unique_skrining_per_patient');
             });
         }
};
