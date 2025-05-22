<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PenyakitPertanyaan extends Model
{
    use HasFactory;

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