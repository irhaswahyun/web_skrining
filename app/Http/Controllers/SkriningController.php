<?php

namespace App\Http\Controllers;

use App\Models\DaftarPenyakit;
use App\Models\DaftarPertanyaan;
use App\Models\Skrining;
use App\Models\Jawaban;
use App\Models\FormSkrining;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // Tambahkan ini untuk bekerja dengan tanggal

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
            ->orderBy('Tanggal_Skrining', 'asc')
            ->paginate(100);

        $formSkrinings = FormSkrining::all();
        $penyakits = DaftarPenyakit::all();
        $pertanyaans = DaftarPertanyaan::all();

        return view('admin.skrining.index', compact('skrinings', 'formSkrinings', 'penyakits', 'pertanyaans'));
    }

    public function getPertanyaanByFormSkrining(Request $request, $id)
    {
        try {
            $formSkrining = FormSkrining::with('penyakit.pertanyaan')->findOrFail($id);
            $pertanyaanList = collect(); // Inisialisasi koleksi kosong

            if ($formSkrining->penyakit && $formSkrining->penyakit->pertanyaan) {
                $pertanyaanList = $formSkrining->penyakit->pertanyaan;
            }

            // --- Logika untuk mendapatkan jawaban sebelumnya ---
            $nikPasien = $request->query('nik_pasien'); // Ambil NIK Pasien dari query parameter
            $tanggalSkrining = $request->query('tanggal_skrining'); // Ambil Tanggal Skrining dari query parameter

            if ($nikPasien && $tanggalSkrining) {
                $parsedDate = Carbon::parse($tanggalSkrining)->format('Y-m-d');

                // Dapatkan semua skrining yang dilakukan oleh pasien ini pada tanggal yang sama
                $skriningsToday = Skrining::where('NIK_Pasien', $nikPasien)
                                            ->whereDate('Tanggal_Skrining', $parsedDate)
                                            ->with('jawabans') // Eager load jawabans
                                            ->get();

                $previousAnswers = [];
                foreach ($skriningsToday as $skrining) {
                    foreach ($skrining->jawabans as $jawaban) {
                        // Simpan jawaban dengan ID_DaftarPertanyaan sebagai kunci
                        $previousAnswers[$jawaban->ID_DaftarPertanyaan] = $jawaban->jawaban;
                    }
                }

                // Masukkan jawaban sebelumnya ke dalam daftar pertanyaan
                $pertanyaanList = $pertanyaanList->map(function ($pertanyaan) use ($previousAnswers) {
                    $pertanyaan->previous_answer = $previousAnswers[$pertanyaan->id] ?? null;
                    return $pertanyaan;
                });
            }
            // --- Akhir logika jawaban sebelumnya ---

            return response()->json($pertanyaanList);

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
                // PERUBAHAN DI SINI: Aturan unique untuk kombinasi NIK_Pasien, id_form_skrining, dan Tanggal_Skrining
                Rule::unique('skrinings')->where(function ($query) use ($request) {
                    // Pastikan Tanggal_Skrining diformat ke tanggal saja untuk perbandingan
                    $tanggalSkrining = Carbon::parse($request->Tanggal_Skrining)->format('Y-m-d');

                    return $query->where('NIK_Pasien', $request->NIK_Pasien)
                                 ->where('id_form_skrining', $request->id_form_skrining)
                                 ->whereDate('Tanggal_Skrining', $tanggalSkrining); // Tambahkan validasi tanggal
                }),
            ],
            'Nama_Pasien' => 'required|string|max:255',
            'Tanggal_Skrining' => 'required|date',
            'id_form_skrining' => 'required|exists:form_skrinings,id',
            'jawaban' => 'required|array',
            'jawaban.*' => 'nullable|string|max:1000',
        ], [
            // Pesan kustom untuk validasi unique
            'NIK_Pasien.unique' => 'Pasien ini sudah melakukan skrining untuk jenis formulir yang sama pada tanggal yang sama.',
        ]);

        try {
            $formSkrining = FormSkrining::find($request->id_form_skrining);
            if (!$formSkrining) {
                return response()->json(['success' => false, 'message' => 'Formulir Skrining tidak ditemukan.'], 404);
            }

            // Cek apakah NIK_Pasien sudah terdaftar di tabel pasiens
            $pasien = Pasien::where('NIK', $request->NIK_Pasien)->first();
            if (!$pasien) {
                // Jika pasien belum ada, buat entri pasien baru
                Pasien::create([
                    'NIK' => $request->NIK_Pasien,
                    'Nama_Pasien' => $request->Nama_Pasien,
                    'Tanggal_Lahir' => '1900-01-01', // Default atau tambahkan validasi untuk ini
                    'Kategori' => 'Umum', // Default atau tambahkan validasi untuk ini
                    'Jenis_Kelamin' => 'L', // Default atau tambahkan validasi untuk ini
                    'Alamat' => '-', // Default atau tambahkan validasi untuk ini
                    'Wilayah' => '-', // Default atau tambahkan validasi untuk ini
                    'No_telp' => '-', // Default atau tambahkan validasi untuk ini
                ]);
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

            // PERUBAHAN DI SINI: Tambahkan validasi unique saat update
            // Kita perlu memeriksa apakah ada skrining lain (selain yang sedang diupdate)
            // yang memiliki NIK_Pasien, id_form_skrining, dan Tanggal_Skrining yang sama.
            $tanggalSkrining = Carbon::parse($request->Tanggal_Skrining)->format('Y-m-d');
            $existingSkrining = Skrining::where('NIK_Pasien', $request->NIK_Pasien)
                                        ->where('id_form_skrining', $request->id_form_skrining_edit)
                                        ->whereDate('Tanggal_Skrining', $tanggalSkrining)
                                        ->where('id', '!=', $id) // Kecualikan skrining yang sedang diupdate
                                        ->first();

            if ($existingSkrining) {
                return response()->json(['success' => false, 'message' => 'Pasien ini sudah melakukan skrining untuk jenis formulir yang sama pada tanggal yang sama.'], 422);
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
            return response()->json(['success' => true, 'message' => 'Data skrining berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gagal menghapus data skrining: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus data skrining.'], 500);
        }
    }

}