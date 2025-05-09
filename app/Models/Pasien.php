<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pasien extends Model
{
    use HasFactory;

    protected $fillable = [
        'NIK', 
        'Nama_Pasien', 
        'Tanggal_Lahir', 
        'Kategori', 
        'Jenis_Kelamin', 
        'Alamat', 
        'No_telp',
    ];
}
