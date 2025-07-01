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
        'kategori',
    ];

    protected $casts = [
        'kategori' => 'array',
    ];

    // Relasi many-to-many dengan DaftarPertanyaan
    public function pertanyaan()
    {
        // Parameter belongsToMany:
        // 1. Nama model yang berelasi (DaftarPertanyaan::class)
        // 2. Nama tabel pivot ('form_skrining_pertanyaan')
        // 3. Foreign key dari model ini (FormSkrining) di tabel pivot ('form_skrining_id')
        // 4. Foreign key dari model berelasi (DaftarPertanyaan) di tabel pivot ('daftar_pertanyaan_id')
        return $this->belongsToMany(
            DaftarPertanyaan::class,
            'form_skrining_pertanyaan', // Nama tabel pivot yang benar
            'form_skrining_id',       // Foreign key FormSkrining di tabel pivot
            'daftar_pertanyaan_id'    // Foreign key DaftarPertanyaan di tabel pivot
        );
    }

    // Metode helper untuk mendapatkan pertanyaan terkait
    public function getPertanyaanTerkait()
    {
        return $this->pertanyaan()->orderBy('created_at', 'asc')->get();
    }

    public function penyakit()
    {
        return $this->belongsTo(DaftarPenyakit::class, 'penyakit_id'); // sesuaikan foreign key-nya
    }
}