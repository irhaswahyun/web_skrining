<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenyakitPertanyaan extends Model
{
    protected $table = 'penyakit_pertanyaans';
    protected $fillable = ['id_daftar_penyakit', 'id_daftar_pertanyaan'];

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