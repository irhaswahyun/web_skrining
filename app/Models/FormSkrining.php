<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSkrining extends Model
{
    use HasFactory;

    protected $table = 'form_skrinings'; // Pastikan nama tabel benar
    protected $fillable = [
        'nama_skrining',
        'nama_form',
        'kategori', // Penting: Pastikan ini masuk fillable
    ];

    // PENTING: Cast kolom 'kategori' sebagai array.
    // Kolom 'kategori' di tabel form_skrinings HARUS menyimpan data JSON array (misal: ["Lansia", "Anak"]).
    // Jika tidak, whereJsonContains tidak akan bekerja dengan benar.
    protected $casts = [
        'kategori' => 'array',
    ];

    // Relasi many-to-many dengan DaftarPertanyaan
    public function pertanyaan()
    {
        return $this->belongsToMany(DaftarPertanyaan::class, 'form_skrining_pertanyaan', 'form_skrining_id', 'daftar_pertanyaan_id');
    }

    // Metode helper untuk mendapatkan pertanyaan terkait
    public function getPertanyaanTerkait()
    {
        return $this->pertanyaan()->orderBy('created_at', 'asc')->get();
    }
}
