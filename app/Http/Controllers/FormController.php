<?php

namespace App\Http\Controllers;

use App\Models\FormSkrining;
use Illuminate\Http\Request;
use App\Models\daftar_penyakit;
use App\Models\daftar_pertanyaan;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FormController extends Controller
{
    /**
     * Menampilkan daftar form skrining.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = FormSkrining::with(['penyakit', 'pertanyaan']);

        if ($request->search) {
            $query->where('nama_skrining', 'LIKE', '%' . $request->search . '%');
        }

        $formSkrinings = $query->get();
        $penyakits = daftar_penyakit::all();
        $pertanyaans = daftar_pertanyaan::all();

        // Hitung jumlah pertanyaan untuk setiap form skrining.
        foreach ($formSkrinings as $formSkrining) {
            $formSkrining->pertanyaans_count = $formSkrining->pertanyaan->count();
        }

        return view('admin.form_skrining.index', compact('formSkrinings', 'penyakits', 'pertanyaans'));
    }

    /**
     * Menyimpan form skrining baru.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_skrining' => 'required|string|max:255',
            'id_daftar_penyakit' => 'required|exists:daftar_penyakits,id',
            'pertanyaan_ids' => 'required|array',
            'pertanyaan_ids.*' => 'exists:daftar_pertanyaans,id',
        ]);

        // Simpan form skrining.
        $form = new FormSkrining();
        $form->nama_skrining = $request->nama_skrining;
        $form->id_daftar_penyakit = $request->id_daftar_penyakit;
        $form->save();

        // Simpan relasi ke pertanyaan (many-to-many).
        $form->pertanyaan()->sync($request->pertanyaan_ids);

        return redirect()->route('form_skrining.index')->with('success', 'Form skrining berhasil disimpan.');
    }

    public function detail($id) {
        $formSkrining = FormSkrining::with('pertanyaan')->findOrFail($id);
        $penyakits = daftar_penyakit::all();
        $pertanyaans = daftar_pertanyaan::all();

        return view('admin.form_skrining.detail', compact('formSkrining', 'penyakits', 'pertanyaans'));
    }

    /**
     * Menampilkan form untuk mengedit form skrining.
     *
     * @param int $id
     * @return View
     */
    public function edit($id): View
    {
        $formSkrining = FormSkrining::with('pertanyaan')->findOrFail($id);
        $penyakits = daftar_penyakit::all();
        $pertanyaans = daftar_pertanyaan::all();

        // Untuk mengirimkan informasi apakah pertanyaan sudah di cek atau belum.
        $pertanyaanIds = $formSkrining->pertanyaan->pluck('id')->toArray();
        $pertanyaanList = [];
        foreach ($pertanyaans as $pertanyaan) {
            $pertanyaanList[] = [
                'id' => $pertanyaan->id,
                'pertanyaan' => $pertanyaan->pertanyaan,
                'checked' => in_array($pertanyaan->id, $pertanyaanIds),
            ];
        }

        return view('admin.form_skrining.edit', compact('formSkrining', 'penyakits', 'pertanyaanList'));
    }

    /**
     * Memperbarui form skrining.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'nama_skrining' => 'required|string|max:255',
            'id_daftar_penyakit' => 'required|exists:daftar_penyakits,id',
            'pertanyaan_ids' => 'required|array',
            'pertanyaan_ids.*' => 'exists:daftar_pertanyaan,id',
        ]);

        // Update form skrining
        $form = FormSkrining::findOrFail($id);
        $form->nama_skrining = $request->nama_skrining;
        $form->id_daftar_penyakit = $request->id_daftar_penyakit;
        $form->save();

        // Update relasi ke pertanyaan
        $form->pertanyaan()->sync($request->pertanyaan_ids);

        return redirect()->route('form_skrining.index')->with('success', 'Form skrining berhasil diperbarui.');
    }

    /**
     * Menghapus form skrining.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function delete(FormSkrining $formSkrining): RedirectResponse // Menggunakan Model Injection
    {
        $formSkrining->pertanyaan()->detach(); // Hapus relasi many-to-many terlebih dahulu
        $formSkrining->delete();

        return redirect()->route('form_skrining.index')->with('success', 'Form skrining berhasil dihapus.');
    }
}

