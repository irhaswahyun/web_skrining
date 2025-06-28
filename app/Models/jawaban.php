<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    use HasFactory;

    protected $table = 'jawabans'; // Pastikan nama tabel ini sama persis di database
    // protected $primaryKey = 'id';

    protected $fillable = [
        'jawaban',
        'ID_DaftarPertanyaan', // Pastikan nama kolom ini sama persis di database Anda
        'ID_Skrining',         // Pastikan nama kolom ini sama persis di database Anda
    ];

    // Relasi many-to-one dengan DaftarPertanyaan
    public function pertanyaan()
    {
        // foreign key 'ID_DaftarPertanyaan' di tabel 'jawabans'
        // Pastikan nama kolom 'ID_DaftarPertanyaan' di tabel 'jawabans' dan tipe datanya BIGINT UNSIGNED
        return $this->belongsTo(DaftarPertanyaan::class, 'ID_DaftarPertanyaan','id');
    }

    // Relasi many-to-one dengan FormSkrining
    public function formSkrining()
    {
        // foreign key 'ID_Skrining' di tabel 'jawabans'
        // Pastikan nama kolom 'ID_Skrining' di tabel 'jawabans' dan tipe datanya BIGINT UNSIGNED
        return $this->belongsTo(FormSkrining::class, 'ID_Skrining','id');
    }
}