<?php
namespace App\Services\Diagnosa;

use App\Models\Skrining;
use App\Models\DaftarPertanyaan;

class MalariaDiagnoser implements DiagnoserInterface // PASTIKAN implements DiagnoserInterface ada
{
    public function analyze(Skrining $skrining): array
    {
        $skrining->load(['pasien', 'formSkrining', 'jawabans.pertanyaan']);

        $namaFormSkrining = strtolower($skrining->formSkrining->nama_skrining);

        if ($namaFormSkrining !== 'skrining malaria') {
            return [
                'hasil_utama' => 'tidak_dapat_didiagnosa',
                'rekomendasi_tindak_lanjut' => 'Formulir skrining ini bukan untuk diagnosa malaria.',
                'detail_diagnosa' => [],
                'catatan' => 'Diagnosa malaria otomatis hanya berlaku untuk form skrining malaria.',
            ];
        }

        $jawabanMap = [];
        foreach ($skrining->jawabans as $jawaban) {
            $jawabanMap[$jawaban->pertanyaan->id] = strtolower($jawaban->jawaban);
        }

        $formPertanyaan = $skrining->formSkrining->pertanyaan;

        $kondisi = [
            'gejala_demam_menggigil' => false,
            'gejala_mual_muntah' => false,
            'gejala_nyeri_kepala' => false,
            'gejala_nyeri_otot' => false,
            'riwayat_penyakit_obat' => false,
            'riwayat_perjalanan_endemis' => false,
            'tanda_anemis_pucat' => false,
            'tanda_ikterik' => false,
            // Tambahkan semua kriteria lain yang relevan untuk malaria di sini
        ];

        $ID_PERTANYAAN_RIWAYAT_OBAT = 8;
        // Periksa jawaban untuk ID pertanyaan riwayat obat
        if (isset($jawabanMap[$ID_PERTANYAAN_RIWAYAT_OBAT])) {
            $jawabanRiwayatObat = $jawabanMap[$ID_PERTANYAAN_RIWAYAT_OBAT];

            // Sesuaikan logika ini berdasarkan nilai ACTUAL yang dikirim dari form HTML Anda.
            // Jika form mengirim 'ya' atau 'Y', maka ini harus dicek.
            // Jika form mengirim '1', maka cek $jawabanRiwayatObat === '1'
            if ($jawabanRiwayatObat === 'iya' || $jawabanRiwayatObat === 'ya') { // Tambahkan 'y' jika mungkin
                $kondisi['riwayat_penyakit_obat'] = true;
            }
            // Tambahkan kondisi lain jika Anda memiliki opsi seperti 'pernah' atau teks bebas lainnya
            // Misalnya: if (str_contains($jawabanRiwayatObat, 'pernah')) { $kondisi['riwayat_penyakit_obat'] = true; }
        }


        foreach ($formPertanyaan as $pertanyaan) {

            if ($pertanyaan->id === $ID_PERTANYAAN_RIWAYAT_OBAT) {
                continue;
            }
            $teksPertanyaan = strtolower($pertanyaan->pertanyaan);
            $jawabanPasien = $jawabanMap[$pertanyaan->id] ?? null;

            if ($jawabanPasien === 'ya') {
                if (str_contains($teksPertanyaan, 'demam atau menggigil')) {
                    $kondisi['gejala_demam_menggigil'] = true;
                }
                if (str_contains($teksPertanyaan, 'mual dan muntah')) {
                    $kondisi['gejala_mual_muntah'] = true;
                }
                if (str_contains($teksPertanyaan, 'nyeri kepala')) {
                    $kondisi['gejala_nyeri_kepala'] = true;
                }
                if (str_contains($teksPertanyaan, 'nyeri otot') || str_contains($teksPertanyaan, 'pegal-pegal')) {
                    $kondisi['gejala_nyeri_otot'] = true;
                }
                // if (str_contains($teksPertanyaan, 'riwayat malaria atau penggunaan obat malaria')) {
                //      $kondisi['riwayat_penyakit_obat'] = true;
                // }
                if (str_contains($teksPertanyaan, 'perjalanan ke daerah endemis malaria')) {
                    $kondisi['riwayat_perjalanan_endemis'] = true;
                }
                if (str_contains($teksPertanyaan, 'anemis') || str_contains($teksPertanyaan, 'pucat')) {
                    $kondisi['tanda_anemis_pucat'] = true;
                }
                if (str_contains($teksPertanyaan, 'ikterik') || str_contains($teksPertanyaan, 'kuning')) {
                    $kondisi['tanda_ikterik'] = true;
                }
                // Lanjutkan dengan semua kriteria lain dari diagram alir Anda:
            }
        }

        $pasien = $skrining->pasien;
        $wilayahPasien = $pasien->wilayah ?? 'Tidak Diketahui';
        $isRiwayatMalariaPositifSebelumnya = $pasien->is_riwayat_malaria_positif ?? false;

        $hasGejala = $kondisi['gejala_demam_menggigil'] || $kondisi['gejala_mual_muntah'] || $kondisi['gejala_nyeri_kepala'] || $kondisi['gejala_nyeri_otot'];

        $hasilUtama = 'belum_didiagnosa';
        $rekomendasiTindakLanjut = 'Sistem tidak dapat menentukan diagnosa.';
        $detailDiagnosa = [
            'status_gejala_teridentifikasi' => $hasGejala ? 'bergejala' : 'Tidak Bergejala',
            'status_riwayat_malaria_atau_obat_input' => $kondisi['riwayat_penyakit_obat'] ? 'ya' : 'Tidak',
            'status_riwayat_malaria_pasien_db' => $isRiwayatMalariaPositifSebelumnya ? 'ya' : 'tidak',
            'tanda_anemis_pucat' => $kondisi['tanda_anemis_pucat'] ? 'ya' : 'tidak',
            'tanda_ikterik' => $kondisi['tanda_ikterik'] ? 'ya' : 'tidak',
            'riwayat_perjalanan_endemis' => $kondisi['riwayat_perjalanan_endemis'] ? 'ya' : 'tidak',
            'alur_diagnosa_terpilih' => null,
            'alasan_diagnosa' => [],
        ];
        $catatanTambahan = ['Pasien dari wilayah: ' . $wilayahPasien . '.'];

        if ($kondisi['riwayat_penyakit_obat'] || $isRiwayatMalariaPositifSebelumnya) {
            $detailDiagnosa['alur_diagnosa_terpilih'] = 'Terdiagnosis Malaria Dari Riwayat/Penggunaan Obat';
            $detailDiagnosa['alasan_diagnosa'][] = 'Pasien memiliki riwayat terdiagnosis malaria dari riwayat/penggunaan obat atau data sebelumnya.';
            $hasilUtama = 'malaria_terkonfirmasi_rujuk_fklt';
            $rekomendasiTindakLanjut = 'Pasien terdiagnosis malaria dari riwayat/penggunaan obat atau data sebelumnya. Direkomendasikan untuk Rujuk ke FKTL (Fasilitas Kesehatan Tingkat Lanjut) untuk pemeriksaan komprehensif.';
            $catatanTambahan[] = 'Perlu pemeriksaan komprehensif lanjutan untuk penanganan yang tepat.';
        } else {
            if ($hasGejala) {
                $detailDiagnosa['alur_diagnosa_terpilih'] = 'Bergejala (Tanpa Hasil RDT)';
                $detailDiagnosa['alasan_diagnosa'][] = 'Pasien melaporkan gejala malaria tetapi tidak ada hasil Rapid Diagnostic Test (RDT) yang tersedia dari skrining.';
                $hasilUtama = 'suspek_malaria_butuh_rdt_dan_pemeriksaan_lanjut';
                $rekomendasiTindakLanjut = 'Pasien bergejala namun hasil Rapid Diagnostic Test (RDT) belum diinput. **Wajib segera lakukan pemeriksaan RDT** dan pemeriksaan komprehensif di Puskesmas.';
                $catatanTambahan[] = 'Pemeriksaan RDT dan komprehensif sangat diperlukan karena ada gejala yang teridentifikasi.';
            } else {
                $detailDiagnosa['alur_diagnosa_terpilih'] = 'Tidak Perlu Penanganan Lanjutan';
                $detailDiagnosa['alasan_diagnosa'][] = 'Pasien tidak bergejala dan tidak memiliki riwayat malaria/penggunaan obat.';
                if ($kondisi['tanda_anemis_pucat'] || $kondisi['tanda_ikterik'] || $kondisi['riwayat_perjalanan_endemis']) {
                     $detailDiagnosa['alasan_diagnosa'][] = 'Ditemukan tanda fisik anemis/ikterik atau riwayat perjalanan ke daerah endemis yang membutuhkan evaluasi lebih lanjut.';
                     $hasilUtama = 'suspek_malaria_tanda_fisik_abnormal_rujuk_puskesmas';
                     $rekomendasiTindakLanjut = 'Pasien tidak bergejala dan tidak ada riwayat malaria, namun menunjukkan tanda anemis atau ikterik atau riwayat perjalanan ke daerah endemis. Segera rujuk ke Puskesmas untuk pemeriksaan komprehensif, termasuk RDT dan pemeriksaan mikroskopis.';
                } else {
                    $hasilUtama = 'Tidak ada indikasi malaria';
                    $rekomendasiTindakLanjut = 'Pasien tidak bergejala dan tidak ada riwayat malaria atau tanda fisik/riwayat mencurigakan lainnya dari skrining awal. Tidak ada indikasi kuat malaria saat ini, namun jika gejala muncul, perlu pemeriksaan lebih lanjut atau observasi.';
                }
            }
        }

         $detailDiagnosa['status_riwayat_malaria_atau_obat_input'] = $kondisi['riwayat_penyakit_obat'] ? 'Iya' : 'Tidak'; // Gunakan 'Ya' (kapital) untuk konsistensi

        $detailDiagnosa['rekomendasi_tindak_lanjut_detail'] = $rekomendasiTindakLanjut;
        $detailDiagnosa['catatan_akhir'] = implode('. ', $catatanTambahan);

        return [
            'hasil_utama' => $hasilUtama,
            'rekomendasi_tindak_lanjut' => $rekomendasiTindakLanjut,
            'detail_diagnosa' => $detailDiagnosa,
            'catatan' => implode('. ', $catatanTambahan),
        ];
    }
}