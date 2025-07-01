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

                {{-- Bagian baru untuk ringkasan status skrining (terindikasi/belum terindikasi) --}}
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-success card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Ringkasan Status Skrining Pasien</h3>
                            </div>
                            <div class="card-body">
                                <div class="row" id="skriningStatusSummaryContainer">
                                    {{-- Konten ringkasan status skrining akan dimuat di sini --}}
                                    <div class="col-12 text-center py-3">
                                        <div class="alert alert-info">Pilih filter di atas untuk melihat ringkasan status skrining.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    {{-- Modal untuk menampilkan daftar pasien berdasarkan status skrining (hasil diagnosa atau 'Belum Diskrining') --}}
    <div class="modal fade" id="pasienListModal" tabindex="-1" role="dialog" aria-labelledby="pasienListModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pasienListModalLabel">Daftar Pasien <span id="modalSkriningStatus"></span> untuk <span id="modalFormSkriningName"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>NIK</th>
                                    <th>Nama Pasien</th>
                                    <th>Tanggal Skrining</th>
                                    <th>Status Indikasi</th>
                                </tr>
                            </thead>
                            <tbody id="pasienListTableBody">
                                {{-- Data pasien akan dimuat di sini --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="printPasienListBtn">Cetak Daftar Pasien</button> {{-- Tombol cetak baru --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal yang sudah ada untuk detail skrining per pasien (pertanyaan & jawaban) --}}
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
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>NIK:</strong> <span id="detail_riwayat_NIK"></span></p>
                            <p><strong>Nama Pasien:</strong> <span id="detail_riwayat_Nama_Pasien"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Nama Skrining:</strong> <span id="detail_riwayat_Nama_Skrining"></span></p>
                            <p><strong>Tanggal Skrining:</strong> <span id="detail_riwayat_Tanggal_Skrining"></span></p>
                            <p><strong>Nama Penyakit Terkait:</strong> <span id="detail_riwayat_Nama_Penyakit"></span></p>
                            <p><strong>Hasil Diagnosa:</strong> <span id="detail_riwayat_Hasil_Diagnosa"></span></p>
                        </div>
                    </div>
                    <hr>
                    <h6>Detail Jawaban:</h6>
                    <div id="detail_riwayat_daftar_pertanyaan_formatted">
                        {{-- Pertanyaan dan jawaban akan dimuat di sini --}}
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

            const rekapContainer = $('#rekapContainer');
            const totalPasienSkriningElem = $('#totalPasienSkrining');
            const totalJenisFormSkriningTersediaElem = $('#totalJenisFormSkriningTersedia');
            const allFormSkriningNames = @json($daftarFormSkrining->pluck('nama_skrining'));
            const skriningStatusSummaryContainer = $('#skriningStatusSummaryContainer');

            // Set total jenis form skrining tersedia di awal
            totalJenisFormSkriningTersediaElem.text(allFormSkriningNames.length);

            // Fungsi utama untuk memuat data rekap
            function loadRekapData() {
                const bulanVal = parseInt($('#filterBulan').val());
                const tahunVal = parseInt($('#filterTahun').val());
                const wilayahVal = $('#filterWilayah').val();

                // Validasi input sebelum request AJAX
                if (!bulanVal || !tahunVal) {
                    rekapContainer.html('<div class="col-12 text-center py-5"><div class="alert alert-warning text-center">Mohon pilih <b>Bulan</b> dan <b>Tahun</b> untuk menerapkan filter.</div></div>');
                    totalPasienSkriningElem.text('0');
                    skriningStatusSummaryContainer.html('<div class="col-12 text-center py-3"><div class="alert alert-info">Pilih filter di atas untuk melihat ringkasan status skrining.</div></div>');
                    return; // Berhenti jika filter belum lengkap
                }

                // Tampilkan spinner loading saat AJAX berjalan untuk rekapContainer
                rekapContainer.html(`
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data rekap...</p>
                    </div>
                `);
                totalPasienSkriningElem.text('Memuat...');

                // Tampilkan spinner loading untuk skriningStatusSummaryContainer
                skriningStatusSummaryContainer.html(`
                    <div class="col-12 text-center py-3">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat ringkasan status skrining...</p>
                    </div>
                `);

                // AJAX call untuk getRekapSummary (Ringkasan per wilayah dan form skrining)
                $.ajax({
                    url: "{{ route('rekap_hasil_skrining.summary') }}",
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
                            console.error("Error from server (summary):", response.message || 'Gagal memuat data rekap dari server.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading rekap data (summary):", xhr.responseText);
                        rekapContainer.html('<div class="col-12 text-center py-5"><div class="alert alert-danger">Gagal memuat data rekap. Silakan coba lagi.</div></div>');
                        totalPasienSkriningElem.text('0');
                    }
                });

                // AJAX call untuk getSkriningStatusSummary (Ringkasan Diagnosa dan Belum Diskrining)
                $.ajax({
                    url: "{{ route('rekap_hasil_skrining.status_summary') }}",
                    method: 'GET',
                    data: {
                        bulan: bulanVal,
                        tahun: tahunVal,
                        wilayah: wilayahVal
                    },
                    beforeSend: function() {
                        // Tampilkan loading spinner sebelum request dikirim
                        $('#skriningStatusSummaryContainer').html(`
                            <div class="col-12 text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2">Memuat data status skrining...</p>
                            </div>
                        `);
                    },
                    success: function(response) {
                        skriningStatusSummaryContainer.empty(); // Kosongkan container

                        if (response.success) {
                            if (Object.keys(response.skriningStatusSummary).length > 0) {
                                $.each(response.skriningStatusSummary, function(formName, counts) {
                                    let formStatusHtml = `
                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <div class="card card-outline card-success h-100">
                                                <div class="card-header">
                                                    <h5 class="card-title text-bold text-truncate">${formName}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                    `;

                                    // Iterate over each diagnosis result for the current form
                                    $.each(counts, function(statusKey, count) {
                                        let iconClass = '';
                                        let bgColorClass = '';
                                        let statusDisplayName = statusKey; // Default display name is the key itself

                                        if (statusKey === 'Belum Diskrining') {
                                            iconClass = 'fas fa-check-circle';
                                            bgColorClass = 'bg-gradient-info';
                                        } else {
                                            // You can customize icons/colors based on specific diagnosis results here
                                            // For example:
                                            if (statusKey.includes('Terkonfirmasi')) {
                                                iconClass = 'fas fa-exclamation-triangle';
                                                bgColorClass = 'bg-gradient-danger';
                                            } else if (statusKey.includes('Tidak Terindikasi')) {
                                                iconClass = 'fas fa-check';
                                                bgColorClass = 'bg-gradient-success';
                                            } else {
                                                iconClass = 'fas fa-stethoscope'; // Default icon for other diagnoses
                                                bgColorClass = 'bg-gradient-warning'; // Default color
                                            }
                                        }

                                        formStatusHtml += `
                                            <div class="col-6">
                                                <div class="info-box ${bgColorClass} clickable-status-card"
                                                     data-form-name="${formName}"
                                                     data-status="${statusKey}"
                                                     data-bulan="${bulanVal}"
                                                     data-tahun="${tahunVal}"
                                                     data-wilayah="${wilayahVal}"
                                                     style="cursor: pointer;">
                                                    <span class="info-box-icon"><i class="${iconClass}"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">${statusDisplayName}</span>
                                                        <span class="info-box-number">${count}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    });

                                    formStatusHtml += `
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    skriningStatusSummaryContainer.append(formStatusHtml);
                                });
                            } else {
                                skriningStatusSummaryContainer.html('<div class="col-12 text-center py-3"><div class="alert alert-info">Tidak ada ringkasan status skrining ditemukan untuk filter yang dipilih.</div></div>');
                            }
                        } else {
                            skriningStatusSummaryContainer.html('<div class="col-12 text-center py-3"><div class="alert alert-danger">Gagal memuat ringkasan status skrining.</div></div>');
                            console.error("Error from server (status summary):", response.message || 'Gagal memuat ringkasan status skrining dari server.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading skrining status summary:", xhr.responseText);
                        skriningStatusSummaryContainer.html('<div class="col-12 text-center py-3"><div class="alert alert-danger">Gagal memuat ringkasan status skrining. Silakan coba lagi.</div></div>');
                    }
                });
            }

            // Event listener untuk tombol 'Terapkan Filter'
            $('#applyFilterBtn').on('click', function() {
                loadRekapData(); // Panggil saat tombol diklik
            });

            // Event listener untuk klik pada kartu status (Diagnosa Spesifik / Belum Diskrining)
            $(document).on('click', '.clickable-status-card', function() {
                const formName = $(this).data('form-name');
                const status = $(this).data('status'); // Ini sekarang akan berisi nama diagnosa atau 'Belum Diskrining'
                const bulan = $(this).data('bulan');
                const tahun = $(this).data('tahun');
                const wilayah = $(this).data('wilayah');

                $('#modalFormSkriningName').text(formName);
                $('#modalSkriningStatus').text(status); // Tampilkan status diagnosa yang sebenarnya
                $('#pasienListTableBody').empty().append(`
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div> Memuat daftar pasien...
                        </td>
                    </tr>
                `);
                $('#pasienListModal').modal('show');

                $.ajax({
                    url: "{{ route('rekap_hasil_skrining.pasien_by_status') }}",
                    method: 'GET',
                    data: {
                        nama_form_skrining: formName,
                        status_indikasi: status, // Kirim nama diagnosa atau 'Belum Diskrining'
                        bulan: bulan,
                        tahun: tahun,
                        wilayah: wilayah
                    },
                    success: function(response) {
                        $('#pasienListTableBody').empty();
                        if (response.success && response.pasienList && response.pasienList.length > 0) {
                            $.each(response.pasienList, function(index, pasien) {
                                let row = `
                                    <tr>
                                        <td>${pasien.NIK}</td>
                                        <td>${pasien.Nama_Pasien}</td>
                                        <td>${pasien.Tanggal_Skrining}</td>
                                        <td>${pasien.Status_Indikasi}</td>
                                    </tr>
                                `;
                                $('#pasienListTableBody').append(row);
                            });
                        } else {
                            $('#pasienListTableBody').append(`
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada pasien ditemukan untuk status ini.</td>
                                </tr>
                            `);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching pasien list by status:", xhr.responseText);
                        $('#pasienListTableBody').empty().append(`
                            <tr>
                                <td colspan="4" class="text-center text-danger">Gagal memuat daftar pasien.</td>
                            </tr>
                        `);
                    }
                });
            });

            // --- Logika untuk Modal Detail Skrining per Pasien (MODAL INI TETAP ADA) ---
            $(document).on('click', '.btn-detail-skrining', function() {
                var skriningId = $(this).data('skrining-id');
                var detailPertanyaanContainer = $('#detail_riwayat_daftar_pertanyaan_formatted');
                
                // Reset dan tampilkan loading di modal
                $('#detail_riwayat_NIK').text('Memuat...');
                $('#detail_riwayat_Nama_Pasien').text('Memuat...');
                $('#detail_riwayat_Nama_Skrining').text('Memuat...');
                $('#detail_riwayat_Tanggal_Skrining').text('Memuat...');
                $('#detail_riwayat_Nama_Penyakit').text('Memuat...');
                $('#detail_riwayat_Hasil_Diagnosa').text('Memuat...'); 
                detailPertanyaanContainer.empty().append('<p class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">Loading...</span></div> Memuat jawaban...</p>');

                $.ajax({
                    url: "{{ route('rekap_hasil_skrining.detail') }}",
                    method: 'GET',
                    data: {
                        skrining_id: skriningId
                    },
                    success: function(response) {
                        if (response.success && response.skriningDetail) {
                            var skriningData = response.skriningDetail;
                            $('#detail_riwayat_NIK').text(skriningData.NIK || 'N/A');
                            $('#detail_riwayat_Nama_Pasien').text(skriningData.Nama_Pasien || 'N/A');
                            $('#detail_riwayat_Nama_Skrining').text(skriningData.Nama_Skrining || 'N/A');
                            $('#detail_riwayat_Tanggal_Skrining').text(skriningData.Tanggal_Skrining || 'N/A');
                            $('#detail_riwayat_Nama_Penyakit').text(skriningData.Nama_Penyakit || 'N/A');
                            $('#detail_riwayat_Hasil_Diagnosa').text(skriningData.Hasil_Diagnosa || 'N/A');

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
                    }
                });
            });

            // Fungsi untuk mencetak isi modal
            $('#printPasienListBtn').on('click', function() {
                const modalTitle = $('#pasienListModalLabel').text();
                const tableContent = $('#pasienListTableBody').html();
                const tableHeader = $('#pasienListModal table thead').html();

                let printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <html>
                    <head>
                        <title>${modalTitle}</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 20px; }
                            h1 { text-align: center; margin-bottom: 20px; }
                            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                            th { background-color: #f2f2f2; }
                            @media print {
                                body { -webkit-print-color-adjust: exact; }
                                table { page-break-inside: auto; }
                                tr { page-break-inside: avoid; page-break-after: auto; }
                                thead { display: table-header-group; }
                                tfoot { display: table-footer-group; }
                            }
                        </style>
                    </head>
                    <body>
                        <h1>${modalTitle}</h1>
                        <table>
                            <thead>
                                ${tableHeader}
                            </thead>
                            <tbody>
                                ${tableContent}
                            </tbody>
                        </table>
                    </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                // Tidak perlu menutup window secara paksa, biarkan pengguna menutupnya setelah mencetak
                // printWindow.close(); 
            });
        });
    </script>
@endsection
