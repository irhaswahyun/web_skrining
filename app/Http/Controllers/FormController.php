<?php

namespace App\Http\Controllers;

use App\Models\FormSkrining;
use Illuminate\Http\Request;
use App\Models\daftar_penyakit;
use App\Models\daftar_pertanyaan;

class FormController extends Controller
{
     // Tampilkan daftar penyakit untuk memilih pertanyaan
     public function index()
     {
         $penyakits = daftar_penyakit::all();
         $pertanyaans = daftar_pertanyaan::all();
         return view('admin.form_skrining.index', compact('penyakits', 'pertanyaans'));
     }

     public function store(Request $request)
    {
        $request->validate([
            'nama_skrining'   => 'required|string|max:255',
            'id_daftar_penyakit'     => 'required|exists:daftar_penyakits,id',
            'pertanyaan_ids'  => 'required|array',
            'pertanyaan_ids.*'=> 'exists:daftar_pertanyaans,id',
        ]);

        // Simpan form skrining
        $form = new FormSkrining();
        $form->nama_skrining = $request->nama_skrining;
        $form->id_daftar_penyakit = $request->id_daftar_penyakit;
        $form->save();

        // Simpan relasi ke pertanyaan (many-to-many)
        $form->pertanyaan()->sync($request->pertanyaan_ids);

        return redirect()->route('form_skrining.index')->with('success', 'Form skrining berhasil disimpan.');
    }
}