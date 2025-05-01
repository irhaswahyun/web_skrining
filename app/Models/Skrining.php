<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skrining extends Model
{
    use HasFactory;

    protected $table = 'skrinings';

    protected $fillable = [
        'ID_Pasien',
        'ID_Pengguna',
        'Unit_Pelayanan',
        'Tanggal_Skrining',
        'ID_DaftarPertanyaan',
    ];

    protected $casts = [
        'Tanggal_Skrining' => 'date',
    ];

    public function daftar_pertanyaan()
    {
        return $this->belongsTo(daftar_pertanyaan::class, 'ID_DaftarPertanyaan');
    }

    public function penyakit() {
        return $this->belongsTo(daftar_penyakit::class);
    }
}