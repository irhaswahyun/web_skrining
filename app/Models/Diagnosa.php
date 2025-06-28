<?php

namespace App\Models; // Pastikan ini sesuai

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosa extends Model
{
    use HasFactory;

    protected $table = 'diagnosas'; // Pastikan nama tabel ini benar di database Anda (plural atau singular)
    protected $guarded = ['id']; // Ini akan mengizinkan semua kolom diisi kecuali 'id'

    // Atau jika Anda lebih suka $fillable, pastikan semua kolom yang Anda masukkan di create() ada di sini:
    // protected $fillable = [
    //     'skrining_id',
    //     'jenis_penyakit',
    //     'hasil_utama',
    //     'rekomendasi_tindak_lanjut',
    //     'detail_diagnosa',
    //     'catatan',
    //     'tanggal_diagnosa',
    // ];

    // Pastikan ini ada jika 'detail_diagnosa' disimpan sebagai JSON
    protected $casts = [
        'detail_diagnosa' => 'array',
        'tanggal_diagnosa' => 'datetime',
    ];

    public function skrining()
    {
        return $this->belongsTo(Skrining::class);
    }
}