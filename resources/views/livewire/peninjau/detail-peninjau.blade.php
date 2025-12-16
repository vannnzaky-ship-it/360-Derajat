<div class="container-fluid p-4">

    {{-- Navigasi & Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            {{-- PERBEDAAN: Link Kembali ke Ranking PENINJAU --}}
            <a href="{{ route('peninjau.laporan.ranking', $siklus->id) }}" class="text-decoration-none text-muted small mb-2 d-block">
                <i class="bi bi-arrow-left"></i> Kembali ke Ranking
            </a>
            <h1 class="h3 mb-0 text-dark">Detail Penilaian Pegawai</h1>
            <p class="text-muted mb-0">Rincian hasil penilaian kinerja individual.</p>
        </div>
        
        {{-- Tombol Cetak (Sama Persis) --}}
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-printer-fill me-2"></i> Cetak Raport
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                <li>
                    <button class="dropdown-item py-2" wire:click="exportPdf">
                        <i class="bi bi-file-pdf text-danger me-2"></i> Format PDF
                    </button>
                </li>
                <li>
                    <button class="dropdown-item py-2" wire:click="exportExcel">
                        <i class="bi bi-file-earmark-excel text-success me-2"></i> Format Excel (CSV)
                    </button>
                </li>
            </ul>
        </div>
    </div>

    {{-- KONTEN RAPORT --}}
    <div class="row g-4">
        {{-- Kolom Kiri (Diagram & Info User) --}}
        <div class="col-lg-7">
            <div class="card shadow border-0 rounded-3 h-100">
                <div class="card-body p-4">
                    {{-- Informasi Pengguna --}}
                    <div class="card border-0 mb-4" style="background-color: var(--bs-tertiary-bg);">
                        <div class="card-body">
                            <h5 class="card-title text-custom-brown mb-1">{{ $user->name }}</h5>
                            <p class="card-text text-muted mb-0 small">NIP: {{ $user->pegawai->nip ?? '-' }}</p>
                            <p class="card-text text-muted mb-0 small">
                                Jabatan: {{ $user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-' }}
                            </p>
                            <p class="card-text text-muted mb-0 small">
                                Siklus: <span class="fw-bold">{{ $siklus->tahun_ajaran }} {{ $siklus->semester }}</span>
                            </p>
                        </div>
                    </div>

                    <hr class="my-4">
                    {{-- Diagram --}}
                    <h6 class="text-center text-muted mb-3">Diagram Penilaian Kinerja (Skala 0-100)</h6>
                    <div wire:ignore>
                        <canvas id="kinerjaChart" style="min-height: 320px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan (Summary & Tabel Detail) --}}
        <div class="col-lg-5">
            {{-- Kartu Total Nilai --}}
            <div class="card shadow-sm border-0 bg-custom-brown bg-gradient text-white mb-4" style="background-color: #c38e44;"> {{-- Force Color jika class bg-custom-brown blm ada --}}
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase small opacity-75">Total Nilai Keseluruhan</h6>
                        <p class="card-text display-5 fw-bold mb-0">{{ number_format((float)$hasilPenilaian['skor_akhir']) }}</p>
                    </div>
                    <i class="bi bi-star-fill opacity-25" style="font-size: 4rem;"></i>
                </div>
            </div>

            {{-- Kartu Predikat --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-3 text-center">
                     <p class="mb-1 small text-muted">Status / Predikat:</p>
                     <p class="mb-0 fw-bold fs-5">
                        @php 
                            // Pastikan konversi ke float agar aman
                            $score = (float) str_replace(',', '', $hasilPenilaian['skor_akhir']); 
                        @endphp
                        
                        @if($score >= 90) <span class="text-success">Sangat Baik</span>
                        @elseif($score >= 76) <span class="text-primary">Baik</span>
                        @elseif($score >= 60) <span class="text-warning">Cukup</span>
                        @else <span class="text-danger">Kurang</span> @endif
                     </p>
                </div>
            </div>

            {{-- Tabel Rincian Nilai --}}
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

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        const ctxRaport = document.getElementById('kinerjaChart');
        
        // Siapkan data dari PHP (Livewire)
        const labels = {!! json_encode(array_keys($tableData)) !!};
        const scores = {!! json_encode(array_values($tableData)) !!};

        if (ctxRaport && labels.length > 0) {
            new Chart(ctxRaport, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Nilai (0-100)',
                        data: scores,
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
    });
</script>
@endpush