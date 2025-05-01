<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class daftar_penyakit extends Model
{
    use HasFactory;

    protected $fillable = ['Nama_Penyakit'];

    public function pertanyaan() {
        return $this->hasMany(daftar_pertanyaan::class);
    }
}
