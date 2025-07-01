<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasien;
use App\Models\Skrining;
use App\Models\FormSkrining;
use App\Models\DaftarPenyakit;
use App\Models\DaftarPertanyaan;
use App\Models\Jawaban;
use App\Models\Diagnosa; // PENTING: Pastikan model Diagnosa diimpor
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RekapSkriningController extends Controller
{
    public function index()
    {
        // Mendapatkan semua wilayah unik dari tabel pasien
        $wilayahs = Pasien::select('Wilayah')
                            ->whereNotNull('Wilayah')
                            ->where('Wilayah', '!=', '')
                            ->distinct()
                            ->orderBy('Wilayah')
                            ->pluck('Wilayah')
                            ->map(function ($wilayah) {
                                return trim($wilayah);
                            });

        $hasNullOrEmptyWilayah = Pasien::whereNull('Wilayah')->orWhere('Wilayah', '')->exists();
        if ($hasNullOrEmptyWilayah && !$wilayahs->contains('Tidak Diketahui')) {
            $wilayahs->prepend('Tidak Diketahui');
        }

        // Mendapatkan daftar semua formulir skrining yang ada
        $daftarFormSkrining = FormSkrining::all();

        return view('admin.rekap_hasil_skrining.index', compact('wilayahs', 'daftarFormSkrining'));
    }

    public function getRekapSummary(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $wilayahFilter = trim($request->input('wilayah'));

        if (empty($bulan) || empty($tahun)) {
            Log::warning("getRekapSummary called without required 'bulan' or 'tahun' parameters.");
            return response()->json([
                'success' => false,
                'error' => 'Parameter bulan dan tahun wajib diisi.',
                'message' => 'Mohon pilih bulan dan tahun untuk melihat rekap data.'
            ], 400);
        }

        try {
            $query = Skrining::with(['formSkrining', 'pasien']);

            $query->whereYear('Tanggal_Skrining', $tahun)
                  ->whereMonth('Tanggal_Skrining', $bulan);

            if (!empty($wilayahFilter)) {
                $query->whereHas('pasien', function ($q) use ($wilayahFilter) {
                    if ($wilayahFilter === 'Tidak Diketahui') {
                        $q->whereNull('Wilayah')->orWhere('Wilayah', '');
                    } else {
                        $q->where(DB::raw('LOWER(Wilayah)'), '=', strtolower($wilayahFilter));
                    }
                });
            }

            $skrinings = $query->get();

            $rekapData = [];
            $totalPasienSkrining = 0;
            $allFormSkriningNames = FormSkrining::pluck('nama_skrining')->toArray();

            foreach ($skrinings as $skrining) {
                if ($skrining->pasien && $skrining->formSkrining) {
                    $wilayah = $skrining->pasien->Wilayah ?? '';
                    if (empty($wilayah)) {
                        $wilayah = 'Tidak Diketahui';
                    }

                    $namaSkrining = $skrining->formSkrining->nama_skrining;

                    if (!isset($rekapData[$wilayah])) {
                        $rekapData[$wilayah] = [];
                    }
                    if (!isset($rekapData[$wilayah][$namaSkrining])) {
                        $rekapData[$wilayah][$namaSkrining] = 0;
                    }
                    $rekapData[$wilayah][$namaSkrining]++;
                    $totalPasienSkrining++;
                } else {
                    Log::warning("Skrining ID {$skrining->id} memiliki relasi pasien atau formSkrining yang hilang.");
                }
            }

            foreach ($rekapData as $wilayah => $formData) {
                foreach ($allFormSkriningNames as $formName) {
                    if (!isset($rekapData[$wilayah][$formName])) {
                        $rekapData[$wilayah][$formName] = 0;
                    }
                }
                ksort($rekapData[$wilayah]);
            }
            ksort($rekapData);

            $totalJenisFormSkriningTersedia = count($allFormSkriningNames);

            return response()->json([
                'success' => true,
                'rekapData' => $rekapData,
                'summary' => [
                    'total_pasien_skrining' => $totalPasienSkrining,
                    'total_jenis_form_skrining_tersedia' => $totalJenisFormSkriningTersedia,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Error in getRekapSummary: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile());
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan internal server.',
                'message' => 'Gagal memuat data rekap. Silakan periksa log server untuk detail lebih lanjut.'
            ], 500);
        }
    }

    public function getSkriningStatusSummary(Request $request)
    {
        $validated = $request->validate([
            'bulan' => 'required|numeric|between:1,12',
            'tahun' => 'required|integer|min:2000|max:2100',
            'wilayah' => 'nullable|string|max:255'
        ]);

        $bulan = (int)$request->bulan;
        $tahun = (int)$request->tahun;
        $wilayahFilter = trim($request->wilayah ?? '');

        try {
            $allFormSkrining = FormSkrining::all();
            $skriningStatusSummary = [];

            foreach ($allFormSkrining as $formSkrining) {
                $query = Skrining::with(['diagnosa', 'pasien'])
                    ->where('id_form_skrining', $formSkrining->id)
                    ->whereYear('Tanggal_Skrining', $tahun)
                    ->whereMonth('Tanggal_Skrining', $bulan);

                if (!empty($wilayahFilter)) {
                    $query->whereHas('pasien', function($q) use ($wilayahFilter) {
                        if ($wilayahFilter === 'Tidak Diketahui') {
                            $q->whereNull('Wilayah')->orWhere('Wilayah', '');
                        } else {
                            $q->where(DB::raw('TRIM(LOWER(Wilayah))'), strtolower(trim($wilayahFilter)));
                        }
                    });
                }
                
                $skrinings = $query->get();

                // Hitung berdasarkan diagnosa
                $statusCounts = $skrinings->map(function($skrining) {
                    if (!$skrining->diagnosa) {
                        return 'Belum Didiagnosa';
                    }
                    return $skrining->diagnosa->hasil_utama ?? 'Tidak Terindikasi';
                })->countBy();

                Log::debug("Diagnosa counts for {$formSkrining->nama_skrining}: " . json_encode($statusCounts->toArray()));

                $skriningStatusSummary[$formSkrining->nama_skrining] = $statusCounts->toArray();

                // Hitung pasien yang belum diskrining
                $queryBelumDiskrining = Pasien::query();
                if (!empty($wilayahFilter)) {
                    $queryBelumDiskrining->where(function($q) use ($wilayahFilter) {
                        if ($wilayahFilter === 'Tidak Diketahui') {
                            $q->whereNull('Wilayah')->orWhere('Wilayah', '');
                        } else {
                            $q->where(DB::raw('TRIM(LOWER(Wilayah))'), strtolower(trim($wilayahFilter)));
                        }
                    });
                }

                $nikSudahDiskrining = $skrinings->pluck('NIK_Pasien');
                $belumDiskrining = $queryBelumDiskrining->whereNotIn('NIK', $nikSudahDiskrining)->count();

                $skriningStatusSummary[$formSkrining->nama_skrining]['Belum Diskrining'] = $belumDiskrining;
            }

            return response()->json([
                'success' => true,
                'skriningStatusSummary' => $skriningStatusSummary,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getSkriningStatusSummary: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat ringkasan status skrining',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }


    /**
     * Normalize diagnosis results for consistent grouping
     */
    protected function normalizeDiagnosisResult($hasil)
    {
        if (empty($hasil)) {
            return 'Belum Didiagnosa';
        }

        $lowerResult = strtolower(trim($hasil));
        
        if (str_contains($lowerResult, 'terkonfirmasi')) {
            return 'Terkonfirmasi';
        } 
        if (str_contains($lowerResult, 'tidak terindikasi') || str_contains($lowerResult, 'negatif')) {
            return 'Tidak Terindikasi';
        }
        if (str_contains($lowerResult, 'positif')) {
            return 'Positif';
        }
        if (str_contains($lowerResult, 'diduga') || str_contains($lowerResult, 'suspek')) {
            return 'Diduga';
        }
        
        return $hasil; // Return original if no match
    }


    /**
     * Mengambil daftar pasien berdasarkan nama form skrining dan status indikasi (hasil diagnosa atau 'Belum Diskrining').
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPasienListBySkriningStatus(Request $request)
    {
        try {
            $request->validate([
                'nama_form_skrining' => 'required|string',
                'status_indikasi' => 'required|string',
                'bulan' => 'required|integer',
                'tahun' => 'required|integer',
                'wilayah' => 'nullable|string'
            ]);

            $formSkrining = FormSkrining::where('nama_skrining', $request->nama_form_skrining)->firstOrFail();
            $bulan = $request->bulan;
            $tahun = $request->tahun;
            $wilayah = $request->wilayah;
            $statusIndikasi = $request->status_indikasi;

            $pasienList = collect();

            if ($statusIndikasi === 'Belum Diskrining') {
                $queryPasien = Pasien::query();
                
                if (!empty($wilayah)) {
                    $queryPasien->where(function($q) use ($wilayah) {
                        if ($wilayah === 'Tidak Diketahui') {
                            $q->whereNull('Wilayah')->orWhere('Wilayah', '');
                        } else {
                            $q->where(DB::raw('LOWER(TRIM(Wilayah))'), strtolower(trim($wilayah)));
                        }
                    });
                }

                $nikSudahDiskrining = Skrining::where('id_form_skrining', $formSkrining->id)
                    ->whereYear('Tanggal_Skrining', $tahun)
                    ->whereMonth('Tanggal_Skrining', $bulan)
                    ->pluck('NIK_Pasien');

                $pasienList = $queryPasien->whereNotIn('NIK', $nikSudahDiskrining)
                    ->get(['NIK', 'Nama_Pasien', 'Wilayah'])
                    ->map(function($pasien) {
                        return [
                            'NIK' => $pasien->NIK,
                            'Nama_Pasien' => $pasien->Nama_Pasien,
                            'Tanggal_Skrining' => 'Belum Diskrining',
                            'Nama_Petugas' => '-', // Tidak ada nama petugas untuk yang belum diskrining
                            'Status_Indikasi' => 'Belum Diskrining',
                            'Wilayah' => $pasien->Wilayah ?? 'Tidak Diketahui'
                        ];
                    });
            } else {
                // Pasien dengan hasil diagnosa tertentu (statusIndikasi adalah hasil_utama)
                $query = Skrining::with(['pasien', 'diagnosa']) // Hapus 'petugas' di sini
                    ->where('id_form_skrining', $formSkrining->id)
                    ->whereYear('Tanggal_Skrining', $tahun)
                    ->whereMonth('Tanggal_Skrining', $bulan);

                if ($statusIndikasi !== 'Semua') {
                    $query->whereHas('diagnosa', function($q) use ($statusIndikasi) {
                        $q->where('hasil_utama', $statusIndikasi);
                    });
                }

                if (!empty($wilayah)) {
                    $query->whereHas('pasien', function($q) use ($wilayah) {
                        if ($wilayah === 'Tidak Diketahui') {
                            $q->whereNull('Wilayah')->orWhere('Wilayah', '');
                        } else {
                            $q->where(DB::raw('LOWER(TRIM(Wilayah))'), strtolower(trim($wilayah)));
                        }
                    });
                }

                $pasienList = $query->get()
                    ->map(function($skrining) {
                        return [
                            'NIK' => $skrining->NIK_Pasien,
                            'Nama_Pasien' => $skrining->pasien->Nama_Pasien ?? 'N/A',
                            'Tanggal_Skrining' => $skrining->Tanggal_Skrining 
                                ? Carbon::parse($skrining->Tanggal_Skrining)->format('d-m-Y')
                                : 'N/A',
                            'Nama_Petugas' => $skrining->Nama_Petugas ?? 'N/A', // Akses sebagai properti langsung
                            'Status_Indikasi' => $skrining->diagnosa->hasil_utama ?? 'Belum Didiagnosa',
                            'Wilayah' => $skrining->pasien->Wilayah ?? 'Tidak Diketahui'
                        ];
                    });
            }

            return response()->json([
                'success' => true,
                'pasienList' => $pasienList,
                'nama_form_skrining' => $formSkrining->nama_skrining,
                'status_indikasi' => $statusIndikasi
            ]);

        } catch (\Exception $e) {
            Log::error("Error in getPasienListBySkriningStatus: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan server',
                'message' => 'Gagal memuat daftar pasien. Silakan coba lagi.',
                'debug' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function pasienList(Request $request)
    {
        // Get and sanitize parameters
        $wilayah = trim($request->query('wilayah', ''));
        $namaFormSkrining = trim($request->query('nama_form_skrining', ''));
        $bulan = $request->query('bulan', null);
        $tahun = $request->query('tahun', null);

        // Debugging: Log incoming parameters
        Log::debug('PasienList Parameters:', [
            'wilayah' => $wilayah,
            'nama_form_skrining' => $namaFormSkrining,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'full_url' => $request->fullUrl()
        ]);

        // Initialize empty collection
        $dataPasienSkrining = collect();

        try {
            // Build the query
            $query = Skrining::with(['pasien', 'formSkrining.penyakit']);

            // Apply form screening filter
            if (!empty($namaFormSkrining)) {
                $query->whereHas('formSkrining', function ($q) use ($namaFormSkrining) {
                    $q->where(DB::raw('LOWER(nama_skrining)'), strtolower($namaFormSkrining));
                });
            }

            // Apply date filters
            if ($tahun) {
                $query->whereYear('Tanggal_Skrining', $tahun);
                
                if ($bulan) {
                    $query->whereMonth('Tanggal_Skrining', $bulan);
                }
            }

            // Apply wilayah filter if provided
            if (!empty($wilayah)) {
                $query->whereHas('pasien', function ($q) use ($wilayah) {
                    if ($wilayah === 'Tidak Diketahui') {
                        $q->whereNull('Wilayah')
                          ->orWhere('Wilayah', '');
                    } else {
                        $q->where(DB::raw('LOWER(Wilayah)'), strtolower($wilayah));
                    }
                });
            }

            // Get the results
            $dataPasienSkrining = $query->orderBy('Tanggal_Skrining', 'desc')->get();

            // Debugging: Log query results
            Log::debug('PasienList Results:', [
                'count' => $dataPasienSkrining->count(),
                'first_item' => $dataPasienSkrining->first()
            ]);

            // Return the view with proper path
            return view('admin.rekap_hasil_skrining.pasien_list', [
                'dataPasienSkrining' => $dataPasienSkrining,
                'wilayah' => $wilayah,
                'nama_form_skrining' => $namaFormSkrining,
                'bulan' => $bulan,
                'tahun' => $tahun
            ]);

        } catch (\Exception $e) {
            Log::error('Error in pasienList: ' . $e->getMessage());
            
            return view('admin.rekap_hasil_skrining.pasien_list', [
                'dataPasienSkrining' => collect(),
                'error' => 'Terjadi kesalahan saat memuat data'
            ]);
        }
    }

    public function getDetailSkrining(Request $request)
    {
        $skriningId = $request->query('skrining_id');

        $skrining = Skrining::with([
            'pasien',
            'formSkrining.penyakit',
            'jawabans.daftarPertanyaan',
            'diagnosa' // Tambahkan relasi diagnosa di sini
        ])->find($skriningId);

        if (!$skrining) {
            return response()->json([
                'success' => false,
                'message' => 'Detail skrining tidak ditemukan.'
            ], 404);
        }

        $detailJawaban = $skrining->jawabans->map(function ($jawaban) {
            return [
                'pertanyaan' => $jawaban->daftarPertanyaan->pertanyaan ?? 'Pertanyaan Tidak Diketahui',
                'jawaban' => $jawaban->jawaban ?? '-',
            ];
        });

        $namaPenyakitTerkait = 'Tidak Diketahui';
        if ($skrining->formSkrining && $skrining->formSkrining->penyakit) {
            $namaPenyakitTerkait = $skrining->formSkrining->penyakit->Nama_Penyakit;
        }

        // Ambil hasil diagnosa dari relasi diagnosa
        $hasilDiagnosa = $skrining->diagnosa->hasil_utama ?? 'Belum Didiagnosa';

        return response()->json([
            'success' => true,
            'skriningDetail' => [
                'NIK' => $skrining->pasien->NIK ?? 'N/A',
                'Nama_Pasien' => $skrining->pasien->Nama_Pasien ?? 'N/A',
                'Nama_Petugas' => $skrining->Nama_Petugas ?? 'N/A', // Akses sebagai properti langsung
                'Nama_Skrining' => $skrining->formSkrining->nama_skrining ?? 'N/A',
                'Tanggal_Skrining' => $skrining->Tanggal_Skrining ? Carbon::parse($skrining->Tanggal_Skrining)->format('d-m-Y H:i') : 'N/A',
                'Nama_Penyakit' => $namaPenyakitTerkait, // Ini adalah penyakit terkait form skrining, bukan hasil diagnosa
                'Hasil_Diagnosa' => $hasilDiagnosa, // Tambahkan hasil diagnosa di sini
                'detail_jawaban' => $detailJawaban,
            ]
        ]);
    }
}
