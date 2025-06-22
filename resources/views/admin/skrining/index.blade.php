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
                                                    <td>{{ \Carbon\Carbon::parse($skrining->Tanggal_Skrining)->format('d-m-Y') }}</td>
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
                                {{-- Jika Anda menggunakan pagination --}}
                                {{-- {{ $skrinings->links('pagination::bootstrap-4') }} --}}
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
                            <input type="text" name="Tanggal_Skrining" id="tanggalSkriningTambah" class="form-control" placeholder="dd-mm-yyyy" required>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
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
                            <input type="text" class="form-control" id="edit_Tanggal_Skrining" name="Tanggal_Skrining" placeholder="dd-mm-yyyy" required>
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
                            <select class="form-control" id="edit_id_form_skrining" name="id_form_skrining_edit" required>
                                {{-- Opsi form skrining akan dimuat oleh JavaScript --}}
                                @isset($formSkrinings)
                                    @foreach($formSkrinings as $formSkrining)
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
@endsection

@section('scripts')
    {{-- Memuat jQuery UI (untuk Datepicker) --}}
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    {{-- Memuat Font Awesome (untuk ikon spinner) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    {{-- Memuat SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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

            // Inisialisasi Datepicker untuk modal Tambah
            $('#tanggalSkriningTambah').datepicker({
                dateFormat: 'dd-mm-yy', // Format yang ditampilkan ke user
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
                dateFormat: 'dd-mm-yy', // Format yang ditampilkan ke user
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
                    if ($('#edit_id_form_skrining').val() && $('#edit_NIK_Pasien').val().length === NIK_LENGTH) {
                        loadPertanyaanEdit();
                    }
                }
            });

            // Fungsi untuk memuat pertanyaan di modal Tambah
            function loadPertanyaanTambah() {
                var formSkriningId = $('#id_form_skrining').val();
                var nikPasien = $('#NIK_Pasien').val();
                var tanggalSkriningVal = $('#tanggalSkriningTambah').val(); // Format dd-mm-yyyy dari datepicker

                var pertanyaanContainer = $('#pertanyaan_list_tambah');
                pertanyaanContainer.empty();
                pertanyaanContainer.html('<p><i class="fas fa-spinner fa-spin"></i> Memuat pertanyaan...</p>');
                $('#pertanyaan_container_tambah').hide();

                if (!formSkriningId || !nikPasien || nikPasien.length !== NIK_LENGTH || !tanggalSkriningVal) {
                    pertanyaanContainer.html('<p>Pilih Formulir Skrining, masukkan NIK Pasien (16 digit), dan Tanggal Skrining untuk memuat pertanyaan.</p>');
                    return;
                }

                var parts = tanggalSkriningVal.split('-');
                var tanggalSkriningFormatted = `${parts[2]}-${parts[1]}-${parts[0]}`;


                $.ajax({
                    url: "{{ url('skrining/pertanyaan') }}/" + formSkriningId,
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
                        $('#pertanyaan_container_tambah').show();
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching pertanyaan by form skrining:", error);
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memuat pertanyaan untuk formulir skrining. Pastikan NIK dan Tanggal Skrining sudah benar. (Kode HTTP: ' + xhr.status + ')',
                        });
                        pertanyaanContainer.html('<p class="text-danger">Gagal memuat pertanyaan. Terjadi kesalahan.</p>');
                        $('#pertanyaan_container_tambah').show();
                    }
                });
            }

            // Fungsi untuk memuat pertanyaan di modal Edit
            function loadPertanyaanEdit() {
                var formSkriningId = $('#edit_id_form_skrining').val();
                var nikPasien = $('#edit_NIK_Pasien').val();
                var tanggalSkriningVal = $('#edit_Tanggal_Skrining').val(); // Format dd-mm-yyyy dari datepicker

                var pertanyaanContainer = $('#edit_pertanyaan_list');
                pertanyaanContainer.empty();
                pertanyaanContainer.html('<p><i class="fas fa-spinner fa-spin"></i> Memuat pertanyaan...</p>');

                if (!formSkriningId || !nikPasien || nikPasien.length !== NIK_LENGTH || !tanggalSkriningVal) {
                    pertanyaanContainer.html('<p>Pilih Formulir Skrining, masukkan NIK Pasien (16 digit), dan Tanggal Skrining untuk memuat pertanyaan.</p>');
                    return;
                }

                var parts = tanggalSkriningVal.split('-');
                var tanggalSkriningFormatted = `${parts[2]}-${parts[1]}-${parts[0]}`;


                $.ajax({
                    url: "{{ url('skrining/pertanyaan') }}/" + formSkriningId,
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
                            text: 'Terjadi kesalahan saat memuat pertanyaan untuk formulir skrining di modal edit. Pastikan NIK dan Tanggal Skrining sudah benar. (Kode HTTP: ' + xhr.status + ')',
                        });
                        pertanyaanContainer.html('<p class="text-danger">Gagal memuat pertanyaan. Terjadi kesalahan.</p>');
                    }
                });
            }

            // Event untuk tombol "Tambah Baru"
            $('#tambahSkriningBaru').click(function() {
                $('#tambahSkriningForm')[0].reset();
                $('#NIK_Pasien').val('');
                $('#Nama_Pasien').val('');
                $('#tanggalSkriningTambah').datepicker('setDate', new Date());
                updateNikLengthInfo('#NIK_Pasien', '#nik_length_info_tambah');
                $('#id_form_skrining').html('<option value="">-- Masukkan NIK Pasien untuk Rekomendasi --</option>');
                $('#form_skrining_recommendation_info').text('Form skrining akan direkomendasikan setelah NIK pasien dimasukkan.');
                $('#pertanyaan_list_tambah').html('<p>Pilih formulir skrining untuk memuat pertanyaan.</p>');
                $('#pertanyaan_container_tambah').hide();
                $('#tambahSkriningModal').modal('show');
            });

            // Event untuk NIK Pasien di Modal Tambah
            $('#NIK_Pasien').on('input', function() {
                updateNikLengthInfo('#NIK_Pasien', '#nik_length_info_tambah');
                var nik = $(this).val();
                $('#Nama_Pasien').val('');
                $('#id_form_skrining').html('<option value="">-- Memuat Rekomendasi... --</option>');
                $('#form_skrining_recommendation_info').html('<i class="fas fa-spinner fa-spin"></i> Mencari rekomendasi...');
                $('#pertanyaan_container_tambah').hide();

                if (nik && nik.length === NIK_LENGTH) {
                    $.ajax({
                        url: "{{ route('pasien.getPasienData') }}",
                        method: 'GET',
                        data: { NIK: nik },
                        success: function(responsePasien) {
                            if (responsePasien.success && responsePasien.data) {
                                var pasien = responsePasien.data;
                                $('#Nama_Pasien').val(pasien.Nama_Pasien);

                                $.ajax({
                                    url: "{{ route('skrining.getRecommendedFormSkrinings') }}",
                                    method: 'GET',
                                    data: { nik_pasien: nik },
                                    success: function(responseForms) {
                                        if (responseForms.success) {
                                            var recommendedForms = responseForms.recommendedForms;
                                            $('#id_form_skrining').html('<option value="">-- Pilih Formulir --</option>');
                                            if (recommendedForms.length > 0) {
                                                $('#form_skrining_recommendation_info').html('<span class="text-success">Rekomendasi Formulir Skrining:</span>');
                                                $.each(recommendedForms, function(index, form) {
                                                    $('#id_form_skrining').append('<option value="' + form.id + '">' + form.nama_skrining + '</option>');
                                                });
                                                if (recommendedForms.length === 1 && $('#id_form_skrining').val() === "") {
                                                    $('#id_form_skrining').val(recommendedForms[0].id).trigger('change');
                                                }
                                            } else {
                                                $('#form_skrining_recommendation_info').html('<span class="text-warning">Tidak ada rekomendasi formulir untuk kategori pasien ini.</span>');
                                                @isset($formSkrinings)
                                                    $.each(@json($formSkrinings), function(index, form) {
                                                        $('#id_form_skrining').append('<option value="' + form.id + '">' + form.nama_skrining + '</option>');
                                                    });
                                                @endisset
                                            }
                                            if ($('#id_form_skrining').val()) {
                                                loadPertanyaanTambah();
                                            }
                                        } else {
                                            $('#form_skrining_recommendation_info').html('<span class="text-danger">' + (responseForms.message || 'Error mencari rekomendasi formulir.') + '</span>');
                                            $('#id_form_skrining').html('<option value="">-- Gagal memuat --</option>');
                                        }
                                    },
                                    error: function(xhrForms) {
                                        $('#form_skrining_recommendation_info').html('<span class="text-danger">Error memuat rekomendasi: ' + (xhrForms.responseJSON ? xhrForms.responseJSON.message : 'Koneksi gagal.') + '</span>');
                                        $('#id_form_skrining').html('<option value="">-- Gagal memuat --</option>');
                                        console.error("Error fetching recommended forms:", xhrForms.responseText);
                                    }
                                });

                            } else {
                                $('#Nama_Pasien').val('');
                                $('#form_skrining_recommendation_info').html('<span class="text-danger">NIK tidak ditemukan.</span>');
                                $('#id_form_skrining').html('<option value="">-- Masukkan NIK Pasien untuk Rekomendasi --</option>');
                                $('#pertanyaan_list_tambah').html('<p>Pilih formulir skrining untuk memuat pertanyaan.</p>');
                                $('#pertanyaan_container_tambah').hide();
                            }
                        },
                        error: function(xhrPasien) {
                            $('#Nama_Pasien').val('');
                            $('#form_skrining_recommendation_info').html('<span class="text-danger">Error mencari data pasien: ' + (xhrPasien.responseJSON ? xhrPasien.responseJSON.message : 'Koneksi gagal.') + '</span>');
                            $('#id_form_skrining').html('<option value="">-- Gagal memuat --</option>');
                            $('#pertanyaan_list_tambah').html('<p>Pilih formulir skrining untuk memuat pertanyaan.</p>');
                            $('#pertanyaan_container_tambah').hide();
                            console.error("Error fetching pasien data:", xhrPasien.responseText);
                        }
                    });
                } else {
                    $('#Nama_Pasien').val('');
                    $('#id_form_skrining').html('<option value="">-- Masukkan NIK Pasien untuk Rekomendasi --</option>');
                    $('#form_skrining_recommendation_info').text('Form skrining akan direkomendasikan setelah NIK pasien dimasukkan.');
                    $('#pertanyaan_list_tambah').html('<p>Pilih formulir skrining untuk memuat pertanyaan.</p>');
                    $('#pertanyaan_container_tambah').hide();
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
                            var errorMessage = response.message || 'Terjadi kesalahan saat menyimpan data.';
                            if (response.errors) {
                                errorMessage = Object.values(response.errors).map(function(err) {
                                    return Array.isArray(err) ? err.join(', ') : err;
                                }).join('<br>');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                html: errorMessage,
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
                            errorMessage = Object.values(xhr.responseJSON.errors).map(function(err) {
                                return Array.isArray(err) ? err.join(', ') : err;
                            }).join('<br>');
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
                var urlUpdate = "{{ route('skrining.update', ':id') }}".replace(':id', skriningId);
                $('#editForm').attr('action', urlUpdate);

                $.ajax({
                    url: "{{ route('skrining.show', ':id') }}".replace(':id', skriningId),
                    method: 'GET',
                    success: function(response) {
                        // Periksa respons untuk memastikan data valid sebelum mengisi form
                        if (response && response.Nama_Petugas) { // Contoh: periksa Nama_Petugas sebagai indikator data valid
                            $('#edit_Nama_Petugas').val(response.Nama_Petugas);
                            $('#edit_NIK_Pasien').val(response.NIK_Pasien);
                            $('#edit_Nama_Pasien').val(response.Nama_Pasien);

                            if (response.Tanggal_Skrining) {
                                // Tanggal dari backend sudah diformat dd-mm-yyyy oleh show() method
                                $('#edit_Tanggal_Skrining').datepicker('setDate', response.Tanggal_Skrining);
                            } else {
                                $('#edit_Tanggal_Skrining').val('');
                                $('#edit_Tanggal_Skrining').datepicker('setDate', null);
                            }

                            var editFormSkriningSelect = $('#edit_id_form_skrining');
                            editFormSkriningSelect.val(response.id_form_skrining);

                            loadPertanyaanEdit(); // Memuat pertanyaan

                            $('#editModal').modal('show');
                        } else {
                            // Tampilkan pesan error jika data tidak valid atau tidak lengkap
                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal!',
                                text: 'Data skrining tidak ditemukan atau tidak lengkap.',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching skrining detail for edit:", error);
                        console.log(xhr.responseText); // Log respons teks untuk debugging lebih lanjut
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memuat data skrining. Silakan coba lagi. (Kode HTTP: ' + xhr.status + ')',
                        });
                    }
                });
            });

            // Tambahkan event handler untuk SUBMIT FORM EDIT
            $('#editForm').on('submit', function(e) {
                e.preventDefault(); // Mencegah submit default form (yang akan me-reload halaman)

                var form = $(this);
                var url = form.attr('action');

                $.ajax({
                    url: url,
                    type: 'POST', // Gunakan POST untuk form, @method('PUT') akan mengonversi di Laravel
                    data: form.serialize(), // Mengirim semua data form, termasuk CSRF token dan _method=PUT
                    success: function(response) {
                        if (response.success) {
                            $('#editModal').modal('hide'); // Sembunyikan modal edit
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                            }).then(() => {
                                window.location.reload(); // Reload halaman setelah sukses
                            });
                        } else {
                            var errorMessage = response.message || 'Terjadi kesalahan saat memperbarui data.';
                            if (response.errors) {
                                // Menggabungkan semua pesan error validasi
                                errorMessage = Object.values(response.errors).map(function(err) {
                                    return Array.isArray(err) ? err.join(', ') : err;
                                }).join('<br>');
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
                        var errorMessage = 'Terjadi kesalahan saat memperbarui data. Silakan cek konsol browser.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).map(function(err) {
                                return Array.isArray(err) ? err.join(', ') : err;
                            }).join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            html: errorMessage,
                        });
                    }
                });
            });

            // Event saat pilihan Form Skrining berubah di Modal Edit
            $('#edit_id_form_skrining').on('change', function() {
                loadPertanyaanEdit();
            });
            // Event saat NIK Pasien di Modal Edit berubah (jika tidak readonly)
            $('#edit_NIK_Pasien').on('input', function() {
                updateNikLengthInfo('#edit_NIK_Pasien', '#nik_length_info_edit');
            });

            $('#editModal').on('hidden.bs.modal', function() {
                $('#editForm')[0].reset();
                $('#edit_pertanyaan_list').empty().append('<p>Memuat pertanyaan...</p>');
                updateNikLengthInfo('#edit_NIK_Pasien', '#nik_length_info_edit');
            });


            // Perbaikan untuk tombol "Hapus": Submit form via AJAX dan reload halaman
            $('.btn-delete').on('click', function(e) {
                e.preventDefault();
                var skriningId = $(this).data('skrining-id');
                var skriningNama = $(this).data('skrining-nama');
                var deleteUrl = "{{ route('skrining.delete', ':id') }}".replace(':id', skriningId);

                $('#skriningToDelete').text(skriningNama);
                $('#deleteForm').attr('action', deleteUrl);

                $('#deleteConfirmationModal').modal('show');

                $('#deleteForm').off('submit').on('submit', function(e) {
                    e.preventDefault();

                    var form = $(this);
                    var url = form.attr('action');

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
                                    text: response.message,
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: response.message || 'Terjadi kesalahan saat menghapus data.',
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error deleting skrining:", error);
                            console.log(xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan saat menghapus data. Silakan cek konsol browser.',
                            });
                        }
                    });
                });
            });

            // Event untuk pencarian
            $('#searchSkrining').on('input', function() {
                var searchValue = $(this).val();
                var url = "{{ route('skrining.index') }}";

                url += (url.indexOf('?') > -1 ? '&' : '?') + 'search=' + searchValue;

                $.get(url, function(data) {
                    $('tbody').html($(data).find('tbody').html());
                }).fail(function(xhr) {
                    console.error("Error during search:", xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat melakukan pencarian.',
                    });
                });
            });

            // Initial call for feather icons
            feather.replace();
        });
    </script>
@endsection
