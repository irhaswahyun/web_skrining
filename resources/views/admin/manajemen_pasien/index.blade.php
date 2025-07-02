@extends('partials.admin.main')

@section('title', 'Data Pasien')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Pasien</h1>
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
                                            <input type="text" class="form-control" placeholder="Cari Pasien" id="searchPasien"
                                                name="search" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button id="tambahPasienBaru" class="btn btn-tambah-baru">
                                            Tambah Baru
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-custom">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><b>No</b></th>
                                            <th><b>NIK</b></th>
                                            <th><b>Nama Pasien</b></th>
                                            <th><b>Tanggal Lahir</b></th>
                                            <th><b>Kategori</b></th>
                                            <th><b>Jenis Kelamin</b></th>
                                            <th><b>Alamat</b></th>
                                            <th><b>Wilayah</b></th> {{-- Kolom Wilayah --}}
                                            <th><b>No Telepon</b></th>
                                            <th><b>Aksi</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pasiens as $i => $pasien)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $pasien->NIK }}</td>
                                                <td>{{ $pasien->Nama_Pasien }}</td>
                                                {{-- Format tanggal lahir di tabel agar konsisten dd-mm-YYYY --}}
                                                <td>{{ \Carbon\Carbon::parse($pasien->Tanggal_Lahir)->format('d-m-Y') }}</td>
                                                <td>{{ $pasien->Kategori }}</td>
                                                <td>{{ $pasien->Jenis_Kelamin }}</td>
                                                <td>{{ $pasien->Alamat }}</td>
                                                <td>{{ $pasien->Wilayah }}</td>
                                                <td>{{ $pasien->No_telp }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary btn-edit"
                                                            data-pasien-id="{{ $pasien->NIK }}"
                                                            data-pasien-nik="{{ $pasien->NIK }}"
                                                            data-pasien-nama="{{ $pasien->Nama_Pasien }}"
                                                            {{-- Format tanggal lahir untuk data attribute ke YYYY-MM-DD (sesuai yang disimpan DB) --}}
                                                            data-pasien-tanggal-lahir="{{ \Carbon\Carbon::parse($pasien->Tanggal_Lahir)->format('Y-m-d') }}"
                                                            data-pasien-kategori="{{ $pasien->Kategori }}"
                                                            data-pasien-jenis-kelamin="{{ $pasien->Jenis_Kelamin }}"
                                                            data-pasien-alamat="{{ $pasien->Alamat }}"
                                                            data-pasien-wilayah="{{ $pasien->Wilayah }}"
                                                            data-pasien-no-telp="{{ $pasien->No_telp }}">
                                                            Edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm btn-delete"
                                                            data-pasien-id="{{ $pasien->NIK }}"
                                                            data-pasien-nama="{{ $pasien->Nama_Pasien }}">
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
            </section>
        </div>

        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Data Pasien</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="edit_NIK">NIK</label>
                                <input type="text" class="form-control" id="edit_NIK" name="NIK"
                                    maxlength="16" minlength="16" pattern="[0-9]{16}" title="NIK harus 16 digit angka.">
                                <div id="editNikFeedback" class="text-danger" style="display: none;"></div>
                                @error('NIK')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="edit_Nama_Pasien">Nama Pasien</label>
                                <input type="text" class="form-control" id="edit_Nama_Pasien" name="Nama_Pasien">
                            </div>
                            <div class="form-group">
                                <label for="edit_Tanggal_Lahir_display">Tanggal Lahir</label>
                                <div class="input-group">
                                    {{-- Input yang terlihat oleh pengguna (display dd-mm-yy) --}}
                                    <input type="text" class="form-control" id="edit_Tanggal_Lahir_display" placeholder="dd-mm-yyyy">
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="edit_Tanggal_Lahir_icon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    {{-- Input tersembunyi yang akan dikirim ke backend (format YYYY-MM-DD) --}}
                                    <input type="hidden" name="Tanggal_Lahir" id="edit_Tanggal_Lahir_hidden">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="edit_Kategori">Kategori</label>
                                <input type="text" class="form-control" id="edit_Kategori" name="Kategori">
                            </div>
                            <div class="form-group">
                                <label for="edit_Jenis_Kelamin">Jenis Kelamin</label>
                                <input type="text" class="form-control" id="edit_Jenis_Kelamin" name="Jenis_Kelamin">
                            </div>
                            <div class="form-group">
                                <label for="edit_Alamat">Alamat</label>
                                <input type="text" class="form-control" id="edit_Alamat" name="Alamat">
                            </div>
                            <div class="form-group">
                                <label for="edit_Wilayah">Wilayah</label>
                                <input type="text" class="form-control" id="edit_Wilayah" name="Wilayah">
                            </div>
                            <div class="form-group">
                                <label for="edit_No_telp">No Telepon</label>
                                <input type="text" class="form-control" id="edit_No_telp" name="No_telp">
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
                        Apakah Anda yakin ingin menghapus pasien <span id="pasienToDelete"></span>?
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

        <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah Data Pasien</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="createForm" method="POST" action="{{ route('pasien.store') }}">
                        @csrf
                        <div class="modal-body">
                           <div class="form-group">
                                 <label for="create_NIK">NIK</label>
                                    <input type="text" class="form-control" id="create_NIK" name="NIK" required
                                        maxlength="16" minlength="16" pattern="[0-9]{16}" title="NIK harus 16 digit angka.">
                                    <div id="createNikFeedback" class="text-danger" style="display: none;"></div>
                                    @error('NIK')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                            </div>
                            <div class="form-group">
                                <label for="create_Nama_Pasien">Nama Pasien</label>
                                <input type="text" class="form-control" id="create_Nama_Pasien" name="Nama_Pasien" required>
                            </div>
                            <div class="form-group">
                                <label for="create_Tanggal_Lahir_display">Tanggal Lahir</label>
                                <div class="input-group">
                                    {{-- Input yang terlihat oleh pengguna (display dd-mm-yy) --}}
                                    <input type="text" class="form-control" id="create_Tanggal_Lahir_display" placeholder="dd-mm-yyyy" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="create_Tanggal_Lahir_icon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    {{-- Input tersembunyi yang akan dikirim ke backend (format YYYY-MM-DD) --}}
                                    <input type="hidden" name="Tanggal_Lahir" id="create_Tanggal_Lahir_hidden">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="create_Kategori">Kategori</label>
                                <input type="text" class="form-control" id="create_Kategori" name="Kategori" required>
                            </div>
                            <div class="form-group">
                                <label for="create_Jenis_Kelamin">Jenis Kelamin</label>
                                <input type="text" class="form-control" id="create_Jenis_Kelamin" name="Jenis_Kelamin" required>
                            </div>
                            <div class="form-group">
                                <label for="create_Alamat">Alamat</label>
                                <input type="text" class="form-control" id="create_Alamat" name="Alamat" required>
                            </div>
                            <div class="form-group">
                                <label for="create_Wilayah">Wilayah</label>
                                <input type="text" class="form-control" id="create_Wilayah" name="Wilayah" required>
                            </div>
                            <div class="form-group">
                                <label for="create_No_telp">No Telepon</label>
                                <input type="text" class="form-control" id="create_No_telp" name="No_telp" required>
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
@endsection

@section('scripts')
    <script>
    $(document).ready(function() {
        feather.replace(); // Inisialisasi Feather Icons

        // --- Fungsi Validasi NIK (Reusable) ---
        function validateNIK(inputId) {
            const input = document.getElementById(inputId);
            const nikFeedbackId = inputId === 'create_NIK' ? 'createNikFeedback' : 'editNikFeedback';
            const nikFeedback = document.getElementById(nikFeedbackId);
            const nikValue = input.value;

            // Pastikan elemen feedback ada sebelum mencoba memanipulasinya
            if (!nikFeedback) {
                console.warn(`Elemen feedback dengan ID '${nikFeedbackId}' tidak ditemukan.`);
                return;
            }

            // Hapus kelas validasi sebelumnya
            input.classList.remove('is-invalid', 'is-valid');
            nikFeedback.style.display = 'none';
            nikFeedback.textContent = ''; // Bersihkan pesan sebelumnya

            if (nikValue.length === 0) {
                // Jangan tampilkan pesan error jika field kosong (validasi 'required' sudah di server)
                // Ini untuk menghindari pesan ganda saat input masih kosong
                return;
            }

            if (!/^\d+$/.test(nikValue)) {
                nikFeedback.textContent = 'NIK hanya boleh berisi angka.';
                nikFeedback.style.display = 'block';
                input.classList.add('is-invalid');
            } else if (nikValue.length < 16) {
                nikFeedback.textContent = 'NIK kurang dari 16 digit.';
                nikFeedback.style.display = 'block';
                input.classList.add('is-invalid');
            } else if (nikValue.length > 16) {
                nikFeedback.textContent = 'NIK lebih dari 16 digit.';
                nikFeedback.style.display = 'block';
                input.classList.add('is-invalid');
            } else {
                // Validasi sukses secara client-side (angka dan 16 digit)
                input.classList.add('is-valid');
            }
        }

        // --- Event Listener untuk Input NIK ---
        // Menggunakan delegasi event untuk input NIK di modal create
        $(document).on('input', '#create_NIK', function() {
            this.value = this.value.replace(/[^0-9]/g, ''); // Hapus non-angka
            validateNIK('create_NIK');
        });

        // Menggunakan delegasi event untuk input NIK di modal edit
        $(document).on('input', '#edit_NIK', function() {
            this.value = this.value.replace(/[^0-9]/g, ''); // Hapus non-angka
            validateNIK('edit_NIK');
        });


        // --- Inisialisasi Datepicker untuk modal Tambah Pasien ---
        $('#create_Tanggal_Lahir_display').datepicker({
            dateFormat: 'dd-mm-yy', // Format tampilan untuk pengguna
            altField: '#create_Tanggal_Lahir_hidden', // Kirim nilai ke hidden input
            altFormat: 'yy-mm-dd', // Format untuk hidden input (yang dikirim ke DB)
            changeMonth: true,
            changeYear: true,
            yearRange: '1950:2050',
            showButtonPanel: true,
            currentText: "Hari Ini",
            closeText: "Selesai",
            onClose: function(dateText, inst) {
                if (dateText === '') {
                    $('#create_Tanggal_Lahir_hidden').val(''); // Kosongkan hidden field juga
                }
            }
        });

        // --- Inisialisasi Datepicker untuk modal Edit Pasien ---
        $('#edit_Tanggal_Lahir_display').datepicker({
            dateFormat: 'dd-mm-yy', // Format tampilan untuk pengguna
            altField: '#edit_Tanggal_Lahir_hidden', // Kirim nilai ke hidden input
            altFormat: 'yy-mm-dd', // Format untuk hidden input (yang dikirim ke DB)
            changeMonth: true,
            changeYear: true,
            yearRange: '1950:2050',
            showButtonPanel: true,
            currentText: "Hari Ini",
            closeText: "Selesai",
            onClose: function(dateText, inst) {
                if (dateText === '') {
                    $('#edit_Tanggal_Lahir_hidden').val(''); // Kosongkan hidden field juga
                }
            }
        });

        // --- Event klik pada ikon kalender untuk input Tanggal Lahir (Tambah) ---
        $('#create_Tanggal_Lahir_icon').on('click', function() {
            $('#create_Tanggal_Lahir_display').datepicker('show');
        });

        // --- Event klik pada ikon kalender untuk input Tanggal Lahir (Edit) ---
        $('#edit_Tanggal_Lahir_icon').on('click', function() {
            $('#edit_Tanggal_Lahir_display').datepicker('show');
        });

        // --- Event untuk tombol "Tambah Baru" ---
        $('#tambahPasienBaru').on('click', function() {
            $('#createForm')[0].reset(); // Reset form
            // Reset validasi visual dan pesan
            $('#create_NIK').removeClass('is-invalid is-valid');
            $('#createNikFeedback').hide().text('');
            // Set tanggal lahir ke hari ini (ini akan otomatis mengisi hidden input juga)
            $('#create_Tanggal_Lahir_display').datepicker('setDate', new Date());
            $('#createModal').modal('show');
        });

        // --- Reset form tambah pasien saat modal ditutup ---
        $('#createModal').on('hidden.bs.modal', function() {
            $('#createForm')[0].reset();
            $('#create_Tanggal_Lahir_display').datepicker('setDate', null);
            // Reset validasi visual dan pesan
            $('#create_NIK').removeClass('is-invalid is-valid');
            $('#createNikFeedback').hide().text('');
            // Hapus pesan error Laravel dari server (jika ada)
            $(this).find('.alert-danger').remove();
        });

        // --- Event untuk tombol "Edit" ---
        $(document).on('click', '.btn-edit', function() {
            var pasienId = $(this).data('pasien-id');
            var pasienNik = $(this).data('pasien-nik');
            var pasienNama = $(this).data('pasien-nama');
            var pasienTanggalLahir = $(this).data('pasien-tanggal-lahir'); // Format YYYY-MM-DD dari blade
            var pasienKategori = $(this).data('pasien-kategori');
            var pasienJenisKelamin = $(this).data('pasien-jenis-kelamin');
            var pasienAlamat = $(this).data('pasien-alamat');
            var pasienWilayah = $(this).data('pasien-wilayah');
            var pasienNoTelp = $(this).data('pasien-no-telp');

            var editUrl = "{{ route('pasien.update', ':id') }}".replace(':id', pasienId);

            // Isi form edit dengan data pasien
            $('#edit_NIK').val(pasienNik);
            $('#edit_Nama_Pasien').val(pasienNama);

            // Menetapkan tanggal lahir untuk datepicker di modal edit
            if (pasienTanggalLahir) {
                var dateObj = $.datepicker.parseDate('yy-mm-dd', pasienTanggalLahir);
                $('#edit_Tanggal_Lahir_display').datepicker('setDate', dateObj);
            } else {
                $('#edit_Tanggal_Lahir_display').val('');
                $('#edit_Tanggal_Lahir_display').datepicker('setDate', null);
            }

            $('#edit_Kategori').val(pasienKategori);
            $('#edit_Jenis_Kelamin').val(pasienJenisKelamin);
            $('#edit_Alamat').val(pasienAlamat);
            $('#edit_Wilayah').val(pasienWilayah);
            $('#edit_No_telp').val(pasienNoTelp);
            $('#editForm').attr('action', editUrl);

            // Tampilkan modal edit
            $('#editModal').modal('show');
        });

        // --- Reset form edit pasien saat modal ditutup ---
        $('#editModal').on('hidden.bs.modal', function() {
            $('#editForm')[0].reset();
            $('#edit_Tanggal_Lahir_display').datepicker('setDate', null);
            // Reset validasi visual dan pesan
            $('#edit_NIK').removeClass('is-invalid is-valid');
            $('#editNikFeedback').hide().text('');
            // Hapus pesan error Laravel dari server (jika ada)
            $(this).find('.alert-danger').remove();
        });

        // --- Event untuk tombol "Hapus" (menggunakan submit form) ---
        $(document).on('click', '.btn-delete', function() {
            var pasienId = $(this).data('pasien-id');
            var pasienNama = $(this).data('pasien-nama');
            var deleteUrl = "{{ route('pasien.delete', ':id') }}".replace(':id', pasienId);

            $('#pasienToDelete').text(pasienNama);
            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteConfirmationModal').modal('show');
        });

        // --- Reset action form hapus saat modal ditutup ---
        $('#deleteConfirmationModal').on('hidden.bs.modal', function() {
            $('#deleteForm').attr('action', '');
        });

        // --- Event untuk pencarian pasien ---
        $('#searchPasien').on('input', function() {
            var searchValue = $(this).val();
            var url = "{{ route('pasien.index') }}";

            // Pastikan URL tidak memiliki query parameter ganda
            var separator = url.indexOf('?') > -1 ? '&' : '?';
            url = url.split('?')[0] + separator + 'search=' + encodeURIComponent(searchValue);

            $.get(url, function(data) {
                // Hanya mengganti isi tbody
                $('tbody').html($(data).find('tbody').html());
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error fetching search results: " + textStatus, errorThrown);
                // Tambahkan penanganan error di UI jika perlu
            });
        });

        // --- Penanganan modal saat ditampilkan (untuk trigger validasi NIK awal) ---
        $('#createModal').on('shown.bs.modal', function() {
            // Panggil validasi NIK saat modal create dibuka
            // Ini berguna jika ada nilai 'old' dari validasi Laravel sebelumnya
            validateNIK('create_NIK');
        });

        $('#editModal').on('shown.bs.modal', function() {
            // Panggil validasi NIK saat modal edit dibuka
            // Ini akan memvalidasi NIK yang sudah ada saat data dimuat
            validateNIK('edit_NIK');
        });

        // --- Form Submit Create Pasien (akan reload halaman jika sukses) ---
        // Biarkan seperti ini karena controller mengembalikan redirect
        $('#createForm').on('submit', function(e) {
            // Optional: Tambahkan validasi NIK terakhir sebelum submit
            // Ini berguna jika pengguna menonaktifkan JS atau mencoba bypass
            // Namun, validasi server adalah yang paling penting.
            // validateNIK('create_NIK');
            // if ($('#create_NIK').hasClass('is-invalid')) {
            //     e.preventDefault();
            // }
        });

        // --- Form Submit Edit Pasien (akan reload halaman jika sukses) ---
        // Biarkan seperti ini karena controller mengembalikan redirect
        $('#editForm').on('submit', function(e) {
            // Optional: Tambahkan validasi NIK terakhir sebelum submit
            // validateNIK('edit_NIK');
            // if ($('#edit_NIK').hasClass('is-invalid')) {
            //     e.preventDefault();
            // }
        });

    });
</script>
@endsection
