<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarPertanyaan extends Model
{
    use HasFactory;

    protected $table = 'daftar_pertanyaans'; // Pastikan nama tabel ini sama persis di database
    protected $primaryKey = 'id';
    
    
    Protected $fillable = [
        'pertanyaan', // Pastikan ini sesuai dengan nama kolom di database Anda
        'catatan',
        'id_form_skrining',
    ];

    // Relasi many-to-many dengan DaftarPenyakit melalui tabel pivot penyakit_pertanyaans
    // public function penyakit()
    // {
    //     return $this->belongsToMany(DaftarPenyakit::class, 
    //     'penyakit_pertanyaans', 
    //     'id_daftar_pertanyaan', 
    //     'id_daftar_penyakit');
    // }

    // Relasi one-to-many dengan Jawaban (satu pertanyaan bisa punya banyak jawaban)
    public function jawabans()
    {
        
        // Foreign key di tabel 'jawabans' adalah 'ID_DaftarPertanyaan'
        // Primary key di tabel 'daftar_pertanyaans' (model ini) adalah 'id'
        return $this->hasMany(Jawaban::class, 'ID_DaftarPertanyaan', 'id');
    }

    public function formSkrining()
    {
        return $this->belongsTo(FormSkrining::class, 'id_form_skrining');
    }
}