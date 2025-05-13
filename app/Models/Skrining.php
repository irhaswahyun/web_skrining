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
        'ID_penyakit_pertanyaan',
    ];

    protected $casts = [
        'Tanggal_Skrining' => 'date',
    ];

    public function formSkrining()
    {
        return $this->belongsTo(FormSkrining::class);
    }

    public function penyakitPertanyaan()
{
    return $this->belongsTo(PenyakitPertanyaan::class, 'ID_penyakit_pertanyaan');
}
}