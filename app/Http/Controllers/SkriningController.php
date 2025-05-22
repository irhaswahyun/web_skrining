<?php

namespace App\Http\Controllers;

use App\Models\DaftarPenyakit;
use App\Models\DaftarPertanyaan;
use App\Models\Skrining;
use App\Models\Jawaban;
use App\Models\FormSkrining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;

class SkriningController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $skrinings = Skrining::with('pasien', 'formSkrining.penyakit')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('pasien', function ($subQuery) use ($search) {
                    $subQuery->where('Nama_Pasien', 'like', '%' . $search . '%');
                });
            })
            // PERBAIKAN DI SINI: Ubah 'desc' menjadi 'asc' untuk Tanggal_Skrining
            ->orderBy('Tanggal_Skrining', 'asc')
            // Anda juga bisa menambahkan pengurutan sekunder berdasarkan ID
            // ->orderBy('id', 'asc')
            ->paginate(10);

        $formSkrinings = FormSkrining::all();
        $penyakits = DaftarPenyakit::all();
        $pertanyaans = DaftarPertanyaan::all();

        return view('admin.skrining.index', compact('skrinings', 'formSkrinings', 'penyakits', 'pertanyaans'));
    }

    public function getPertanyaanByFormSkrining($id)
    {
        try {
            $formSkrining = FormSkrining::with('penyakit.pertanyaan')->findOrFail($id);

            if ($formSkrining->penyakit && $formSkrining->penyakit->pertanyaan) {
                $pertanyaan = $formSkrining->penyakit->pertanyaan;
                return response()->json($pertanyaan);
            } else {
                return response()->json([], 200);
            }
        } catch (ModelNotFoundException $e) {
            Log::error('Formulir Skrining tidak ditemukan: ' . $e->getMessage());
            return response()->json(['error' => 'Formulir Skrining tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil pertanyaan (dari getPertanyaanByFormSkrining): ' . $e->getMessage() . ' - ' . $e->getFile() . ' on line ' . $e->getLine());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat pertanyaan. Silakan cek konsol browser untuk detail lebih lanjut.'], 500);
        }
    }

     public function store(Request $request)
         {
             $request->validate([
                 'Nama_Petugas' => 'required|string|max:255',
                 'NIK_Pasien' => [
                     'required',
                     'string',
                     'max:255',
                     // PERBAIKAN: Aturan unique untuk kombinasi NIK_Pasien dan id_form_skrining
                     Rule::unique('skrinings')->where(function ($query) use ($request) {
                         return $query->where('NIK_Pasien', $request->NIK_Pasien)
                                      ->where('id_form_skrining', $request->id_form_skrining);
                     }),
                 ],
                 'Nama_Pasien' => 'required|string|max:255',
                 'Tanggal_Skrining' => 'required|date',
                 'id_form_skrining' => 'required|exists:form_skrinings,id',
                 'jawaban' => 'required|array',
                 'jawaban.*' => 'nullable|string|max:1000',
             ], [
                 // Pesan kustom untuk validasi unique
                 'NIK_Pasien.unique' => 'Pasien ini sudah melakukan skrining untuk jenis formulir yang dipilih.',
             ]);

             try {
                 $formSkrining = FormSkrining::find($request->id_form_skrining);
                 if (!$formSkrining) {
                     return response()->json(['success' => false, 'message' => 'Formulir Skrining tidak ditemukan.'], 404);
                 }

                 $skrining = Skrining::create([
                     'Nama_Petugas' => $request->Nama_Petugas,
                     'NIK_Pasien' => $request->NIK_Pasien,
                     'Nama_Pasien' => $request->Nama_Pasien,
                     'Tanggal_Skrining' => $request->Tanggal_Skrining,
                     'id_form_skrining' => $request->id_form_skrining,
                 ]);

                 foreach ($request->jawaban as $pertanyaan_id => $isi_jawaban) {
                     if (!is_null($isi_jawaban) && $isi_jawaban !== '') {
                         Jawaban::create([
                             'ID_Skrining' => $skrining->id,
                             'ID_DaftarPertanyaan' => $pertanyaan_id,
                             'jawaban' => $isi_jawaban,
                         ]);
                     }
                 }

                 return response()->json(['success' => true, 'message' => 'Skrining berhasil ditambahkan!']);

             } catch (\Exception $e) {
                 Log::error('Error saving skrining: ' . $e->getMessage() . ' - ' . $e->getFile() . ' on line ' . $e->getLine());
                 return response()->json(['success' => false, 'message' => 'Gagal menambahkan skrining. Silakan coba lagi.'], 500);
             }
         }

        public function show($id)
        {
        $skrining = Skrining::with([
            'pasien',
            'formSkrining.penyakit.pertanyaan',
            'jawabans.daftarPertanyaan'
        ])->find($id);

        if (!$skrining) {
            return response()->json(['message' => 'Skrining tidak ditemukan'], 404);
        }

        $detailData = [
            'id' => $skrining->id,
            'Nama_Petugas' => $skrining->Nama_Petugas,
            'NIK_Pasien' => $skrining->NIK_Pasien,
            'Nama_Pasien' => $skrining->Nama_Pasien,
            'Tanggal_Skrining' => $skrining->Tanggal_Skrining->format('Y-m-d'),
            'id_form_skrining' => $skrining->id_form_skrining,
            'nama_skrining' => $skrining->formSkrining->nama_skrining ?? '-',
            'nama_penyakit' => $skrining->formSkrining->penyakit->nama_penyakit ?? '-',
            'pertanyaan' => [],
        ];

        if ($skrining->formSkrining && $skrining->formSkrining->penyakit && $skrining->formSkrining->penyakit->pertanyaan) {
            foreach ($skrining->formSkrining->penyakit->pertanyaan as $pertanyaan) {
                $jawabanObj = $skrining->jawabans->firstWhere('ID_DaftarPertanyaan', $pertanyaan->id);
                $detailData['pertanyaan'][] = [
                    'id' => $pertanyaan->id,
                    'pertanyaan' => $pertanyaan->pertanyaan,
                    'jawaban' => $jawabanObj ? $jawabanObj->jawaban : '',
                ];
            }
        }

        return response()->json($detailData);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama_Petugas' => 'required|string|max:255',
            'NIK_Pasien' => 'required|string|max:255',
            'Nama_Pasien' => 'required|string|max:255',
            'Tanggal_Skrining' => 'required|date',
            'id_form_skrining_edit' => 'required|exists:form_skrinings,id',
            'jawaban' => 'required|array',
            'jawaban.*' => 'nullable|string|max:1000',
        ]);

        try {
            $skrining = Skrining::find($id);
            if (!$skrining) {
                return response()->json(['success' => false, 'message' => 'Skrining tidak ditemukan.'], 404);
            }

            $formSkrining = FormSkrining::find($request->id_form_skrining_edit);
            if (!$formSkrining) {
                return response()->json(['success' => false, 'message' => 'Formulir Skrining untuk update tidak ditemukan.'], 404);
            }

            $skrining->update([
                'Nama_Petugas' => $request->Nama_Petugas,
                'NIK_Pasien' => $request->NIK_Pasien,
                'Nama_Pasien' => $request->Nama_Pasien,
                'Tanggal_Skrining' => $request->Tanggal_Skrining,
                'id_form_skrining' => $request->id_form_skrining_edit,
            ]);

            $skrining->jawabans()->delete();

            foreach ($request->jawaban as $pertanyaan_id => $isi_jawaban) {
                if (!is_null($isi_jawaban) && $isi_jawaban !== '') {
                    Jawaban::create([
                        'ID_Skrining' => $skrining->id,
                        'ID_DaftarPertanyaan' => $pertanyaan_id,
                        'jawaban' => $isi_jawaban,
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Skrining berhasil diperbarui!']);

        } catch (\Exception $e) {
            Log::error('Error updating skrining: ' . $e->getMessage() . ' - ' . $e->getFile() . ' on line ' . $e->getLine());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui skrining. Silakan coba lagi.'], 500);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $skrining = Skrining::findOrFail($id);
            $skrining->jawabans()->delete();
            $skrining->delete();
            DB::commit();
            return response()->json(['message' => 'Data skrining berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal menghapus data skrining: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data skrining.'], 500);
        }
    }
}
