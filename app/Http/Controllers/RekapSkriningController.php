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
    $wilayah = trim($request->query('wilayah', null));
    $namaFormSkrining = trim($request->query('nama_form_skrining'));
    $bulan = $request->query('bulan', null);
    $tahun = $request->query('tahun', null);

    // --- STEP 1: Cek input dari URL ---
    // dd([
    //     'wilayah_dari_url' => $wilayah,
    //     'nama_form_skrining_dari_url' => $namaFormSkrining,
    //     'bulan_dari_url' => $bulan,
    //     'tahun_dari_url' => $tahun,
    //     'tipe_nama_form_skrining' => gettype($namaFormSkrining),
    //     'tipe_wilayah' => gettype($wilayah),
    //     'url_lengkap' => $request->fullUrl(),
    // ]);
    // Saat Anda menjalankan ini, pastikan nilai-nilai di atas SAMA PERSIS dengan yang Anda harapkan.
    // Misalnya, nama_form_skrining_dari_url harus "Skrining Flu", bukan "Tidak Diketahui" atau string kosong.
    // Jika di sini sudah salah, berarti masalahnya ada di LINKING DARI HALAMAN REKAP (index.blade.php)

    $dataPasienSkrining = collect();

    try {
        if (empty($wilayah) || empty($namaFormSkrining) || empty($bulan) || empty($tahun)) {
            Log::warning("Pasien list request missing parameters. Wilayah: {$wilayah}, Form: {$namaFormSkrining}, Bulan: {$bulan}, Tahun: {$tahun}");
            return view('admin.rekap_hasil_skrining.pasien_list', compact('dataPasienSkrining', 'wilayah', 'namaFormSkrining', 'bulan', 'tahun'));
        }

        $query = Skrining::with(['pasien', 'formSkrining.penyakit']);

        // --- STEP 2: Cek query SQL yang dihasilkan ---
        // Ini akan sangat membantu melihat apakah filter yang dibuat sudah benar
        $tempQuery = clone $query; // Kloning query agar tidak terhenti di dd()
        // Anda bisa coba salah satu dd() di bawah ini
        // dd($tempQuery->toSql(), $tempQuery->getBindings()); // Menampilkan SQL mentah dan binding parameter
        // dd($tempQuery->get()->toArray()); // Menjalankan query dan menampilkan hasilnya

        $query->whereHas('formSkrining', function ($q) use ($namaFormSkrining) {
            $q->where(DB::raw('LOWER(nama_skrining)'), '=', strtolower($namaFormSkrining));
        });

        $query->whereYear('Tanggal_Skrining', $tahun)
              ->whereMonth('Tanggal_Skrining', $bulan);

        $query->whereHas('pasien', function ($q) use ($wilayah) {
            if ($wilayah === 'Tidak Diketahui') {
                $q->whereNull('Wilayah')->orWhere('Wilayah', '');
            } else {
                $q->where(DB::raw('LOWER(Wilayah)'), '=', strtolower($wilayah));
            }
        });

        $dataPasienSkrining = $query->get();

        // --- STEP 3: Cek hasil akhir setelah semua filter diterapkan ---
        // dd($dataPasienSkrining->toArray()); // Apakah di sini sudah ada data?

        return view('admin.rekap_hasil_skrining.pasien_list', compact('dataPasienSkrining', 'wilayah', 'namaFormSkrining', 'bulan', 'tahun'));

    } catch (\Exception $e) {
        // ... (error handling) ...
    }
}
    public function getDetailSkrining(Request $request)
    {
        $skriningId = $request->query('skrining_id');

        $skrining = Skrining::with([
            'pasien',
            'formSkrining.penyakit',
            'jawabans.daftarPertanyaan'
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
                'jawaban' => $jawaban->jawaban,
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
                'Nama_Petugas' => $skrining->Nama_Petugas,
                'Nama_Skrining' => $skrining->formSkrining->nama_skrining ?? 'N/A',
                'Tanggal_Skrining' => $skrining->Tanggal_Skrining ? $skrining->Tanggal_Skrining->format('d-m-Y') : 'N/A',
                'Nama_Penyakit_Terkait' => $namaPenyakitTerkait,
                'detail_jawaban' => $detailJawaban,
            ]
        ]);
    }
}