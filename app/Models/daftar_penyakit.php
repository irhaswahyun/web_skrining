<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class daftar_penyakit extends Model
{
    use HasFactory;

    protected $fillable = ['Nama_Penyakit'];

    public function pertanyaan() {
        return $this->belongsToMany(daftar_pertanyaan::class, 'form_skrinings', 'id_daftar_penyakit', 'id_daftar_pertanyaan');
    }
}
