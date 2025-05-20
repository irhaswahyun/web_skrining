<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarPertanyaan extends Model
{
    use HasFactory;

    protected $table = 'daftar_pertanyaans'; // Pastikan nama tabel ini sama persis di database

    protected $fillable = [
        'pertanyaan', // Pastikan ini sesuai dengan nama kolom di database Anda
        'catatan',
    ];

    // Relasi many-to-many dengan DaftarPenyakit melalui tabel pivot penyakit_pertanyaans
    public function penyakit()
    {
        // Pastikan nama tabel pivot 'penyakit_pertanyaans' benar
        // 'daftar_pertanyaan_id' = foreign key di tabel pivot yang merujuk ke model ini (DaftarPertanyaan)
        // 'daftar_penyakit_id' = foreign key di tabel pivot yang merujuk ke DaftarPenyakit
        // Ini adalah konvensi Laravel, jadi asumsikan nama kolom di tabel pivot sudah sesuai
        return $this->belongsToMany(DaftarPenyakit::class, 'penyakit_pertanyaans', 'id_daftar_pertanyaan', 'id_daftar_penyakit');
    }

    // Relasi one-to-many dengan Jawaban (satu pertanyaan bisa punya banyak jawaban)
    public function jawaban()
    {
        // foreign key 'ID_DaftarPertanyaan' di tabel 'jawabans'
        // 'id' adalah kunci lokal di tabel ini (daftar_pertanyaans)
        // Pastikan nama kolom 'ID_DaftarPertanyaan' di tabel 'jawabans' dan tipe datanya BIGINT UNSIGNED
        return $this->hasMany(Jawaban::class, 'ID_DaftarPertanyaan', 'id');
    }
}