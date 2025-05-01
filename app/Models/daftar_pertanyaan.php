<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class daftar_pertanyaan extends Model
{
    use HasFactory;

    protected $fillable = ['Pertanyaan'];

    public function jawaban()
    {
        return $this->hasMany(jawaban::class, 'ID_DaftarPertanyaan');
    }

    public function penyakit() {
        return $this->hasMany(daftar_penyakit::class);
    }
}
