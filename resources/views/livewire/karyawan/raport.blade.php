<div class="container-fluid p-4">
    <style>
        /* --- Default Light Mode --- */
        .bg-gradient-gold { background: linear-gradient(135deg, #C38E44 0%, #8E652E 100%); }
        .avatar-circle { 
            width: 54px; height: 54px; 
            background-color: #fdf3e3; color: #C38E44; 
            display: flex; align-items: center; justify-content: center; 
            border-radius: 16px; font-weight: 700; font-size: 1.4rem; 
            box-shadow: 0 2px 6px rgba(195, 142, 68, 0.15); 
        }
        .trophy-watermark { 
            position: absolute; bottom: -15px; right: -15px; 
            opacity: 0.12 !important; color: white; z-index: 0; 
            transform: rotate(15deg); pointer-events: none; 
        }
        .card-content-z { position: relative; z-index: 2; }
        
        .header-filter-box { 
            background-color: #fff; padding: 8px 15px; 
            border-radius: 50px; border: 1px solid #e0e0e0; 
            display: flex; align-items: center; gap: 10px; 
        }
        .filter-select { 
            border: none; font-weight: 600; font-size: 0.85rem; 
            color: #444; background: transparent; cursor: pointer; 
        }
        .filter-select:focus { outline: none; box-shadow: none; }

        /* ========================================= */
        /* DARK MODE FIXES                           */
        /* ========================================= */
        
        /* 1. Global Backgrounds & Text */
        [data-bs-theme="dark"] .bg-white {
            background-color: #1e1e1e !important;
            color: #e0e0e0 !important;
        }
        [data-bs-theme="dark"] .text-dark { color: #f8f9fa !important; }
        [data-bs-theme="dark"] .text-secondary { color: #adb5bd !important; }
        [data-bs-theme="dark"] .text-muted { color: #999 !important; }

        /* 2. Card Styles */
        [data-bs-theme="dark"] .card {
            background-color: #1e1e1e !important;
            border-color: #333 !important;
        }
        
        /* 3. Filter Box & Select */
        [data-bs-theme="dark"] .header-filter-box {
            background-color: #2c2c2c;
            border-color: #444;
        }
        [data-bs-theme="dark"] .filter-select {
            color: #e0e0e0;
            background-color: transparent;
        }
        [data-bs-theme="dark"] .filter-select option {
            background-color: #2c2c2c; /* Background Opsi Dropdown */
            color: #e0e0e0;
        }

        /* 4. Avatar & Icons */
        [data-bs-theme="dark"] .avatar-circle {
            background-color: rgba(195, 142, 68, 0.2); /* Gold Transparan */
            color: #C38E44;
            box-shadow: none;
            border-color: #444 !important;
        }
        
        /* 5. Tabel Detail Kompetensi */
        [data-bs-theme="dark"] .table {
            color: #e0e0e0 !important;
        }
        [data-bs-theme="dark"] .table td {
            border-bottom-color: #333 !important;
        }
        [data-bs-theme="dark"] .border-bottom {
            border-bottom-color: #333 !important;
        }
        
        /* 6. Dropdown Menu (Cetak) */
        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #2c2c2c;
            border-color: #444;
        }
        [data-bs-theme="dark"] .dropdown-item {
            color: #e0e0e0;
        }
        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #3a3a3a;
        }
    </style>
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="avatar-circle me-3 flex-shrink-0 border"><i class="bi bi-person-badge-fill"></i></div>
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Raport Kinerja</h1>
                <p class="text-secondary mb-0">Rincian hasil penilaian kinerja mandiri.</p>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            {{-- Filter Jabatan --}}
            <div class="header-filter-box shadow-sm">
                <i class="bi bi-briefcase text-warning"></i>
                <select wire:model.live="selectedJabatanId" class="filter-select">
                    <option value="all">Semua Jabatan (Gabungan)</option>
                    @foreach($listJabatanFull as $jbt)
                        <option value="{{ $jbt->id }}">{{ $jbt->nama_jabatan }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Semester --}}
            <div class="header-filter-box shadow-sm">
                <i class="bi bi-calendar-event text-warning"></i>
                <select wire:model.live="selectedSemester" class="filter-select">
                    @foreach ($listSemester as $id => $label)
                        <option value="{{ $id }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Dropdown Cetak --}}
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

    @if(empty($tableData))
        <div class="d-flex flex-column align-items-center justify-content-center py-5 bg-white rounded-4 shadow-sm text-center" style="min-height: 450px;">
            <div class="bg-light rounded-circle p-4 mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                @if($isLocked) <i class="bi bi-lock-fill text-warning" style="font-size: 2.5rem;"></i>
                @else <i class="bi bi-journal-x text-muted" style="font-size: 2.5rem;"></i> @endif
            </div>
            <h5 class="text-dark fw-bold mb-1">{{ $isLocked ? 'Hasil Belum Tersedia' : 'Data Tidak Ditemukan' }}</h5>
            <p class="text-muted px-4" style="max-width: 500px;">{{ $isLocked ? $lockMessage : 'Data untuk pilihan ini belum tersedia.' }}</p>
        </div>
    @else
        <div class="row g-4">
            {{-- KOLOM KIRI (INFO & CHART) --}}
            <div class="col-lg-8">
                {{-- Kartu Profil --}}
                <div class="card shadow-sm border-0 rounded-4 bg-white mb-4">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="avatar-circle me-3 flex-shrink-0 bg-white shadow-sm border" style="font-size: 1.2rem;">{{ strtoupper(substr($namaUser, 0, 1)) }}</div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold text-dark mb-0">{{ $namaUser }}</h5>
                            <div class="small mt-1">
                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                    <span class="text-muted"><i class="bi bi-upc-scan me-1"></i> NRP: {{ $nipUser }}</span>
                                    <span class="text-muted opacity-50">|</span>
                                    <span class="d-flex align-items-center fw-bold text-warning text-uppercase">
                                        <i class="bi bi-briefcase me-1"></i> {{ $this->label_jabatan }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kartu Chart --}}
                <div class="card shadow-sm border-0 rounded-4 bg-white">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark mb-4">Statistik Kompetensi - {{ $this->label_jabatan }}</h6>
                        <div style="height: 380px;" wire:ignore><canvas id="kinerjaChart"></canvas></div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN (SKOR & RANKING) --}}
            <div class="col-lg-4">
                {{-- Kartu Total Skor (Gold Gradient) --}}
                <div class="card shadow border-0 bg-gradient-gold text-white rounded-4 mb-4 py-5 text-center position-relative overflow-hidden card-hover">
                    <div class="trophy-watermark"><i class="bi bi-trophy-fill" style="font-size: 10rem;"></i></div>
                    <div class="card-content-z">
                        <h6 class="text-white-50 text-uppercase small fw-bold mb-2">Total Skor</h6>
                        <h1 class="display-2 fw-bold mb-3" style="text-shadow: 0 4px 10px rgba(0,0,0,0.2);">{{ number_format($finalScore, 2) }}</h1>
                        <span class="badge bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm fs-6">
                            <i class="bi bi-star-fill text-warning me-1"></i> {{ $mutu }}
                        </span>
                    </div>
                </div>

                {{-- Kartu Peringkat (Minimalis) --}}
                <div class="card shadow-sm border-0 rounded-4 bg-white mb-4 card-hover">
                    <div class="card-body p-3 text-center">
                        <h6 class="fw-bold text-secondary mb-2" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
                            Peringkat Anda
                        </h6>
                        <div>
                            <span class="text-dark small fw-medium">
                                Urutan ke 
                                <span class="fw-bolder mx-1" style="color: #C38E44; font-size: 1.1rem;">{{ $ranking }}</span> 
                                dari {{ $totalPegawai }} Pegawai
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Kartu Detail Kompetensi (Tabel Kecil) --}}
                <div class="card shadow-sm border-0 rounded-4 bg-white">
                    <div class="card-body p-0 pb-2">
                        <div class="p-4 border-bottom"><h6 class="fw-bold text-dark mb-0">Detail Kompetensi</h6></div>
                        <table class="table table-hover align-middle mb-0">
                            <tbody>
                                @foreach ($tableData as $kategori => $nilai)
                                <tr>
                                    <td class="px-4 py-3 text-secondary small fw-medium">{{ $kategori }}</td>
                                    <td class="px-4 py-3 text-end"><span class="badge {{ $nilai < 60 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} rounded-pill px-3">{{ number_format($nilai, 2) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
            
            // Cek Mode Gelap untuk warna grid & teks chart
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const gridColor = isDark ? '#444' : '#f3f3f3';
            const textColor = isDark ? '#ccc' : '#666';

            if (chartInstance) chartInstance.destroy();
            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{ label: 'Skor', data: data.scores, backgroundColor: '#C38E44', borderRadius: 8, barPercentage: 0.5 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                    scales: { 
                        x: { 
                            beginAtZero: true, max: 100, 
                            grid: { color: gridColor },
                            ticks: { color: textColor }
                        }, 
                        y: { 
                            grid: { display: false },
                            ticks: { color: textColor }
                        } 
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
        document.addEventListener('livewire:initialized', () => { renderChart(@json($chartData)); });
        window.addEventListener('refreshChart', event => { renderChart(event.detail.data); });
    </script>
    @endpush
</div>