<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSkrining extends Model
{
    use HasFactory;

    protected $table = 'form_skrinings';
    protected $fillable = [
        'nama_skrining',
        'id_daftar_penyakit',
    ];

    public function penyakit()
    {
        return $this->belongsTo(DaftarPenyakit::class, 'id_daftar_penyakit');
    }

    // Perbaikan pada getPertanyaanTerkait
    public function getPertanyaanTerkait()
    {
        // Menggunakan relasi 'pertanyaan' yang seharusnya didefinisikan di model DaftarPenyakit
        return $this->penyakit->pertanyaan ?? collect(); // Menggunakan null coalescing untuk menghindari error jika penyakit tidak ada
    }
}