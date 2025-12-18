<div class="container-fluid p-4">
    
    <style>
        .text-custom { color: #c38e44 !important; }
        .bg-custom { background-color: #c38e44 !important; color: white; }
        /* --- STYLE TAMBAHAN MODE GELAP (DARK MODE) --- */
        
        /* 1. Form Input & Group */
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .input-group-text {
            background-color: #2b3035 !important; /* Abu-abu gelap */
            border-color: #373b3e !important;
            color: #ffffff !important;
        }
        
        /* Placeholder warna abu terang */
        [data-bs-theme="dark"] ::placeholder {
            color: #adb5bd !important;
            opacity: 1;
        }

        /* 2. Card Background */
        [data-bs-theme="dark"] .card {
            background-color: #212529 !important;
            border-color: #373b3e !important;
        }
        
        [data-bs-theme="dark"] .card-header {
            background-color: #212529 !important;
            border-bottom: 1px solid #373b3e !important;
        }

        /* 3. Teks */
        [data-bs-theme="dark"] .text-dark { color: #ffffff !important; }
        [data-bs-theme="dark"] .text-muted { color: #adb5bd !important; }
        [data-bs-theme="dark"] .text-secondary { color: #adb5bd !important; }

        /* 4. Tabel */
        [data-bs-theme="dark"] .table {
            color: #e0e0e0 !important;
            border-color: #373b3e !important;
        }
        [data-bs-theme="dark"] .table tbody td {
            background-color: #212529 !important;
            border-bottom: 1px solid #373b3e !important;
        }
        [data-bs-theme="dark"] .table-hover tbody tr:hover {
            background-color: #2c3034 !important;
        }

        /* 5. Progress Bar Background */
        [data-bs-theme="dark"] .progress {
            background-color: #373b3e !important; /* Track progress jadi gelap */
        }

        /* 6. Tombol */
        [data-bs-theme="dark"] .btn-outline-secondary {
            color: #adb5bd !important;
            border-color: #6c757d !important;
        }
        [data-bs-theme="dark"] .btn-outline-secondary:hover {
            background-color: #373b3e !important;
            color: #fff !important;
        }
    </style>

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Monitoring Proses Penilai</h2>
            @if($activeSiklus)
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted">Periode: <strong class="text-custom">{{ $activeSiklus->tahun_ajaran }} {{ $activeSiklus->semester }}</strong></span>
                </div>
            @endif
        </div>
    </div>

    {{-- LOGIKA TAMPILAN: Hanya Tampil jika Ada Sesi DAN Belum Expired --}}
    @if(!$activeSession || $isExpired)
        
        {{-- TAMPILAN PESAN KOSONG / PERINGATAN --}}
        <div class="alert alert-light border shadow-sm rounded-4 p-5 text-center">
            <div class="mb-3">
                @if($isExpired)
                    {{-- Ikon jika Expired --}}
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle" style="width: 80px; height: 80px;">
                        <i class="bi bi-x-octagon fs-1"></i>
                    </div>
                @else
                    {{-- Ikon jika Belum Ada --}}
                    <div class="spinner-grow text-warning" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                @endif
            </div>

            @if($isExpired)
                <h4 class="fw-bold text-danger">Masa Penilaian Telah Berakhir</h4>
                <p class="text-muted mb-0">
                    Sesi penilaian untuk periode ini sudah ditutup atau melewati batas waktu.<br>
                    Data monitoring tidak lagi ditampilkan.
                </p>
            @else
                <h4 class="fw-bold text-dark">Belum Ada Sesi Penilaian</h4>
                <p class="text-muted mb-0">
                    Fitur ini hanya aktif ketika Sesi Random Penilai sedang <strong>BERLANGSUNG (OPEN)</strong>.
                </p>
            @endif

            <div class="mt-4">
                <a href="{{ route('admin.siklus-semester') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
                    <i class="bi bi-gear-fill me-2"></i> Ke Manajemen Siklus
                </a>
            </div>
        </div>

    @else
        
        {{-- TAMPILAN TABEL DATA (Hanya Muncul Pas Sesi Jalan) --}}
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-broadcast text-success me-2 animate-pulse"></i> Sedang Berlangsung
                        </h6>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <div class="input-group ms-auto" style="max-width: 300px;">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-start-0" 
                                   placeholder="Cari Nama Pegawai..." 
                                   wire:model.live.debounce="300ms">
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-custom text-white">
                        <tr>
                            <th class="ps-4 py-3" width="5%">No</th>
                            <th class="py-3">Pegawai</th>
                            <th class="py-3">Jabatan</th>
                            <th class="py-3 text-center" width="25%">Progress Menilai</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataProgress as $index => $row)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row['nama'] }}</div>
                                <div class="small text-muted">{{ $row['nip'] }}</div>
                            </td>
                            <td>
                                <span class="small text-dark">{{ Str::limit($row['jabatan'], 40) }}</span>
                            </td>
                            <td class="text-center px-4">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="progress flex-grow-1" style="height: 8px; background-color: #e9ecef;">
                                        <div class="progress-bar {{ $row['badge'] }}" role="progressbar" 
                                             style="width: {{ $row['persen'] }}%"></div>
                                    </div>
                                    <span class="ms-3 fw-bold small">{{ $row['persen'] }}%</span>
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    Menilai {{ $row['sudah'] }} dari {{ $row['total'] }} Target
                                </div>
                            </td>
                            <td class="text-center">
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
                            <td class="text-center">
                                <a href="{{ route('admin.detail-progress', ['siklusId' => $activeSiklus->id, 'userId' => $row['user_id']]) }}" 
                                   class="btn btn-sm btn-outline-secondary rounded-circle shadow-sm">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
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