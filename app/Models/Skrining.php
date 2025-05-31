<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skrining extends Model
{
    use HasFactory;

    protected $table = 'skrinings';

    protected $fillable = [
        'Nama_Petugas',
        'NIK_Pasien',
        'Nama_Pasien',
        'Tanggal_Skrining',
        'id_form_skrining' // Pastikan id_daftar_penyakit TIDAK ada di sini
    ];

    protected $casts = [
        'Tanggal_Skrining' => 'date',
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'NIK_Pasien', 'NIK');
    }

    public function formSkrining() // Relasi ini akan digunakan untuk mendapatkan nama skrining dan penyakitnya
    {
        return $this->belongsTo(FormSkrining::class, 'id_form_skrining', 'id');
    }

    public function jawabans()
    {
        return $this->hasMany(Jawaban::class, 'ID_Skrining', 'id');
    }

    
}
