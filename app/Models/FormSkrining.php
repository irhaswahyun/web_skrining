<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormSkrining extends Model
{
    protected $fillable = ['nama_skrining', 'id_daftar_penyakit'];

    public function pertanyaan()
    {
        return $this->belongsToMany(daftar_pertanyaan::class, 'penyakit_pertanyaans', 'id_daftar_penyakit', 'id_daftar_pertanyaan');
    }


    public function penyakit()
    {
        return $this->belongsTo(daftar_penyakit::class, 'id_daftar_penyakit', 'id');
    }
}
