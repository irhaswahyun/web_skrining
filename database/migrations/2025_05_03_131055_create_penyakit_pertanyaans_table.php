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
        Schema::create('form_skrining_pertanyaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_form_skrining')->references('id')->on('form_skrinings')->onDelete('cascade');
            $table->foreignId('id_daftar_pertanyaan')->references('id')->on('daftar_pertanyaans')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyakit_pertanyaans');
    }
};
