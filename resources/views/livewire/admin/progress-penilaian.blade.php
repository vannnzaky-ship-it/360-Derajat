<div class="container-fluid p-4">
    
    {{-- CSS CUSTOM --}}
    <style>
        .text-custom { color: #c38e44 !important; }
        .bg-custom { background-color: #c38e44 !important; color: white; }
        
        /* --- RESPONSIVE TABLE --- */
        @media (max-width: 767px) {
            .table-responsive thead { display: none; }
            .table-responsive tbody tr {
                display: block; margin-bottom: 1rem; background-color: #fff;
                border: 1px solid rgba(0,0,0,0.1); border-radius: 12px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05); padding: 15px;
            }
            .table-responsive tbody td { display: block; width: 100%; border: none !important; padding: 5px 0; }
            .table-responsive tbody td:nth-child(1) { display: none; }
            .table-responsive tbody td:nth-child(2) { font-size: 1.1rem; margin-bottom: 5px; border-bottom: 1px solid rgba(0,0,0,0.05) !important; padding-bottom: 10px; }
            .table-responsive tbody td:nth-child(3) { color: #6c757d; font-size: 0.85rem; margin-bottom: 10px; }
            .table-responsive tbody td:nth-child(3)::before { content: "Jabatan: "; font-weight: bold; color: #333; }
            .table-responsive tbody td:nth-child(4) { margin-bottom: 10px; }
            .table-responsive tbody td:nth-child(5), .table-responsive tbody td:nth-child(6) { display: inline-block; width: auto; margin-top: 10px; }
            .table-responsive tbody td:nth-child(6) { float: right; }
        }

        /* --- DARK MODE --- */
        [data-bs-theme="dark"] .form-control, [data-bs-theme="dark"] .input-group-text { background-color: #2b3035 !important; border-color: #373b3e !important; color: #fff !important; }
        [data-bs-theme="dark"] .card { background-color: #212529 !important; border-color: #373b3e !important; }
        [data-bs-theme="dark"] .table tbody tr { background-color: #212529 !important; }
        [data-bs-theme="dark"] .table-responsive tbody tr { background-color: #212529 !important; border-color: #373b3e !important; }
    </style>

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Monitoring Proses Penilai</h2>
            @if($activeSiklus)
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">Periode: <strong class="text-custom">{{ $activeSiklus->tahun_ajaran }} {{ $activeSiklus->semester }}</strong></span>
                </div>
            @endif
        </div>

        {{-- TOMBOL UNDUH PDF (Hanya jika data ada & sesi aktif/expired tetap bisa download report) --}}
        @if($activeSession && count($dataProgress) > 0)
            <div>
                <button wire:click="downloadPdf" class="btn btn-danger btn-sm shadow-sm rounded-pill px-3">
                    <span wire:loading.remove wire:target="downloadPdf">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Unduh Laporan PDF
                    </span>
                    <span wire:loading wire:target="downloadPdf">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Generating...
                    </span>
                </button>
            </div>
        @endif
    </div>

    {{-- LOGIKA TAMPILAN: Sesi Tidak Ada / Expired (Hanya Info) --}}
    {{-- Namun, tabel tetap ditampilkan jika Expired agar admin bisa melihat hasil akhir, kecuali user mau menyembunyikannya --}}
    @if(!$activeSession)
        
        <div class="alert alert-light border shadow-sm rounded-4 p-5 text-center">
            <div class="mb-3">
                <div class="spinner-grow text-warning" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <h4 class="fw-bold text-dark">Belum Ada Sesi Penilaian</h4>
            <p class="text-muted mb-0 small">Fitur ini hanya aktif ketika Sesi Random Penilai sedang <strong>BERLANGSUNG (OPEN)</strong>.</p>
            <div class="mt-4">
                <a href="{{ route('admin.siklus-semester') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold btn-sm">
                    <i class="bi bi-gear-fill me-2"></i> Ke Manajemen Siklus
                </a>
            </div>
        </div>

    @else
        
        {{-- INFO JIKA EXPIRED --}}
        @if($isExpired)
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                <div>
                    <strong>Masa Penilaian Telah Berakhir</strong>
                    <div class="small">Sesi penilaian sudah ditutup. Anda melihat data terakhir.</div>
                </div>
            </div>
        @endif

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="row align-items-center gy-3">
                    <div class="col-12 col-md-6">
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            @if($isExpired)
                                <i class="bi bi-stop-circle text-danger me-2"></i> Status: Selesai
                            @else
                                <i class="bi bi-broadcast text-success me-2 animate-pulse"></i> Sedang Berlangsung
                            @endif
                        </h6>
                    </div>
                    <div class="col-12 col-md-6 text-md-end">
                        <div class="input-group ms-auto w-100 w-md-auto" style="max-width: 300px;">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" 
                                   placeholder="Cari Pegawai..." 
                                   wire:model.live.debounce="300ms">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive px-3 px-md-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-custom text-white">
                        <tr>
                            <th class="ps-4 py-3" width="5%">No</th>
                            <th class="py-3" width="25%">Pegawai</th>
                            <th class="py-3" width="20%">Jabatan</th>
                            <th class="py-3 text-center" width="25%">Progress Menilai</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($dataProgress as $index => $row)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row['nama'] }}</div>
                                <div class="small text-muted d-none d-md-block">{{ $row['nip'] }}</div>
                            </td>
                            <td>
                                <span class="small text-dark">{{ Str::limit($row['jabatan'], 30) }}</span>
                            </td>
                            <td class="px-md-4">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="progress flex-grow-1" style="height: 8px; background-color: #e9ecef;">
                                        <div class="progress-bar {{ $row['badge'] }}" role="progressbar" 
                                             style="width: {{ $row['persen'] }}%"></div>
                                    </div>
                                    <span class="ms-2 fw-bold small">{{ $row['persen'] }}%</span>
                                </div>
                                <div class="text-muted small fst-italic">
                                    Menilai {{ $row['sudah'] }} dari {{ $row['total'] }}
                                </div>
                            </td>
                            <td class="text-center text-md-center text-start">
                                @if($row['total'] == 0)
                                    <span class="badge bg-light text-secondary border">No Task</span>
                                @elseif($row['persen'] == 100)
                                    <span class="badge bg-success rounded-pill px-3">Selesai</span>
                                @elseif($row['persen'] == 0)
                                    <span class="badge bg-danger rounded-pill px-3">Belum</span>
                                @else
                                    <span class="badge bg-warning text-dark rounded-pill px-3">Proses</span>
                                @endif
                            </td>
                            <td class="text-center text-md-center text-end">
                                <a href="{{ route('admin.detail-progress', ['siklusId' => $activeSiklus->id, 'userId' => $row['user_id']]) }}" 
                                   class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm px-3">
                                    Detail <i class="bi bi-chevron-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-search display-6 d-block mb-2 opacity-50"></i>
                                Data tidak ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>