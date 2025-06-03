@extends('partials.admin.main')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="row align-items-center mb-2">
                <div class="col">
                    <h2 class="h5 page-title">Welcome!</h2>
                </div>
                <div class="col-auto">
                    <form class="form-inline">
                        <div class="form-group d-none d-lg-inline">
                            <label for="reportrange" class="sr-only">Date Ranges</label>
                            <div id="reportrange" class="px-2 py-2 text-muted">
                                <span class="small"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-sm"><span class="fe fe-refresh-ccw fe-16 text-muted"></span></button>
                            <button type="button" class="btn btn-sm mr-2"><span class="fe fe-filter fe-16 text-muted"></span></button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Bagian Kartu Statis --}}
            <div class="row">
                {{-- KARTU TOTAL PASIEN --}}
                <div class="col-6 col-lg-3 mb-4">
                    <div class="card shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center flex-column flex-md-row">
                            <div class="card-icon mr-3 mb-2 mb-md-0">
                                <i class="fa-solid fa-users"></i> {{-- ICON FONT AWESOME --}}
                            </div>
                            <div>
                                <h6 class="text-uppercase text-muted mb-1">Total Pasien</h6>
                                <span class="h2 mb-0" id="jumlahPasien">{{ $jumlahPasien }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KARTU TOTAL JENIS SKRINING --}}
                <div class="col-6 col-lg-3 mb-4">
                    <div class="card shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center flex-column flex-md-row">
                            <div class="card-icon mr-3 mb-2 mb-md-0">
                                <i class="fa-solid fa-clipboard-list"></i> {{-- ICON FONT AWESOME --}}
                            </div>
                            <div>
                                <h6 class="text-uppercase text-muted mb-1">Total Skrining</h6>
                                <span class="h2 mb-0" id="jumlahListSkrining">{{ $jumlahFormSkrining }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KARTU TOTAL PASIEN SKRINING --}}
                <div class="col-6 col-lg-3 mb-4">
                    <div class="card shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center flex-column flex-md-row">
                            <div class="card-icon mr-3 mb-2 mb-md-0">
                                <i class="fa-solid fa-viruses"></i> {{-- ICON FONT AWESOME --}}
                            </div>
                            <div>
                                <h6 class="text-uppercase text-muted mb-1">Total Pasien Skrining</h6>
                                <span class="h2 mb-0" id="jumlahPasienSkrining">{{ $jumlahPasienSkrining }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div> {{-- End of row for static cards --}}

            {{-- Bagian Kontainer Kartu Dinamis per Jenis Skrining --}}
            <h5 class="mt-4 mb-3">Jumlah Pasien Berdasarkan Jenis Skrining</h5>
            <div class="row" id="dynamic-skrining-cards">
                {{-- Kartu-kartu dinamis akan dimuat di sini oleh JavaScript --}}
            </div>

            {{-- Bagian 'Balance' dan 'chartbox' Anda yang statis --}}
            <div class="mb-2 align-items-center">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row mt-1 align-items-center">
                            <div class="col-12 col-lg-4 text-left pl-4">
                                <p class="mb-1 small text-muted">Balance</p>
                                <span class="h3">$12,600</span>
                                <span class="small text-muted">+20%</span>
                                <span class="fa-solid fa-arrow-up text-success"></span>
                                <p class="text-muted mt-2"> Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui </p>
                            </div>
                            <div class="col-6 col-lg-2 text-center py-4">
                                <p class="mb-1 small text-muted">Today</p>
                                <span class="h3">$2600</span><br />
                                <span class="small text-muted">+20%</span>
                                <span class="fa-solid fa-arrow-up text-success"></span>
                            </div>
                            <div class="col-6 col-lg-2 text-center py-4 mb-2">
                                <p class="mb-1 small text-muted">Goal Value</p>
                                <span class="h3">$260</span><br />
                                <span class="small text-muted">+6%</span>
                                <span class="fa-solid fa-arrow-up text-success"></span>
                            </div>
                            <div class="col-6 col-lg-2 text-center py-4">
                                <p class="mb-1 small text-muted">Completions</p>
                                <span class="h3">26</span><br />
                                <span class="small text-muted">+20%</span>
                                <span class="fa-solid fa-arrow-up text-success"></span>
                            </div>
                            <div class="col-6 col-lg-2 text-center py-4">
                                <p class="mb-1 small text-muted">Conversion</p>
                                <span class="h3">6%</span><br />
                                <span class="small text-muted">-2%</span>
                                <span class="fa-solid fa-arrow-down text-danger"></span>
                            </div>
                        </div>
                        <div class="chartbox mr-4">
                            <div id="areaChart"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Fungsi untuk menentukan ikon Font Awesome berdasarkan nama skrining ---
        function getFontAwesomeIconForSkrining(skriningName) {
            const lowerCaseName = skriningName.toLowerCase();
            if (lowerCaseName.includes('jantung')) return 'fa-solid fa-heart-pulse'; // Atau fa-heartbeat
            if (lowerCaseName.includes('obesitas')) return 'fa-solid fa-weight-scale'; // Atau fa-person-walking
            if (lowerCaseName.includes('paru')) return 'fa-solid fa-lungs'; // Atau fa-wind
            if (lowerCaseName.includes('stroke')) return 'fa-solid fa-brain'; // Atau fa-house-medical-flag
            if (lowerCaseName.includes('diabetes')) return 'fa-solid fa-droplet'; // Simbol tetes darah
            if (lowerCaseName.includes('hipertensi')) return 'fa-solid fa-chart-line'; // Atau fa-heart-pulse
            if (lowerCaseName.includes('stunting')) return 'fa-solid fa-child'; // Atau fa-person-falling-boulder
            if (lowerCaseName.includes('dbd') || lowerCaseName.includes('demam berdarah')) return 'fa-solid fa-mosquito'; // Atau fa-thermometer
            if (lowerCaseName.includes('tbc') || lowerCaseName.includes('tuberkulosis')) return 'fa-solid fa-lungs'; // Atau fa-x-ray
            if (lowerCaseName.includes('hiv') || lowerCaseName.includes('aids')) return 'fa-solid fa-virus-slash'; // Atau fa-viruses
            if (lowerCaseName.includes('ibu hamil') || lowerCaseName.includes('bumil')) return 'fa-solid fa-person-pregnant'; // Atau fa-baby
            if (lowerCaseName.includes('lansia')) return 'fa-solid fa-person-cane'; // Atau fa-hand-holding-heart
            if (lowerCaseName.includes('remaja')) return 'fa-solid fa-user-graduate'; // Atau fa-child-reaching
            if (lowerCaseName.includes('anak')) return 'fa-solid fa-child'; // Atau fa-face-smile
            // Tambahkan lebih banyak kondisi if sesuai dengan nama skrining Anda
            return 'fa-solid fa-file-waveform'; // Ikon default jika tidak ada yang cocok
        }

        // --- Data yang diteruskan dari Laravel (Controller) ke JavaScript ---
        // @json($skriningCounts) akan mengubah koleksi PHP menjadi objek JSON yang bisa dipakai JS
        const skriningCounts = @json($skriningCounts);
        const dynamicSkriningCardsContainer = document.getElementById('dynamic-skrining-cards');

        // Iterasi setiap data skrining dan buat kartu dinamis
        skriningCounts.forEach(skrining => {
            const iconClass = getFontAwesomeIconForSkrining(skrining.nama_skrining);

            const cardHtml = `
                <div class="col-6 col-lg-3 mb-4">
                    <div class="card shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center flex-column flex-md-row">
                            <div class="card-icon mr-3 mb-2 mb-md-0">
                                <i class="${iconClass}"></i>
                            </div>
                            <div>
                                <h6 class="text-uppercase text-muted mb-1">${skrining.nama_skrining}</h6>
                                <span class="h2 mb-0">${skrining.patient_count}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            // Tambahkan HTML kartu ke dalam kontainer
            dynamicSkriningCardsContainer.insertAdjacentHTML('beforeend', cardHtml);
        });
    });
</script>
@endsection