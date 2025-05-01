@extends('partials.admin.main')

@section('title', 'Manajemen Daftar Penyakit')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Manajemen Daftar Penyakit</h1>
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
                                            <input type="text" class="form-control" placeholder="Cari Penyakit" id="searchPenyakit"
                                                   name="search" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button id="tambahPenyakitBaru" class="btn btn-tambah-baru">
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
                                            <th><b>Nama Penyakit</b></th>
                                            <th><b>Aksi</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($daftarPenyakits as $i => $data)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $data->Nama_Penyakit }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary btn-edit"
                                                            data-id="{{ $data->id }}" data-nama="{{ $data->Nama_Penyakit }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm btn-delete"
                                                            data-penyakit-id="{{ $data->id }}"
                                                            data-penyakit-nama="{{ $data->Nama_Penyakit }}">
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

    <div class="modal fade" id="tambahPenyakitModal" tabindex="-1" role="dialog" aria-labelledby="tambahPenyakitModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPenyakitModalLabel">Tambah Penyakit Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="tambahPenyakitForm" method="POST" action="{{ route('daftar_penyakit.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_penyakit">Nama Penyakit</label>
                            <input type="text" class="form-control" id="nama_penyakit" name="Nama_Penyakit" required>
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

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Penyakit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_Nama_Penyakit">Nama Penyakit</label>
                            <input type="text" class="form-control" id="edit_Nama_Penyakit" name="Nama_Penyakit">
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
                    Apakah Anda yakin ingin menghapus penyakit <span id="penyakitToDelete"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <a href="#" id="deleteConfirmButton" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#tambahPenyakitBaru').click(function() {
                $('#tambahPenyakitModal').modal('show');
            });

            $('.btn-edit').on('click', function() {
                var penyakitId = $(this).data('id');
                var penyakitNama = $(this).data('nama');
                var editUrl = "{{ route('daftar_penyakit.update', ':id') }}".replace(':id', penyakitId);

                $('#edit_Nama_Penyakit').val(penyakitNama);
                $('#editForm').attr('action', editUrl);
                $('#editModal').modal('show');
            });

            $('#editModal').on('hidden.bs.modal', function() {
                $('#editForm')[0].reset();
            });

            // UNTUK HAPUS //
            $('.btn-delete').on('click', function() {
                var penyakitId = $(this).data('penyakit-id');
                var penyakitNama = $(this).data('penyakit-nama');
                var deleteUrl = "{{ route('daftar_penyakit.delete', ':id') }}".replace(':id', penyakitId);

                $('#penyakitToDelete').text(penyakitNama);
                $('#deleteConfirmButton').attr('href', deleteUrl);
                $('#deleteConfirmationModal').modal('show');
            });

            // UNTUK SEARCH //
            $('#searchPenyakit').on('input', function() {
                var searchValue = $(this).val();
                var url = "{{ route('daftar_penyakit.index') }}";

                // Tambahkan parameter 'search' ke URL
                url += (url.indexOf('?') > -1 ? '&' : '?') + 'search=' + searchValue;

                // Lakukan permintaan AJAX untuk memperbarui tabel
                $.get(url, function(data) {
                    // Ganti isi tabel dengan data yang diterima
                    $('tbody').html($(data).find('tbody').html());
                });
            });

            feather.replace();
        });
    </script>
@endsection