<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'pasiens';
    protected $primaryKey = 'NIK';
    public $incrementing = false;
    public $keyType = 'string';

    protected $fillable = [
        'NIK', 
        'Nama_Pasien', 
        'Tanggal_Lahir', 
        'Kategori', 
        'Jenis_Kelamin', 
        'Alamat', 
        'No_telp',
    ];

    public function skrining()
    {
        return $this->hasMany(Skrining::class, 'NIK_Pasien', 'NIK');
    }
}
