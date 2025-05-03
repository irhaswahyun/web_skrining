@extends('partials.admin.main')

@section('title', 'Form Skrining')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">

                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="font-weight-bold">FORM SKRINING</h4>
                    </div>
                    <div class="card-body">

                        <form action="{{ route('form_skrining.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="nama_skrining">Nama Skrining</label>
                                <input type="text" name="nama_skrining" class="form-control"
                                    placeholder="Masukkan nama skrining" required>
                            </div>

                            <div class="form-group">
                                <label for="penyakit_id">Nama Penyakit</label>
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

                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-success">Simpan</button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
