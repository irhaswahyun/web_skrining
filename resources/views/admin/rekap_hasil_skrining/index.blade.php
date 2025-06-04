@extends('partials.admin.main')

@section('title', 'Rekap Hasil Skrining')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Rekap Hasil Skrining</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline"> 
                            <div class="card-header">
                                <h3 class="card-title">Filter Rekapitulasi</h3>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-end"> 
                                    <div class="col-md-3 mb-3">
                                        <label for="filterBulan">Filter Bulan :</label>
                                        <select id="filterBulan" class="form-control">
                                            <option value="">Pilih Bulan</option>
                                            @php
                                                $months = [
                                                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                                    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                                    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                                ];
                                            @endphp
                                            @foreach($months as $num => $name)
                                                <option value="{{ $num }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="filterTahun">Filter Tahun :</label>
                                        <select id="filterTahun" class="form-control">
                                            <option value="">Pilih Tahun</option>
                                            @php
                                                $currentYear = date('Y');
                                                $startYear = 2020; // Tahun awal yang ingin Anda tampilkan
                                                for ($year = $currentYear; $year >= $startYear; $year--) {
                                                    echo "<option value='{$year}'>{$year}</option>";
                                                }
                                            @endphp
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="filterWilayah">Filter Wilayah :</label>
                                        <select id="filterWilayah" class="form-control">
                                            <option value="">Semua Wilayah</option>
                                            @foreach($wilayahs as $wilayah)
                                                <option value="{{ $wilayah }}">{{ $wilayah }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <button class="btn btn-primary w-100" id="applyFilterBtn">
                                            <i data-feather="filter"></i> Terapkan Filter
                                        </button>
                                    </div>
                                </div>

                                <div class="row mt-4 justify-content-center">
                                    <div class="col-md-4 px-2">
                                        <div class="info-box custom-info-box-blue">
                                            <div class="info-box-content text-center d-flex flex-column justify-content-center align-items-center">
                                                <span class="info-box-text">Total Pasien Skrining</span>
                                                <span class="info-box-number" id="totalPasienSkrining">0</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 px-2">
                                        <div class="info-box custom-info-box-purple">
                                            <div class="info-box-content text-center d-flex flex-column justify-content-center align-items-center">
                                                <span class="info-box-text">Total Jenis Formulir Skrining Tersedia</span>
                                                <span class="info-box-number" id="totalJenisFormSkriningTersedia">{{ $daftarFormSkrining->pluck('nama_skrining')->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kontainer utama untuk menampilkan kartu rekap --}}
                <div class="row" id="rekapContainer">
                    {{-- Ini adalah pesan yang akan muncul saat halaman pertama kali dimuat --}}
                    <div class="col-12 text-center py-5" id="initialMessage">
                        <div class="alert alert-info">Mohon pilih <b>Bulan</b>, <b>Tahun</b>, dan <b>Wilayah</b> lalu klik <b>"Terapkan Filter"</b> untuk melihat data rekap.</div>
                    </div>
                    {{-- Kartu rekap akan dimuat di sini oleh JavaScript setelah filter diterapkan --}}
                </div>

            </div>
        </section>
    </div>
@endsection

@section('scripts')
    {{-- Hapus SweetAlert2 jika tidak digunakan untuk notifikasi umum --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

    <script>
        $(document).ready(function() {
            feather.replace(); // Pastikan Feather Icons diinisialisasi

            const rekapContainer = $('#rekapContainer');
            const totalPasienSkriningElem = $('#totalPasienSkrining');
            const totalJenisFormSkriningTersediaElem = $('#totalJenisFormSkriningTersedia');
            const allFormSkriningNames = @json($daftarFormSkrining->pluck('nama_skrining'));

            // Set total jenis form skrining tersedia di awal
            totalJenisFormSkriningTersediaElem.text(allFormSkriningNames.length);

            // Fungsi utama untuk memuat data rekap
            function loadRekapData() {
                const bulanVal = $('#filterBulan').val();
                const tahunVal = $('#filterTahun').val();
                const wilayahVal = $('#filterWilayah').val();

                // Validasi input sebelum request AJAX
                if (!bulanVal || !tahunVal) {
                    rekapContainer.html('<div class="col-12 text-center py-5"><div class="alert alert-warning text-center">Mohon pilih <b>Bulan</b> dan <b>Tahun</b> untuk menerapkan filter.</div></div>');
                    totalPasienSkriningElem.text('0');
                    return; // Berhenti jika filter belum lengkap
                }

                // Tampilkan spinner loading saat AJAX berjalan
                rekapContainer.html(`
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data rekap...</p>
                    </div>
                `);
                totalPasienSkriningElem.text('Memuat...');

                $.ajax({
                    url: "{{ route('rekap_hasil_skrining.summary') }}", // Route yang sudah ada
                    method: 'GET',
                    data: {
                        bulan: bulanVal,
                        tahun: tahunVal,
                        wilayah: wilayahVal
                    },
                    success: function(response) {
                        rekapContainer.empty(); // Kosongkan container sebelum mengisi ulang

                        if (response.success) {
                            totalPasienSkriningElem.text(response.summary.total_pasien_skrining);

                            if (Object.keys(response.rekapData).length > 0) {
                                $.each(response.rekapData, function(wilayah, formData) {
                                    let wilayahSection = `
                                        <div class="col-12 mb-4">
                                            <div class="card card-outline card-primary">
                                                <div class="card-header">
                                                    <h4 class="card-title text-primary">Rekap Wilayah: ${wilayah}</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                    `;
                                    
                                    allFormSkriningNames.forEach(function(formName) {
                                        const count = formData[formName] !== undefined ? formData[formName] : 0;
                                        // Ini adalah link yang akan mengarahkan ke halaman pasien-list.blade.php
                                        // URL akan terbentuk seperti: /rekap-hasil-skrining/pasien-list?wilayah=Boyolangu&nama_form_skrining=Skrining%20Malaria&bulan=06&tahun=2025
                                        const pasienListUrl = "{{ route('rekap_hasil_skrining.pasien_list', [], false) }}" +
                                            `?wilayah=${encodeURIComponent(wilayah)}` +
                                            `&nama_form_skrining=${encodeURIComponent(formName)}` +
                                            `&bulan=${encodeURIComponent(bulanVal)}` +
                                            `&tahun=${encodeURIComponent(tahunVal)}`;

                                        wilayahSection += `
                                            <div class="col-lg-3 col-md-6 mb-3">
                                                <a href="${pasienListUrl}" class="card card-outline card-info d-block h-100 text-decoration-none text-dark clickable-card">
                                                    <div class="card-body d-flex flex-column justify-content-between">
                                                        <h5 class="card-title text-bold text-truncate">${formName}</h5>
                                                        <p class="card-text">Jumlah Pasien Skrining: <strong>${count}</strong></p>
                                                    </div>
                                                </a>
                                            </div>
                                        `;
                                    });
                                    wilayahSection += `
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    rekapContainer.append(wilayahSection);
                                });
                            } else {
                                rekapContainer.html('<div class="col-12 text-center py-5"><div class="alert alert-info">Tidak ada data rekap ditemukan untuk filter yang dipilih.</div></div>');
                            }
                        } else {
                            rekapContainer.html('<div class="col-12 text-center py-5"><div class="alert alert-danger">Gagal memuat data rekap.</div></div>');
                            console.error("Error from server:", response.message || 'Gagal memuat data rekap dari server.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading rekap data:", xhr.responseText);
                        rekapContainer.html('<div class="col-12 text-center py-5"><div class="alert alert-danger">Gagal memuat data rekap. Silakan coba lagi.</div></div>');
                        totalPasienSkriningElem.text('0');
                    }
                });
            }

            // TIDAK MEMANGGIL loadRekapData() saat document ready.
            // Biarkan pesan awal yang ada di HTML secara default muncul.

            // Event listener untuk tombol 'Terapkan Filter'
            $('#applyFilterBtn').on('click', function() {
                loadRekapData(); // Panggil saat tombol diklik
            });

            // --- Logika untuk Modal Detail Skrining per Pasien (MODAL INI TETAP ADA) ---
            // Ini adalah modal yang Anda miliki untuk detail skrining individu (pertanyaan & jawaban)
            // Tombol pemicu modal ini diharapkan ada di halaman pasien-list.blade.php
            $(document).on('click', '.btn-detail-skrining', function() {
                var skriningId = $(this).data('skrining-id');
                var detailPertanyaanContainer = $('#detail_riwayat_daftar_pertanyaan_formatted');
                
                // Reset dan tampilkan loading di modal
                $('#detail_riwayat_NIK').text('Memuat...');
                $('#detail_riwayat_Nama_Pasien').text('Memuat...');
                $('#detail_riwayat_Nama_Petugas').text('Memuat...');
                $('#detail_riwayat_Nama_Skrining').text('Memuat...');
                $('#detail_riwayat_Tanggal_Skrining').text('Memuat...');
                $('#detail_riwayat_Nama_Penyakit').text('Memuat...');
                detailPertanyaanContainer.empty().append('<p class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">Loading...</span></div> Memuat jawaban...</p>');

                $.ajax({
                    url: "{{ route('rekap_hasil_skrining.detail') }}", // Route yang sudah ada
                    method: 'GET',
                    data: {
                        skrining_id: skriningId
                    },
                    success: function(response) {
                        if (response.success && response.skriningDetail) {
                            var skriningData = response.skriningDetail;
                            $('#detail_riwayat_NIK').text(skriningData.NIK || 'N/A');
                            $('#detail_riwayat_Nama_Pasien').text(skriningData.Nama_Pasien || 'N/A');
                            $('#detail_riwayat_Nama_Petugas').text(skriningData.Nama_Petugas || 'N/A');
                            $('#detail_riwayat_Nama_Skrining').text(skriningData.Nama_Skrining || 'N/A');
                            $('#detail_riwayat_Tanggal_Skrining').text(skriningData.Tanggal_Skrining || 'N/A');
                            $('#detail_riwayat_Nama_Penyakit').text(skriningData.Nama_Penyakit || 'N/A');

                            detailPertanyaanContainer.empty();
                            if (skriningData.detail_jawaban && skriningData.detail_jawaban.length > 0) {
                                $.each(skriningData.detail_jawaban, function(key, value) {
                                    var answerContent = value.jawaban || '-';
                                    var questionItem = `
                                        <div class="form-group mb-3">
                                            <label>${value.pertanyaan}</label>
                                            <p class="form-control-plaintext border rounded p-2 bg-light">${answerContent}</p>
                                        </div>
                                    `;
                                    detailPertanyaanContainer.append(questionItem);
                                });
                            } else {
                                detailPertanyaanContainer.html('<p>Tidak ada pertanyaan terkait.</p>');
                            }

                            $('#detailRiwayatModal').modal('show');
                        } else {
                            detailPertanyaanContainer.html('<p class="text-danger">Gagal memuat detail.</p>');
                            console.error("Error from server:", response.message || 'Data detail skrining tidak ditemukan.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching detail skrining:", xhr.responseText);
                        detailPertanyaanContainer.html('<p class="text-danger">Gagal memuat detail pertanyaan.</p>');
                        // Logika error yang lebih detail bisa ditambahkan ke konsol jika perlu
                    }
                });
            });
        });
    </script>
@endsection
