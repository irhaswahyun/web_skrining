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
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th>Pertanyaan</th>
                                            <th>Catatan</th>
                                            <th style="width: 15%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pertanyaans as $i => $data)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $data->pertanyaan }}</td>
                                                <td>{{ $data->catatan }}</td>
                                                <td>
                                                        <button class="btn btn-sm btn-primary btn-edit"
                                                            data-id="{{ $data->id }}"
                                                            data-pertanyaan="{{ $data->pertanyaan }}"
                                                            data-catatan="{{ $data->catatan }}">
                                                            Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger btn-delete"
                                                            data-pertanyaan-id="{{ $data->id }}"
                                                            data-pertanyaan-text="{{ $data->pertanyaan }}"
                                                            data-pertanyaan-catatan="{{ $data->catatan }}">
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

    {{-- Modal Tambah --}}
    <div class="modal fade" id="tambahPertanyaanModal" tabindex="-1" role="dialog" aria-labelledby="tambahPertanyaanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pertanyaan Baru</h5>
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
                        <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
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

    {{-- Modal Edit --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pertanyaan</h5>
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
                        <div class="form-group">
                            <label for="edit_catatan">Catatan</label>
                            <textarea class="form-control" id="edit_catatan" name="catatan" rows="3"></textarea>
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

    {{-- Modal Hapus --}}
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog"
        aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus pertanyaan: <span id="pertanyaanToDelete" class="font-weight-bold"></span>?
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
    $(document).ready(function () {
        $('#tambahPertanyaanBaru').click(function () {
            $('#tambahPertanyaanModal').modal('show');
        });

        $('.btn-edit').on('click', function () {
            var id = $(this).data('id');
            var pertanyaan = $(this).data('pertanyaan');
            var catatan = $(this).data('catatan');
            var url = "{{ route('daftar_pertanyaan.update', ':id') }}".replace(':id', id);

            $('#edit_pertanyaan').val(pertanyaan);
            $('#edit_catatan').val(catatan);
            $('#editForm').attr('action', url);
            $('#editModal').modal('show');
        });

        $('#editModal').on('hidden.bs.modal', function () {
            $('#editForm')[0].reset();
        });

        $('.btn-delete').on('click', function () {
            var id = $(this).data('pertanyaan-id');
            var text = $(this).data('pertanyaan-text');
            var url = "{{ route('daftar_pertanyaan.delete', ':id') }}".replace(':id', id);

            $('#pertanyaanToDelete').text(text);
            $('#deleteConfirmButton').attr('href', url);
            $('#deleteConfirmationModal').modal('show');
        });

        $('#searchPertanyaan').on('input', function () {
            var val = $(this).val();
            var url = "{{ route('daftar_pertanyaan.index') }}?search=" + val;

            $.get(url, function (data) {
                $('tbody').html($(data).find('tbody').html());
            });
        });

        feather.replace();
    });
</script>
@endsection
