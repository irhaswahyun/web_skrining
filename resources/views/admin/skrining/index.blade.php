@extends('partials.admin.main')

@section('title', 'Manajemen Skrining')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Manajemen Skrining</h1>
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
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i data-feather="search"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" placeholder="Cari Nama Pasien"
                                                id="searchSkrining" name="search" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button id="cetakHasilUtamaBtn" class="btn btn-info" style="display:none;">
                                            <i class="fas fa-print"></i> Cetak Hasil Pasien Terpilih
                                        </button>
                                        <button id="tambahSkriningBaru" class="btn btn-tambah-baru">
                                            Tambah Baru
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-custom">
                                        <thead class="thead-light">
                                            <tr>
                                                <th><b>No</b></th>
                                                <th><b>Nama Petugas</b></th>
                                                <th><b>NIK Pasien</b></th>
                                                <th><b>Nama Pasien</b></th>
                                                <th><b>Tanggal Skrining</b></th>
                                                <th><b>Nama Skrining</b></th>
                                                <th><b>Aksi</b></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($skrinings as $skrining)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $skrining->Nama_Petugas }}</td>
                                                    <td>{{ $skrining->pasien->NIK ?? '-' }}</td>
                                                    <td>{{ $skrining->Nama_Pasien }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($skrining->Tanggal_Skrining)->format('Y-m-d') }}
                                                    </td>
                                                    <td>{{ $skrining->formSkrining->nama_skrining ?? '-' }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary btn-edit"
                                                            data-id="{{ $skrining->id }}">
                                                            Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-success btn-view-diagnosa"
                                                            data-id="{{ $skrining->id }}">Lihat Hasil Skrining</button>
                                                        <button class="btn btn-danger btn-sm btn-delete"
                                                            data-skrining-id="{{ $skrining->id }}"
                                                            data-skrining-nama="{{ $skrining->Nama_Pasien }}">
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- MODAL TAMBAH SKRINING BARU --}}
    <div class="modal fade" id="tambahSkriningModal" tabindex="-1" role="dialog"
        aria-labelledby="tambahSkriningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahSkriningModalLabel">Tambah Skrining Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="tambahSkriningForm" method="POST" action="{{ route('skrining.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="Nama_Petugas">Nama Petugas</label>
                            <input type="text" name="Nama_Petugas" class="form-control"
                                placeholder="Masukkan Nama Petugas" required>
                        </div>
                        <div class="form-group">
                            <label for="Tanggal_Skrining">Tanggal Skrining</label>
                            <input type="text" name="Tanggal_Skrining" id="tanggalSkriningTambah" class="form-control"
                                placeholder="Y-m-d" required>
                        </div>
                        <div class="form-group">
                            <label for="NIK_Pasien">NIK Pasien</label>
                            <input type="text" name="NIK_Pasien" id="NIK_Pasien" class="form-control"
                                placeholder="Masukkan NIK Pasien (16 digit)" required maxlength="16">
                            <small id="nik_length_info_tambah" class="form-text text-muted">0/16 digit</small>
                        </div>
                        <div class="form-group">
                            <label for="Nama_Pasien">Nama Pasien</label>
                            <input type="text" name="Nama_Pasien" id="Nama_Pasien" class="form-control"
                                placeholder="Nama Pasien akan muncul otomatis" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="id_form_skrining">Formulir Skrining</label>
                            <select class="form-control" id="id_form_skrining" name="id_form_skrining" required>
                                <option value="">-- Masukkan NIK Pasien untuk Rekomendasi --</option>
                            </select>
                            <small id="form_skrining_recommendation_info" class="form-text text-muted">
                                Form skrining akan direkomendasikan setelah NIK pasien dimasukkan.
                            </small>
                        </div>
                        <div class="form-group" id="pertanyaan_container_tambah">
                            <label>Pertanyaan</label>
                            <div id="pertanyaan_list_tambah">
                                <p>Pilih formulir skrining untuk memuat pertanyaan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            id="btnTutupTambah">Tutup</button>
                        <button type="submit" class="btn btn-success" id="btnSimpanSkriningTambah">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT SKRINING --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Skrining</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_Nama_Petugas">Nama Petugas</label>
                            <input type="text" class="form-control" id="edit_Nama_Petugas" name="Nama_Petugas"
                                placeholder="Masukkan Nama Petugas" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_Tanggal_Skrining">Tanggal Skrining</label>
                            <input type="text" class="form-control" id="edit_Tanggal_Skrining"
                                name="Tanggal_Skrining" placeholder="Y-m-d" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_NIK_Pasien">NIK Pasien</label>
                            <input type="text" class="form-control" id="edit_NIK_Pasien" name="NIK_Pasien"
                                placeholder="Masukkan NIK Pasien" required readonly>
                            <small id="nik_length_info_edit" class="form-text text-muted">0/16 digit</small>
                        </div>
                        <div class="form-group">
                            <label for="edit_Nama_Pasien">Nama Pasien</label>
                            <input type="text" class="form-control" id="edit_Nama_Pasien" name="Nama_Pasien"
                                placeholder="Masukkan Nama Pasien" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit_id_form_skrining">Formulir Skrining</label>
                            <select class="form-control" id="edit_id_form_skrining" name="id_form_skrining_edit"
                                required>
                                @isset($formSkrinings)
                                    @foreach ($formSkrinings as $formSkrining)
                                        <option value="{{ $formSkrining->id }}">{{ $formSkrining->nama_skrining }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pertanyaan</label>
                            <div id="edit_pertanyaan_list">
                                <p>Memuat pertanyaan...</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog"
        aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data skrining untuk pasien: <span id="skriningToDelete"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL HASIL DIAGNOSA --}}
    <div class="modal fade" id="hasilDiagnosaDisplayModal" tabindex="-1" role="dialog"
        aria-labelledby="hasilDiagnosaDisplayModal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hasilDiagnosaDisplayModal">Hasil Skrining</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="printableDiagnosaArea">
                    <div id="diagnosaContent">
                        <h4 class="text-success mb-3">Hasil Skrining Pasien :</h4>
                        <p id="hasilUtamaDiagnosaDisplay"><strong>Hasil Utama :</strong></p>
                        <p id="rekomendasiDiagnosaDisplay"><strong>Rekomendasi Tindak Lanjut :</strong></p>
                        <div id="detailDiagnosaDisplay"
                            style="margin-top: 15px; border-top: 1px dashed #a5d6a7; padding-top: 15px;">
                            <h5 class="mb-2">Detail Analisis :</h5>
                            <ul>
                                <li id="gejalaTeridentifikasiDisplay"><strong>Gejala Teridentifikasi :</strong></li>
                                <li id="riwayatMalariaObatDisplay"><strong>Riwayat Malaria/Obat :</strong></li>
                                <li id="alurDiagnosaDisplay"><strong>Alur Pemerikasaan Lanjut :</strong></li>
                                <li id="alasanDiagnosaDisplay"><strong>Alasan Diagnosa :</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        id="btnTutupDiagnosa">Tutup</button>
                    <button type="button" class="btn btn-success" id="btnSkriningSelesai">Skrining Selesai</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            if (typeof jQuery == 'undefined') {
                console.error("jQuery is not loaded! Please ensure jQuery library is included before this script.");
                return; // Hentikan eksekusi script jika jQuery tidak ada
            }

            const NIK_LENGTH = 16; // Konstanta untuk panjang NIK
            let lastDisplayedSkriningId = null;

            // Fungsi untuk mengupdate info panjang NIK
            function updateNikLengthInfo(inputId, infoId) {
            const inputElement = $(inputId); // Dapatkan elemen input menggunakan selector
            const infoElement = $(infoId);   // Dapatkan elemen info

            if (!inputElement.length) { // Cek jika elemen input tidak ditemukan
                console.warn(`Element with selector '${inputId}' not found for updateNikLengthInfo.`);
                return;
            }
            if (!infoElement.length) { // Cek jika elemen info tidak ditemukan
                console.warn(`Info element with selector '${infoId}' not found for updateNikLengthInfo.`);
                return;
            }

            const nikValue = inputElement.val(); // Ambil nilai NIK dari elemen input
            const currentLength = nikValue.length; // Hitung panjangnya

            // Reset teks info awal
            infoElement.text(`${currentLength}/${NIK_LENGTH} digit`);

            // Cek apakah NIK hanya angka
            const isNumeric = /^\d*$/.test(nikValue); // Memastikan hanya angka atau kosong

            // Hapus semua kelas warna sebelumnya untuk menghindari konflik
            infoElement.removeClass('text-muted text-success text-danger');

            if (currentLength === NIK_LENGTH && isNumeric) {
                infoElement.addClass('text-success'); // Hijau jika valid
            } else if (currentLength > NIK_LENGTH) {
                infoElement.addClass('text-danger').text(`Terlalu panjang! ${currentLength}/${NIK_LENGTH} digit.`); // Merah jika terlalu panjang
            } else if (!isNumeric && nikValue !== '') {
                infoElement.addClass('text-danger').text(`Hanya boleh angka! ${currentLength}/${NIK_LENGTH} digit.`); // Merah jika ada non-angka
            } else {
                infoElement.addClass('text-muted'); // Abu-abu jika belum lengkap
            }
            // Menonaktifkan rekomendasi/pencarian pasien jika NIK tidak valid di modal tambah
            if (inputId === '#NIK_Pasien') { // Hanya berlaku untuk NIK_Pasien di modal tambah
                 if (currentLength !== NIK_LENGTH || !isNumeric) {
                    $('#Nama_Pasien').val('');
                    $('#id_form_skrining').html('<option value="">-- Masukkan NIK Pasien untuk Rekomendasi --</option>');
                    $('#form_skrining_recommendation_info').text('Form skrining akan direkomendasikan setelah NIK pasien dimasukkan.');
                    $('#pertanyaan_list_tambah').html('<p>Pilih formulir skrining untuk memuat pertanyaan.</p>');
                    $('#pertanyaan_container_tambah').hide();
                }
            }
        }


            // Fungsi untuk mencetak hasil skrining
            function cetakHasilSkrining(containerId) {
                const printContents = document.getElementById(containerId).innerHTML;
                const printWindow = window.open('', '_blank');
                printWindow.document.write('<html><head><title>Cetak Hasil Skrining</title>');
                printWindow.document.write('<style>');
                printWindow.document.write(`
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h4 { color: #28a745; margin-bottom: 10px; }
                    p { margin-bottom: 5px; }
                    strong { font-weight: bold; }
                    ul { list-style: none; padding-left: 0; }
                    li { margin-bottom: 5px; }
                    #detailDiagnosa, #detailDiagnosaEdit { border-top: 1px dashed #a5d6a7; padding-top: 15px; margin-top: 15px; }
                    .no-print { display: none; }
                `);
                printWindow.document.write('</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write(printContents);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }

            // Fungsi untuk menampilkan diagnosa di modal baru
            function displayDiagnosaInNewModal(diagnosaData) {
                $('#hasilUtamaDiagnosaDisplay').html('<strong>Hasil Utama :</strong> ' + diagnosaData.hasil_utama
                    .replace(/_/g, ' ').toUpperCase());
                $('#rekomendasiDiagnosaDisplay').html('<strong>Rekomendasi Tindak Lanjut :</strong> ' + diagnosaData
                    .rekomendasi_tindak_lanjut);

                if (diagnosaData) {
                    // Asumsi ada modal dengan ID 'diagnosaModal' dan elemen-elemen untuk menampilkan hasil
                    $('#diagnosaModalLabel').text('Hasil Skrining untuk ' + diagnosaData.skrining
                    .Nama_Pasien); // Contoh judul
                    $('#diagnosaHasilUtama').text(diagnosaData.hasil_utama);
                    $('#diagnosaRekomendasi').text(diagnosaData.rekomendasi_tindak_lanjut);
                    $('#diagnosaDetail').text(diagnosaData.detail_diagnosa); // Jika Anda menampilkan detail
                    $('#diagnosaCatatan').text(diagnosaData.catatan); // Jika Anda menampilkan catatan

                    // Tampilkan modal diagnosa
                    $('#diagnosaModal').modal('show');
                } else {
                    Swal.fire('Info', 'Belum ada hasil skrining otomatis untuk data ini.', 'info');
                }

                if (diagnosaData.detail_diagnosa) {
                    const detailDiagnosa = diagnosaData.detail_diagnosa;
                    $('#gejalaTeridentifikasiDisplay').html('<strong>Gejala Teridentifikasi :</strong> ' + (
                        detailDiagnosa.status_gejala_teridentifikasi || '-'));
                    $('#riwayatMalariaObatDisplay').html('<strong>Riwayat Malaria/Obat </strong> ' + (detailDiagnosa
                        .status_riwayat_malaria_atau_obat_input || '-'));
                    $('#alurDiagnosaDisplay').html('<strong>Rekomendasi Tindak Lanjut :</strong> ' + (detailDiagnosa
                        .alur_diagnosa_terpilih || '-'));
                    const alasanDiagnosaText = Array.isArray(detailDiagnosa.alasan_diagnosa) ? detailDiagnosa
                        .alasan_diagnosa.join(', ') : (detailDiagnosa.alasan_diagnosa || '-');
                    $('#alasanDiagnosaDisplay').html('<strong>Alasan Tindak Lanjut :</strong> ' +
                        alasanDiagnosaText);
                } else {
                    $('#gejalaTeridentifikasiDisplay').html('');
                    $('#riwayatMalariaObatDisplay').html('');
                    $('#alurDiagnosaDisplay').html('');
                    $('#alasanDiagnosaDisplay').html('');
                }

                $('#hasilDiagnosaDisplayModal').modal('show');
                $('#cetakHasilUtama').show();
            }

            // Fungsi untuk memuat pertanyaan di modal Tambah
            function loadPertanyaanTambah() {
                var formSkriningId = $('#id_form_skrining').val();
                var nikPasien = $('#NIK_Pasien').val();
                var tanggalSkriningUntukBackend = $('#tanggalSkriningTambah').val();

                var pertanyaanContainer = $('#pertanyaan_list_tambah');
                pertanyaanContainer.empty();
                pertanyaanContainer.html('<p><i class="fas fa-spinner fa-spin"></i> Memuat pertanyaan...</p>');
                $('#pertanyaan_container_tambah').hide();

                if (!formSkriningId || !nikPasien || nikPasien.length !== NIK_LENGTH || !
                    tanggalSkriningUntukBackend) {
                    pertanyaanContainer.html(
                        '<p>Pilih Formulir Skrining, masukkan NIK Pasien (16 digit), dan Tanggal Skrining untuk memuat pertanyaan.</p>'
                    );
                    return;
                }

                $.ajax({
                    url: "{{ url('skrining/pertanyaan') }}/" + formSkriningId,
                    method: 'GET',
                    data: {
                        nik_pasien: nikPasien,
                        tanggal_skrining: tanggalSkriningUntukBackend
                    },
                    success: function(response) {
                        if (response.success === false) {
                            console.error('Backend melaporkan kegagalan:', response.message);
                            pertanyaanContainer.html(`<p class="text-danger">Gagal memuat pertanyaan: ${response.message || 'Terjadi kesalahan di server.'}</p>`);
                            Swal.fire('Error', response.message || 'Gagal memuat pertanyaan.', 'error');
                            $('#pertanyaan_container_tambah').show(); // Pastikan kontainer tetap terlihat untuk pesan error
                            return; // Hentikan pemrosesan lebih lanjut
                        }
                        pertanyaanContainer.empty();
                        if (response && response.length > 0) {
                            $.each(response, function(index, pertanyaan) {
                                var catatanHtml = '';
                                if (pertanyaan.catatan) {
                                    catatanHtml =
                                        `<small class="text-muted d-block mt-1 mb-2">Catatan: ${pertanyaan.catatan}</small>`;
                                }
                                var inputValue = pertanyaan.previous_answer !== null ?
                                    pertanyaan.previous_answer : '';
                                var inputHtml =
                                    `<input type="text" class="form-control" name="jawaban[${pertanyaan.id}]" placeholder="Masukkan jawaban..." value="${inputValue}" required>`;
                                var pertanyaanItem = `
                                    <div class="form-group mb-3">
                                        <label>${pertanyaan.pertanyaan}</label>
                                        ${catatanHtml}
                                        ${inputHtml}
                                    </div>
                                `;
                                pertanyaanContainer.append(pertanyaanItem);
                            });
                        } else {
                            pertanyaanContainer.html('<p>Tidak ada pertanyaan untuk formulir ini.</p>');
                        }
                        $('#pertanyaan_container_tambah').show();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching pertanyaan:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memuat pertanyaan untuk formulir skrining. Pastikan NIK dan Tanggal Skrining sudah benar.'
                        });
                        pertanyaanContainer.html(
                            '<p class="text-danger">Gagal memuat pertanyaan. Terjadi kesalahan.</p>'
                        );
                        $('#pertanyaan_container_tambah').show();
                    }
                });
            }

            // Fungsi untuk memuat pertanyaan di modal Edit
            function loadPertanyaanEdit(formSkriningId, existingJawabanArray = []) {
                $('#edit_pertanyaan_list').html(
                    '<p><i class="fas fa-spinner fa-spin"></i> Memuat pertanyaan...</p>');

                if (!formSkriningId) {
                    $('#edit_pertanyaan_list').html('<p>Silakan pilih formulir skrining.</p>');
                    return;
                }

                var urlToFetchPertanyaan = "{{ url('skrining/pertanyaan') }}/" + formSkriningId;
                var nikPasien = $('#edit_NIK_Pasien').val();
                var tanggalSkriningFormatted = null;
                var tanggalInput = $('#edit_Tanggal_Skrining').val();

                if (tanggalInput) {
                    const parts = tanggalInput.split('-');
                    if (parts.length === 3) {
                        tanggalSkriningFormatted = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    } else if (tanggalInput.length === 10 && tanggalInput.includes('-')) {
                        tanggalSkriningFormatted = tanggalInput;
                    }
                }

                $.ajax({
                    url: urlToFetchPertanyaan,
                    method: 'GET',
                    data: {
                        nik_pasien: nikPasien,
                        tanggal_skrining: tanggalSkriningFormatted
                    },
                    success: function(response) {
                         if (response.success === false) {
                            console.error('Backend melaporkan kegagalan:', response.message);
                            $('#edit_pertanyaan_list').html(`<p class="text-danger">Gagal memuat pertanyaan: ${response.message || 'Terjadi kesalahan di server.'}</p>`);
                            Swal.fire('Error', response.message || 'Gagal memuat pertanyaan.', 'error');
                            return; // Hentikan pemrosesan lebih lanjut
                        }
                        $('#edit_pertanyaan_list').empty();
                        let pertanyaanList = [];

                        if (Array.isArray(response)) {
                            pertanyaanList = response;
                        } else if (response.success && Array.isArray(response.data)) {
                            pertanyaanList = response.data;
                        } else if (response.success && Array.isArray(response.pertanyaan)) {
                            pertanyaanList = response.pertanyaan;
                        } else {
                            console.error("Struktur respons pertanyaan tidak valid:", response);
                            $('#edit_pertanyaan_list').html(
                                '<p class="text-danger">Gagal memuat pertanyaan: Struktur data tidak dikenal.</p>'
                            );
                            return;
                        }

                        if (pertanyaanList.length > 0) {
                            $.each(pertanyaanList, function(index, pertanyaan) {
                                var catatanHtml = '';
                                if (pertanyaan.catatan) {
                                    catatanHtml =
                                        `<small class="text-muted d-block mt-1 mb-2">Catatan: ${pertanyaan.catatan}</small>`;
                                }

                                var inputValue = '';
                                if (pertanyaan.previous_answer !== undefined && pertanyaan
                                    .previous_answer !== null) {
                                    inputValue = pertanyaan.previous_answer;
                                } else if (pertanyaan.jawaban_tersimpan !== undefined &&
                                    pertanyaan.jawanyaan_tersimpan !== null) {
                                    inputValue = pertanyaan.jawaban_tersimpan;
                                }

                                if (existingJawabanArray && existingJawabanArray.length > 0) {
                                    let foundAnswer = existingJawabanArray.find(
                                        jawaban => jawaban.ID_DaftarPertanyaan == pertanyaan
                                        .id
                                    );
                                    if (foundAnswer) {
                                        inputValue = foundAnswer.jawaban;
                                    }
                                }

                                var inputHtml =
                                    `<input type="text" class="form-control" name="jawaban[${pertanyaan.id}]" value="${inputValue || ''}" placeholder="Masukkan jawaban...">`;

                                var questionHtml = `
                                    <div class="form-group mb-3">
                                        <label>${pertanyaan.pertanyaan}</label>
                                        ${catatanHtml}
                                        <input type="hidden" name="pertanyaan[${pertanyaan.id}][id]" value="${pertanyaan.id}">
                                        ${inputHtml}
                                    </div>
                                `;
                                $('#edit_pertanyaan_list').append(questionHtml);
                            });
                        } else {
                            $('#edit_pertanyaan_list').html(
                                '<p>Tidak ada pertanyaan untuk formulir ini.</p>');
                        }

                        if (response.message) {
                            Swal.fire('Info', response.message, 'info');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching questions:", xhr.responseText);
                        let errorMessage = 'Error memuat pertanyaan.';
                        try {
                            let responseJson = JSON.parse(xhr.responseText);
                            if (responseJson.message) {
                                errorMessage += `: ${responseJson.message}`;
                            }
                        } catch (e) {}
                        $('#edit_pertanyaan_list').html(`<p class="text-danger">${errorMessage}</p>`);
                        Swal.fire('Error', errorMessage, 'error');
                    }
                });
            }

            // Fungsi untuk memuat ulang data tabel skrining
            function loadSkriningTable() {
                console.log("Memuat ulang data tabel skrining...");

                $.ajax({
                    url: "{{ route('skrining.getData') }}",
                    method: 'GET',
                    success: function(response) {
                        const tbody = $('table.table-custom tbody');
                        tbody.empty();

                        if (response && response.length > 0) {
                            $.each(response, function(index, skrining) {
                                const rowNumber = index + 1;
                                const row = `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${skrining.Nama_Petugas}</td>
                                        <td>${skrining.pasien ? skrining.pasien.NIK : '-'}</td>
                                        <td>${skrining.Nama_Pasien}</td>
                                        <td>${new Date(skrining.Tanggal_Skrining).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })}</td>
                                        <td>${skrining.form_skrining ? skrining.form_skrining.nama_skrining : '-'}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary btn-edit" data-id="${skrining.id}">Edit</button>
                                            <button class="btn btn-danger btn-sm btn-delete" data-skrining-id="${skrining.id}" data-skrining-nama="${skrining.Nama_Pasien}">Hapus</button>
                                            <button class="btn btn-sm btn-success btn-view-diagnosa" data-id="${skrining.id}">Lihat Diagnosa</button>
                                        </td>
                                    </tr>
                                `;
                                tbody.append(row);
                            });
                        } else if (response.skrinings && response.skrinings.length > 0) {
                            $.each(response.skrinings, function(index, skrining) {
                                const row = `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${skrining.Nama_Petugas}</td>
                                        <td>${skrining.pasien ? skrining.pasien.NIK : '-'}</td>
                                        <td>${skrining.Nama_Pasien}</td>
                                        <td>${new Date(skrining.Tanggal_Skrining).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })}</td>
                                        <td>${skrining.form_skrining ? skrining.form_skrining.nama_skrining : '-'}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary btn-edit" data-id="${skrining.id}">Edit</button>
                                            <button class="btn btn-danger btn-sm btn-delete" data-skrining-id="${skrining.id}" data-skrining-nama="${skrining.Nama_Pasien}">Hapus</button>
                                            <button class="btn btn-sm btn-success btn-view-diagnosa" data-id="${skrining.id}">Lihat Diagnosa</button>
                                        </td>
                                    </tr>
                                `;
                                tbody.append(row);
                            });
                        } else {
                            tbody.append(
                                '<tr><td colspan="7" class="text-center">Tidak ada data skrining.</td></tr>'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading skrining table:", error);
                        Swal.fire('Error', 'Gagal memuat data skrining.', 'error');
                        $('table.table-custom tbody').html(
                            '<tr><td colspan="7" class="text-center text-danger">Gagal memuat data.</td></tr>'
                        );
                    }
                });
            }

            // Inisialisasi Datepicker untuk modal Tambah
            $('#tanggalSkriningTambah').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: '1945:2050',
                showButtonPanel: true,
                currentText: "Hari Ini",
                closeText: "Selesai",
                onClose: function(dateText, inst) {
                    if (dateText === '') {
                        $(this).val('');
                    }
                    if ($('#id_form_skrining').val() && $('#NIK_Pasien').val().length === NIK_LENGTH) {
                        loadPertanyaanTambah();
                    }
                }
            });

            // Inisialisasi Datepicker untuk modal Edit
            $('#edit_Tanggal_Skrining').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: '1945:2050',
                showButtonPanel: true,
                currentText: "Hari Ini",
                closeText: "Selesai",
                onClose: function(dateText, inst) {
                    if (dateText === '') {
                        $(this).val('');
                    }
                    if ($('#edit_id_form_skrining').val() && $('#edit_NIK_Pasien').val().length ===
                        NIK_LENGTH) {
                        var skriningId = $('#editForm').attr('action').split('/').pop();
                        loadPertanyaanEdit(skriningId);
                    }
                }
            });

            // Event untuk tombol "Tambah Baru"
            $('#tambahSkriningBaru').click(function() {
                $('#tambahSkriningForm')[0].reset();
               $('#NIK_Pasien').val('');
                    // Panggil updateNikLengthInfo dengan selector input
                    updateNikLengthInfo('#NIK_Pasien', '#nik_length_info_tambah');
                $('#Nama_Pasien').val('');
                $('#tanggalSkriningTambah').datepicker('setDate', new Date());
                updateNikLengthInfo('#NIK_Pasien', '#nik_length_info_tambah');
                $('#id_form_skrining').html(
                    '<option value="">-- Masukkan NIK Pasien untuk Rekomendasi --</option>');
                $('#form_skrining_recommendation_info').text(
                    'Form skrining akan direkomendasikan setelah NIK pasien dimasukkan.');
                $('#pertanyaan_list_tambah').html(
                    '<p>Pilih formulir skrining untuk memuat pertanyaan.</p>');
                $('#pertanyaan_container_tambah').hide();
                $('#btnSimpanSkriningTambah').prop('disabled', false).text('Simpan');
                $('#btnTutupTambah').text('Batal');
                $('#tambahSkriningModal').modal('show');
            });

            // Event untuk NIK Pasien di Modal Tambah
            $('#NIK_Pasien').on('input', function() {
                 // Hapus karakter non-digit secara langsung
                let value = $(this).val();
                value = value.replace(/\D/g, ''); // Hanya tinggalkan digit
                $(this).val(value);

                updateNikLengthInfo('#NIK_Pasien', '#nik_length_info_tambah');

                var nik = $(this).val();
                $('#Nama_Pasien').val('');
                $('#id_form_skrining').html('<option value="">-- Memuat Rekomendasi... --</option>');
                $('#form_skrining_recommendation_info').html(
                    '<i class="fas fa-spinner fa-spin"></i> Mencari rekomendasi...');
                $('#pertanyaan_container_tambah').hide();

                if (nik && nik.length === NIK_LENGTH && /^\d{16}$/.test(nik)) {
                      $('#form_skrining_recommendation_info').html('<i class="fa fa-spinner fa-spin"></i> Memuat Rekomendasi...');
            $('#id_form_skrining').html('<option value="">Memuat...</option>');
            $('#pertanyaan_container_tambah').hide();
                    $.ajax({
                        url: "{{ route('pasien.getPasienData') }}",
                        method: 'GET',
                        data: {
                            NIK: nik
                        },
                        success: function(responsePasien) {
                            if (responsePasien.success && responsePasien.data) {
                                var pasien = responsePasien.data;
                                $('#Nama_Pasien').val(pasien.Nama_Pasien);

                                $.ajax({
                                    url: "{{ route('skrining.getRecommendedFormSkrinings') }}",
                                    method: 'GET',
                                    data: {
                                        nik_pasien: nik
                                    },
                                    success: function(responseForms) {
                                        if (responseForms.success) {
                                            var recommendedForms = responseForms
                                                .recommendedForms;
                                            $('#id_form_skrining').html(
                                                '<option value="">-- Pilih Formulir --</option>'
                                            );
                                            if (recommendedForms.length > 0) {
                                                $('#form_skrining_recommendation_info')
                                                    .html(
                                                        '<span class="text-success">Rekomendasi Formulir Skrining:</span>'
                                                    );
                                                $.each(recommendedForms, function(
                                                    index, form) {
                                                    $('#id_form_skrining')
                                                        .append(
                                                            '<option value="' +
                                                            form.id + '">' +
                                                            form
                                                            .nama_skrining +
                                                            '</option>');
                                                });
                                                if (recommendedForms.length === 1 &&
                                                    $('#id_form_skrining').val() ===
                                                    "") {
                                                    $('#id_form_skrining').val(
                                                            recommendedForms[0].id)
                                                        .trigger('change');
                                                }
                                            } else {
                                                $('#form_skrining_recommendation_info')
                                                    .html(
                                                        '<span class="text-warning">Tidak ada rekomendasi formulir untuk kategori pasien ini.</span>'
                                                    );
                                                @isset($formSkrinings)
                                                    $.each(@json($formSkrinings),
                                                        function(index, form) {
                                                            $('#id_form_skrining')
                                                                .append(
                                                                    '<option value="' +
                                                                    form.id +
                                                                    '">' + form
                                                                    .nama_skrining +
                                                                    '</option>'
                                                                );
                                                        });
                                                @endisset
                                            }
                                            if ($('#id_form_skrining').val() && $(
                                                    '#tanggalSkriningTambah')
                                                .val() && nik.length === NIK_LENGTH
                                                ) {
                                                loadPertanyaanTambah();
                                            }
                                        } else {
                                            $('#form_skrining_recommendation_info')
                                                .html('<span class="text-danger">' +
                                                    (responseForms.message ||
                                                        'Error mencari rekomendasi formulir.'
                                                    ) + '</span>');
                                            $('#id_form_skrining').html(
                                                '<option value="">-- Gagal memuat --</option>'
                                            );
                                        }
                                    },
                                    error: function(xhrForms) {
                                        $('#form_skrining_recommendation_info')
                                            .html(
                                                '<span class="text-danger">Error memuat rekomendasi: ' +
                                                (xhrForms.responseJSON ? xhrForms
                                                    .responseJSON.message :
                                                    'Koneksi gagal.') + '</span>');
                                        $('#id_form_skrining').html(
                                            '<option value="">-- Gagal memuat --</option>'
                                        );
                                        console.error(
                                            "Error fetching recommended forms:",
                                            xhrForms.responseText);
                                    }
                                });

                            } else {
                                $('#Nama_Pasien').val('');
                                $('#form_skrining_recommendation_info').html(
                                    '<span class="text-danger">NIK tidak ditemukan.</span>');
                                $('#id_form_skrining').html(
                                    '<option value="">-- Masukkan NIK Pasien untuk Rekomendasi --</option>'
                                );
                                $('#pertanyaan_list_tambah').html(
                                    '<p>Pilih formulir skrining untuk memuat pertanyaan.</p>'
                                );
                                $('#pertanyaan_container_tambah').hide();
                            }
                        },
                        error: function(xhrPasien) {
                            $('#Nama_Pasien').val('');
                            $('#form_skrining_recommendation_info').html(
                                '<span class="text-danger">Error mencari data pasien: ' + (
                                    xhrPasien.responseJSON ? xhrPasien.responseJSON
                                    .message : 'Koneksi gagal.') + '</span>');
                            $('#id_form_skrining').html(
                                '<option value="">-- Gagal memuat --</option>');
                            $('#pertanyaan_list_tambah').html(
                                '<p>Pilih formulir skrining untuk memuat pertanyaan.</p>');
                            $('#pertanyaan_container_tambah').hide();
                            console.error("Error fetching pasien data:", xhrPasien
                                .responseText);
                        }
                    });
                } else {
                    $('#Nama_Pasien').val('');
                    $('#id_form_skrining').html(
                        '<option value="">-- Masukkan NIK Pasien untuk Rekomendasi --</option>');
                    $('#form_skrining_recommendation_info').text(
                        'Form skrining akan direkomendasikan setelah NIK pasien dimasukkan.');
                    $('#pertanyaan_list_tambah').html(
                        '<p>Pilih formulir skrining untuk memuat pertanyaan.</p>');
                    $('#pertanyaan_container_tambah').hide();
                }
            });

            // Event untuk id_form_skrining di Modal Tambah
            $('#id_form_skrining').on('change', function() {
                loadPertanyaanTambah();
            });

            // Event saat modal Edit dibuka
    $('#editModal').on('shown.bs.modal', function() {
        // Panggil updateNikLengthInfo dengan selector string untuk input NIK edit
        updateNikLengthInfo('#edit_NIK_Pasien', '#nik_length_info_edit'); // <<< INI JUGA DIPERBAIKI
    });

            // Event saat modal Tambah dibuka
            $('#tambahSkriningModal').on('shown.bs.modal', function() {
                // Panggil updateNikLengthInfo dengan selector string untuk input NIK tambah
                updateNikLengthInfo('#NIK_Pasien', '#nik_length_info_tambah'); // <<< INI JUGA DIPERBAIKI
            });

            // Event untuk submit form Tambah Skrining Baru
            $('#tambahSkriningForm').submit(function(e) {
                e.preventDefault();
                 var nik = $('#NIK_Pasien').val();
                var form = $(this);
                var url = form.attr('action');
                var formData = new FormData(form[0]); // PERBAIKI: 'new new FormData' jadi 'new FormData'

                if (nik.length !== NIK_LENGTH || !/^\d{16}$/.test(nik)) {
                    Swal.fire('Peringatan!', 'NIK Pasien harus 16 digit angka.', 'warning');
                    $('#NIK_Pasien').focus();
                    return; // Hentikan proses submit
                }
                $('#btnSimpanSkriningTambah').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#tambahSkriningModal').modal('hide');

                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            // lastDisplayedSkriningId = response.skrining_id; // Ini opsional, bisa dihapus jika tidak digunakan

                            const newSkrining = response
                                .skrining; // Ini sekarang akan berisi objek skrining lengkap
                            const newDiagnosa = response.diagnosa; // Data diagnosa

                            if (newSkrining) { // Pastikan objek skrining ada
                                const tableBody = $(
                                    '.table-custom tbody'
                                    ); // Pastikan ini selector yang benar untuk tbody

                                // Mendapatkan nomor urut yang benar
                                // Jika Anda ingin nomor urut selalu dimulai dari 1 di tampilan saat ini,
                                // dan item baru selalu paling atas, Anda mungkin perlu logika yang lebih canggih
                                // jika ada pagination atau sorting.
                                // Untuk saat ini, kita bisa menghitung ulang atau memberi nomor urut dummy.
                                // Jika loadSkriningTable() dipanggil di awal, nomor urutnya akan benar.
                                // Untuk penambahan dinamis, kita bisa pakai nomor baris + 1 atau fetch dari server.
                                // Contoh sederhana:
                                const rowNumber = tableBody.children('tr').length +
                                    1; // Mendapatkan jumlah baris yang sudah ada, lalu ditambah 1 untuk yang baru

                                const newRow = `
                        <tr>
                            <td>${rowNumber}</td> 
                            <td>${newSkrining.Nama_Petugas}</td>
                            <td>${newSkrining.pasien.NIK || '-'}</td>
                            <td>${newSkrining.Nama_Pasien}</td>
                            <td>${newSkrining.Tanggal_Skrining.split('T')[0]}</td>
                            <td>${newSkrining.form_skrining.nama_skrining || '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-primary btn-edit" data-id="${newSkrining.id}">Edit</button>
                                ${newDiagnosa ? `<button class="btn btn-sm btn-success btn-view-diagnosa1" data-id="${newSkrining.id}">Lihat Hasil Skrining</button>` : `<button class="btn btn-sm btn-warning btn-view-diagnosa" data-id="${newSkrining.id}">Lihat Diagnosa</button>`}
                                <button class="btn btn-danger btn-sm btn-delete" data-skrining-id="${newSkrining.id}" data-skrining-nama="${newSkrining.Nama_Pasien}">Hapus</button>
                            </td>
                        </tr>
                    `;
                                tableBody.append(newRow); // Tambahkan di paling atas

                                // Panggil fungsi untuk menampilkan diagnosa di modal baru
                                displayDiagnosaInNewModal(
                                    newDiagnosa
                                    ); // Ini harus dipanggil jika Anda ingin modal diagnosa muncul
                                // segera setelah skrining berhasil disimpan.

                                // Jika Anda menggunakan event delegation untuk tombol-tombol di tabel,
                                // Anda tidak perlu memanggil fungsi terpisah untuk melampirkan event listener
                                // setelah menambahkan baris baru.
                                // Contoh event delegation: $(document).on('click', '.btn-edit', function() { ... });
                                // Jika Anda tidak menggunakan event delegation, Anda mungkin perlu
                                // memanggil fungsi seperti `attachTableEventListeners()` di sini.

                            } else {
                                console.error("Objek skrining tidak ditemukan dalam respons.");
                                Swal.fire('Error!',
                                    'Terjadi kesalahan: Data skrining tidak lengkap dari server.',
                                    'error');
                            }

                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                        $('#btnSimpanSkriningTambah').prop('disabled', false).text('Simpan');
                    },
                    error: function(xhr, status, error) {
                        console.error("Error submitting form:", error);
                        // Tambahkan detail error dari respons jika ada
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data skrining.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', errorMessage, 'error');
                        $('#btnSimpanSkriningTambah').prop('disabled', false).text('Simpan');
                    }
                });
            });

            // Event untuk tombol edit
            $(document).on('click', '.btn-edit', function() {
                var skriningId = $(this).data('id');
                var editUrl = "{{ url('skrining/show') }}/" + skriningId;
                var updateUrl = "{{ url('skrining/update') }}/" + skriningId;

                $('#editForm').attr('action', updateUrl);
                $('#edit_pertanyaan_list').html(
                    '<p><i class="fas fa-spinner fa-spin"></i> Memuat data...</p>');

                $.ajax({
                    url: editUrl,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var skrining = response.data;
                            var formSkrinings = response.formSkrinings;

                            if (skrining.Tanggal_Skrining) {
                                let rawDateString = skrining.Tanggal_Skrining;
                                let dateOnly = '';
                                try {
                                    if (rawDateString.includes('T')) {
                                        dateOnly = rawDateString.split('T')[0];
                                    } else {
                                        dateOnly = rawDateString.split(' ')[0];
                                    }

                                    const parts = dateOnly.split('-');
                                    if (parts.length === 3) {
                                        $('#edit_Tanggal_Skrining').val(
                                            `${parts[2]}-${parts[1]}-${parts[0]}`);
                                    } else {
                                        $('#edit_Tanggal_Skrining').val(rawDateString);
                                    }
                                } catch (e) {
                                    console.error("Error parsing Tanggal_Skrining:", e);
                                    $('#edit_Tanggal_Skrining').val(rawDateString);
                                }
                            } else {
                                $('#edit_Tanggal_Skrining').val('');
                            }

                            $('#edit_Nama_Petugas').val(skrining.Nama_Petugas);
                            $('#edit_NIK_Pasien').val(skrining.pasien ? skrining.pasien.NIK :
                                '-');
                            updateNikLengthInfo('#edit_NIK_Pasien', '#nik_length_info_edit');
                            $('#edit_Nama_Pasien').val(skrining.Nama_Pasien);

                            var formSkriningSelect = $('#edit_id_form_skrining');
                            formSkriningSelect.empty();
                            formSkriningSelect.append(
                                '<option value="">-- Pilih Formulir Skrining --</option>');
                            if (formSkrinings && formSkrinings.length > 0) {
                                $.each(formSkrinings, function(index, form) {
                                    formSkriningSelect.append(new Option(form
                                        .nama_skrining, form.id));
                                });
                            } else {
                                formSkriningSelect.append(
                                    '<option value="">Tidak ada formulir skrining tersedia</option>'
                                );
                            }
                            if (skrining.id_form_skrining) {
                                formSkriningSelect.val(skrining.id_form_skrining);
                            }

                            if (skrining.form_skrining && skrining.form_skrining.id) {
                                loadPertanyaanEdit(skrining.form_skrining.id, skrining
                                    .jawabans);
                            } else {
                                console.warn(
                                    "Form Skrining ID tidak tersedia dari data skrining.");
                                $('#edit_pertanyaan_list').html(
                                    '<p class="text-danger">Formulir Skrining tidak terhubung dengan data skrining ini.</p>'
                                );
                            }

                            $('#editModal').modal('show');
                        } else {
                            Swal.fire('Error', response.message ||
                                'Gagal memuat data skrining.', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error',
                            'Terjadi kesalahan saat mengambil data untuk edit: ' + (xhr
                                .responseJSON ? xhr.responseJSON.message : xhr.statusText),
                            'error');
                        console.error("Error fetching edit data:", xhr.responseText);
                    }
                });
            });

            // Event untuk id_form_skrining di Modal Edit
            $('#edit_id_form_skrining').on('change', function() {
                var selectedFormSkriningId = $(this).val();
                loadPertanyaanEdit(selectedFormSkriningId, []);
            });

            // Event untuk submit form Edit Skrining
            $('#editForm').submit(function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var formData = new FormData(form[0]);

                $('#editModal button[type="submit"]').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Menyimpan Perubahan...');

                var tanggalInput = $('#edit_Tanggal_Skrining').val();
                var tanggalSkriningFormatted = null;

                if (tanggalInput) {
                    const parts = tanggalInput.split('-');
                    if (parts.length === 3) {
                        tanggalSkriningFormatted = `${parts[2]}-${parts[1]}-${parts[0]}`;
                    } else {
                        tanggalSkriningFormatted = tanggalInput;
                    }
                }

                if (tanggalSkriningFormatted) {
                    formData.set('Tanggal_Skrining', tanggalSkriningFormatted);
                } else {
                    formData.set('Tanggal_Skrining', '');
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#editModal').modal('hide');

                        if (response.success) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            lastDisplayedSkriningId = response.skrining_id;
                            displayDiagnosaInNewModal(response.diagnosa);
                            loadSkriningTable();
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                        $('#editModal button[type="submit"]').prop('disabled', false).text(
                            'Simpan Perubahan');
                    },
                    error: function(xhr, status, error) {
                        console.error("Error submitting edit form:", error);
                        Swal.fire('Error!',
                            'Terjadi kesalahan saat menyimpan perubahan data skrining.',
                            'error');
                        $('#editModal button[type="submit"]').prop('disabled', false).text(
                            'Simpan Perubahan');
                    }
                });
            });

            // Event untuk tombol delete
            $('.btn-delete').click(function() {
                var skriningId = $(this).data('skrining-id');
                var skriningNama = $(this).data('skrining-nama');
                var deleteUrl = "{{ url('skrining') }}/" + skriningId;

                $('#skriningToDelete').text(skriningNama);
                $('#deleteForm').attr('action', deleteUrl);
                $('#deleteConfirmationModal').modal('show');
            });

            // Event untuk form delete
            $('#deleteForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const actionUrl = form.attr('action');

                Swal.fire({
                    title: 'Menghapus Data...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                $('#deleteConfirmationModal').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message ||
                                    'Terjadi kesalahan saat menghapus data.',
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        let errorMessage = 'Terjadi kesalahan pada server.';
                        if (xhr.responseJSON) {
                            errorMessage = xhr.responseJSON.message || errorMessage;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage
                        });
                        console.error("AJAX Error (Delete):", xhr.responseText);
                    }
                });
            });

            // Event untuk tombol "Lihat Diagnosa"
            $(document).on('click', '.btn-view-diagnosa', function() {
                const skriningId = $(this).data('id');
                lastDisplayedSkriningId = skriningId;

                $('#hasilDiagnosaDisplayModal.modal-body').html(
                    '<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat hasil diagnosa...</p>'
                );
                $('#hasilDiagnosaDisplayModal').modal('show');

                $.ajax({
                    url: "{{ url('skrining/diagnosa') }}/" + skriningId,
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.diagnosa) {
                            displayDiagnosaInNewModal(response.diagnosa);
                        } else {
                            Swal.fire('Error', response.message || 'Gagal memuat diagnosa.',
                                'error');
                            $('#hasilDiagnosaDisplayModal .modal-body').html(
                                '<p class="text-danger text-center">Gagal memuat data diagnosa.</p>'
                            );
                        }
                    },
                    error: function(xhr) {
                        console.error("Error fetching diagnosa data:", xhr.responseText);
                        Swal.fire('Error', 'Terjadi kesalahan saat memuat hasil diagnosa.',
                            'error');
                        $('#hasilDiagnosaDisplayModal .modal-body').html(
                            '<p class="text-danger text-center">Terjadi kesalahan saat memuat hasil diagnosa.</p>'
                        );
                    }
                });
            });

            // Event untuk tombol "Skrining Selesai"
            $('#btnSkriningSelesai').on('click', function() {
                $('#hasilDiagnosaDisplayModal').modal('hide');
                $('#cetakHasilUtama').hide();
                lastDisplayedSkriningId = null;
            });

            // Event untuk tombol "Tutup Diagnosa"
            $('#btnTutupDiagnosa').on('click', function() {
                $('#hasilDiagnosaDisplayModal').modal('hide');
                $('#cetakHasilUtama').hide();
                lastDisplayedSkriningId = null;
            });

            // Event untuk pencarian
            $('#searchSkrining').on('keyup', function() {
                var searchText = $(this).val().toLowerCase();
                $('table.table-custom tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                });
            });

            // Inisialisasi Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            } else {
                console.warn("Feather Icons library not loaded.");
            }
        });
    </script>
@endsection
