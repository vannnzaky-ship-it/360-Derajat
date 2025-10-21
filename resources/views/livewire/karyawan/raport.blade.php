<div class="container-fluid p-4">
    
    <!-- Header Halaman -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-0">Raport Kinerja</h1>
            <p class="text-muted mb-0">Ringkasan hasil penilaian kinerja Anda.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
             <!-- Pemilih Semester -->
            <select wire:model.live="selectedSemester" class="form-select form-select-sm" style="width: 200px;">
                @foreach ($listSemester as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>

            <!-- Tombol Cetak/Export -->
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-printer me-1"></i> Cetak Raport
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><button class="dropdown-item" wire:click="export('pdf')"><i class="bi bi-file-pdf me-2"></i> PDF</button></li>
                    <li><button class="dropdown-item" wire:click="export('excel')"><i class="bi bi-file-earmark-excel me-2"></i> Excel (CSV)</button></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Alert untuk fitur export -->
    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- ==== KONTEN UTAMA ==== -->
    <div class="row g-4">
        <!-- Kolom Kiri (Diagram & Info User) -->
        <div class="col-lg-7">
            <div class="card shadow border-0 rounded-3 h-100">
                <div class="card-body p-4">
                    <!-- Informasi Pengguna (Kartu Teks) -->
                    <div class="card border-0 mb-4" style="background-color: var(--bs-tertiary-bg);">
                        <div class="card-body">
                            <h5 class="card-title text-custom-brown mb-1">{{ $namaUser }}</h5>
                            <p class="card-text text-muted mb-0 small">NIP: {{ $nipUser }}</p>
                            <p class="card-text text-muted mb-0 small">Jabatan: {{ $jabatanUser }}</p>
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- Diagram -->
                    <h6 class="text-center text-muted mb-3">Diagram Penilaian Kinerja</h6>
                    <div wire:ignore>
                        <canvas id="kinerjaChart" style="min-height: 320px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (Summary & Tabel Detail) -->
        <div class="col-lg-5">
            <!-- Kartu Total Nilai -->
            <div class="card shadow-sm border-0 bg-custom-brown bg-gradient text-white mb-4">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase small opacity-75">Total Nilai Keseluruhan</h6>
                        <p class="card-text display-5 fw-bold mb-0">{{ number_format(array_sum($tableData)) }}</p>
                    </div>
                    <i class="bi bi-star-fill opacity-25" style="font-size: 4rem;"></i>
                </div>
            </div>

            <!-- Kartu Ranking -->
             <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-3 text-center">
                     <p class="mb-1 small text-muted">Posisi Anda Semester Ini:</p>
                     <p class="mb-0 fw-bold fs-5">{{ $ranking }}</p>
                </div>
            </div>

            <!-- Tabel Rincian Nilai -->
            <div class="card shadow-sm border-0">
                 <div class="card-header bg-transparent border-0 pt-3">
                     <h6 class="text-muted mb-0">Rincian Nilai per Kategori</h6>
                 </div>
                 <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless table-striped mb-0">
                            <tbody>
                                @foreach ($tableData as $kategori => $nilai)
                                <tr>
                                    <td class="py-2">{{ $kategori }}</td>
                                    <td class="text-end fw-bold py-2">{{ number_format($nilai) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script untuk Chart (Tidak perlu diubah) -->
    @push('scripts')
    <script>
        let chartInstanceRaport = null; 

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
                        label: 'Nilai',
                        data: data.scores,
                        backgroundColor: '#C38E44',
                        borderColor: '#a8793a',
                        borderWidth: 1,
                        borderRadius: 5,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: { beginAtZero: true, suggestedMax: 100 },
                        y: { grid: { display: false } }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        function initializeChart() {
            let initialChartData = @this.chartData;
            if (initialChartData && initialChartData.labels) {
                renderRaportChart(initialChartData);
            }
        }

        document.addEventListener('livewire:initialized', initializeChart);
        document.addEventListener('livewire:navigated', initializeChart);
    </script>
    @endpush
</div>

