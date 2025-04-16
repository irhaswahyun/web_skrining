@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah Skrining</h3>

    <form action="{{ route('skrining.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="pasien" class="form-label">Pilih Pasien</label>
            <select name="pasien_id" class="form-select">
                <option value="">-- Pilih Pasien --</option>
                @foreach($pasiens as $pasien)
                    <option value="{{ $pasien->id }}">{{ $pasien->nama }} ({{ $pasien->nik }})</option>
                @endforeach
            </select>
            @error('pasien_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="jenis_skrining" class="form-label">Jenis Skrining</label>
            <select name="jenis_skrining" class="form-select">
                <option value="">-- Pilih Jenis --</option>
                <option value="Jantung">Jantung</option>
                <option value="Diabetes">Diabetes</option>
                <option value="Hipertensi">Hipertensi</option>
                <!-- Tambah sesuai kebutuhan -->
            </select>
            @error('jenis_skrining')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-success" type="submit">Simpan</button>
        <a href="{{ route('skrining.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
