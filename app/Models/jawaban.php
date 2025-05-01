<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class jawaban extends Model
{
    use HasFactory;

    protected $fillable = ['ID_DaftarPertanyaan', 'jawaban', 'ID_Skrining'];

    public function pertanyaan()
    {
        return $this->belongsTo(daftar_pertanyaan::class, 'ID_DaftarPertanyaan');
    }

    public function skrining()
    {
        return $this->belongsTo(Skrining::class, 'ID_Skrining');
    }
}
