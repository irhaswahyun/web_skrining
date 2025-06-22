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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class SkriningController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $skrinings = Skrining::with('pasien', 'formSkrining')
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
            $formSkrining = FormSkrining::with('pertanyaan')->findOrFail($id);
            $pertanyaanList = $formSkrining->pertanyaan;

            $nikPasien = $request->query('nik_pasien');
            $tanggalSkriningRaw = $request->query('tanggal_skrining'); // Diharapkan format YYYY-MM-DD dari frontend

            if ($nikPasien && $tanggalSkriningRaw) {
                try {
                    // Pastikan parsing tanggal dari frontend (YYYY-MM-DD) ke YYYY-MM-DD untuk query DB
                    // Jika datepicker mengirim dd-mm-yyyy, ini yang harus digunakan:
                    // $parsedDate = Carbon::createFromFormat('d-m-Y', $tanggalSkriningRaw)->format('Y-m-d');
                    $parsedDate = Carbon::createFromFormat('Y-m-d', $tanggalSkriningRaw)->format('Y-m-d');


                    $skriningsToday = Skrining::where('NIK_Pasien', $nikPasien)
                        ->whereDate('Tanggal_Skrining', $parsedDate)
                        ->with('jawabans')
                        ->get();

                    $previousAnswers = [];
                    foreach ($skriningsToday as $skrining) {
                        foreach ($skrining->jawabans as $jawaban) {
                            $previousAnswers[$jawaban->ID_DaftarPertanyaan] = $jawaban->jawaban;
                        }
                    }

                    $pertanyaanList = $pertanyaanList->map(function ($pertanyaan) use ($previousAnswers) {
                        $pertanyaan->previous_answer = $previousAnswers[$pertanyaan->id] ?? null;
                        return $pertanyaan;
                    });

                } catch (\Exception $e) {
                    return response()->json($pertanyaanList);
                }
            }

            return response()->json($pertanyaanList);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Formulir Skrining tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat memuat pertanyaan untuk formulir skrining. Pastikan NIK dan Tanggal Skrining sudah benar.'], 500);
        }
    }

    public function getRecommendedFormSkrinings(Request $request)
    {
        try {
            $request->validate([
                'nik_pasien' => 'required|string|max:255|exists:pasiens,NIK',
            ]);

            $nikPasien = $request->nik_pasien;
            $pasien = Pasien::where('NIK', $nikPasien)->first();

            $recommendedForms = collect();

            if ($pasien) {
                $kategoriPasien = $pasien->Kategori;

                if (is_string($kategoriPasien)) {
                    $decodedKategori = json_decode($kategoriPasien, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedKategori)) {
                        $kategoriPasien = $decodedKategori;
                    } else {
                        $kategoriPasien = [$kategoriPasien];
                    }
                } elseif (!is_array($kategoriPasien)) {
                    $kategoriPasien = ['Umum'];
                }

                if (is_array($kategoriPasien) && count($kategoriPasien) > 0) {
                    $recommendedForms = FormSkrining::where(function($query) use ($kategoriPasien) {
                        foreach ($kategoriPasien as $kat) {
                            $query->orWhereJsonContains('kategori', $kat);
                        }
                    })->get();
                }
            }

            return response()->json([
                'success' => true,
                'pasien' => $pasien,
                'recommendedForms' => $recommendedForms,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'NIK Pasien tidak valid atau tidak ditemukan.'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memuat rekomendasi formulir.'], 500);
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
                Rule::unique('skrinings')->where(function ($query) use ($request) {
                    $tanggalSkriningRaw = $request->Tanggal_Skrining; // format dd-mm-yyyy dari frontend
                    try {
                        $tanggalSkrining = Carbon::createFromFormat('d-m-Y', $tanggalSkriningRaw)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return $query->whereRaw('0=1');
                    }
                    return $query->where('NIK_Pasien', $request->NIK_Pasien)
                                 ->where('id_form_skrining', $request->id_form_skrining)
                                 ->whereDate('Tanggal_Skrining', $tanggalSkrining);
                }),
            ],
            'Nama_Pasien' => 'required|string|max:255',
            'Tanggal_Skrining' => 'required|date_format:d-m-Y',
            'id_form_skrining' => 'required|exists:form_skrinings,id',
            'jawaban' => 'required|array',
            'jawaban.*' => 'nullable|string|max:1000',
        ], [
            'NIK_Pasien.unique' => 'Pasien ini sudah melakukan skrining untuk jenis formulir yang sama pada tanggal yang sama.',
            'Tanggal_Skrining.date_format' => 'Format Tanggal Skrining harus dd-mm-yyyy.',
        ]);

        try {
            $formSkrining = FormSkrining::find($request->id_form_skrining);
            if (!$formSkrining) {
                return response()->json(['success' => false, 'message' => 'Formulir Skrining tidak ditemukan.'], 404);
            }

            $pasien = Pasien::where('NIK', $request->NIK_Pasien)->first();
            if (!$pasien) {
                Pasien::create([
                    'NIK' => $request->NIK_Pasien,
                    'Nama_Pasien' => $request->Nama_Pasien,
                    'Tanggal_Lahir' => '1900-01-01',
                    'Kategori' => 'Umum',
                    'Jenis_Kelamin' => 'L',
                    'Alamat' => '-',
                    'Wilayah' => '-',
                    'No_telp' => '-',
                ]);
            }

            $tanggalSkriningFormatted = Carbon::createFromFormat('d-m-Y', $request->Tanggal_Skrining)->format('Y-m-d');

            $skrining = Skrining::create([
                'Nama_Petugas' => $request->Nama_Petugas,
                'NIK_Pasien' => $request->NIK_Pasien,
                'Nama_Pasien' => $request->Nama_Pasien,
                'Tanggal_Skrining' => $tanggalSkriningFormatted,
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
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan skrining. Silakan coba lagi.'], 500);
        }
    }

    public function show($id)
    {
        try {
            // Eager load relasi yang dibutuhkan
            $skrining = Skrining::with([
                'pasien',
                'formSkrining', // Load FormSkrining terlebih dahulu
                'jawabans.daftarPertanyaan'
            ])->findOrFail($id); // Gunakan findOrFail untuk langsung 404 jika tidak ditemukan

            // Muat relasi pertanyaan hanya jika formSkrining ada
            if ($skrining->formSkrining) {
                $skrining->load('formSkrining.pertanyaan');
            }

            // Pastikan relasi 'pasien' dan 'formSkrining' tidak null sebelum mengakses propertinya
            $namaPasien = $skrining->pasien->Nama_Pasien ?? '-';
            $nikPasien = $skrining->pasien->NIK ?? '-';
            $namaSkrining = $skrining->formSkrining->nama_skrining ?? '-';

            $detailData = [
                'id' => $skrining->id,
                'Nama_Petugas' => $skrining->Nama_Petugas,
                'NIK_Pasien' => $nikPasien,
                'Nama_Pasien' => $namaPasien,
                // Format Tanggal_Skrining kembali ke dd-mm-yyyy untuk tampilan di frontend
                'Tanggal_Skrining' => Carbon::parse($skrining->Tanggal_Skrining)->format('d-m-Y'),
                'id_form_skrining' => $skrining->id_form_skrining,
                'nama_skrining' => $namaSkrining,
                'pertanyaan' => [],
            ];

            // Inisialisasi array pertanyaan
            $pertanyaanArray = [];

            // Pastikan relasi formSkrining dan pertanyaannya ada sebelum iterasi
            if ($skrining->formSkrining && $skrining->formSkrining->pertanyaan) {
                foreach ($skrining->formSkrining->pertanyaan as $pertanyaan) {
                    $jawabanObj = $skrining->jawabans->firstWhere('ID_DaftarPertanyaan', $pertanyaan->id);
                    $pertanyaanArray[] = [
                        'id' => $pertanyaan->id,
                        'pertanyaan' => $pertanyaan->pertanyaan,
                        'jawaban' => $jawabanObj ? $jawabanObj->jawaban : '',
                    ];
                }
            }
            $detailData['pertanyaan'] = $pertanyaanArray; // Assign array yang sudah terisi atau kosong

            return response()->json($detailData);

        } catch (ModelNotFoundException $e) {
            // Menangkap jika Skrining tidak ditemukan
            return response()->json(['message' => 'Skrining tidak ditemukan.', 'error_detail' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            // Menangkap error umum lainnya
            return response()->json(['message' => 'Terjadi kesalahan saat memuat data detail. Silakan coba lagi.', 'error_detail' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama_Petugas' => 'required|string|max:255',
            'NIK_Pasien' => 'required|string|max:255',
            'Nama_Pasien' => 'required|string|max:255',
            'Tanggal_Skrining' => 'required|date_format:d-m-Y',
            'id_form_skrining_edit' => 'required|exists:form_skrinings,id',
            'jawaban' => 'required|array',
            'jawaban.*' => 'nullable|string|max:1000',
        ], [
            'NIK_Pasien.unique' => 'Pasien ini sudah melakukan skrining untuk jenis formulir yang sama pada tanggal yang sama.',
            'Tanggal_Skrining.date_format' => 'Format Tanggal Skrining harus dd-mm-yyyy.',
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

            $tanggalSkriningRaw = $request->Tanggal_Skrining;
            try {
                $tanggalSkrining = Carbon::createFromFormat('d-m-Y', $tanggalSkriningRaw)->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Format Tanggal Skrining tidak valid.'], 422);
            }

            $existingSkrining = Skrining::where('NIK_Pasien', $request->NIK_Pasien)
                                        ->where('id_form_skrining', $request->id_form_skrining_edit)
                                        ->whereDate('Tanggal_Skrining', $tanggalSkrining)
                                        ->where('id', '!=', $id)
                                        ->first();

            if ($existingSkrining) {
                return response()->json(['success' => false, 'message' => 'Pasien ini sudah melakukan skrining untuk jenis formulir yang sama pada tanggal yang sama.'], 422);
            }

            $tanggalSkriningFormatted = Carbon::createFromFormat('d-m-Y', $request->Tanggal_Skrining)->format('Y-m-d');

            $skrining->update([
                'Nama_Petugas' => $request->Nama_Petugas,
                'NIK_Pasien' => $request->NIK_Pasien,
                'Nama_Pasien' => $request->Nama_Pasien,
                'Tanggal_Skrining' => $tanggalSkriningFormatted,
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
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus data skrining.'], 500);
        }
    }
}
