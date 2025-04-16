@extends('layouts.app')

@section('title', 'Skrining')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Skrining</h3>
    <a href="{{ route('skrining.create') }}" class="btn btn-outline-success">Tambah Baru</a>

    <form method="GET" action="{{ route('skrining.index') }}" class="mb-3">
        <input type="text" name="search" class="form-control" placeholder="Cari Data Pasien">
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama Pasien</th>
                <th>Skrining</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($skrinings as $i => $data)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $data->nik }}</td>
                <td>{{ $data->nama_pasien }}</td>
                <td>{{ $data->jenis_skrining }}</td>
                <td>
                    <a href="{{ route('skrining.edit', $data->id) }}" class="btn btn-info">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
