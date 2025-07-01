<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pasien;
use App\Models\Jawaban;
use App\Models\Diagnosa;
use App\Models\Skrining;
use App\Models\FormSkrining;
use Illuminate\Http\Request;
use App\Models\DaftarPenyakit;
use Illuminate\Validation\Rule;
use App\Models\DaftarPertanyaan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\Diagnosa\Factories\DiagnoserFactory; // Pastikan namespace ini benar

class SkriningController extends Controller
{
    protected DiagnoserFactory $diagnoserFactory;

    // Injeksi DiagnoserFactory melalui constructor
    public function __construct(DiagnoserFactory $diagnoserFactory)
    {
        $this->diagnoserFactory = $diagnoserFactory;
    }

    /**
     * Menampilkan daftar data skrining.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $skrinings = Skrining::with('pasien', 'formSkrining', 'diagnosa') // Tambahkan 'diagnosa' untuk relasi
            ->when($search, function ($query) use ($search) {
                $query->whereHas('pasien', function ($subQuery) use ($search) {
                    $subQuery->where('Nama_Pasien', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('Tanggal_Skrining', 'asc') // Mengurutkan dari terbaru
            ->paginate(100);

        $formSkrinings = FormSkrining::all();
        $penyakits = DaftarPenyakit::all();
        $pertanyaans = DaftarPertanyaan::all();

        return view('admin.skrining.index', compact('skrinings', 'formSkrinings', 'penyakits', 'pertanyaans'));
    }

    /**
     * Mengambil data skrining untuk tabel AJAX.
     */
    public function getSkriningData(Request $request)
    {
        // Ambil semua data skrining dengan relasi yang diperlukan untuk tabel
        // Pastikan relasi 'pasien', 'formSkrining', dan 'diagnosa' sudah didefinisikan di model Skrining
        $skrinings = Skrining::with(['pasien', 'formSkrining', 'diagnosa'])
            ->orderBy('Tanggal_Skrining', 'asc') // Pastikan diurutkan DESC
            ->orderBy('id', 'asc') // Tambahan pengurutan berdasarkan ID
            ->get();

        // Mengubah format tanggal_skrining jika diperlukan untuk tampilan frontend
        $skrinings->map(function ($item) {
            $item->Tanggal_Skrining_Formatted = Carbon::parse($item->Tanggal_Skrining)->format('d-m-Y');
            return $item;
        });

        return response()->json($skrinings);
    }


    /**
     * Mengambil pertanyaan berdasarkan ID Form Skrining.
     */
     public function getPertanyaanByFormSkrining(Request $request, $id)
    {
        try {
            // 1. Pastikan $id adalah ID numerik dari FormSkrining.
            //    Jika rute Anda seperti yang di atas (/{id}), maka $id akan otomatis menjadi bagian setelah /pertanyaan/.
            //    Jika frontend mengirim "riplay-2023-07-01:1" ke sini, maka ini akan gagal.
            $formSkrining = FormSkrining::with('pertanyaan')->findOrFail($id);
            $pertanyaanList = $formSkrining->pertanyaan;

            $nikPasien = $request->query('nik_pasien');
            $tanggalSkriningRaw = $request->query('tanggal_skrining');

            // --- Bagian Logging untuk Debugging ---
            Log::info('getPertanyaanByFormSkrining - ID Form Skrining:', ['id' => $id]);
            Log::info('getPertanyaanByFormSkrining - NIK Pasien:', ['nik' => $nikPasien]);
            Log::info('getPertanyaanByFormSkrining - Tanggal Skrining Raw:', ['tanggal' => $tanggalSkriningRaw]);
            // --- Akhir Bagian Logging ---

            if ($nikPasien && $tanggalSkriningRaw) {
                try {
                    // 2. Penting: Pastikan format tanggal dari frontend adalah 'Y-m-d' (misal: 2023-07-01).
                    //    Jika tidak, `createFromFormat` akan melemparkan pengecualian.
                    $parsedDate = Carbon::createFromFormat('Y-m-d', $tanggalSkriningRaw)->format('Y-m-d');
                    Log::info('getPertanyaanByFormSkrining - Tanggal Skrining Parsed:', ['parsedDate' => $parsedDate]);


                    // 3. TAMBAHKAN FILTER ID FORM SKRINING DI SINI!
                    //    Ini memastikan Anda hanya mengambil jawaban sebelumnya untuk FORMULIR SPESIFIK yang sedang diisi.
                    $skriningsToday = Skrining::where('NIK_Pasien', $nikPasien)
                        ->whereDate('Tanggal_Skrining', $parsedDate)
                        ->where('id_form_skrining', $id) // <--- GARIS INI SANGAT PENTING DITAMBAHKAN
                        ->with('jawabans')
                        ->get();

                    Log::info('getPertanyaanByFormSkrining - Skrining ditemukan (untuk NIK, Tanggal, Form ID):', ['count' => $skriningsToday->count()]);

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
                    // 4. Perbaiki penanganan error untuk parsing tanggal atau query.
                    //    Mengembalikan 500 error di sini agar frontend tahu ada masalah server.
                    Log::error("Error in getPertanyaanByFormSkrining date parsing/query for NIK: {$nikPasien}, Date: {$tanggalSkriningRaw}, Form ID: {$id}. Error: " . $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getFile());
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal memproses tanggal atau data skrining sebelumnya. Pastikan format tanggal YYYY-MM-DD.',
                        'error_details' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine()
                    ], 500);
                }
            }

