<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormSkriningPertanyaan extends Model
{
    use HasFactory;

    protected $table = 'form_skrining_pertanyaans';
    protected $fillable = ['id_form_skrining', 'id_daftar_pertanyaan'];

    public function daftarPertanyaan()
    {
        return $this->belongsTo(DaftarPertanyaan::class, 'id_daftar_pertanyaan');
    }

    // Perbaikan pada relasi daftarPenyakit
    public function daftarPenyakit()
    {
       return $this->belongsTo(DaftarPenyakit::class, 'id_daftar_penyakit');
    }
}