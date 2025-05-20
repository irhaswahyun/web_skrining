<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class DaftarPenyakit extends Model
{
    use HasFactory;

    protected $table = 'daftar_penyakits'; // Nama tabel yang benar
    protected $primaryKey = 'id'; // Jika primary key bukan 'id', tentukan di sini

    protected $fillable = ['nama_penyakit'];

   public function pertanyaan()
    {
        return $this->belongsToMany(DaftarPertanyaan::class, 'penyakit_pertanyaans', 'id_daftar_penyakit', 'id_daftar_pertanyaan');
    }

    public function formSkrinings()
    {
        return $this->hasMany(FormSkrining::class, 'id_daftar_penyakit');
    }
}