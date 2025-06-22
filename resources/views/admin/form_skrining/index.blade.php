@extends('partials.admin.main')

@section('title', 'Manajemen Form Skrining')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Manajemen Form Skrining</h1>
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
                                            <input type="text" class="form-control" placeholder="Cari Nama Skrining"
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
                                <table class="table table-custom">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><b>No</b></th>
                                            <th><b>Nama Skrining</b></th>
                                            <th><b>Kategori</b></th> {{-- Kolom baru untuk Kategori --}}
                                            <th><b>Jumlah Pertanyaan</b></th>
                                            <th><b>Aksi</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($formSkrinings as $i => $skrining)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $skrining->nama_skrining }}</td>
                                                <td>
                                                    @if ($skrining->kategori && is_array($skrining->kategori) && count($skrining->kategori) > 0)
                                                        {{ implode(', ', $skrining->kategori) }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $skrining->pertanyaans_count }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info btn-detail"
                                                        data-id="{{ $skrining->id }}">Detail</button>
                                                    <button class="btn btn-sm btn-primary btn-edit"
                                                        data-id="{{ $skrining->id }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm btn-delete"
                                                        data-skrining-id="{{ $skrining->id }}"
                                                        data-skrining-nama="{{ $skrining->nama_skrining }}">
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
                    <h5 class="modal-title" id="tambahSkriningModalLabel">Tambah Form Skrining Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="tambahSkriningForm" method="POST" action="{{ route('form_skrining.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_skrining">Nama Skrining</label>
                            <input type="text" name="nama_skrining" class="form-control"
                                placeholder="Masukkan nama skrining" required>
                            @error('nama_skrining')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- BAGIAN BARU: PEMILIHAN KATEGORI UNTUK TAMBAH --}}
                        <div class="form-group">
                            <label for="kategori_tambah">Pilih Kategori Pasien (Bisa Pilih Banyak)</label>
                            <select name="kategori[]" id="kategori_tambah" class="form-control select2-multiple" multiple="multiple" style="width: 100%;" required>
                                {{-- Opsi kategori diisi dari $kategoriOptions yang sudah tersedia --}}
                                @isset($kategoriOptions)
                                    @foreach ($kategoriOptions as $kategoriItem)
                                        <option value="{{ $kategoriItem }}">{{ $kategoriItem }}</option>
                                    @endforeach
                                @endisset
                            </select>
                            @error('kategori')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Pertanyaan</label>
                            <div class="form-check" id="tambah_pertanyaan_list">
                                {{-- Loop semua pertanyaan dari controller index --}}
                                @foreach ($pertanyaans as $pertanyaan) {{-- Variabel ini harusnya sudah tersedia dari method index --}}
                                    <div>
                                        <input type="checkbox" name="pertanyaan_ids[]" value="{{ $pertanyaan->id }}"
                                            id="tambah_pertanyaan_{{ $pertanyaan->id }}" class="form-check-input">
                                        <label class="form-check-label" for="tambah_pertanyaan_{{ $pertanyaan->id }}">
                                            {{ $pertanyaan->pertanyaan }}
                                        </label>
                                    </div>
                                @endforeach
                                @error('pertanyaan_ids')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
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
                            <label for="edit_nama_skrining">Nama Skrining</label>
                            <input type="text" class="form-control" id="edit_nama_skrining" name="nama_skrining"
                                placeholder="Masukkan nama skrining" required>
                            @error('nama_skrining')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- BAGIAN BARU: PEMILIHAN KATEGORI UNTUK EDIT --}}
                        <div class="form-group">
                            <label for="kategori_edit">Pilih Kategori Pasien (Bisa Pilih Banyak)</label>
                            <select name="kategori[]" id="kategori_edit" class="form-control select2-multiple" multiple="multiple" style="width: 100%;">
                                {{-- Opsi akan diisi oleh JavaScript dari respons AJAX --}}
                            </select>
                            @error('kategori')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Pertanyaan</label>
                            {{-- CONTAINER tempat pertanyaan (checkboxes) akan diisi oleh JavaScript --}}
                            <div class="form-check" id="edit_pertanyaan_list">
                                <p>Memuat pertanyaan...</p> {{-- Placeholder loading --}}
                            </div>
                            @error('pertanyaan_ids')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
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

    {{-- MODAL DETAIL FORM SKRINING --}}
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Form Skrining</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 class="font-weight-bold">Nama Skrining:</h6>
                    <p id="detail_nama_skrining"></p>

                    {{-- BAGIAN BARU: DETAIL KATEGORI --}}
                    <h6 class="font-weight-bold">Kategori Pasien:</h6>
                    <ul id="detail_kategori_pasien" class="list-unstyled">
                        {{-- Akan diisi via JS --}}
                    </ul>

                    <h6 class="font-weight-bold">Daftar Pertanyaan:</h6>
                    <ul id="detail_daftar_pertanyaan">
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
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
                    Apakah Anda yakin ingin menghapus form skrining: <span id="skriningToDelete"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST">
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
    {{-- Memuat Select2 CSS dan JS --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 untuk modal tambah
            $('#kategori_tambah').select2({
                placeholder: "Pilih kategori",
                allowClear: true,
                dropdownParent: $('#tambahSkriningModal') // Penting untuk modal Bootstrap
            });

            // Inisialisasi Select2 untuk modal edit
            $('#kategori_edit').select2({
                placeholder: "Pilih kategori",
                allowClear: true,
                dropdownParent: $('#editModal') // Penting untuk modal Bootstrap
            });


            // Event untuk tombol "Tambah Baru"
            $('#tambahSkriningBaru').click(function() {
                $('#tambahSkriningModal').modal('show');
            });

            // Event untuk tombol "Edit"
            $('.btn-edit').on('click', function() {
                var formSkriningId = $(this).data('id');
                var urlUpdate = "{{ route('form_skrining.update', ':id') }}";
                urlUpdate = urlUpdate.replace(':id', formSkriningId);

                $('#editForm').attr('action', urlUpdate);

                $.ajax({
                    url: "{{ route('form_skrining.edit', ':id') }}".replace(':id', formSkriningId),
                    method: 'GET',
                    success: function(response) {
                        console.log('Response for Edit Modal:', response);

                        $('#edit_nama_skrining').val(response.formSkrining.nama_skrining);

                        // Hapus baris ini karena kolom 'id_daftar_penyakit' sudah dihapus
                        // $('#edit_id_daftar_penyakit').val(response.formSkrining.penyakit.id);

                        // --- Logic untuk Kategori (BARU) ---
                        var kategoriSelect = $('#kategori_edit');
                        kategoriSelect.empty(); // Kosongkan opsi sebelumnya

                        var kategoriOptions = response.kategoriOptions; // Ambil dari response
                        var selectedKategori = response.formSkrining.kategori || []; // Ambil dari formSkrining

                        $.each(kategoriOptions, function(index, kategoriItem) {
                            var option = new Option(kategoriItem, kategoriItem, false, false);
                            kategoriSelect.append(option);
                        });
                        // Set opsi yang sudah terpilih di Select2
                        kategoriSelect.val(selectedKategori).trigger('change');
                        // --- Akhir Logic untuk Kategori ---


                        var pertanyaanContainer = $('#edit_pertanyaan_list');
                        pertanyaanContainer.empty();

                        var allPertanyaans = response.allPertanyaans; // Ambil dari response
                        var relatedPertanyaanIds = response.relatedPertanyaanIds; // Ambil dari response

                        if (allPertanyaans && allPertanyaans.length > 0) {
                            $.each(allPertanyaans, function(index, pertanyaan) {
                                var isChecked = relatedPertanyaanIds.includes(pertanyaan.id);
                                pertanyaanContainer.append(
                                    `<div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            name="pertanyaan_ids[]"
                                            value="${pertanyaan.id}"
                                            id="edit_pertanyaan_${pertanyaan.id}"
                                            ${isChecked ? 'checked' : ''}>
                                        <label class="form-check-label" for="edit_pertanyaan_${pertanyaan.id}">
                                            ${pertanyaan.pertanyaan}
                                        </label>
                                    </div>`
                                );
                            });
                        } else {
                            pertanyaanContainer.append('<p>Tidak ada pertanyaan yang tersedia.</p>');
                        }

                        $('#editModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching form skrining detail for edit:", error);
                        console.log(xhr.responseText);
                        alert('Terjadi kesalahan saat memuat data form skrining. Silakan cek konsol browser.');
                    }
                });
            });

            $('#editModal').on('hidden.bs.modal', function() {
                $('#editForm')[0].reset();
                $('#edit_pertanyaan_list').empty();
                $('#edit_pertanyaan_list').append('<p>Memuat pertanyaan...</p>');
                // Reset Select2 di modal edit
                $('#kategori_edit').val(null).trigger('change');
            });

            // Event untuk tombol "Detail"
            $('.btn-detail').on('click', function() {
                var skriningId = $(this).data('id');

                $.get("{{ route('form_skrining.detail', ':id') }}".replace(':id', skriningId), function(data) {
                    if (data.formSkrining) {
                        $('#detail_nama_skrining').text(data.formSkrining.nama_skrining);

                        // Hapus baris ini karena Nama Penyakit sudah tidak relevan
                        // $('#detail_nama_penyakit').text(data.formSkrining.penyakit ? data.formSkrining.penyakit.Nama_Penyakit : '-');

                        // --- Logic untuk Kategori (BARU) ---
                        var detailKategoriList = $('#detail_kategori_pasien');
                        detailKategoriList.empty();
                        if (data.formSkrining.kategori && Array.isArray(data.formSkrining.kategori) && data.formSkrining.kategori.length > 0) {
                            $.each(data.formSkrining.kategori, function(index, kategori) {
                                detailKategoriList.append('<li>' + kategori + '</li>');
                            });
                        } else {
                            detailKategoriList.append('<li>Tidak ada kategori spesifik</li>');
                        }
                        // --- Akhir Logic untuk Kategori ---

                        $('#detail_daftar_pertanyaan').empty();
                        if (data.formSkrining.related_pertanyaan && data.formSkrining.related_pertanyaan.length > 0) {
                            $.each(data.formSkrining.related_pertanyaan, function(key, value) {
                                $('#detail_daftar_pertanyaan').append(`<li>${value.pertanyaan}</li>`);
                            });
                        } else {
                            $('#detail_daftar_pertanyaan').append('<li>Tidak ada pertanyaan terkait.</li>');
                        }

                        $('#detailModal').modal('show');
                    } else {
                        alert('Data tidak ditemukan');
                    }
                }).fail(function(xhr) {
                    console.error("Error fetching detail:", xhr.responseText);
                    alert('Terjadi kesalahan saat memuat data.');
                });
            });

            $('.btn-delete').on('click', function() {
                var skriningId = $(this).data('skrining-id');
                var skriningNama = $(this).data('skrining-nama');
                var deleteUrl = "{{ route('form_skrining.delete', ':id') }}".replace(':id', skriningId);

                $('#skriningToDelete').text(skriningNama);
                $('#deleteForm').attr('action', deleteUrl);
                $('#deleteConfirmationModal').modal('show');
            });

            $('#searchSkrining').on('input', function() {
                var searchValue = $(this).val();
                var url = "{{ route('form_skrining.index') }}";

                // Menggunakan parameter 'search' untuk request AJAX
                url += (url.indexOf('?') > -1 ? '&' : '?') + 'search=' + searchValue;

                // Melakukan AJAX request dan memperbarui hanya bagian tbody
                $.get(url, function(data) {
                    // Membuat objek jQuery dari HTML yang diterima dan mencari tbody
                    var newTbody = $(data).find('tbody').html();
                    $('tbody').html(newTbody);
                }).fail(function(xhr) {
                    console.error("Error during search:", xhr.responseText);
                    alert('Terjadi kesalahan saat melakukan pencarian.');
                });
            });

            feather.replace();
        });
    </script>
@endsection
