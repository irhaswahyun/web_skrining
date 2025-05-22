<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSkrining extends Model
{
    use HasFactory;

    protected $table = 'form_skrinings';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'nama_form',
        'nama_skrining',
        'id_daftar_penyakit',
    ];

    public function penyakit()
    {
        return $this->belongsTo(DaftarPenyakit::class, 'id_daftar_penyakit', 'id');
    }

    public function pertanyaan()
    {
        return $this->belongsToMany(DaftarPertanyaan::class, 'id_form_skrinings','penyakit_pertanyaans', 'id_daftar_penyakit', 'id_daftar_pertanyaan');
    }

    // Perbaikan pada getPertanyaanTerkait
    public function getPertanyaanTerkait()
    {
        // Menggunakan relasi 'pertanyaan' yang seharusnya didefinisikan di model DaftarPenyakit
        return $this->penyakit->pertanyaan ?? collect(); // Menggunakan null coalescing untuk menghindari error jika penyakit tidak ada
    }
}