<?php

namespace App\Http\Controllers;

use App\Models\FormSkrining;
use Illuminate\Http\Request;
use App\Models\DaftarPenyakit;
use App\Models\DaftarPertanyaan;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FormController extends Controller
{
    public function index(Request $request): View
    {
        // Memuat relasi 'penyakit' yang telah didefinisikan di model FormSkrining
        $query = FormSkrining::with(['penyakit']);

        if ($request->search) {
            $query->where('nama_skrining', 'LIKE', '%' . $request->search . '%');
        }

        $formSkrinings = $query->get();
        $penyakits = DaftarPenyakit::all();
        $pertanyaans = DaftarPertanyaan::all(); // Mengirim semua pertanyaan ke view

        foreach ($formSkrinings as $formSkrining) {
            // Memanggil metode getPertanyaanTerkait() dari model FormSkrining
            $formSkrining->pertanyaans_count = $formSkrining->getPertanyaanTerkait()->count();
        }

        return view('admin.form_skrining.index', compact('formSkrinings', 'penyakits', 'pertanyaans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_skrining' => 'required|string|max:255',
            // id_daftar_penyakit di sini adalah nama dari form input,
            // yang akan dipetakan ke kolom 'id_daftar_penyakit' di database
            'id_daftar_penyakit' => 'required|exists:daftar_penyakits,id',
            'pertanyaan_ids' => 'required|array',
            'pertanyaan_ids.*' => 'exists:daftar_pertanyaans,id',
        ]);

        $form = new FormSkrining();
        $form->nama_skrining = $request->nama_skrining;
        // Gunakan nama kolom yang ada di database Anda untuk assignment
        // Ini akan dipetakan ke kolom 'id_daftar_penyakit' di database oleh model FormSkrining
        $form->id_daftar_penyakit = $request->id_daftar_penyakit;
        $form->save();

        // Mengaitkan pertanyaan ke penyakit yang dipilih (via model DaftarPenyakit)
        $daftarPenyakit = DaftarPenyakit::find($request->id_daftar_penyakit);
        if ($daftarPenyakit) {
            $daftarPenyakit->pertanyaan()->sync($request->pertanyaan_ids);
        }

        return redirect()->route('form_skrining.index')->with('success', 'Form skrining berhasil disimpan.');
    }

    public function detail($id)
    {
        $formSkrining = FormSkrining::with('penyakit')->findOrFail($id);
        // Menambahkan properti related_pertanyaan yang berisi koleksi pertanyaan terkait
        $formSkrining->related_pertanyaan = $formSkrining->getPertanyaanTerkait();

        return response()->json(['formSkrining' => $formSkrining]);
    }

    public function edit($id): View
    {
        $formSkrining = FormSkrining::with('penyakit')->findOrFail($id);
        $penyakits = DaftarPenyakit::all();
        $allPertanyaans = DaftarPertanyaan::all();

        $relatedPertanyaanIds = $formSkrining->getPertanyaanTerkait()->pluck('id')->toArray();

        $pertanyaanList = [];
        foreach ($allPertanyaans as $pertanyaan) {
            $pertanyaanList[] = [
                'id' => $pertanyaan->id,
                'pertanyaan' => $pertanyaan->pertanyaan,
                'checked' => in_array($pertanyaan->id, $relatedPertanyaanIds),
            ];
        }

        return view('admin.form_skrining.edit', compact('formSkrining', 'penyakits', 'pertanyaanList'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'nama_skrining' => 'required|string|max:255',
            'id_daftar_penyakit' => 'required|exists:daftar_penyakits,id',
            'pertanyaan_ids' => 'required|array',
            'pertanyaan_ids.*' => 'exists:daftar_pertanyaans,id',
        ]);

        $form = FormSkrining::findOrFail($id);
        $form->nama_skrining = $request->nama_skrining;
        // Gunakan nama kolom yang ada di database Anda untuk assignment
        // Ini akan dipetakan ke kolom 'id_daftar_penyakit' di database oleh model FormSkrining
        $form->id_daftar_penyakit = $request->id_daftar_penyakit;
        $form->save();

        // Mengupdate pertanyaan yang terkait dengan penyakit ini
        $daftarPenyakit = DaftarPenyakit::find($request->id_daftar_penyakit);
        if ($daftarPenyakit) {
            $daftarPenyakit->pertanyaan()->sync($request->pertanyaan_ids);
        }

        return redirect()->route('form_skrining.index')->with('success', 'Form skrining berhasil diperbarui.');
    }

    public function delete(FormSkrining $formSkrining): RedirectResponse
    {
        
        if ($formSkrining->penyakit) {
            $formSkrining->penyakit->pertanyaan()->detach();
        }

        $formSkrining->delete();

        return redirect()->route('form_skrining.index')->with('success', 'Form skrining berhasil dihapus.');
    }
}

