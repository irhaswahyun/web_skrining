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
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Penyakit</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($penyakits as $penyakit)
                                        <tr>
                                            <td>{{ $penyakit->Nama_Penyakit }}</td>
                                            <td>
                                                <a href="{{ route('form.edit', $penyakit->id) }}" class="btn btn-sm btn-primary">Atur Pertanyaan</a>
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

    {{-- <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog"
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
                    Apakah Anda yakin ingin menghapus skrining ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <a href="#" id="deleteConfirmButton" class="btn btn-danger">Hapus</a>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

{{-- @section('scripts')
    <script>
        $(document).ready(function() {
            feather.replace();

            // Script untuk delete (jika Anda menampilkan daftar skrining)
            // $('.btn-delete').on('click', function() {
            //     var skriningId = $(this).data('skrining-id');
            //     var deleteUrl = "{{ route('form_skrining.delete', ':id') }}".replace(':id', skriningId);
            //     $('#deleteConfirmButton').attr('href', deleteUrl);
            //     $('#deleteConfirmationModal').modal('show');
            // });
        });
    </script>
@endsection --}}