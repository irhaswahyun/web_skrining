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
                        <div class="card card-primary card-outline"> {{-- Menggunakan card-outline untuk tampilan lebih bersih --}}
                            <div class="card-header">
                                <h3 class="card-title">Filter Rekapitulasi</h3>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-end"> {{-- align-items-end untuk meratakan tombol filter ke bawah --}}
                                    <div class="col-md-3 mb-3">
                                        <label for="filterBulan">Filter Bulan:</label>
                                        <select id="filterBulan" class="form-control">
                                            <option value="">Pilih Bulan</option>
                                            @php
                                                $months = [
                                                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                                    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                                    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                                ];
                                                $currentMonth = date('m');
                                            @endphp
                                            @foreach($months as $num => $name)
                                                <option value="{{ $num }}" @if($num == $currentMonth) selected @endif>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="filterTahun">Filter Tahun:</label>
                                        <select id="filterTahun" class="form-control">
                                            <option value="">Pilih Tahun</option>
                                            @php
                                                $currentYear = date('Y');
                                                $startYear = 2020; // Tahun awal yang ingin Anda tampilkan
                                                for ($year = $currentYear; $year >= $startYear; $year--) {
                                                    echo "<option value='{$year}'";
                                                    if ($year == $currentYear) {
                                                        echo " selected";
                                                    }
                                                    echo ">{$year}</option>";
                                                }
                                            @endphp
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="filterWilayah">Filter Wilayah:</label>
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
                                            <span class="info-box-icon"><i class="far fa-clipboard"></i></span>
                                            <div class="info-box-content text-center d-flex flex-column justify-content-center align-items-center">
                                                <span class="info-box-text">Total Pasien Skrining</span>
                                                <span class="info-box-number" id="totalPasienSkrining">0</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 px-2">
                                        <div class="info-box custom-info-box-purple">
                                            <span class="info-box-icon"><i class="far fa-list-alt"></i></span>
                                            <div class="info-box-content text-center d-flex flex-column justify-content-center align-items-center">
                                                <span class="info-box-text">Total Jenis Formulir Skrining Tersedia</span>
                                                <span class="info-box-number" id="totalJenisFormSkriningTersedia">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="rekapContainer">
                    {{-- Pesan default akan ditampilkan di sini atau spinner saat memuat --}}
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data rekap...</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- MODAL DETAIL RIWAYAT SKRINING --}}
    <div class="modal fade" id="detailRiwayatModal" tabindex="-1" role="dialog" aria-labelledby="detailRiwayatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailRiwayatModalLabel">Detail Riwayat Skrining</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 class="font-weight-bold">NIK Pasien:</h6>
                    <p id="detail_riwayat_NIK"></p>
                    <h6 class="font-weight-bold">Nama Pasien:</h6>
                    <p id="detail_riwayat_Nama_Pasien"></p>
                    <h6 class="font-weight-bold">Nama Petugas:</h6>
                    <p id="detail_riwayat_Nama_Petugas"></p>
                    <h6 class="font-weight-bold">Nama Skrining:</h6>
                    <p id="detail_riwayat_Nama_Skrining"></p>
                    <h6 class="font-weight-bold">Nama Penyakit:</h6>
                    <p id="detail_riwayat_Nama_Penyakit"></p>
                    <h6 class="font-weight-bold">Tanggal Skrining:</h6>
                    <p id="detail_riwayat_Tanggal_Skrining"></p>
                    <h6 class="font-weight-bold">Pertanyaan & Jawaban:</h6>
                    <div id="detail_riwayat_daftar_pertanyaan_formatted">
                        <p>Memuat pertanyaan...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            feather.replace(); // Pastikan Feather Icons diinisialisasi

            // Mengambil daftar nama form skrining dari PHP untuk memastikan semua tampil
            const allFormSkriningNames = @json($daftarFormSkrining->pluck('nama_skrining'));

            // Fungsi untuk menampilkan pop-up error
            function showErrorPopup(message = 'Terjadi kesalahan saat memuat data rekap.') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: message,
                });
            }

            // Fungsi utama untuk memuat data rekap
            function loadRekapData(initialLoad = false) { // Tambah parameter untuk menandai pemuatan awal
                var rekapContainer = $('#rekapContainer');
                var bulanVal = $('#filterBulan').val();
                var tahunVal = $('#filterTahun').val();
                var wilayahVal = $('#filterWilayah').val();

                // Sembunyikan rekapContainer jika ini pemuatan awal dan tidak ada filter yang dipilih
                if (initialLoad) {
                    // Cek apakah filter default sudah dipilih (misal: bulan/tahun saat ini)
                    const isDefaultFilterSelected = bulanVal && tahunVal;
                    if (!isDefaultFilterSelected) {
                        rekapContainer.html('<div class="col-12"><div class="alert alert-info text-center">Silakan pilih bulan, tahun, dan wilayah lalu klik "Terapkan Filter" untuk melihat data rekap.</div></div>');
                        $('#totalPasienSkrining').text('0');
                        $('#totalJenisFormSkriningTersedia').text(allFormSkriningNames.length); // Tampilkan total form yang tersedia
                        return; // Jangan lakukan AJAX call jika tidak ada filter
                    }
                }

                // Tampilkan spinner loading
                rekapContainer.html(`
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data rekap...</p>
                    </div>
                `);

                // Validasi input sebelum request AJAX
                if (!bulanVal || !tahunVal) {
                    rekapContainer.html('<div class="col-12"><div class="alert alert-warning text-center">Mohon pilih **Bulan** dan **Tahun** untuk menerapkan filter.</div></div>');
                    showErrorPopup('Mohon pilih Bulan dan Tahun.');
                    $('#totalPasienSkrining').text('0');
                    $('#totalJenisFormSkriningTersedia').text(allFormSkriningNames.length);
                    return;
                }

                $.ajax({
                    url: "{{ route('rekap_hasil_skrining.summary') }}",
                    method: 'GET',
                    data: {
                        bulan: bulanVal, // Kirim bulan (01-12)
                        tahun: tahunVal, // Kirim tahun (YYYY)
                        wilayah: wilayahVal
                    },
                    success: function(response) {
                        rekapContainer.empty(); // Kosongkan container sebelum mengisi ulang
                        if (response.success) {
                            $('#totalPasienSkrining').text(response.summary.total_pasien_skrining);
                            $('#totalJenisFormSkriningTersedia').text(response.summary.total_jenis_form_skrining_tersedia);

                            if (Object.keys(response.rekapData).length > 0) {
                                $.each(response.rekapData, function(wilayah, formData) {
                                    var wilayahSection = `
                                        <div class="col-12 mb-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title text-primary">Rekap Wilayah: ${wilayah}</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                    `;
                                    // Loop melalui semua nama form skrining untuk memastikan card ada, meskipun jumlahnya 0
                                    allFormSkriningNames.forEach(function(formName) {
                                        const count = formData[formName] !== undefined ? formData[formName] : 0;
                                        // Buat URL untuk halaman daftar pasien (ini adalah placeholder, Anda perlu rute aktual)
                                        const pasienListUrl = "{{ route('rekap_hasil_skrining.pasien_list', [], false) }}" + // false agar tidak absolut
                                            `?wilayah=${encodeURIComponent(wilayah)}` +
                                            `&nama_form_skrining=${encodeURIComponent(formName)}` +
                                            `&bulan=${encodeURIComponent(bulanVal)}` + // Gunakan bulanVal
                                            `&tahun=${encodeURIComponent(tahunVal)}`; // Gunakan tahunVal

                                        wilayahSection += `
                                            <div class="col-lg-3 col-md-6 mb-3">
                                                <a href="${pasienListUrl}" class="card card-outline card-info clickable-card d-block h-100" style="text-decoration: none; color: inherit;">
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
                                rekapContainer.html('<div class="col-12"><div class="alert alert-info text-center">Tidak ada data rekap ditemukan untuk filter yang dipilih.</div></div>');
                            }
                        } else {
                            rekapContainer.html('<div class="col-12"><div class="alert alert-danger text-center">Gagal memuat data rekap.</div></div>');
                            showErrorPopup(response.message || 'Gagal memuat data rekap dari server.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading rekap data:", xhr.responseText); // Tampilkan respons error dari server
                        rekapContainer.html('<div class="col-12"><div class="alert alert-danger text-center">Gagal memuat data rekap. Silakan coba lagi.</div></div>');
                        let errorMessage = 'Terjadi kesalahan saat memuat data rekap. Silakan coba lagi.';
                        if (xhr.status === 400) { // Bad Request, mungkin karena validasi di backend
                            try {
                                const errorResponse = JSON.parse(xhr.responseText);
                                errorMessage = errorResponse.message || errorResponse.error || errorMessage;
                            } catch (e) {
                                // Fallback if response is not JSON
                            }
                        } else if (xhr.status === 500) { // Internal Server Error
                            errorMessage = 'Terjadi kesalahan server internal. Periksa log server.';
                        }
                        showErrorPopup(errorMessage);
                    }
                });
            }

            // Memuat data pertama kali saat halaman dimuat, dengan filter default (bulan/tahun saat ini)
            // Atau tampilkan pesan jika filter default tidak ada
            loadRekapData(true);

            // Event listener untuk tombol 'Terapkan Filter'
            $('#applyFilterBtn').on('click', function() {
                loadRekapData(false); // Panggil tanpa initialLoad=true agar selalu memuat
            });

            // Event listener untuk tombol detail (jika Anda memiliki tombol detail di tempat lain)
            $(document).on('click', '.btn-detail-skrining', function() {
                var skriningId = $(this).data('skrining-id');
                var detailPertanyaanContainer = $('#detail_riwayat_daftar_pertanyaan_formatted');
                detailPertanyaanContainer.empty().append('<p>Memuat pertanyaan...</p>');

                $.ajax({
                    url: "{{ route('rekap_hasil_skrining.detail') }}",
                    method: 'GET',
                    data: {
                        skrining_id: skriningId
                    },
                    success: function(response) {
                        if (response.success && response.skriningDetail) {
                            var skriningData = response.skriningDetail;
                            $('#detail_riwayat_NIK').text(skriningData.NIK);
                            $('#detail_riwayat_Nama_Pasien').text(skriningData.Nama_Pasien);
                            $('#detail_riwayat_Nama_Petugas').text(skriningData.Nama_Petugas);
                            $('#detail_riwayat_Nama_Skrining').text(skriningData.Nama_Skrining);
                            $('#detail_riwayat_Nama_Penyakit').text(skriningData.Nama_Penyakit);
                            $('#detail_riwayat_Tanggal_Skrining').text(skriningData.Tanggal_Skrining);

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
                            showErrorPopup(response.message || 'Data detail skrining tidak ditemukan.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching detail skrining:", xhr.responseText);
                        let errorMessage = 'Terjadi kesalahan saat mengambil detail skrining. Silakan coba lagi.';
                        if (xhr.status === 404) {
                            errorMessage = 'Detail skrining tidak ditemukan.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Terjadi kesalahan server internal. Periksa log server.';
                        }
                        showErrorPopup(errorMessage);
                        detailPertanyaanContainer.html('<p class="text-danger">Gagal memuat detail pertanyaan.</p>');
                    }
                });
            });
        });
    </script>
@endsection