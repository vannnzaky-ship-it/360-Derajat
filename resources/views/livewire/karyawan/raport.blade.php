<div class="container-fluid p-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-0">Raport Kinerja</h1>
            <p class="text-muted mb-0">Ringkasan hasil penilaian kinerja Anda.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
             <select wire:model.live="selectedSemester" class="form-select form-select-sm" style="width: 200px;">
                @foreach ($listSemester as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                @endforeach
            </select>

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

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(empty($tableData))
        <div class="alert alert-warning text-center shadow-sm border-0">
            <i class="bi bi-exclamation-circle me-2"></i> 
            Belum ada data penilaian yang selesai untuk semester/siklus ini.
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow border-0 rounded-3 h-100">
                    <div class="card-body p-4">
                        <div class="card border-0 mb-4" style="background-color: var(--bs-tertiary-bg);">
                            <div class="card-body">
                                <h5 class="card-title text-custom-brown mb-1">{{ $namaUser }}</h5>
                                <p class="card-text text-muted mb-0 small">NIP: {{ $nipUser }}</p>
                                <p class="card-text text-muted mb-0 small">Jabatan: {{ $jabatanUser }}</p>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-center text-muted mb-3">Diagram Penilaian Kinerja (Skala 0-100)</h6>
                        <div wire:ignore>
                            <canvas id="kinerjaChart" style="min-height: 320px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm border-0 bg-custom-brown bg-gradient text-white mb-4">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-uppercase small opacity-75">Total Nilai Keseluruhan</h6>
                            <p class="card-text display-5 fw-bold mb-0">{{ number_format($finalScore) }}</p>
                        </div>
                        <i class="bi bi-star-fill opacity-25" style="font-size: 4rem;"></i>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-3 text-center">
                         <p class="mb-1 small text-muted">Status / Predikat:</p>
                         <p class="mb-0 fw-bold fs-5">
                            @if($finalScore >= 90) Sangat Baik
                            @elseif($finalScore >= 76) Baik
                            @elseif($finalScore >= 60) Cukup
                            @else Kurang @endif
                         </p>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                     <div class="card-header bg-transparent border-0 pt-3">
                         <h6 class="text-muted mb-0">Rincian Nilai per Kompetensi</h6>
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
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chartInstanceRaport = null; 

        // Fungsi Render Chart
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
                        backgroundColor: '#C38E44', // Warna Custom Brown
                        borderColor: '#a8793a',
                        borderWidth: 1,
                        borderRadius: 5,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y', // Bar Horizontal
                    scales: {
                        x: { beginAtZero: true, suggestedMax: 100 },
                        y: { grid: { display: false } }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.x + ' / 100';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Listener Livewire
        document.addEventListener('livewire:initialized', () => {
            // Ambil data pertama kali
            let data = @this.chartData;
            if (data && data.labels.length > 0) {
                renderRaportChart(data);
            }
        });

        // Update chart saat semester diganti (data berubah)
        document.addEventListener('livewire:updated', () => {
             let data = @this.chartData;
             if (data && data.labels.length > 0) {
                 renderRaportChart(data);
             }
        });
    </script>
    @endpush
</div>