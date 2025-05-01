<?php

namespace App\Http\Controllers;

use App\Models\daftar_penyakit;
use App\Models\daftar_pertanyaan;
use Illuminate\Http\Request;

class FormController extends Controller
{
     // Tampilkan daftar penyakit untuk memilih pertanyaan
     public function index()
     {
         $penyakits = daftar_penyakit::all();
         return view('admin.form_skrining.index', compact('penyakits'));
     }
 
     // Tampilkan form untuk memilih pertanyaan sesuai penyakit
     public function editPertanyaan($id)
     {
         $penyakit = daftar_penyakit::findOrFail($id);
         $pertanyaans = daftar_pertanyaan::all();
         $selectedPertanyaanIds = optional($penyakit->pertanyaans)->pluck('id') ?? collect(); // pertanyaan yang sudah dipilih
 
         return view('admin.form_skrining.edit', compact('penyakit', 'pertanyaans', 'selectedPertanyaanIds'));
     }
 
     // Simpan pertanyaan yang dipilih untuk penyakit tertentu
     public function updatePertanyaan(Request $request, $id)
     {
         $request->validate([
             'pertanyaan_ids' => 'required|array',
             'pertanyaan_ids.*' => 'exists:daftar_pertanyaans,id',
         ]);
 
         $penyakit = daftar_penyakit::findOrFail($id);
         $penyakit->pertanyaans()->sync($request->pertanyaan_ids); // relasi many-to-many
 
         return redirect()->route('form.index')->with('success', 'Pertanyaan berhasil diperbarui.');
     }
}