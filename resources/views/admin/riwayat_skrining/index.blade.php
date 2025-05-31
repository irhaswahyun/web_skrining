@extends('partials.admin.main')

@section('title', 'Riwayat Skrining Pasien')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Riwayat Skrining Pasien</h1>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-md-6 offset-md-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Masukkan NIK Pasien"
                                                id="nikPasienInput" name="nik" autocomplete="off">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="button" id="searchRiwayatBtn">
                                                    <i data-feather="search"></i> Cari Riwayat
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4 justify-content-center" id="skriningSummary">
                                    {{-- Info Box 1: Total Skrining Dilakukan --}}
                                    <div class="col-md-4 px-2">
                                        <div class="info-box custom-info-box-blue">
                                            <span class="info-box-icon"><i class="far fa-clipboard"></i></span>
                                            <div class="info-box-content text-center d-flex flex-column justify-content-center align-items-center">
                                                <span class="info-box-text">Total Skrining Dilakukan</span>
                                                <span class="info-box-number" id="totalSkriningDilakukan">0</span>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Info Box 2: Jenis Skrining Dilakukan --}}
                                    {{-- <div class="col-md-4 px-2">
                                        <div class="info-box custom-info-box-green">
                                            <span class="info-box-icon"><i class="far fa-check-square"></i></span>
                                            <div class="info-box-content text-center d-flex flex-column justify-content-center align-items-center">
                                                <span class="info-box-text">Jenis Skrining Dilakukan</span>
                                                <span class="info-box-number" id="jumlahJenisSkriningDilakukan">0</span>
                                            </div>
                                        </div>
                                    </div> --}}
                                    {{-- Info Box 3: Total Jenis Skrining Tersedia --}}
                                    <div class="col-md-4 px-2">
                                        <div class="info-box custom-info-box-purple">
                                            <span class="info-box-icon"><i class="far fa-list-alt"></i></span>
                                            <div class="info-box-content text-center d-flex flex-column justify-content-center align-items-center">
                                                <span class="info-box-text">Total Jenis Skrining Tersedia</span>
                                                <span class="info-box-number" id="totalJenisSkriningTersedia">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-custom">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><b>No</b></th>
                                                <th><b>NIK</b></th>
                                                <th><b>Nama Pasien</b></th>
                                                <th><b>Nama Petugas</b></th>
                                                <th><b>Nama Skrining</b></th>
                                                <th><b>Tanggal</b></th>
                                                <th><b>Kondisi</b></th>
                                                <th><b>Aksi</b></th>
                                            </tr>
                                        </thead>
                                        <tbody id="riwayatSkriningTableBody">
                                            <tr>
                                                <td colspan="8" class="text-center">Masukkan NIK Pasien untuk melihat riwayat skrining.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
    </div>

    {{-- MODAL DETAIL RIWAYAT SKRINING --}}
    <div class="modal fade" id="detailRiwayatModal" tabindex="-1" role="dialog" aria-labelledby="detailRiwayatModalLabel"
        aria-hidden="true">
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
                    {{-- <h6 class="font-weight-bold">Nama Penyakit:</h6>
                    <p id="detail_riwayat_Nama_Penyakit"></p> --}}
                    <h6 class="font-weight-bold">Tanggal Skrining:</h6>
                    <p id="detail_riwayat_Tanggal_Skrining"></p>
                    <h6 class="font-weight-bold">Kondisi:</h6>
                    <p id="detail_riwayat_Kondisi"></p>
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
    @endsection

    @section('scripts')
        <script>
            $(document).ready(function() {
                feather.replace(); // Inisialisasi ikon Feather

                // Event untuk tombol "Cari Riwayat"
                $('#searchRiwayatBtn').on('click', function() {
                    fetchRiwayatSkrining();
                });

                // Event untuk input NIK (tekan Enter)
                $('#nikPasienInput').on('keypress', function(e) {
                    if (e.which == 13) { // Jika tombol Enter ditekan
                        fetchRiwayatSkrining();
                    }
                });

                function fetchRiwayatSkrining() {
                    var nik = $('#nikPasienInput').val();
                    var tableBody = $('#riwayatSkriningTableBody');

                    // Reset summary counts
                    $('#totalSkriningDilakukan').text('0');
                    $('#jumlahJenisSkriningDilakukan').text('0');
                    $('#totalJenisSkriningTersedia').text('0');

                    tableBody.empty(); // Kosongkan tabel sebelum memuat data baru
                    tableBody.append('<tr><td colspan="8" class="text-center">Memuat riwayat skrining...</td></tr>');

                    $.ajax({
                        url: "{{ route('riwayat_skrining.get_history') }}",
                        method: 'GET',
                        data: {
                            nik: nik
                        },
                        success: function(response) {
                            tableBody.empty(); // Kosongkan lagi setelah data diterima

                            if (response.success) {
                                // Update summary counts
                                $('#totalSkriningDilakukan').text(response.summary.total_skrining_dilakukan);
                                $('#jumlahJenisSkriningDilakukan').text(response.summary.jumlah_jenis_skrining_dilakukan);
                                $('#totalJenisSkriningTersedia').text(response.summary.total_jenis_skrining_tersedia);

                                if (response.data.length > 0) {
                                    $.each(response.data, function(index, skrining) {
                                        var row = `
                                            <tr>
                                                <td>${index + 1}</td>
                                                <td>${skrining.NIK}</td>
                                                <td>${skrining.Nama_Pasien}</td>
                                                <td>${skrining.Nama_Petugas}</td>
                                                <td>${skrining.Nama_Skrining}</td>
                                                <td>${skrining.Tanggal_Skrining}</td>
                                                <td>${skrining.Kondisi}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info btn-detail-riwayat"
                                                            data-skrining-id="${skrining.id}"
                                                            data-skrining-data='${JSON.stringify(skrining)}'>Detail</button>
                                                </td>
                                            </tr>
                                        `;
                                        tableBody.append(row);
                                    });
                                } else {
                                    tableBody.append('<tr><td colspan="8" class="text-center">' + (response.message || 'Tidak ada riwayat skrining ditemukan untuk NIK ini.') + '</td></tr>');
                                }
                            } else {
                                tableBody.append('<tr><td colspan="8" class="text-center">' + (response.message || 'Terjadi kesalahan saat memuat data.') + '</td></tr>');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: response.message || 'Terjadi kesalahan saat memuat data.',
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching riwayat skrining:", error);
                            console.log(xhr.responseText);
                            var errorMessage = 'Terjadi kesalahan saat mengambil riwayat skrining. Silakan coba lagi.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            tableBody.empty();
                            tableBody.append('<tr><td colspan="8" class="text-center text-danger">' + errorMessage + '</td></tr>');
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMessage,
                            });
                        }
                    });
                }

                // Event untuk tombol "Detail" di Riwayat Skrining
                $(document).on('click', '.btn-detail-riwayat', function() {
                    var skriningData = $(this).data('skrining-data');

                    if (skriningData) {
                        $('#detail_riwayat_NIK').text(skriningData.NIK);
                        $('#detail_riwayat_Nama_Pasien').text(skriningData.Nama_Pasien);
                        $('#detail_riwayat_Nama_Petugas').text(skriningData.Nama_Petugas);
                        $('#detail_riwayat_Nama_Skrining').text(skriningData.Nama_Skrining);
                        $('#detail_riwayat_Nama_Penyakit').text(skriningData.Nama_Penyakit);
                        $('#detail_riwayat_Tanggal_Skrining').text(skriningData.Tanggal_Skrining);
                        $('#detail_riwayat_Kondisi').text(skriningData.Kondisi);

                        var detailPertanyaanContainer = $('#detail_riwayat_daftar_pertanyaan_formatted');
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Data riwayat skrining tidak ditemukan.',
                        });
                    }
                });

                // Reset modal saat ditutup
                $('#detailRiwayatModal').on('hidden.bs.modal', function() {
                    $('#detail_riwayat_NIK').text('');
                    $('#detail_riwayat_Nama_Pasien').text('');
                    $('#detail_riwayat_Nama_Petugas').text('');
                    $('#detail_riwayat_Nama_Skrining').text('');
                    $('#detail_riwayat_Nama_Penyakit').text('');
                    $('#detail_riwayat_Tanggal_Skrining').text('');
                    $('#detail_riwayat_Kondisi').text('');
                    $('#detail_riwayat_daftar_pertanyaan_formatted').empty();
                    $('#detail_riwayat_daftar_pertanyaan_formatted').append('<p>Memuat pertanyaan...</p>');
                });

            });
        </script>
    @endsection

    