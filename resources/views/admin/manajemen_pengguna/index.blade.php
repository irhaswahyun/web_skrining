@extends('partials.admin.main')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Manajemen Pengguna</h1>
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
                                            <input type="text" class="form-control" placeholder="Cari Pengguna" id="searchUser"
                                                name="search" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button id="tambahPenggunaBaru" class="btn btn-tambah-baru">
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
                                            <th><b>Nama</b></th>
                                            <th><b>Email</b></th>
                                            <th><b>Role</b></th>
                                            <th><b>Aksi</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $i => $user)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $user->nama }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->role }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary btn-edit"
                                                        data-id="{{ $user->id }}" data-nama="{{ $user->nama }}"
                                                        data-email="{{ $user->email }}"
                                                        data-role-id="{{ $user->id_role }}">
                                                        Edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm btn-delete"
                                                        data-user-id="{{ $user->id }}"
                                                        data-user-nama="{{ $user->nama }}">
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

    <div class="modal fade" id="tambahPenggunaModal" tabindex="-1" role="dialog"
        aria-labelledby="tambahPenggunaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPenggunaModalLabel">Tambah Pengguna Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="tambahPenggunaForm" method="POST" action="{{ route('pengguna.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="id_role">Role</label>
                            <select class="form-control" id="id_role" name="id_role" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"> {{ $role->Nama_Role }} </option>
                                @endforeach
                            </select>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_Nama">Nama</label>
                            <input type="text" class="form-control" id="edit_Nama" name="nama">
                        </div>
                        <div class="form-group">
                            <label for="edit_Email">Email</label>
                            <input type="email" class="form-control" id="edit_Email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="edit_Role">Role</label>
                            <select class="form-control" id="edit_Role" name="id_role">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"> {{ $role->Nama_Role }} </option>
                                @endforeach
                            </select>
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
                    Apakah Anda yakin ingin menghapus pengguna <span id="userToDelete"></span>?
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
            $('#tambahPenggunaBaru').click(function() {
                $('#tambahPenggunaModal').modal('show');
            });

            $('.btn-edit').on('click', function() {
                var userId = $(this).data('id');
                var userName = $(this).data('nama');
                var userEmail = $(this).data('email');
                var roleId = $(this).data('role-id');
                var editUrl = "{{ route('pengguna.update', ':id') }}".replace(':id', userId);

                $('#edit_Nama').val(userName);
                $('#edit_Email').val(userEmail);
                $('#edit_Role').val(roleId);
                $('#editForm').attr('action', editUrl);
                $('#editModal').modal('show');
            });

            $('#editModal').on('hidden.bs.modal', function() {
                $('#editForm')[0].reset();
            });

            // UNTUK HAPUS //
            $('.btn-delete').on('click', function() {
                var userId = $(this).data('user-id');
                var userName = $(this).data('user-nama');
                var deleteUrl = "{{ route('pengguna.delete', ':id') }}".replace(':id', userId);

                $('#userToDelete').text(userName);
                $('#deleteConfirmButton').attr('href', deleteUrl);
                $('#deleteConfirmationModal').modal('show');
            });

            // UNTUK SEARCH //
            $('#searchUser').on('input', function() {
                var searchValue = $(this).val();
                var url = "{{ route('pengguna.index') }}";

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