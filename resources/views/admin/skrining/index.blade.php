@extends('partials.admin.main')

@section('title', 'Data Skrining')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Data Skrining</h1>
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
                                            <input type="text" class="form-control" placeholder="Cari Skrining" id="searchSkrining"
                                                   name="search" autocomplete="off">
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
                                        <th><b>ID Daftar Pertanyaan</b></th>
                                        <th><b>Aksi</b></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($skrinings as $i => $skrining)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>{{ $skrining->Nama_Petugas }}</td>
                                            <td>{{ $skrining->NIK_Pasien }}</td>
                                            <td>{{ $skrining->Nama_Pasien }}</td>
                                            <td>{{ $skrining->Tanggal_Skrining->format('Y-m-d') }}</td>
                                            <td>{{ $skrining->penyakitPertanyaan->formSkrining->nama_skrining }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary btn-edit"
                                                        data-skrining-id="{{ $skrining->id }}"
                                                        data-skrining-nama-petugas="{{ $skrining->Nama_Petugas }}"
                                                        data-skrining-nik-pasien="{{ $skrining->NIK_Pasien }}"
                                                        data-skrining-nama-pasien="{{ $skrining->Nama_Pasien }}"
                                                        data-skrining-tanggal="{{ $skrining->Tanggal_Skrining->format('Y-m-d') }}"
                                                        data-skrining-pertanyaan="{{ $skrining->ID_DaftarPertanyaan }}">
                                                    Edit
                                                </button>
                                                <button class="btn btn-danger btn-sm btn-delete"
                                                        data-skrining-id="{{ $skrining->id }}"
                                                        data-skrining-nama-pasien="{{ $skrining->Nama_Pasien }}">
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

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Data Skrining</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_Nama_Petugas">Nama Petugas</label>
                            <input type="text" class="form-control" id="edit_Nama_Petugas" name="Nama_Petugas">
                        </div>
                        <div class="form-group">
                            <label for="edit_NIK_Pasien">NIK Pasien</label>
                            <input type="text" class="form-control" id="edit_NIK_Pasien" name="NIK_Pasien">
                        </div>
                        <div class="form-group">
                            <label for="edit_Nama_Pasien">Nama Pasien</label>
                            <input type="text" class="form-control" id="edit_Nama_Pasien" name="Nama_Pasien">
                        </div>
                        <div class="form-group">
                            <label for="edit_Tanggal_Skrining">Tanggal Skrining</label>
                            <input type="date" class="form-control" id="edit_Tanggal_Skrining" name="Tanggal_Skrining">
                        </div>
                        <div class="form-group">
                            <label for="edit_ID_penyakit_pertanyaan">Nama Skrining</label>
                            <select name="edit_ID_penyakit_pertanyaan" class="form-control" required>
                                <option value="">-- Pilih Nama Skrining --</option>
                                @foreach ($penyakit_pertanyaans as $penyakit_pertanyaan)
                                    <option value="{{ $penyakit_pertanyaan->id }}">{{ $penyakit_pertanyaan->Nama_Penyakit }}</option>
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
                    Apakah Anda yakin ingin menghapus skrining untuk pasien <span id="skriningToDelete"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <a href="#" id="deleteConfirmButton" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Tambah Data Skrining</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="createForm" method="POST" action="{{ route('skrining.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="create_Nama_Petugas">Nama Petugas</label>
                            <input type="text" class="form-control" id="create_Nama_Petugas" name="Nama_Petugas" required>
                        </div>
                        <div class="form-group">
                            <label for="create_NIK_Pasien">NIK Pasien</label>
                            <input type="text" class="form-control" id="create_NIK_Pasien" name="NIK_Pasien" required>
                        </div>
                        <div class="form-group">
                            <label for="create_Nama_Pasien">Nama Pasien</label>
                            <input type="text" class="form-control" id="create_Nama_Pasien" name="Nama_Pasien" required>
                        </div>
                        <div class="form-group">
                            <label for="create_Tanggal_Skrining">Tanggal Skrining</label>
                            <input type="date" class="form-control" id="create_Tanggal_Skrining" name="Tanggal_Skrining" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_ID_penyakit_pertanyaan">Nama Skrining</label>
                            <select name="edit_ID_penyakit_pertanyaan" class="form-control" required>
                                <option value="">-- Pilih Nama Skrining --</option>
                                @foreach ($penyakit_pertanyaans as $penyakit_pertanyaan)
                                    <option value="{{ $penyakit_pertanyaan->id }}">{{ $penyakit_pertanyaan->Nama_Penyakit }} - {{ $penyakit_pertanyaan->formSkrining->nama_skrining }}</option>
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#tambahSkriningBaru').on('click', function() {
                $('#createModal').modal('show');
            });

            $('#createModal').on('hidden.bs.modal', function() {
                $('#createForm')[0].reset();
            });

            $('.btn-edit').on('click', function() {
                var skriningId = $(this).data('skrining-id');
                var namaPetugas = $(this).data('skrining-nama-petugas');
                var nikPasien = $(this).data('skrining-nik-pasien');
                var namaPasien = $(this).data('skrining-nama-pasien');
                var tanggalSkrining = $(this).data('skrining-tanggal');
                var idPertanyaan = $(this).data('skrining-pertanyaan');

                var editUrl = "{{ route('skrining.update', ':id') }}".replace(':id', skriningId);

                $('#edit_Nama_Petugas').val(namaPetugas);
                $('#edit_NIK_Pasien').val(nikPasien);
                $('#edit_Nama_Pasien').val(namaPasien);
                $('#edit_Tanggal_Skrining').val(tanggalSkrining);
                $('#edit_ID_DaftarPertanyaan').val(idPertanyaan);
                $('#editForm').attr('action', editUrl);
                $('#editModal').modal('show');
            });

            $('#editModal').on('hidden.bs.modal', function() {
                $('#editForm')[0].reset();
            });

            // UNTUK HAPUS //
            $('.btn-delete').on('click', function() {
                var skriningId = $(this).data('skrining-id');
                var namaPasien = $(this).data('skrining-nama-pasien');
                var deleteUrl = "{{ route('skrining.delete', ':id') }}".replace(':id', skriningId);

                $('#skriningToDelete').text(namaPasien);
                $('#deleteConfirmButton').attr('href', deleteUrl);
                $('#deleteConfirmationModal').modal('show');
            });

            // UNTUK PENCARIAN //
            $('#searchSkrining').on('input', function() {
                var searchValue = $(this).val();
                var url = "{{ route('skrining.index') }}";

                url += (url.indexOf('?') > -1 ? '&' : '?') + 'search=' + searchValue;

                $.get(url, function(data) {
                    $('tbody').html($(data).find('tbody').html());
                });
            });

            feather.replace();
        });
    </script>
@endsection