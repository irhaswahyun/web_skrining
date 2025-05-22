<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class DaftarPenyakit extends Model
{
    use HasFactory;

    protected $table = 'daftar_penyakits'; // Nama tabel yang benar
    protected $primaryKey = 'id'; // Pastikan primary key adalah 'id'

    protected $fillable = ['Nama_Penyakit']; // Diubah menjadi Nama_Penyakit

    public function pertanyaan()
    {
        // Ini adalah relasi many-to-many melalui tabel pivot 'penyakit_pertanyaans'
        return $this->belongsToMany(DaftarPertanyaan::class,
        'penyakit_pertanyaans',
        'id_daftar_penyakit', // Foreign key di tabel pivot yang mengacu ke DaftarPenyakit
        'id_daftar_pertanyaan'); // Foreign key di tabel pivot yang mengacu ke DaftarPertanyaan
    }

    public function formSkrinings()
    {
        return $this->hasMany(FormSkrining::class, 'id_daftar_penyakit');
    }

    public function skrinings()
    {
        return $this->hasMany(Skrining::class, 'id_daftar_penyakit');
    }
}
