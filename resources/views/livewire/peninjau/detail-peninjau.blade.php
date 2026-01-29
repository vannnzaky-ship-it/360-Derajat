<div class="container-fluid p-4">
    <style>
        .bg-gradient-gold { background: linear-gradient(135deg, #C38E44 0%, #8E652E 100%); }
        .card-hover:hover { transform: translateY(-3px); transition: all 0.3s ease; box-shadow: 0 1rem 3rem rgba(0,0,0,.15)!important; }
        .avatar-circle { width: 54px; height: 54px; background-color: #fdf3e3; color: #C38E44; display: flex; align-items: center; justify-content: center; border-radius: 16px; font-weight: 700; font-size: 1.4rem; box-shadow: 0 2px 6px rgba(195, 142, 68, 0.15); }
        .trophy-watermark { position: absolute; bottom: -15px; right: -15px; opacity: 0.12 !important; color: white; z-index: 0; transform: rotate(15deg); pointer-events: none; }
        .card-content-z { position: relative; z-index: 2; }
        
        /* Filter Box Style */
        .header-filter-box { background-color: #fff; padding: 8px 15px; border-radius: 50px; border: 1px solid #e0e0e0; display: flex; align-items: center; gap: 10px; }
        .filter-select { border: none; font-weight: 600; font-size: 0.85rem; color: #444; background: transparent; cursor: pointer; }
        .filter-select:focus { outline: none; box-shadow: none; }

        /* Dark Mode Support */
        [data-bs-theme="dark"] .header-filter-box { background-color: #2c3034; border-color: #373b3e; }
        [data-bs-theme="dark"] .filter-select { color: #fff; }
        [data-bs-theme="dark"] .bg-light { background-color: #2c3034 !important; color: #ffffff !important; border: 1px solid #373b3e !important; }
        [data-bs-theme="dark"] .card { background-color: #212529 !important; border-color: #373b3e !important; }
        [data-bs-theme="dark"] h1, [data-bs-theme="dark"] h5, [data-bs-theme="dark"] h6, [data-bs-theme="dark"] .text-dark { color: #ffffff !important; }
        [data-bs-theme="dark"] .text-muted { color: #adb5bd !important; }
        [data-bs-theme="dark"] .bg-white { background-color: #212529 !important; }
        [data-bs-theme="dark"] .table td { background-color: #212529 !important; color: #ffffff !important; border-bottom: 1px solid #373b3e !important; }
        [data-bs-theme="dark"] text { fill: #adb5bd !important; }
    </style>
    
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div class="d-flex align-items-center">
             {{-- Tombol Kembali --}}
            <a href="{{ route('peninjau.laporan.ranking', $siklus->id) }}" class="btn btn-outline-secondary btn-sm me-3 rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-arrow-left"></i>
            </a>

            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Raport Pegawai</h1>
                <p class="text-secondary mb-0">Peninjau View Mode</p>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            {{-- FILTER JABATAN --}}
            <div class="header-filter-box shadow-sm">
                <i class="bi bi-briefcase text-warning"></i>
                <select wire:model.live="selectedJabatanId" class="filter-select">
                    <option value="all">Semua Jabatan (Gabungan)</option>
                    @foreach($listJabatanFull as $jbt)
                        <option value="{{ $jbt->id }}">{{ $jbt->nama_jabatan }}</option>
                    @endforeach
                </select>
            </div>

            {{-- INFO SIKLUS --}}
            <div class="header-filter-box shadow-sm bg-light text-muted">
                <i class="bi bi-calendar-check text-secondary"></i>
                <span style="font-size: 0.85rem; font-weight: 600;">{{ $siklus->tahun_ajaran }} {{ $siklus->semester }}</span>
            </div>

            {{-- DROPDOWN DOWNLOAD --}}
            <div class="dropdown">
                <button class="btn btn-sm btn-dark rounded-pill px-4 py-2 dropdown-toggle fw-semibold shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="height: 42px;">
                    <i class="bi bi-printer me-2"></i>Cetak
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-2">
                    <li><button class="dropdown-item rounded-3 py-2 mb-1" wire:click="exportPdf"><i class="bi bi-file-pdf me-2 text-danger"></i> Format PDF</button></li>
                    <li><button class="dropdown-item rounded-3 py-2" wire:click="exportExcel"><i class="bi bi-file-earmark-excel me-2 text-success"></i> Format Excel</button></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    @if(empty($tableData))
        <div class="d-flex flex-column align-items-center justify-content-center py-5 bg-white rounded-4 shadow-sm text-center" style="min-height: 450px;">
            <div class="bg-light rounded-circle p-4 mb-3">
                <i class="bi bi-journal-x text-muted" style="font-size: 3.5rem;"></i>
            </div>
            <h5 class="text-dark fw-bold mb-1">Data Tidak Ditemukan</h5>
            <p class="text-muted px-4">Pegawai ini belum memiliki nilai pada jabatan/siklus ini.</p>
        </div>
    @else
        <div class="row g-4">
            {{-- KOLOM KIRI --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 h-100 bg-white">
                    <div class="card-body p-4 p-md-5">
                        {{-- Info User --}}
                        <div class="d-flex align-items-center mb-5 p-3 rounded-4 border border-light bg-light bg-opacity-50">
                            <div class="avatar-circle me-3 flex-shrink-0 bg-white text-dark shadow-sm border" style="font-size: 1.2rem;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                    <span class="d-flex align-items-center"><i class="bi bi-upc-scan me-1"></i> NRP: {{ $user->pegawai->nip ?? '-' }}</span>
                                    <span class="d-flex align-items-center text-warning fw-bold">
                                        <i class="bi bi-briefcase me-1"></i> {{ $this->label_jabatan }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Chart --}}
                        <div>
                            <div class="d-flex justify-content-between align-items-end mb-3">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">Statistik Kompetensi</h6>
                                    <small class="text-muted">Visualisasi perbandingan nilai per kategori.</small>
                                </div>
                                <span class="badge bg-light text-secondary border rounded-pill px-3">Skala 0-100</span>
                            </div>
                            <div class="position-relative" style="height: 380px; width: 100%;" wire:ignore>
                                <canvas id="kinerjaChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="col-lg-4">
                {{-- Total Score Card --}}
                <div class="card shadow border-0 bg-gradient-gold text-white rounded-4 mb-4 overflow-hidden position-relative card-hover">
                    <div class="trophy-watermark"><i class="bi bi-trophy-fill" style="font-size: 7rem; transform: rotate(15deg); margin-right: -20px; margin-top: -10px;"></i></div>
                    <div class="card-body p-4 position-relative z-1 text-center py-5">
                        <h6 class="text-white-50 text-uppercase small fw-bold mb-3 ls-1">Total Nilai Akhir</h6>
                        <div class="d-flex justify-content-center align-items-baseline mb-3">
                            <h1 class="display-1 fw-bold mb-0" style="text-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                                {{ number_format($finalScore, 2) }}
                            </h1>
                        </div>
                        <div class="inline-block">
                            <span class="badge bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm fs-6">
                                <i class="bi bi-star-fill text-warning me-1"></i> {{ $mutu }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Ranking Card (BARU) --}}
                <div class="card shadow-sm border-0 rounded-4 bg-white mb-4 card-hover">
                    <div class="card-body p-3 text-center">
                        <h6 class="fw-bold text-secondary mb-2" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                            Peringkat Pegawai
                        </h6>
                        <div>
                            <span class="text-dark small fw-medium">
                                Urutan ke <span class="fw-bolder mx-1" style="color: #C38E44; font-size: 1.1rem;">{{ $ranking }}</span> 
                                dari {{ $totalPegawai }} Pegawai
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Tabel Rincian --}}
                <div class="card shadow-sm border-0 rounded-4 bg-white">
                    <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-list-check me-2 text-primary"></i>Rincian Nilai</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive px-2 pb-2">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                                <tbody>
                                    @foreach ($tableData as $kategori => $nilai)
                                    <tr>
                                        <td class="px-4 py-3 text-secondary border-bottom-0 fw-medium">{{ $kategori }}</td>
                                        <td class="px-4 py-3 text-end border-bottom-0">
                                            <span class="badge {{ $nilai < 60 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} rounded-pill px-3">
                                                {{ number_format($nilai, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 text-center pb-4 pt-0">
                        <small class="text-muted fst-italic" style="font-size: 0.75rem;">Generated by System</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chartInstance = null;
        function renderChart(data) {
            const ctx = document.getElementById('kinerjaChart');
            if (!ctx) return;
            if (chartInstance) chartInstance.destroy();
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Nilai (0-100)',
                        data: data.scores,
                        backgroundColor: '#C38E44',
                        borderColor: '#a8793a',
                        borderWidth: 1,
                        borderRadius: 8,
                        barPercentage: 0.6,
                        hoverBackgroundColor: '#8E652E'
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: 'rgba(50, 50, 50, 0.9)', padding: 12, cornerRadius: 8 }
                    },
                    scales: {
                        x: { beginAtZero: true, max: 100, grid: { color: '#f3f3f3', borderDash: [5, 5] } },
                        y: { grid: { display: false } }
                    },
                    layout: { padding: { left: 10, right: 20, top: 20, bottom: 20 } },
                    animation: { duration: 1500, easing: 'easeOutQuart' }
                }
            });
        }
        
        document.addEventListener('livewire:initialized', () => { 
            const initialData = @json($chartData);
            if(initialData.labels && initialData.labels.length > 0) renderChart(initialData); 
        });
        window.addEventListener('refreshChart', event => { renderChart(event.detail.data); });
    </script>
    @endpush
</div>