            return response()->json($pertanyaanList);

        } catch (ModelNotFoundException $e) {
            // 5. Log ModelNotFoundException agar bisa dilacak.
            Log::error("FormSkrining with ID {$id} not found in getPertanyaanByFormSkrining. Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Formulir Skrining tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            // 6. Log error umum lainnya.
            Log::error("General error in getPertanyaanByFormSkrining: " . $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getFile());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server: ' . $e->getMessage(), 'error_details' => $e->getMessage()], 500);
        }
    }


    /**
     * Mengambil rekomendasi form skrining berdasarkan NIK Pasien.
     */
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

                // Pastikan kategori pasien di-decode dengan benar jika disimpan sebagai JSON string
                if (is_string($kategoriPasien)) {
                    $decodedKategori = json_decode($kategoriPasien, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedKategori)) {
                        $kategoriPasien = $decodedKategori;
                    } else {
                        // Jika bukan JSON array yang valid, anggap sebagai string tunggal
                        $kategoriPasien = [$kategoriPasien];
                    }
                } elseif (!is_array($kategoriPasien)) {
                    // Default jika kategori tidak ada atau tidak valid
                    $kategoriPasien = ['Umum'];
                }

                if (is_array($kategoriPasien) && count($kategoriPasien) > 0) {
                    $recommendedForms = FormSkrining::where(function ($query) use ($kategoriPasien) {
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
            return response()->json(['success' => false, 'message' => 'NIK Pasien tidak valid atau tidak ditemukan.', 'errors' => $e->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memuat rekomendasi formulir.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Menyimpan data skrining baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'Nama_Petugas' => 'required|string|max:255',
            'NIK_Pasien' => [
                'required',
                'string',
                'max:255',
                Rule::unique('skrinings')->where(function ($query) use ($request) {
                    $tanggalSkriningRaw = $request->Tanggal_Skrining;
                    try {
                        $tanggalSkrining = Carbon::createFromFormat('Y-m-d', $tanggalSkriningRaw)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return $query->whereRaw('0=1');
                    }
                    return $query->where('NIK_Pasien', $request->NIK_Pasien)
                        ->where('id_form_skrining', $request->id_form_skrining)
                        ->whereDate('Tanggal_Skrining', $tanggalSkrining);
                }),
            ],
            'Nama_Pasien' => 'required|string|max:255',
            'Tanggal_Skrining' => 'required|date_format:Y-m-d',
            'id_form_skrining' => 'required|exists:form_skrinings,id',
            'jawaban' => 'required|array',
            'jawaban.*' => 'nullable|string|max:1000',
        ], [
            'NIK_Pasien.unique' => 'Pasien ini sudah melakukan skrining untuk jenis formulir yang sama pada tanggal yang sama.',
            'Tanggal_Skrining.date_format' => 'Format Tanggal Skrining harus YYYY-MM-DD.',
        ]);

        DB::beginTransaction();
        try {
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
                $pasien = Pasien::where('NIK', $request->NIK_Pasien)->first();
            }

            $tanggalSkriningUntukDb = $request->Tanggal_Skrining;

            $skrining = Skrining::create([
                'Nama_Petugas' => $request->Nama_Petugas,
                'NIK_Pasien' => $request->NIK_Pasien,
                'Nama_Pasien' => $request->Nama_Pasien,
                'Tanggal_Skrining' => $tanggalSkriningUntukDb,
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

            $formSkrining = FormSkrining::find($skrining->id_form_skrining);
            $jenisPenyakit = strtolower($formSkrining->nama_skrining);

            $diagnoser = $this->diagnoserFactory->getDiagnoser($jenisPenyakit);

            if (!$diagnoser) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Skrining berhasil disimpan. Hasil otomatis tidak tersedia untuk jenis ini.',
                    'skrining_id' => $skrining->id,
                    'skrining' => $skrining->load(['pasien', 'formSkrining']), // <--- PENTING: SUDAH DITAMBAHKAN DI SINI
                    'diagnosa' => null
                ], 200);
            }

            $skrining->load([
                'pasien',
                'formSkrining.pertanyaan',
                'jawabans.pertanyaan'
            ]);

            $diagnosaResult = $diagnoser->analyze($skrining);

            $diagnosa = Diagnosa::create([
                'skrining_id' => $skrining->id,
                'jenis_penyakit' => $jenisPenyakit,
                'hasil_utama' => $diagnosaResult['hasil_utama'],
                'rekomendasi_tindak_lanjut' => $diagnosaResult['rekomendasi_tindak_lanjut'],
                'detail_diagnosa' => $diagnosaResult['detail_diagnosa'],
                'catatan' => $diagnosaResult['catatan'],
                'tanggal_diagnosa' => now(),
            ]);

            if (in_array($diagnosa->hasil_utama, ['Malaria Terkonfirmasi, Rujuk FKLNT'])) {
                $pasien->update(['is_riwayat_malaria_positif' => true]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Skrining berhasil disimpan dan hasil skrining telah ditentukan!',
                'skrining_id' => $skrining->id,
                'skrining' => $skrining->load(['pasien', 'formSkrining']),
                'diagnosa' => $diagnosa->load('skrining')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan skrining atau mendiagnosa.',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    /**
     * Menampilkan detail skrining untuk modal edit.
     */
    // public function show($id)
    // {
    //     try {
    //         // Load relasi yang diperlukan: pasien, formSkrining dengan pertanyaan, jawaban dengan pertanyaan, dan diagnosa
    //         $skrining = Skrining::with(['pasien', 'formSkrining.pertanyaan', 'jawabans.pertanyaan', 'diagnosa'])->find($id);

    //         if (!$skrining) {
    //             return response()->json(['success' => false, 'message' => 'Data skrining tidak ditemukan.'], 404);
    //         }

    //         // Ambil semua form skrining yang tersedia untuk dropdown di frontend
    //         $formSkrinings = FormSkrining::all();

    //         // Kumpulkan jawaban yang sudah ada dalam format yang mudah digunakan di frontend
    //         $existingJawaban = [];
    //         foreach ($skrining->jawabans as $jawaban) {
    //             $existingJawaban[$jawaban->ID_DaftarPertanyaan] = $jawaban->jawaban;
    //         }

    //         // Tambahkan jawaban yang sudah ada ke data pertanyaan
    //         // Ini akan diproses oleh JavaScript di frontend saat mengisi form edit
    //         foreach ($skrining->formSkrining->pertanyaan as $pertanyaan) {
    //             $pertanyaan->jawaban_tersimpan = $existingJawaban[$pertanyaan->id] ?? null;
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'data' => $skrining,
    //             'formSkrinings' => $formSkrinings,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => 'Gagal memuat data skrining untuk edit: ' . $e->getMessage()], 500);
    //     }
    // }

    public function show($id)
    {
        try {
            $skrining = Skrining::with([
                'pasien',
                'formSkrining.pertanyaan', // Relasi Many-to-Many via pivot table
                'jawabans.pertanyaan',
                'diagnosa'
            ])->find($id);

            if (!$skrining) {
                return response()->json(['success' => false, 'message' => 'Data skrining tidak ditemukan.'], 404);
            }

            // Ambil semua form skrining yang tersedia untuk dropdown di frontend
            $formSkrinings = FormSkrining::all();

            // Kumpulkan jawaban yang sudah ada dalam format yang mudah digunakan di frontend
            $existingJawaban = [];
            foreach ($skrining->jawabans as $jawaban) {
                $existingJawaban[$jawaban->ID_DaftarPertanyaan] = $jawaban->jawaban;
            }

            // Tambahkan jawaban yang sudah ada ke data pertanyaan
            // Ini akan diproses oleh JavaScript di frontend saat mengisi form edit
            // Pastikan $skrining->formSkrining ada sebelum mengakses propertinya.
            if ($skrining->formSkrining) {
                foreach ($skrining->formSkrining->pertanyaan as $pertanyaan) {
                    $pertanyaan->jawaban_tersimpan = $existingJawaban[$pertanyaan->id] ?? null;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $skrining,
                'formSkrinings' => $formSkrinings,
            ]);
        } catch (\Exception $e) {
            // Log error untuk debugging lebih lanjut
            \Log::error("Error in SkriningController@show: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memuat data skrining untuk edit: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Memperbarui data skrining yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        // Validasi data input untuk update
        $request->validate([
            'Nama_Petugas' => 'required|string|max:255',
            'NIK_Pasien' => 'required|string|max:255', // Sesuaikan max length jika NIK 16 digit
            'Nama_Pasien' => 'required|string|max:255',
            'Tanggal_Skrining' => 'required|date_format:Y-m-d', // PENTING: Validasi ini HARUS Y-m-d
            'id_form_skrining_edit' => 'required|exists:form_skrinings,id', // Nama input di frontend adalah id_form_skrining_edit
            'jawaban' => 'required|array',
            'jawaban.*' => 'nullable|string|max:1000',
        ], [
            'NIK_Pasien.unique' => 'Pasien ini sudah melakukan skrining untuk jenis formulir yang sama pada tanggal yang sama.',
            'Tanggal_Skrining.date_format' => 'Format Tanggal Skrining harus YYYY-MM-DD.',
        ]);

        DB::beginTransaction(); // Mulai transaksi database
        try {
            $skrining = Skrining::find($id);
            if (!$skrining) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Skrining tidak ditemukan.'], 404);
            }

            $formSkrining = FormSkrining::find($request->id_form_skrining_edit);
            if (!$formSkrining) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Formulir Skrining untuk update tidak ditemukan.'], 404);
            }

            // Periksa unique constraint secara manual untuk update (exclude skrining saat ini)
            $tanggalSkriningForUnique = Carbon::createFromFormat('Y-m-d', $request->Tanggal_Skrining)->format('Y-m-d');
            $existingSkrining = Skrining::where('NIK_Pasien', $request->NIK_Pasien)
                ->where('id_form_skrining', $request->id_form_skrining_edit)
                ->whereDate('Tanggal_Skrining', $tanggalSkriningForUnique)
                ->where('id', '!=', $id) // Exclude current skrining
                ->first();

            if ($existingSkrining) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Pasien ini sudah melakukan skrining untuk jenis formulir yang sama pada tanggal yang sama.'], 422);
            }

            // Tanggal_Skrining sudah divalidasi sebagai Y-m-d, jadi langsung gunakan dari request
            $tanggalSkriningUntukDb = $request->Tanggal_Skrining;

            // Update data skrining
            $skrining->update([
                'Nama_Petugas' => $request->Nama_Petugas,
                'NIK_Pasien' => $request->NIK_Pasien,
                'Nama_Pasien' => $request->Nama_Pasien,
                'Tanggal_Skrining' => $tanggalSkriningUntukDb,
                'id_form_skrining' => $request->id_form_skrining_edit, // Menggunakan nama input dari frontend
            ]);

            // Hapus semua jawaban lama dan simpan yang baru
            $skrining->jawabans()->delete(); // Pastikan relasi jawabans() ada di model Skrining

            foreach ($request->jawaban as $pertanyaan_id => $isi_jawaban) {
                if (!is_null($isi_jawaban) && $isi_jawaban !== '') {
                    Jawaban::create([
                        'ID_Skrining' => $skrining->id,
                        'ID_DaftarPertanyaan' => $pertanyaan_id,
                        'jawaban' => $isi_jawaban,
                    ]);
                }
            }

            // RE-DIAGNOSA SETELAH UPDATE (DIREKOMENDASIKAN)
            // Ini akan memastikan diagnosa terupdate sesuai perubahan jawaban
            $jenisPenyakit = strtolower($formSkrining->nama_skrining);
            $diagnoser = $this->diagnoserFactory->getDiagnoser($jenisPenyakit);

            $diagnosa = null; // Inisialisasi variabel diagnosa

            if ($diagnoser) { // Hanya diagnosa jika ada diagnoser yang sesuai
                $skrining->load([
                    'pasien',
                    'formSkrining.pertanyaan',
                    'jawabans.pertanyaan'
                ]);
                $diagnosaResult = $diagnoser->analyze($skrining);

                // Update atau buat diagnosa baru
                $diagnosa = Diagnosa::updateOrCreate(
                    ['skrining_id' => $skrining->id],
                    [
                        'jenis_penyakit' => $jenisPenyakit,
                        'hasil_utama' => $diagnosaResult['hasil_utama'],
                        'rekomendasi_tindak_lanjut' => $diagnosaResult['rekomendasi_tindak_lanjut'],
                        'detail_diagnosa' => $diagnosaResult['detail_diagnosa'],
                        'catatan' => $diagnosaResult['catatan'],
                        'tanggal_diagnosa' => now(),
                    ]
                );

                // Perbarui status riwayat malaria pasien jika diperlukan setelah re-diagnosa
                if (in_array($diagnosaResult['hasil_utama'], ['Malaria Terkonfirmasi, Rujuk FKLNT'])) { // Sesuaikan nama hasil diagnosa
                    $pasien = Pasien::where('NIK', $skrining->NIK_Pasien)->first(); // Ambil pasien lagi
                    if ($pasien) $pasien->update(['is_riwayat_malaria_positif' => true]);
                }
            }

            DB::commit(); // Selesaikan transaksi database

            return response()->json([
                'success' => true,
                'message' => 'Skrining berhasil diperbarui!',
                'skrining_id' => $skrining->id, // Mengembalikan ID skrining
                'diagnosa' => $diagnosa ? $diagnosa->load('skrining') : null // Mengembalikan data diagnosa, atau null jika tidak ada
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika ada error
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui skrining.',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Mengambil data diagnosa berdasarkan ID skrining.
     */
    public function getDiagnosa($id)
    {
        try {
            // Log::info('ID yang diminta:', ['id' => $id]);
            // Ambil data skrining beserta diagnosanya berdasarkan ID
            Log::info('Skrining ID yang diminta:', ['id' => $id]);

            // Ambil diagnosa berdasarkan skrining_id
            $diagnosa = Diagnosa::with('skrining')->where('skrining_id', $id)->first();

            if (!$diagnosa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data hasil skrining tidak ditemukan untuk skrining ini.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'diagnosa' => $diagnosa
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat diagnosa: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Menghapus data skrining.
     */
    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $skrining = Skrining::findOrFail($id);
            // Hapus semua jawaban terkait terlebih dahulu
            $skrining->jawabans()->delete();
            // Hapus diagnosa terkait (jika ada)
            $skrining->diagnosa()->delete();
            // Kemudian hapus skriningnya
            $skrining->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data skrining berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus data skrining.', 'error' => $e->getMessage()], 500);
        }
    }
}
