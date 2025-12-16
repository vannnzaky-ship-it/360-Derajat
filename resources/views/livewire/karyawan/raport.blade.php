<div class="container-fluid p-4">

    <style>
        /* Gradient Emas/Cokelat yang mewah */
        .bg-gradient-gold {
            background: linear-gradient(135deg, #C38E44 0%, #8E652E 100%);
        }
        /* Efek hover pada card nilai */
        .card-hover:hover {
            transform: translateY(-3px);
            transition: all 0.3s ease;
            box-shadow: 0 1rem 3rem rgba(0,0,0,.15)!important;
        }
        /* Avatar inisial nama */
        .avatar-circle {
            width: 54px;
            height: 54px;
            background-color: #fdf3e3; /* Warna background muda */
            color: #C38E44; /* Warna teks emas */
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px; /* Rounded kotak modern */
            font-weight: 700;
            font-size: 1.4rem;
            box-shadow: 0 2px 6px rgba(195, 142, 68, 0.15);
        }
        /* Custom scrollbar untuk tabel jika panjang */
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #e0e0e0;
            border-radius: 4px;
        }
    </style>
    
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div class="d-flex align-items-center">
            <div class="avatar-circle me-3 flex-shrink-0">
                <i class="bi bi-clipboard-data-fill"></i>
            </div>
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Raport Kinerja</h1>
                <p class="text-secondary mb-0">Ringkasan hasil penilaian kinerja Anda.</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 bg-white p-2 rounded-pill shadow-sm border">
             <select wire:model.live="selectedSemester" class="form-select form-select-sm border-0 bg-light rounded-pill px-3 py-2" style="width: 220px; font-weight: 500; cursor: pointer;">
                @foreach ($listSemester as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                @endforeach
            </select>

            <div class="vr mx-1 opacity-25"></div>

            <div class="dropdown">
                <button class="btn btn-sm btn-dark rounded-pill px-4 py-2 dropdown-toggle fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-printer me-2"></i>Cetak
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-2">
                    <li><button class="dropdown-item rounded-3 py-2 mb-1" wire:click="export('pdf')"><i class="bi bi-file-pdf me-2 text-danger"></i> Export ke PDF</button></li>
                    <li><button class="dropdown-item rounded-3 py-2" wire:click="export('excel')"><i class="bi bi-file-earmark-excel me-2 text-success"></i> Export ke Excel</button></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Alert Error (Misal dipaksa cetak saat terkunci) --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i> 
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle-fill me-2 fs-5"></i> 
                <div>{{ session('info') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- LOGIKA TAMPILAN DATA KOSONG / TERKUNCI --}}
    @if(empty($tableData))
        <div class="d-flex flex-column align-items-center justify-content-center py-5 bg-white rounded-4 shadow-sm text-center" style="min-height: 400px;">
            <div class="bg-light rounded-circle p-4 mb-3">
                @if($isLocked)
                    {{-- Ikon Gembok jika Terkunci --}}
                    <i class="bi bi-lock-fill text-warning" style="font-size: 3.5rem;"></i>
                @else
                    {{-- Ikon Data Kosong Biasa --}}
                    <i class="bi bi-journal-x text-muted" style="font-size: 3.5rem;"></i>
                @endif
            </div>
            
            @if($isLocked)
                <h5 class="text-dark fw-bold">Hasil Penilaian Belum Dibuka</h5>
                <p class="text-muted mb-0">{{ $lockMessage }}</p>
            @else
                <h5 class="text-dark fw-bold">Data Belum Tersedia</h5>
                <p class="text-muted mb-0">Penilaian untuk semester/siklus ini belum selesai atau belum ada data.</p>
            @endif
        </div>
    @else
        {{-- TAMPILAN RAPORT (JIKA DATA ADA & TIDAK TERKUNCI) --}}
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 h-100 bg-white">
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="d-flex align-items-center mb-5 p-3 rounded-4 border border-light bg-light bg-opacity-50">
                            <div class="avatar-circle me-3 flex-shrink-0 bg-white text-dark shadow-sm border" style="font-size: 1.2rem;">
                                {{ substr($namaUser, 0, 1) }}
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold text-dark mb-1">{{ $namaUser }}</h5>
                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                    <span class="d-flex align-items-center"><i class="bi bi-upc-scan me-1"></i> NIP: {{ $nipUser }}</span>
                                    <span class="d-flex align-items-center"><i class="bi bi-briefcase me-1"></i> {{ $jabatanUser }}</span>
                                </div>
                            </div>
                        </div>

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

            <div class="col-lg-4">
                
                <div class="card shadow border-0 bg-gradient-gold text-white rounded-4 mb-4 overflow-hidden position-relative card-hover">
                    <div class="position-absolute top-0 end-0 p-3 opacity-25" style="pointer-events: none;">
                        <i class="bi bi-trophy-fill" style="font-size: 7rem; transform: rotate(15deg); margin-right: -20px; margin-top: -10px;"></i>
                    </div>
                    
                    <div class="card-body p-4 position-relative z-1 text-center py-5">
                        <h6 class="text-white-50 text-uppercase small fw-bold mb-3 ls-1">Total Nilai Akhir</h6>
                        <div class="d-flex justify-content-center align-items-baseline mb-3">
                            <h1 class="display-1 fw-bold mb-0" style="text-shadow: 0 4px 8px rgba(0,0,0,0.2);">{{ number_format($finalScore) }}</h1>
                        </div>
                        
                        <div class="inline-block">
                            <span class="badge bg-white text-dark rounded-pill px-4 py-2 fw-bold shadow-sm fs-6">
                                <i class="bi bi-star-fill text-warning me-1"></i>
                                @if($finalScore >= 90) Sangat Baik
                                @elseif($finalScore >= 76) Baik
                                @elseif($finalScore >= 60) Cukup
                                @else Kurang @endif
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-4 bg-white">
                    <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
                        <h6 class="fw-bold text-dark mb-0"><i class="bi bi-list-check me-2 text-primary"></i>Rincian Nilai</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive px-2 pb-2">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.95rem;">
                                <thead class="d-none">
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tableData as $kategori => $nilai)
                                    <tr>
                                        <td class="px-4 py-3 text-secondary border-bottom-0 fw-medium">
                                            {{ $kategori }}
                                        </td>
                                        <td class="px-4 py-3 text-end border-bottom-0">
                                            <span class="badge {{ $nilai < 60 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} rounded-pill px-3">
                                                {{ number_format($nilai) }}
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
        let chartInstanceRaport = null; 

        // Fungsi Render Chart (Logika Asli Tetap Sama)
        function renderRaportChart(data) {
            const ctxRaport = document.getElementById('kinerjaChart');
            if (!ctxRaport) return;

            if (chartInstanceRaport) {
                chartInstanceRaport.destroy();
            }

            chartInstanceRaport = new Chart(ctxRaport, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Nilai (0-100)',
                        data: data.scores,
                        backgroundColor: '#C38E44', // Warna Cokelat Emas Anda
                        borderColor: '#a8793a',
                        borderWidth: 1,
                        borderRadius: 8, // Sedikit lebih bulat
                        barPercentage: 0.6, // Ukuran bar proporsional
                        hoverBackgroundColor: '#8E652E'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y', // Bar Horizontal
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(50, 50, 50, 0.9)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { size: 13, family: "'Segoe UI', sans-serif" },
                            bodyFont: { size: 14, family: "'Segoe UI', sans-serif", weight: 'bold' },
                            callbacks: {
                                label: function(context) {
                                    return 'Nilai: ' + context.parsed.x;
                                }
                            }
                        }
                    },
                    scales: {
                        x: { 
                            beginAtZero: true, 
                            max: 100, // Memastikan max 100 agar diagram rapi
                            grid: {
                                color: '#f3f3f3',
                                borderDash: [5, 5]
                            },
                            ticks: { font: { size: 11 } }
                        },
                        y: { 
                            grid: { display: false },
                            ticks: { 
                                font: { size: 12, weight: '500' },
                                color: '#555'
                            }
                        }
                    },
                    layout: {
                        padding: { left: 10, right: 20, top: 20, bottom: 20 }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuart'
                    }
                }
            });
        }

        // Listener Livewire (Tetap Sama)
        document.addEventListener('livewire:initialized', () => {
            let data = @this.chartData;
            if (data && data.labels.length > 0) {
                renderRaportChart(data);
            }
        });

        // Update chart saat data berubah
        document.addEventListener('livewire:updated', () => {
             let data = @this.chartData;
             if (data && data.labels.length > 0) {
                 renderRaportChart(data);
             }
        });
    </script>
    @endpush
</div>