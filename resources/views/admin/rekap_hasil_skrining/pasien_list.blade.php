@extends('partials.admin.main')

@section('title', 'Daftar Pasien Skrining')

@push('styles')
    {{-- Optional: jika ada CSS spesifik halaman --}}
    {{-- <style>
    /* CSS khusus untuk halaman ini */
</style> --}}
@endpush

@section('content')
    {{-- HAPUS ATAU KOMENTARI dd() INI SETELAH DEBUGGING --}}
    {{-- dd([
        'wilayah_di_view' => $wilayah,
        'namaFormSkrining_di_view' => $namaFormSkrining,
        'bulan_di_view' => $bulan,
        'tahun_di_view' => $tahun,
        'dataPasienSkrining_count' => $dataPasienSkrining->count(), // Menggunakan count() untuk Collection
        'dataPasienSkrining_first_item' => $dataPasienSkrining->first(), // Untuk melihat struktur 1 data pertama
    ]); --}}

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            Daftar Pasien Skrining "{{ $namaFormSkrining ?? 'Tidak Diketahui' }}"
                            @if ($wilayah && $wilayah !== 'Tidak Diketahui')
                                di Wilayah "{{ $wilayah }}"
                            @elseif($wilayah === 'Tidak Diketahui')
                                di Wilayah Tidak Diketahui
                            @endif
                            @if ($bulan && $tahun)
                                pada Bulan {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->isoFormat('MMMM') }}
                            @elseif($tahun)
                                pada Tahun {{ $tahun }}
                            @endif
                        </h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="{{ route('rekap_hasil_skrining.index') }}" class="btn btn-secondary">
                            <i data-feather="arrow-left"></i> Kembali ke Rekap
                        </a>
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
                                <h3 class="card-title">Tabel Pasien Skrining</h3>
                            </div>
                            <div class="card-body">
                                @if (session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                {{-- Tombol Debugging (biarkan tetap ada untuk sementara) --}}
                                {{-- <button id="checkDataTableStatus" class="btn btn-sm btn-outline-primary mb-3">Check DataTables Status</button> --}}

                                <div class="table-responsive">
                                    <table id="pasienSkriningTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>NIK</th>
                                                <th>Nama Pasien</th>
                                                {{-- <th>Nama Petugas</th> --}}
                                                {{-- <th>Nama Skrining</th> --}}
                                                <th>Tanggal</th>
                                                {{-- <th>Penyakit Terkait</th> --}} {{-- Di-komen karena tidak ada --}}
                                                {{-- <th>Aksi</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($dataPasienSkrining as $index => $skrining)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $skrining->pasien->NIK ?? 'N/A' }}</td>
                                                    <td>{{ $skrining->pasien->Nama_Pasien ?? 'N/A' }}</td>
                                                    {{-- <td>{{ $skrining->Nama_Petugas }}</td> --}}
                                                    {{-- <td>{{ $skrining->formSkrining->nama_skrining ?? 'N/A' }}</td> --}}
                                                    <td>{{ \Carbon\Carbon::parse($skrining->Tanggal_Skrining)->format('d-m-Y') }}
                                                    </td>
                                                    {{-- <td>{{ $skrining->formSkrining->penyakit->Nama_Penyakit ?? 'Tidak Diketahui' }}</td> --}}
                                                    {{-- <td>
                                                        <button class="btn btn-sm btn-info btn-detail-skrining"
                                                            data-skrining-id="{{ $skrining->id }}"
                                                            >Detail</button>
                                                    </td> --}}
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Tidak ada pasien ditemukan.</td>
                                                    {{-- Sesuaikan colspan menjadi 6 (jumlah kolom aktual) --}}
                                                </tr>
                                            @endforelse
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

    {{-- MODAL DETAIL RIWAYAT SKRINING (tetap sama) --}}
    <div class="modal fade" id="detailRiwayatModal" tabindex="-1" role="dialog" aria-labelledby="detailRiwayatModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailRiwayatModalLabel">Detail Riwayat Skrining</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 class="font-weight-bold">NIK Pasien:</h6>
                    <p id="detail_riwayat_NIK"></p>
                    <h6 class="font-weight-bold">Nama Pasien:</h6>
                    <p id="detail_riwayat_Nama_Pasien"></p>
                    <h6 class="font-weight-bold">Nama Petugas:</h6>
                    <p id="detail_riwayat_Nama_Petugas"></p>
                    <h6 class="font-weight-bold">Nama Skrining:</h6>
                    <p id="detail_riwayat_Nama_Skrining"></p>
                    <h6 class="font-weight-bold">Nama Penyakit:</h6>
                    <p id="detail_riwayat_Nama_Penyakit"></p>
                    <h6 class="font-weight-bold">Tanggal Skrining:</h6>
                    <p id="detail_riwayat_Tanggal_Skrining"></p>
                    <h6 class="font-weight-bold">Pertanyaan & Jawaban:</h6>
                    <div id="detail_riwayat_daftar_pertanyaan_formatted">
                        <p>Memuat pertanyaan...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var pasienTable; // Deklarasikan variabel untuk instance DataTable

            // Inisialisasi DataTables
            console.log("Mencoba inisialisasi DataTables...");
            if ($.fn.DataTable) {
                try {
                    pasienTable = $('#pasienSkriningTable').DataTable({
                        "paging": true,
                        "lengthChange": true,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
                        "responsive": true,
                        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"] // Aktifkan kembali
                    });

                    // Jika ada tombol, tambahkan ke DOM tabel
                    if (pasienTable.buttons) {
                        pasienTable.buttons().container().appendTo('#pasienSkriningTable_wrapper .col-md-6:eq(0)');
                    }

                    console.log("DataTables berhasil diinisialisasi!");
                } catch (e) {
                    console.error("Error saat inisialisasi DataTables:", e.message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Inisialisasi DataTables!',
                        text: 'Terjadi kesalahan saat menginisialisasi tabel: ' + e.message +
                            '. Cek console browser untuk detail.',
                    });
                }
            } else {
                console.error(
                    "jQuery DataTable is not a function. Pastikan jQuery dan DataTables dimuat dengan benar di main.blade.php."
                );
                Swal.fire({
                    icon: 'error',
                    title: 'DataTables Library Missing!',
                    text: 'Pastikan library DataTables dimuat dengan benar di main.blade.php.',
                });
            }

            // Handler untuk tombol detail skrining
            $(document).on('click', '.btn-detail-skrining', function() {
                var skriningId = $(this).data('skrining-id');
                var detailPertanyaanContainer = $('#detail_riwayat_daftar_pertanyaan_formatted');
                detailPertanyaanContainer.empty().append('<p>Memuat pertanyaan...</p>');

                $.ajax({
                    url: "{{ route('rekap_hasil_skrining.detail') }}",
                    method: 'GET',
                    data: {
                        skrining_id: skriningId
                    },
                    success: function(response) {
                        if (response.success && response.skriningDetail) {
                            var skriningData = response.skriningDetail;
                            $('#detail_riwayat_NIK').text(skriningData.NIK);
                            $('#detail_riwayat_Nama_Pasien').text(skriningData.Nama_Pasien);
                            $('#detail_riwayat_Nama_Petugas').text(skriningData.Nama_Petugas);
                            $('#detail_riwayat_Nama_Skrining').text(skriningData.Nama_Skrining);
                            $('#detail_riwayat_Nama_Penyakit').text(skriningData
                                .Nama_Penyakit_Terkait);
                            $('#detail_riwayat_Tanggal_Skrining').text(skriningData
                                .Tanggal_Skrining);

                            detailPertanyaanContainer.empty();
                            if (skriningData.detail_jawaban && skriningData.detail_jawaban
                                .length > 0) {
                                $.each(skriningData.detail_jawaban, function(key, value) {
                                    var answerContent = value.jawaban || '-';
                                    var questionItem = `
                                        <div class="form-group mb-3">
                                            <label>${value.pertanyaan}</label>
                                            <p class="form-control-plaintext border rounded p-2 bg-light">${answerContent}</p>
                                        </div>
                                    `;
                                    detailPertanyaanContainer.append(questionItem);
                                });
                            } else {
                                detailPertanyaanContainer.html(
                                    '<p>Tidak ada pertanyaan terkait.</p>');
                            }

                            $('#detailRiwayatModal').modal('show');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message ||
                                    'Data detail skrining tidak ditemukan.',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching detail skrining:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat mengambil detail skrining. Silakan coba lagi.',
                        });
                        detailPertanyaanContainer.html(
                            '<p class="text-danger">Gagal memuat detail pertanyaan.</p>');
                    }
                });
            });

            // Handler untuk tombol check DataTables status
            $('#checkDataTableStatus').on('click', function() {
                if ($.fn.DataTable) {
                    if ($.fn.DataTable.isDataTable('#pasienSkriningTable')) {
                        alert('DataTables sudah diinisialisasi untuk #pasienSkriningTable. (Client-side)');
                    } else {
                        alert('DataTables BELUM diinisialisasi untuk #pasienSkriningTable.');
                    }
                } else {
                    alert('Library DataTables ($.fn.DataTable) tidak ditemukan di jQuery.');
                }
            });
        });
    </script>
@endpush
