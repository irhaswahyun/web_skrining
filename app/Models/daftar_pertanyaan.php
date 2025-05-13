<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class daftar_pertanyaan extends Model
{
    use HasFactory;

    protected $fillable = ['pertanyaan' , 'catatan'];

    public function jawaban()
    {
        return $this->hasMany(jawaban::class, 'ID_DaftarPertanyaan');
    }

    public function penyakit() {
        return $this->belongsToMany(daftar_penyakit::class, 'form_skrinings', 'id_daftar_penyakit', 'id_daftar_pertanyaan');
    }
}
