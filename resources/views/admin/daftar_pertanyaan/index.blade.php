@extends('partials.admin.main')

@section('title', 'Manajemen Daftar Pertanyaan')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Manajemen Daftar Pertanyaan</h1>
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
                                            <input type="text" class="form-control" placeholder="Cari Pertanyaan" id="searchPertanyaan"
                                                   name="search" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button id="tambahPertanyaanBaru" class="btn btn-tambah-baru">
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
                                            <th><b>Pertanyaan</b></th>
                                            <th><b>Aksi</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pertanyaans as $i => $data)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $data->pertanyaan }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary btn-edit"
                                                            data-id="{{ $data->id }}" data-pertanyaan="{{ $data->pertanyaan }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm btn-delete"
                                                            data-pertanyaan-id="{{ $data->id }}"
                                                            data-pertanyaan-text="{{ $data->pertanyaan }}">
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

    <div class="modal fade" id="tambahPertanyaanModal" tabindex="-1" role="dialog" aria-labelledby="tambahPertanyaanModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPertanyaanModalLabel">Tambah Pertanyaan Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="tambahPertanyaanForm" method="POST" action="{{ route('daftar_pertanyaan.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="pertanyaan">Pertanyaan</label>
                            <textarea class="form-control" id="pertanyaan" name="pertanyaan" rows="3" required></textarea>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Pertanyaan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_pertanyaan">Pertanyaan</label>
                            <textarea class="form-control" id="edit_pertanyaan" name="pertanyaan" rows="3"></textarea>
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
                    Apakah Anda yakin ingin menghapus pertanyaan: <span id="pertanyaanToDelete"></span>?
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
            $('#tambahPertanyaanBaru').click(function() {
                $('#tambahPertanyaanModal').modal('show');
            });

            $('.btn-edit').on('click', function() {
                var pertanyaanId = $(this).data('id');
                var pertanyaanText = $(this).data('pertanyaan');
                var editUrl = "{{ route('daftar_pertanyaan.update', ':id') }}".replace(':id', pertanyaanId);

                $('#edit_pertanyaan').val(pertanyaanText);
                $('#editForm').attr('action', editUrl);
                $('#editModal').modal('show');
            });

            $('#editModal').on('hidden.bs.modal', function() {
                $('#editForm')[0].reset();
            });

            // UNTUK HAPUS //
            $('.btn-delete').on('click', function() {
                var pertanyaanId = $(this).data('pertanyaan-id');
                var pertanyaanText = $(this).data('pertanyaan-text');
                var deleteUrl = "{{ route('daftar_pertanyaan.delete', ':id') }}".replace(':id', pertanyaanId);

                $('#pertanyaanToDelete').text(pertanyaanText);
                $('#deleteConfirmButton').attr('href', deleteUrl);
                $('#deleteConfirmationModal').modal('show');
            });

            // UNTUK SEARCH //
            $('#searchPertanyaan').on('input', function() {
                var searchValue = $(this).val();
                var url = "{{ route('daftar_pertanyaan.index') }}";

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