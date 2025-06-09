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
                                                    <td>{{ $skrining->Tanggal_Skrining->format('d-m-Y') }}</td>
                                                    <td>{{ $skrining->formSkrining->nama_skrining ?? '-' }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary btn-edit"
                                                            data-id="{{ $skrining->id }}">
                                                            Edit
                                                        </button>
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
            </section>
        </div>

        {{-- MODAL TAMBAH FORM SKRINING BARU --}}
        <div class="modal fade" id="tambahSkriningModal" tabindex="-1" role="dialog"
            aria-labelledby="tambahSkriningModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
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
                                <label for="NIK_Pasien">NIK Pasien</label>
                                <input type="text" name="NIK_Pasien" id="NIK_Pasien" class="form-control"
                                    placeholder="Masukkan NIK Pasien" required>
                                <small id="nik_length_info_tambah" class="form-text text-muted"></small>
                            </div>
                            <div class="form-group">
                                <label for="Nama_Pasien">Nama Pasien</label>
                                <input type="text" name="Nama_Pasien" id="Nama_Pasien" class="form-control"
                                    placeholder="Nama Pasien akan muncul otomatis" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="Tanggal_Skrining">Tanggal Skrining</label>
                                {{-- PASTIKAN TYPE="TEXT" untuk menggunakan jQuery UI Datepicker --}}
                                <input type="text" name="Tanggal_Skrining" id="tanggalSkriningTambah" class="form-control" placeholder="dd-mm-yyyy" required>
                            </div>
                            <div class="form-group">
                                <label for="id_form_skrining">Formulir Skrining</label>
                                <select class="form-control" id="id_form_skrining" name="id_form_skrining" required>
                                    <option value="">-- Pilih Formulir --</option>
                                    @isset($formSkrinings)
                                        @foreach($formSkrinings as $formSkrining)
                                            <option value="{{ $formSkrining->id }}">{{ $formSkrining->nama_skrining }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Pertanyaan</label>
                                <div id="pertanyaan_list_tambah">
                                    <p>Pilih formulir skrining untuk memuat pertanyaan.</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL EDIT FORM SKRINING --}}
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Form Skrining</h5>
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
                                <label for="edit_NIK_Pasien">NIK Pasien</label>
                                <input type="text" class="form-control" id="edit_NIK_Pasien" name="NIK_Pasien"
                                    placeholder="Masukkan NIK Pasien" required>
                                <small id="nik_length_info_edit" class="form-text text-muted"></small>
                            </div>
                            <div class="form-group">
                                <label for="edit_Nama_Pasien">Nama Pasien</label>
                                <input type="text" class="form-control" id="edit_Nama_Pasien" name="Nama_Pasien"
                                    placeholder="Masukkan Nama Pasien" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit_Tanggal_Skrining">Tanggal Skrining</label>
                                {{-- PASTIKAN TYPE="TEXT" untuk menggunakan jQuery UI Datepicker --}}
                                <input type="text" class="form-control" id="edit_Tanggal_Skrining" name="Tanggal_Skrining" placeholder="dd-mm-yyyy" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_id_form_skrining">Formulir Skrining</label>
                                <select class="form-control" id="edit_id_form_skrining" name="id_form_skrining_edit" required>
                                    <option value="">-- Pilih Formulir --</option>
                                    @foreach ($formSkrinings as $formSkrining)
                                        <option value="{{ $formSkrining->id }}">{{ $formSkrining->nama_skrining }}</option>
                                    @endforeach
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
                        Apakah Anda yakin ingin menghapus form skrining atas nama: <span id="skriningToDelete"></span>?
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
    @endsection

    @section('scripts')
