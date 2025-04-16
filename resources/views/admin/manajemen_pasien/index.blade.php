@extends('partials.admin.main')

@section('title', 'Manajemen Pasien')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Manajemen Pasien</h3>
    <a href="{{ route('manajemen_pasien.create') }}" class="btn btn-outline-success">Tambah Baru</a>

    <form method="GET" action="{{ route('manajemen_pasien.index') }}" class="mb-3">
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
            @foreach ($pasiens as $i => $data)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $data->NIK }}</td>
                <td>{{ $data->Nama_Pasien }}</td>
                <td>{{ $data->Tanggal_Lahir }}</td>
                <td>{{ $data->Jenis_Kelamin }}</td>
                <td>{{ $data->Alamat }}</td>
                <td>{{ $data->No_telp }}</td>
                <td>
                    <a href="{{ route('manajemen_pasien.edit', $data->id) }}" class="btn btn-info">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
