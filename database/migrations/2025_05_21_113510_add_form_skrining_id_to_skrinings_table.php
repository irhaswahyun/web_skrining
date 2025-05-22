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
            $table->foreignId('id_form_skrining')->nullable()->constrained('form_skrinings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('skrinings', function (Blueprint $table) {
            $table->dropForeign(['id_form_skrining']);
            $table->dropColumn('id_form_skrining');
        });
    }
};