<script>
    $(document).ready(function() {
        const NIK_LENGTH = 16; // Konstanta untuk panjang NIK

        // Fungsi untuk mengupdate info panjang NIK
        function updateNikLengthInfo(inputId, infoId) {
            const currentLength = $(inputId).val().length;
            const infoElement = $(infoId);
            infoElement.text(`${currentLength}/${NIK_LENGTH} digit`);

            if (currentLength === NIK_LENGTH) {
                infoElement.removeClass('text-muted text-danger').addClass('text-success');
            } else if (currentLength > NIK_LENGTH) {
                infoElement.removeClass('text-muted text-success').addClass('text-danger');
            } else {
                infoElement.removeClass('text-success text-danger').addClass('text-muted');
            }
        }

        // Panggil fungsi update saat input NIK berubah di modal Tambah
        $('#NIK_Pasien').on('input', function() {
            updateNikLengthInfo('#NIK_Pasien', '#nik_length_info_tambah');
        });

        // Panggil fungsi update saat input NIK berubah di modal Edit
        $('#edit_NIK_Pasien').on('input', function() {
            updateNikLengthInfo('#edit_NIK_Pasien', '#nik_length_info_edit');
        });

        // Inisialisasi info NIK saat modal Tambah dibuka (jika ada nilai default atau untuk memastikan tampil)
        $('#tambahSkriningModal').on('shown.bs.modal', function () {
            updateNikLengthInfo('#NIK_Pasien', '#nik_length_info_tambah');
        });

        // Inisialisasi info NIK saat modal Edit dibuka
        $('#editModal').on('shown.bs.modal', function () {
            // Beri sedikit delay untuk memastikan NIK sudah terisi dari AJAX call
            // Ini mungkin tidak lagi diperlukan jika loadPertanyaanEdit() dipanggil setelah semua terisi di success callback
            // Namun, tetap jaga untuk kehati-hatian atau jika ada proses asinkron lain.
            setTimeout(function() {
                updateNikLengthInfo('#edit_NIK_Pasien', '#nik_length_info_edit');
            }, 100);
        });

        // Inisialisasi Datepicker untuk modal Tambah
        $('#tanggalSkriningTambah').datepicker({
            dateFormat: 'dd-mm-yy',
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
                loadPertanyaanTambah();
            }
        });

        // Inisialisasi Datepicker untuk modal Edit
        $('#edit_Tanggal_Skrining').datepicker({
            dateFormat: 'dd-mm-yy',
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
                loadPertanyaanEdit();
            }
        });

        // Fungsi untuk memuat pertanyaan di modal Tambah
        function loadPertanyaanTambah() {
            var formSkriningId = $('#id_form_skrining').val();
            var nikPasien = $('#NIK_Pasien').val();
            // Format tanggal dari datepicker (dd-mm-yy) menjadi yyyy-mm-dd untuk backend
            var tanggalSkriningFormatted = null;
            var tanggalSkriningVal = $('#tanggalSkriningTambah').val();
            if (tanggalSkriningVal) {
                var parts = tanggalSkriningVal.split('-');
                if (parts.length === 3) {
                    tanggalSkriningFormatted = `${parts[2]}-${parts[1]}-${parts[0]}`;
                }
            }

            var pertanyaanContainer = $('#pertanyaan_list_tambah');
            pertanyaanContainer.empty();
            pertanyaanContainer.html('<p>Memuat pertanyaan...</p>');

            // Pastikan semua input yang dibutuhkan ada sebelum memuat pertanyaan
            if (formSkriningId && nikPasien && nikPasien.length === NIK_LENGTH && tanggalSkriningFormatted) {
                $.ajax({
                    url: "{{ route('skrining.get-pertanyaan-by-form-skrining', ':id') }}".replace(':id', formSkriningId),
                    method: 'GET',
                    data: {
                        nik_pasien: nikPasien,
                        tanggal_skrining: tanggalSkriningFormatted
                    },
                    success: function(response) {
                        pertanyaanContainer.empty();

                        if (response && response.length > 0) {
                            $.each(response, function(index, pertanyaan) {
                                var catatanHtml = '';
                                if (pertanyaan.catatan) {
                                    catatanHtml = `<small class="text-muted d-block mt-1 mb-2">Catatan: ${pertanyaan.catatan}</small>`;
                                }
                                // Mengisi value input dengan previous_answer jika ada
                                var inputValue = pertanyaan.previous_answer !== null ? pertanyaan.previous_answer : '';
                                var inputHtml = `<input type="text" class="form-control" name="jawaban[${pertanyaan.id}]" placeholder="Masukkan jawaban..." value="${inputValue}" required>`;
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
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching pertanyaan by form skrining:", error);
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memuat pertanyaan untuk formulir skrining.',
                        });
                        pertanyaanContainer.html('<p class="text-danger">Gagal memuat pertanyaan. Terjadi kesalahan.</p>');
                    }
                });
            } else {
                pertanyaanContainer.html('<p>Pilih Formulir Skrining, masukkan NIK Pasien (16 digit), dan Tanggal Skrining untuk memuat pertanyaan.</p>');
            }
        }

        // Fungsi untuk memuat pertanyaan di modal Edit
        function loadPertanyaanEdit() {
            var formSkriningId = $('#edit_id_form_skrining').val();
            var nikPasien = $('#edit_NIK_Pasien').val();
            // Format tanggal dari datepicker (dd-mm-yy) menjadi yyyy-mm-dd untuk backend
            var tanggalSkriningFormatted = null;
            var tanggalSkriningVal = $('#edit_Tanggal_Skrining').val();
            if (tanggalSkriningVal) {
                var parts = tanggalSkriningVal.split('-');
                if (parts.length === 3) {
                    tanggalSkriningFormatted = `${parts[2]}-${parts[1]}-${parts[0]}`;
                }
            }

            var pertanyaanContainer = $('#edit_pertanyaan_list');
            pertanyaanContainer.empty();
            pertanyaanContainer.html('<p>Memuat pertanyaan...</p>');

            // Pastikan semua input yang dibutuhkan ada sebelum memuat pertanyaan
            if (formSkriningId && nikPasien && nikPasien.length === NIK_LENGTH && tanggalSkriningFormatted) {
                $.ajax({
                    url: "{{ route('skrining.get-pertanyaan-by-form-skrining', ':id') }}".replace(':id', formSkriningId),
                    method: 'GET',
                    data: {
                        nik_pasien: nikPasien,
                        tanggal_skrining: tanggalSkriningFormatted
                    },
                    success: function(response) {
                        pertanyaanContainer.empty();

                        if (response && response.length > 0) {
                            $.each(response, function(index, pertanyaan) {
                                var catatanHtml = '';
                                if (pertanyaan.catatan) {
                                    catatanHtml = `<small class="text-muted d-block mt-1 mb-2">Catatan: ${pertanyaan.catatan}</small>`;
                                }
                                // Mengisi value input dengan previous_answer jika ada
                                var inputValue = pertanyaan.previous_answer !== null ? pertanyaan.previous_answer : '';
                                var inputHtml = `<input type="text" class="form-control" name="jawaban[${pertanyaan.id}]" placeholder="Masukkan jawaban..." value="${inputValue}" required>`;
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
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching pertanyaan by form skrining for edit:", error);
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memuat pertanyaan untuk formulir skrining di modal edit.',
                        });
                        pertanyaanContainer.html('<p class="text-danger">Gagal memuat pertanyaan. Terjadi kesalahan.</p>');
                    }
                });
            } else {
                pertanyaanContainer.html('<p>Pilih Formulir Skrining, masukkan NIK Pasien (16 digit), dan Tanggal Skrining untuk memuat pertanyaan.</p>');
            }
        }


        // Event untuk tombol "Tambah Baru"
        $('#tambahSkriningBaru').click(function() {
            $('#tambahSkriningForm')[0].reset();
            $('#pertanyaan_list_tambah').html('<p>Pilih formulir skrining untuk memuat pertanyaan.</p>');
            $('#tanggalSkriningTambah').datepicker('setDate', new Date());
            updateNikLengthInfo('#NIK_Pasien', '#nik_length_info_tambah'); // Inisialisasi info NIK saat modal dibuka
            $('#tambahSkriningModal').modal('show');
        });

        // Event untuk NIK Pasien di Modal Tambah
        $('#NIK_Pasien').on('change', function() {
            var nik = $(this).val();
            $('#Nama_Pasien').val(''); // Kosongkan nama pasien setiap kali NIK berubah

            if (nik && nik.length === NIK_LENGTH) { // Hanya panggil AJAX jika NIK 16 digit
                $.ajax({
                    url: "{{ route('pasien.getPasienData') }}",
                    method: 'GET',
                    data: { NIK: nik },
                    success: function(response) {
                        if (response.success && response.data) { // Periksa response.data
                            $('#Nama_Pasien').val(response.data.Nama_Pasien);
                            loadPertanyaanTambah(); // Panggil ini setelah nama pasien terisi
                        } else {
                            Swal.fire({
                                icon: 'warning', // Ubah ke warning karena NIK tidak ditemukan, bukan error umum
                                title: 'Informasi!',
                                text: response.message || 'Data pasien tidak ditemukan untuk NIK ini.',
                            });
                            $('#Nama_Pasien').val(''); // Pastikan nama pasien kosong jika tidak ditemukan
                            loadPertanyaanTambah(); // Tetap panggil ini untuk memperbarui pesan pertanyaan
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching pasien data:", error);
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mencari data pasien. Silakan cek konsol browser.',
                        });
                        $('#Nama_Pasien').val(''); // Pastikan nama pasien kosong jika ada error
                        loadPertanyaanTambah();
                    }
                });
            } else {
                $('#Nama_Pasien').val(''); // Kosongkan jika NIK tidak valid
                $('#pertanyaan_list_tambah').empty().append('<p>Pilih Formulir Skrining, masukkan NIK Pasien (16 digit), dan Tanggal Skrining untuk memuat pertanyaan.</p>');
            }
        });

        // Event untuk id_form_skrining di Modal Tambah
        $('#id_form_skrining').on('change', function() {
            loadPertanyaanTambah();
        });

        // Event untuk submit form Tambah Skrining Baru
        $('#tambahSkriningForm').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                url: url,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#tambahSkriningModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        // Jika ada validasi error dari backend, tampilkan di sini
                        var errorMessage = response.message || 'Terjadi kesalahan saat menyimpan data.';
                        if (response.errors) {
                            errorMessage = Object.values(response.errors).join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: errorMessage, // Gunakan html agar <br> bekerja
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error saving skrining:", error);
                    console.log(xhr.responseText);
                    var errorMessage = 'Terjadi kesalahan saat menyimpan data. Silakan cek konsol browser.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join('<br>');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: errorMessage,
                    });
                }
            });
        });

        // Event untuk tombol "Edit"
        $('.btn-edit').on('click', function() {
            var skriningId = $(this).data('id');

            $.ajax({
                url: "{{ route('skrining.show', ':id') }}".replace(':id', skriningId),
                method: 'GET',
                success: function(response) {
                    console.log('Response for Edit Modal:', response);
                    $('#editForm').attr('action', "{{ route('skrining.update', ':id') }}".replace(':id', skriningId));

                    // Pastikan semua field diisi sesuai response
                    $('#edit_Nama_Petugas').val(response.Nama_Petugas);
                    $('#edit_NIK_Pasien').val(response.NIK_Pasien);
                    $('#edit_Nama_Pasien').val(response.Nama_Pasien);

                    if (response.Tanggal_Skrining) {
                        var dateObj = $.datepicker.parseDate('yy-mm-dd', response.Tanggal_Skrining);
                        $('#edit_Tanggal_Skrining').datepicker('setDate', dateObj);
                    } else {
                        $('#edit_Tanggal_Skrining').val('');
                        $('#edit_Tanggal_Skrining').datepicker('setDate', null);
                    }

                    // *** PENTING: SETELAH MENGISI SEMUA DATA DI ATAS, BARU SET VALUE UNTUK SELECT FORM SKRINING ***
                    // Ini penting agar `loadPertanyaanEdit()` yang dipanggil setelahnya memiliki nilai yang benar
                    // jika ada event 'change' pada elemen ini.
                    $('#edit_id_form_skrining').val(response.id_form_skrining);

                    // Panggil fungsi loadPertanyaanEdit setelah data dasar terisi
                    // Ini akan memuat pertanyaan dan mengisi dengan jawaban yang ada
                    loadPertanyaanEdit();

                    // Perbarui info NIK setelah NIK terisi di modal edit
                    updateNikLengthInfo('#edit_NIK_Pasien', '#nik_length_info_edit');

                    $('#editModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching skrining detail for edit:", error);
                    console.log(xhr.responseText);
                    var errorMessage = 'Terjadi kesalahan saat memuat data form skrining.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage + ' Silakan cek konsol browser.',
                    });
                }
            });
        });

        // Event change untuk dropdown formulir skrining di modal edit
        $('#edit_id_form_skrining').on('change', function() {
            loadPertanyaanEdit();
        });

        // Event change untuk NIK Pasien di modal edit
        $('#edit_NIK_Pasien').on('change', function() {
            // Ketika NIK pasien diubah di modal edit, lakukan pencarian nama pasien
            var nik = $(this).val();
            $('#edit_Nama_Pasien').val(''); // Kosongkan nama pasien setiap kali NIK berubah

            if (nik && nik.length === NIK_LENGTH) {
                $.ajax({
                    url: "{{ route('pasien.getPasienData') }}",
                    method: 'GET',
                    data: { NIK: nik },
                    success: function(response) {
                        if (response.success && response.data) {
                            $('#edit_Nama_Pasien').val(response.data.Nama_Pasien);
                            loadPertanyaanEdit(); // Panggil ini setelah nama pasien terisi
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Informasi!',
                                text: response.message || 'Data pasien tidak ditemukan untuk NIK ini.',
                            });
                            $('#edit_Nama_Pasien').val('');
                            loadPertanyaanEdit();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching pasien data for edit:", error);
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat mencari data pasien untuk edit. Silakan cek konsol browser.',
                        });
                        $('#edit_Nama_Pasien').val('');
                        loadPertanyaanEdit();
                    }
                });
            } else {
                $('#edit_Nama_Pasien').val('');
                $('#edit_pertanyaan_list').empty().append('<p>Pilih Formulir Skrining, masukkan NIK Pasien (16 digit), dan Tanggal Skrining untuk memuat pertanyaan.</p>');
            }
        });

        $('#editModal').on('hidden.bs.modal', function() {
            $('#editForm')[0].reset();
            $('#edit_pertanyaan_list').empty();
            $('#edit_pertanyaan_list').append('<p>Memuat pertanyaan...</p>');
            // Reset info NIK saat modal ditutup
            $('#nik_length_info_edit').empty();
        });

        $('#tambahSkriningModal').on('hidden.bs.modal', function() {
            // Reset info NIK saat modal ditutup
            $('#nik_length_info_tambah').empty();
        });


        // Event untuk tombol "Delete"
        $('.btn-delete').on('click', function() {
            var skriningId = $(this).data('skrining-id');
            var skriningNama = $(this).data('skrining-nama');
            var deleteUrl = "{{ route('skrining.delete', ':id') }}".replace(':id', skriningId);

            $('#skriningToDelete').text(skriningNama);
            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteConfirmationModal').modal('show');
        });

        $('#deleteConfirmationModal').on('hidden.bs.modal', function() {
            $('#deleteForm').attr('action', '');
        });

        $('#searchSkrining').on('input', function() {
            var searchValue = $(this).val();
            var url = "{{ route('skrining.index') }}";

            // Pastikan parameter search tidak digandakan jika ada paginasi atau parameter lain
            url += (url.indexOf('?') > -1 ? '&' : '?') + 'search=' + encodeURIComponent(searchValue);

            // Menggunakan AJAX untuk memuat ulang hanya bagian tabel, bukan seluruh halaman
            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    // Hanya mengambil bagian tbody dari response dan mengganti tbody yang ada
                    // Anda mungkin perlu menyesuaikan selector ini jika struktur HTML Anda berbeda
                    $('tbody').html($(data).find('tbody').html());
                    // Jika Anda menggunakan paginasi AJAX, Anda juga perlu memperbarui bagian paginasi
                    // Misalnya: $('.pagination-container').html($(data).find('.pagination-container').html());
                },
                error: function(xhr, status, error) {
                    console.error("Error searching skrining:", error);
                    console.log(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mencari data skrining.',
                    });
                }
            });
        });

        // Edit Skrining Submit
        $('#editForm').on('submit', function(e) {
            e.preventDefault();

            var form = $(this);
            var url = form.attr('action');

            $.ajax({
                url: url,
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#editModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        var errorMessage = response.message || 'Terjadi kesalahan saat menyimpan perubahan.';
                        if (response.errors) {
                            errorMessage = Object.values(response.errors).join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: errorMessage,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error updating skrining:", error);
                    console.log(xhr.responseText);
                    var errorMessage = 'Terjadi kesalahan saat menyimpan perubahan. Silakan cek konsol browser.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join('<br>');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        html: errorMessage,
                    });
                }
            });
        });

        // Submit form hapus
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');

        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#deleteConfirmationModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Terjadi kesalahan saat menghapus data.'
                    });
                }
            },
            error: function(xhr) {
                let msg = 'Terjadi kesalahan.';
                if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: msg
                });
            }
        });
    });
    });
</script>
@endsection