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
                                            <th><b>Nama Penyakit</b></th>
                                            <th><b>Jumlah Pertanyaan</b></th>
                                            <th><b>Aksi</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($formSkrinings as $i => $skrining)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $skrining->nama_skrining }}</td>
                                                <td>{{ $skrining->penyakit->Nama_Penyakit ?? '-' }}</td>
                                                <td>{{ $skrining->pertanyaans_count }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info btn-detail"
                                                        data-id="{{ $skrining->id }}">Detail</button>
                                                    <button class="btn btn-sm btn-primary btn-edit"
                                                        data-id="{{ $skrining->id }}"
                                                        data-nama="{{ $skrining->nama_skrining }}"
                                                        data-penyakit-id="{{ $skrining->id_daftar_penyakit }}">
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
    </div>
    </section>
    </div>

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
                        </div>
                        <div class="form-group">
                            <label for="id_daftar_penyakit">Nama Penyakit</label>
                            <select name="id_daftar_penyakit" class="form-control" required>
                                <option value="">-- Pilih Penyakit --</option>
                                @foreach ($penyakits as $penyakit)
                                    <option value="{{ $penyakit->id }}">{{ $penyakit->Nama_Penyakit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pertanyaan</label>
                            <div class="form-check">
                                @foreach ($pertanyaans as $pertanyaan)
                                    <div>
                                        <input type="checkbox" name="pertanyaan_ids[]" value="{{ $pertanyaan->id }}"
                                            class="form-check-input">
                                        <label class="form-check-label">{{ $pertanyaan->pertanyaan }}</label>
                                    </div>
                                @endforeach
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
                        </div>
                        <div class="form-group">
                            <label for="edit_id_daftar_penyakit">Nama Penyakit</label>
                            <select class="form-control" id="edit_id_daftar_penyakit" name="id_daftar_penyakit" required>
                                <option value="">-- Pilih Penyakit --</option>
                                @foreach ($penyakits as $penyakit)
                                    <option value="{{ $penyakit->id }}">{{ $penyakit->Nama_Penyakit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pertanyaan</label>
                            <div class="form-check" id="edit_pertanyaan_list">
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
                    <h6 class="font-weight-bold">Nama Penyakit:</h6>
                    <p id="detail_nama_penyakit"></p>
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
                    {{-- <a href="#" id="deleteConfirmButton" class="btn btn-danger">Hapus</a> --}}
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
    <script>
        $(document).ready(function() {
            $('#tambahSkriningBaru').click(function() {
                $('#tambahSkriningModal').modal('show');
            });

            $('.btn-edit').on('click', function() {
                // Ambil data dari atribut tombol
                var id = $(this).data('id');
                var nama = $(this).data('nama');
                var penyakitId = $(this).data('penyakit-id');

                // Set form action
                $('#editForm').attr('action', '/skrining/' + id);

                // Set nilai input
                $('#edit_nama_skrining').val(nama);
                $('#edit_id_daftar_penyakit').val(penyakitId);

                // Optional: Kosongkan dan isi ulang pertanyaan jika perlu

                // Tampilkan modal
                $('#editModal').modal('show');
            });

            $('#editModal').on('hidden.bs.modal', function() {
                $('#editForm')[0].reset();
                $('#edit_pertanyaan_list').empty();
            });

            $('.btn-detail').on('click', function() {
    var skriningId = $(this).data('id');

    // Gunakan route yang benar untuk detail
    $.get("{{ route('form_skrining.detail', ':id') }}".replace(':id', skriningId), function(data) {
        // Pastikan data yang diterima sesuai dengan struktur yang diinginkan
        if (data.formSkrining) {
            // Mengisi modal dengan data
            $('#detail_nama_skrining').text(data.formSkrining.nama_skrining);
            $('#detail_nama_penyakit').text(data.formSkrining.penyakit ? data.formSkrining.penyakit.Nama_Penyakit : '-');
            
            // Mengisi daftar pertanyaan
            $('#detail_daftar_pertanyaan').empty();
            if (data.formSkrining.pertanyaan && data.formSkrining.pertanyaan.length > 0) {
                $.each(data.formSkrining.pertanyaan, function(key, value) {
                    $('#detail_daftar_pertanyaan').append(`<li>${value.pertanyaan}</li>`);
                });
            } else {
                $('#detail_daftar_pertanyaan').append('<li>Tidak ada pertanyaan terkait.</li>');
            }
            
            // Menampilkan modal
            $('#detailModal').modal('show');
        } else {
            alert('Data tidak ditemukan');
        }
    }).fail(function() {
        alert('Terjadi kesalahan saat memuat data.');
    });
});


            $('.btn-delete').on('click', function() {
                var skriningId = $(this).data('skrining-id');
                var skriningNama = $(this).data('skrining-nama');
                var deleteUrl = "{{ route('form_skrining.delete', ':id') }}".replace(':id',
                    skriningId); // Menggunakan route delete

                $('#skriningToDelete').text(skriningNama);
                $('#deleteForm').attr('action', deleteUrl);
                $('#deleteConfirmationModal').modal('show');
            });

            $('#searchSkrining').on('input', function() {
                var searchValue = $(this).val();
                var url = "{{ route('form_skrining.index') }}";

                url += (url.indexOf('?') > -1 ? '&' : '?') + 'search=' + searchValue;

                $.get(url, function(data) {
                    $('tbody').html($(data).find('tbody').html());
                });
            });

            feather.replace();
        });
    </script>
@endsection
