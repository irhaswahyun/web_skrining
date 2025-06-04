@extends('partials.admin.main')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="row align-items-center mb-2">
                <div class="col">
                    <h2 class="h5 page-title">Selamat Datang, {{ Auth::user()->nama }}</h2>
                    <div class="col-auto">
                    </div>
                </div>
            </div>

            {{-- Bagian Kartu Statis --}}
            <div class="row">
                {{-- KARTU TOTAL PASIEN --}}
                <div class="col-6 col-lg-3 mb-4">
                    <div class="card shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center flex-column flex-md-row">
                            <div class="card-icon mr-3 mb-2 mb-md-0">
                                <i class="fa-solid fa-users"></i>
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
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                            <div>
                                <h6 class="text-uppercase text-muted mb-1">Total Jenis Skrining</h6>
                                <span class="h2 mb-0" id="jumlahListSkrining">{{ $jumlahFormSkrining }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KARTU TOTAL PASIEN SKRINING --}}
                {{-- <div class="col-6 col-lg-3 mb-4">
                    <div class="card shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center flex-column flex-md-row">
                            <div class="card-icon mr-3 mb-2 mb-md-0">
                                <i class="fa-solid fa-notes-medical"></i>
                            </div>
                            <div>
                                <h6 class="text-uppercase text-muted mb-1">Total Pasien Skrining</h6>
                                <span class="h2 mb-0" id="jumlahPasienSkrining">{{ $jumlahPasienSkrining }}</span>
                            </div>
                        </div>
                    </div>
                </div> --}}

                {{-- KARTU JUMLAH PENYAKIT YANG TERDAFTAR (Opsional) --}}
                <div class="col-6 col-lg-3 mb-4">
                    <div class="card shadow text-center">
                        <div class="card-body d-flex align-items-center justify-content-center flex-column flex-md-row">
                            <div class="card-icon mr-3 mb-2 mb-md-0">
                                <i class="fa-solid fa-viruses"></i>
                            </div>
                            <div>
                                <h6 class="text-uppercase text-muted mb-1">Total Penyakit Terdaftar</h6>
                                <span class="h2 mb-0" id="jumlahPenyakitTerdaftar">{{ $jumlahPenyakitTerdaftar ?? 0 }}</span>
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

            {{-- BAGIAN BARU: GRAFIK DINAMIS PER WILAYAH --}}
            <h5 class="mt-4 mb-3">Grafik Pasien Skrining per Wilayah</h5>
            <div class="row" id="dynamic-chart-containers">
                {{-- Kontainer grafik akan dimuat di sini oleh JavaScript --}}
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Bagian untuk Kartu Dinamis Skrining ---
        function getFontAwesomeIconForSkrining(skriningName) {
            const lowerCaseName = skriningName.toLowerCase();
            if (lowerCaseName.includes('jantung')) return 'fa-solid fa-heart-pulse';
            if (lowerCaseName.includes('obesitas')) return 'fa-solid fa-weight-scale';
            if (lowerCaseName.includes('paru')) return 'fa-solid fa-lungs';
            if (lowerCaseName.includes('stroke')) return 'fa-solid fa-brain';
            if (lowerCaseName.includes('diabetes')) return 'fa-solid fa-droplet';
            if (lowerCaseName.includes('hipertensi')) return 'fa-solid fa-chart-line';
            if (lowerCaseName.includes('stunting')) return 'fa-solid fa-child';
            if (lowerCaseName.includes('dbd') || lowerCaseName.includes('demam berdarah')) return 'fa-solid fa-mosquito';
            if (lowerCaseName.includes('tbc') || lowerCaseName.includes('tuberkulosis')) return 'fa-solid fa-lungs';
            if (lowerCaseName.includes('hiv') || lowerCaseName.includes('aids')) return 'fa-solid fa-virus-slash';
            if (lowerCaseName.includes('ibu hamil') || lowerCaseName.includes('bumil')) return 'fa-solid fa-person-pregnant';
            if (lowerCaseName.includes('lansia')) return 'fa-solid fa-person-cane';
            if (lowerCaseName.includes('remaja')) return 'fa-solid fa-user-graduate';
            if (lowerCaseName.includes('anak')) return 'fa-solid fa-child';
            return 'fa-solid fa-file-waveform';
        }

        const skriningCounts = @json($skriningCounts);
        const dynamicSkriningCardsContainer = document.getElementById('dynamic-skrining-cards');

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
            dynamicSkriningCardsContainer.insertAdjacentHTML('beforeend', cardHtml);
        });

        // --- BAGIAN BARU: Grafik Dinamis per Wilayah ---
        const wilayahList = @json($wilayahList);
        const jenisSkriningList = @json($jenisSkriningList);
        const dynamicChartContainers = document.getElementById('dynamic-chart-containers');

        const charts = {}; // Objek untuk menyimpan instance Chart.js

        wilayahList.forEach(wilayah => {
            // Buat kontainer untuk setiap grafik
            const chartCol = document.createElement('div');
            chartCol.className = 'col-md-6 mb-4';
            chartCol.innerHTML = `
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 text-uppercase">${wilayah}</h6>
                            <select class="form-control form-control-sm w-auto chart-skrining-filter" data-wilayah="${wilayah}">
                                @foreach($jenisSkriningList as $js)
                                    <option value="{{ $js }}">{{ $js }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- MENAMBAHKAN STYLE MAX-HEIGHT PADA CANVAS UNTUK MENGATUR PROPORSI --}}
                        <canvas id="chart-${wilayah.replace(/\s+/g, '-')}" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            `;
            dynamicChartContainers.appendChild(chartCol);

            // Inisialisasi Chart.js untuk setiap wilayah
            const ctx = document.getElementById(`chart-${wilayah.replace(/\s+/g, '-')}`).getContext('2d');
            charts[wilayah] = new Chart(ctx, {
                type: 'line', // Jenis grafik garis
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: `Jumlah Pasien (${jenisSkriningList[0] || 'Pilih Skrining'})`, // Perbaikan label awal
                        data: [], // Data akan diisi nanti via AJAX
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Jumlah Pasien'
                            },
                            ticks: {
                                display: true,
                                precision: 0 // Pastikan angka bulat
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Bulan'
                            },
                            ticks: {
                                display: true
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: `${wilayah} - ${jenisSkriningList[0] || 'Pilih Skrining'}` // Judul awal
                        }
                    }
                }
            });

            // Set default filter value dan panggil fungsi updateChart
            const defaultSkrining = jenisSkriningList[0];
            if (defaultSkrining) {
                const selectElement = chartCol.querySelector('.chart-skrining-filter');
                selectElement.value = defaultSkrining;
                updateChart(wilayah, defaultSkrining); // Panggil untuk memuat data awal
            }
        });

        // Event listener untuk setiap dropdown filter
        document.querySelectorAll('.chart-skrining-filter').forEach(selectElement => {
            selectElement.addEventListener('change', function() {
                const selectedWilayah = this.dataset.wilayah;
                const selectedSkrining = this.value;
                updateChart(selectedWilayah, selectedSkrining);
            });
        });

        // Fungsi untuk mengambil data dan memperbarui grafik
        function updateChart(wilayah, namaSkrining) {
            const chart = charts[wilayah];
            const year = new Date().getFullYear(); // Ambil tahun sekarang

            fetch("{{ route('chart.data') }}?wilayah=" + encodeURIComponent(wilayah) + "&nama_skrining=" + encodeURIComponent(namaSkrining) + "&year=" + year)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    chart.data.datasets[0].data = data.data;
                    chart.data.datasets[0].label = `Jumlah Pasien (${data.skriningName})`;
                    chart.options.plugins.title.text = `${data.wilayah} - ${data.skriningName}`; // Update judul chart
                    chart.update(); // Perbarui grafik
                })
                .catch(error => {
                    console.error('Error fetching chart data for ' + wilayah + ' - ' + namaSkrining + ':', error);
                    chart.data.datasets[0].data = []; // Kosongkan data jika ada error
                    chart.options.plugins.title.text = `Gagal memuat data untuk ${wilayah}: ${namaSkrining}`; // Pesan error lebih ramah
                    chart.data.datasets[0].label = `Jumlah Pasien (Gagal Memuat)`; // Update label legend
                    chart.update();
                });
        }
    });
</script>
@endsection