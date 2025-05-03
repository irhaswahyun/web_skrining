<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyakitPertanyaan extends Model
{
    protected $table = 'penyakit_pertanyaans';
    protected $fillable = ['id_daftar_penyakit', 'id_daftar_pertanyaan'];
}
