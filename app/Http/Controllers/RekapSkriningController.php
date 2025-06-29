<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pasien;
use App\Models\Skrining;
use App\Models\FormSkrining;
use App\Models\DaftarPenyakit;
use App\Models\DaftarPertanyaan;
use App\Models\Jawaban;
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

        // Perbaikan: Variabel yang benar adalah $hasNullOrEmptyWilayah
        $hasNullOrEmptyWilayah = Pasien::whereNull('Wilayah')->orWhere('Wilayah', '')->exists();
        if ($hasNullOrEmptyWilayah && !$wilayahs->contains('Tidak Diketahui')) { // Menggunakan variabel yang benar
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
            if ($wilayah === 'Tidak Diketahui') {
                $query->where(function($q) {
                    $q->whereNull('Wilayah')
                      ->orWhere('Wilayah', '');
                });
            } else {
                $query->whereHas('pasien', function ($q) use ($wilayah) {
                    $q->where(DB::raw('LOWER(Wilayah)'), strtolower($wilayah));
                });
            }
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

        // Penting: Pastikan nama relasi 'jawabans' dan 'daftarPertanyaan' ini BENAR
        // di model Skrining dan Jawaban Anda.
        $skrining = Skrining::with([
            'pasien',
            'formSkrining.penyakit',
            'jawabans.daftarPertanyaan' // Ini berarti Skrining punya hasMany Jawaban,
                                       // dan Jawaban punya belongsTo DaftarPertanyaan
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
                'jawaban' => $jawaban->jawaban ?? '-', // Tambahkan null coalescing untuk jawaban
            ];
        });

        $namaPenyakitTerkait = 'Tidak Diketahui';
        if ($skrining->formSkrining && $skrining->formSkrining->penyakit) {
            $namaPenyakitTerkait = $skrining->formSkrining->penyakit->Nama_Penyakit;
        }

        return response()->json([
            'success' => true,
            'skriningDetail' => [
                // 'id' => $skrining->id,
                'NIK' => $skrining->pasien->NIK ?? 'N/A',
                'Nama_Pasien' => $skrining->pasien->Nama_Pasien ?? 'N/A',
                'Nama_Petugas' => $skrining->Nama_Petugas ?? 'N/A', // Pastikan kolom ini ada dan diisi
                'Nama_Skrining' => $skrining->formSkrining->nama_skrining ?? 'N/A',
                'Tanggal_Skrining' => $skrining->Tanggal_Skrining ? Carbon::parse($skrining->Tanggal_Skrining)->format('d-m-Y H:i') : 'N/A', // Ganti format menjadi H:i juga jika perlu
                'detail_jawaban' => $detailJawaban, // Ini adalah array yang akan di-loop di frontend
                // 'Kondisi' => $skrining->Kondisi ?? 'N/A', // Uncomment jika ingin menampilkan Kondisi
            ]
        ]);
    }
}