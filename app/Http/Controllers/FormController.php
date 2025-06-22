<?php

namespace App\Http\Controllers;

use App\Models\FormSkrining;
use Illuminate\Http\Request;
use App\Models\DaftarPertanyaan;
use App\Models\Pasien; // Import model Pasien
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule; // Pastikan ini diimpor untuk menggunakan Rule::unique

class FormController extends Controller
{
    public function index(Request $request): View
    {
        $query = FormSkrining::query();

        if ($request->search) {
            $query->where('nama_skrining', 'LIKE', '%' . $request->search . '%');
        }

        $formSkrinings = $query->get();
        $pertanyaans = DaftarPertanyaan::all();

        // Mengambil daftar kategori unik dari tabel 'pasiens'
        $kategoriOptions = Pasien::distinct('kategori')->pluck('kategori')->toArray();

        foreach ($formSkrinings as $formSkrining) {
            $formSkrining->pertanyaans_count = $formSkrining->getPertanyaanTerkait()->count();
        }

        return view('admin.form_skrining.index', compact('formSkrinings', 'pertanyaans', 'kategoriOptions'));
    }

    public function create(): View
    {
        // Mengambil daftar kategori unik dari tabel 'pasiens' untuk form pembuatan
        $kategoriOptions = Pasien::distinct('kategori')->pluck('kategori')->toArray();
        $allPertanyaans = DaftarPertanyaan::all();

        return view('admin.form_skrining.create', compact('kategoriOptions', 'allPertanyaans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // ATURAN UNIK UNTUK NAMA_SKRINING: harus unik di tabel form_skrinings pada kolom nama_skrining
            'nama_skrining' => 'required|string|max:255|unique:form_skrinings,nama_skrining',
            'kategori' => 'nullable|array',
            'kategori.*' => 'string|max:255',
            'pertanyaan_ids' => 'required|array',
            'pertanyaan_ids.*' => 'exists:daftar_pertanyaans,id',
        ], [
            // Pesan kustom untuk validasi unique
            'nama_skrining.unique' => 'Nama Skrining ini sudah ada. Harap gunakan nama yang berbeda.',
            // Anda juga bisa menambahkan pesan kustom untuk validasi lain jika diperlukan
        ]);

        $form = new FormSkrining();
        $form->nama_skrining = $request->nama_skrining;

        if ($request->has('nama_form')) {
            $form->nama_form = $request->nama_form;
        }

        $form->kategori = $request->input('kategori', []);

        $form->save();

        $form->pertanyaan()->sync($request->pertanyaan_ids);

        return redirect()->route('form_skrining.index')->with('success', 'Form skrining berhasil disimpan.');
    }

    public function detail($id)
    {
        $formSkrining = FormSkrining::findOrFail($id);
        $formSkrining->related_pertanyaan = $formSkrining->getPertanyaanTerkait();

        return response()->json(['formSkrining' => $formSkrining]);
    }

    public function edit($id)
    {
        $formSkrining = FormSkrining::findOrFail($id);
        $allPertanyaans = DaftarPertanyaan::all();
        $kategoriOptions = Pasien::distinct('kategori')->pluck('kategori')->toArray();
        $relatedPertanyaanIds = $formSkrining->getPertanyaanTerkait()->pluck('id')->toArray();

        return response()->json([
            'formSkrining' => $formSkrining,
            'allPertanyaans' => $allPertanyaans,
            'relatedPertanyaanIds' => $relatedPertanyaanIds,
            'kategoriOptions' => $kategoriOptions,
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            // ATURAN UNIK UNTUK NAMA_SKRINING PADA UPDATE
            // Rule::unique akan mengabaikan record dengan ID saat ini agar tidak error saat update nama yang sama
            'nama_skrining' => [
                'required',
                'string',
                'max:255',
                Rule::unique('form_skrinings', 'nama_skrining')->ignore($id),
            ],
            'kategori' => 'nullable|array',
            'kategori.*' => 'string|max:255',
            'pertanyaan_ids' => 'required|array',
            'pertanyaan_ids.*' => 'exists:daftar_pertanyaans,id',
        ], [
            // Pesan kustom untuk validasi unique
            'nama_skrining.unique' => 'Nama Skrining ini sudah ada. Harap gunakan nama yang berbeda.',
            // Anda juga bisa menambahkan pesan kustom untuk validasi lain jika diperlukan
        ]);

        $form = FormSkrining::findOrFail($id);
        $form->nama_skrining = $request->nama_skrining;

        if ($request->has('nama_form')) {
            $form->nama_form = $request->nama_form;
        }

        $form->kategori = $request->input('kategori', []);

        $form->save();

        $form->pertanyaan()->sync($request->pertanyaan_ids);

        return redirect()->route('form_skrining.index')->with('success', 'Form skrining berhasil diperbarui.');
    }

    public function delete(FormSkrining $formSkrining): RedirectResponse
    {
        $formSkrining->pertanyaan()->detach();
        $formSkrining->delete();

        return redirect()->route('form_skrining.index')->with('success', 'Form skrining berhasil dihapus.');
    }
}
