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
                                <input type="text" class="form-control" id="edit_NIK" name="NIK">
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
                                <input type="text" class="form-control" id="create_NIK" name="NIK" required>
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

            // Inisialisasi Datepicker untuk modal Tambah Pasien
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

            // Inisialisasi Datepicker untuk modal Edit Pasien
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

            // Event klik pada ikon kalender untuk input Tanggal Lahir (Tambah)
            $('#create_Tanggal_Lahir_icon').on('click', function() {
                $('#create_Tanggal_Lahir_display').datepicker('show');
            });

            // Event klik pada ikon kalender untuk input Tanggal Lahir (Edit)
            $('#edit_Tanggal_Lahir_icon').on('click', function() {
                $('#edit_Tanggal_Lahir_display').datepicker('show');
            });

            // Event untuk tombol "Tambah Baru"
            $('#tambahPasienBaru').on('click', function() {
                $('#createForm')[0].reset(); // Reset form
                // Set tanggal lahir ke hari ini (ini akan otomatis mengisi hidden input juga)
                $('#create_Tanggal_Lahir_display').datepicker('setDate', new Date());
                $('#createModal').modal('show');
            });

            // Reset form tambah pasien saat modal ditutup
            $('#createModal').on('hidden.bs.modal', function() {
                $('#createForm')[0].reset();
                $('#create_Tanggal_Lahir_display').datepicker('setDate', null);
            });

            // Event untuk tombol "Edit"
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

                $('#edit_NIK').val(pasienNik);
                $('#edit_Nama_Pasien').val(pasienNama);

                // Menetapkan tanggal lahir untuk datepicker di modal edit
                if (pasienTanggalLahir) {
                    // Parse tanggal YYYY-MM-DD dari data-attribute, lalu set ke datepicker display
                    var dateObj = $.datepicker.parseDate('yy-mm-dd', pasienTanggalLahir);
                    $('#edit_Tanggal_Lahir_display').datepicker('setDate', dateObj);
                    // Hidden field akan otomatis terisi oleh datepicker
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

                $('#editModal').modal('show');
            });

            // Reset form edit pasien saat modal ditutup
            $('#editModal').on('hidden.bs.modal', function() {
                $('#editForm')[0].reset();
                $('#edit_Tanggal_Lahir_display').datepicker('setDate', null);
            });

            // Event untuk tombol "Hapus" (menggunakan submit form)
            $(document).on('click', '.btn-delete', function() {
                var pasienId = $(this).data('pasien-id');
                var pasienNama = $(this).data('pasien-nama');
                var deleteUrl = "{{ route('pasien.delete', ':id') }}".replace(':id', pasienId);

                $('#pasienToDelete').text(pasienNama);
                $('#deleteForm').attr('action', deleteUrl);
                $('#deleteConfirmationModal').modal('show');
            });

            // Reset action form hapus saat modal ditutup
            $('#deleteConfirmationModal').on('hidden.bs.modal', function() {
                $('#deleteForm').attr('action', '');
            });

            // Event untuk pencarian pasien
            $('#searchPasien').on('input', function() {
                var searchValue = $(this).val();
                var url = "{{ route('pasien.index') }}";

                url += (url.indexOf('?') > -1 ? '&' : '?') + 'search=' + searchValue;

                $.get(url, function(data) {
                    $('tbody').html($(data).find('tbody').html());
                });
            });

            // Form Submit Create Pasien (akan reload halaman jika sukses)
            $('#createForm').on('submit', function(e) {
                // Tidak perlu e.preventDefault() jika controller mengembalikan redirect
            });

            // Form Submit Edit Pasien (akan reload halaman jika sukses)
            $('#editForm').on('submit', function(e) {
                // Tidak perlu e.preventDefault() jika controller mengembalikan redirect
            });
        });
    </script>
@endsection
