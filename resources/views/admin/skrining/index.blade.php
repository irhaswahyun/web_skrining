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
                                <table class="table table-custom">
                                    <thead class="thead-light">
                                        <tr>
                                            <th><b>No</b></th>
                                            <th><b>Nama Petugas</b></th>
                                            <th><b>NIK Pasien</b></th>
                                            <th><b>Nama Pasien</b></th>
                                            <th><b>Tanggal Skrining</b></th>
                                            <th><b>Penyakit</b></th>
                                            <th><b>Aksi</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($skrinings as $skrining)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $skrining->Nama_Petugas }}</td>
                                                <td>{{ $skrining->pasien->NIK }}</td>
                                                <td>{{ $skrining->Nama_Pasien }}</td>
                                                <td>{{ $skrining->Tanggal_Skrining }}</td>
                                                <td>{{ $skrining->penyakit->nama_penyakit ?? '-' }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info btn-detail"
                                                        data-id="{{ $skrining->id }}">Detail</button>
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
                        </div>
                        <div class="form-group">
                            <label for="Nama_Pasien">Nama Pasien</label>
                            <input type="text" name="Nama_Pasien" id="Nama_Pasien" class="form-control"
                                placeholder="Nama Pasien akan muncul otomatis" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="Tanggal_Skrining">Tanggal Skrining</label>
                            <input type="date" name="Tanggal_Skrining" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="id_penyakit">Formulir Skrining</label>
                            <select name="id_penyakit" id="id_penyakit" class="form-control" required>
                                <option value="">-- Pilih Formulir --</option>
                                @foreach ($penyakits as $penyakit)
                                    <option value="{{ $penyakit->id }}">{{ $penyakit->nama_penyakit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                        <label>Pertanyaan</label>
                        <div id="pertanyaan_list">
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
                        </div>
                        <div class="form-group">
                            <label for="edit_Nama_Pasien">Nama Pasien</label>
                            <input type="text" class="form-control" id="edit_Nama_Pasien" name="Nama_Pasien"
                                placeholder="Masukkan Nama Pasien" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit_Tanggal_Skrining">Tanggal Skrining</label>
                            <input type="date" class="form-control" id="edit_Tanggal_Skrining" name="Tanggal_Skrining"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="edit_id_penyakit">Penyakit</label>
                            <select class="form-control" id="edit_id_penyakit" name="id_penyakit" required>
                                <option value="">-- Pilih Penyakit --</option>
                                @foreach ($penyakits as $penyakit)
                                    <option value="{{ $penyakit->id }}">{{ $penyakit->nama_penyakit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pertanyaan</label>
                            <div class="form-check" id="edit_pertanyaan_list">
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
                    <h6 class="font-weight-bold">Nama Petugas:</h6>
                    <p id="detail_Nama_Petugas"></p>
                    <h6 class="font-weight-bold">NIK Pasien:</h6>
                    <p id="detail_NIK_Pasien"></p>
                    <h6 class="font-weight-bold">Nama Pasien:</h6>
                    <p id="detail_Nama_Pasien"></p>
                    <h6 class="font-weight-bold">Tanggal Skrining:</h6>
                    <p id="detail_Tanggal_Skrining"></p>
                     <h6 class="font-weight-bold">Penyakit:</h6>
                    <p id="detail_nama_penyakit"></p>
                    <h6 class="font-weight-bold">Pertanyaan:</h6>
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
            // Event untuk tombol "Tambah Baru"
            $('#tambahSkriningBaru').click(function() {
                $('#tambahSkriningModal').modal('show');
            });

            // Event untuk NIK Pasien di Modal Tambah
            $('#NIK_Pasien').on('change', function() {
                var nik = $(this).val();

                $.ajax({
                    url: "{{ route('pasien.getPasienData') }}", // Route ke controller Anda
                    method: 'GET',
                    data: {
                        NIK: nik
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#Nama_Pasien').val(response.data.Nama_Pasien);
                        } else {
                            $('#Nama_Pasien').val('');
                            alert(response.message); // Tampilkan pesan error
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching pasien data:", error);
                        console.log(xhr.responseText);
                        alert('Terjadi kesalahan saat mencari data pasien. Silakan cek konsol browser.');
                    }
                });
            });

            // Event untuk Penyakit di Modal Tambah
            $('#id_penyakit').on('change', function() {
                var penyakitId = $(this).val();

                $.ajax({
                    url: "{{ route('skrining.get-pertanyaan-by-penyakit', ':id') }}".replace(':id',
                        penyakitId),
                    method: 'GET',
                    success: function(response) {
                        var pertanyaanContainer = $('#pertanyaan_list');
                        pertanyaanContainer.empty();

                        if (response && response.length > 0) {
                            $.each(response, function(index, pertanyaan) {
                                pertanyaanContainer.append(
                                    `<div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            name="pertanyaan_ids[]"
                                            value="${pertanyaan.id}"
                                            id="tambah_pertanyaan_${pertanyaan.id}">
                                        <label class="form-check-label" for="tambah_pertanyaan_${pertanyaan.id}">
                                            ${pertanyaan.pertanyaan}
                                        </label>
                                    </div>`
                                );
                            });
                        } else {
                            pertanyaanContainer.html('<p>Tidak ada pertanyaan untuk penyakit ini.</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching pertanyaan:", error);
                        console.log(xhr.responseText);
                        alert('Terjadi kesalahan saat memuat pertanyaan. Silakan cek konsol browser.');
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
                        $('#editForm').attr('action', "{{ route('skrining.update', ':id') }}".replace(':id',
                            skriningId));

                        $('#edit_Nama_Petugas').val(response.Nama_Petugas);
                        $('#edit_NIK_Pasien').val(response.NIK_Pasien);
                        $('#edit_Nama_Pasien').val(response.Nama_Pasien);
                        $('#edit_Tanggal_Skrining').val(response.Tanggal_Skrining);
                        $('#edit_id_penyakit').val(response.id_penyakit);

                        // Fetch pertanyaan for the selected penyakit on edit
                        var penyakitId = response.id_penyakit; // Get penyakit ID from skrining data.

                        $.ajax({
                            url: "{{ route('skrining.get-pertanyaan-by-penyakit', ':id') }}".replace(
                                ':id', penyakitId),
                            method: 'GET',
                            success: function(pertanyaanResponse) {
                                var pertanyaanContainer = $('#edit_pertanyaan_list');
                                pertanyaanContainer.empty();

                                var relatedPertanyaanIds = response.pertanyaan.map(p => p.id);

                                if (pertanyaanResponse && pertanyaanResponse.length > 0) {
                                    $.each(pertanyaanResponse, function(index, pertanyaan) {
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
                                    pertanyaanContainer.html(
                                        '<p>Tidak ada pertanyaan untuk penyakit ini.</p>');
                                }
                                $('#editModal').modal('show');
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error("Error fetching pertanyaan:", errorThrown);
                                console.log("Response Text:", jqXHR.responseText);
                                alert(
                                    'Terjadi kesalahan saat memuat pertanyaan untuk penyakit yang dipilih saat edit.'
                                );
                            }
                        });


                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching skrining detail for edit:", error);
                        console.log(xhr.responseText);
                        alert('Terjadi kesalahan saat memuat data form skrining. Silakan cek konsol browser.');
                    }
                });
            });

            $('#editModal').on('hidden.bs.modal', function() {
                $('#editForm')[0].reset();
                $('#edit_pertanyaan_list').empty();
                $('#edit_pertanyaan_list').append('<p>Memuat pertanyaan...</p>');
            });

            // Event untuk tombol "Detail"
            $('.btn-detail').on('click', function() {
                var skriningId = $(this).data('id');

                $.get("{{ route('skrining.show', ':id') }}".replace(':id', skriningId), function(data) {
                    if (data) {
                        $('#detail_Nama_Petugas').text(data.Nama_Petugas);
                        $('#detail_NIK_Pasien').text(data.NIK_Pasien);
                        $('#detail_Nama_Pasien').text(data.Nama_Pasien);
                        $('#detail_Tanggal_Skrining').text(data.Tanggal_Skrining);
                        $('#detail_nama_penyakit').text(data.penyakit ? data.penyakit.nama_penyakit : '-');

                        $('#detail_daftar_pertanyaan').empty();
                        if (data.pertanyaan && data.pertanyaan.length > 0) {
                            $.each(data.pertanyaan, function(key, value) {
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
                var deleteUrl = "{{ route('skrining.destroy', ':id') }}".replace(':id', skriningId);

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

                url += (url.indexOf('?') > -1 ? '&' : '?') + 'search=' + searchValue;

                $.get(url, function(data) {
                    $('tbody').html($(data).find('tbody').html());
                });
            });

            // Edit Skrining
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
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat menyimpan perubahan.',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error updating skrining:", error);
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menyimpan perubahan. Silakan cek konsol browser.',
                        });
                    }
                });
            });

            // Delete Skrining
            $('#deleteForm').on('submit', function(e) {
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
                                text: 'Terjadi kesalahan saat menghapus data.',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error deleting skrining:", error);
                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menghapus data. Silakan cek konsol browser.',
                        });
                    }
                });
            });

            feather.replace();
        });
    </script>
@endsection
