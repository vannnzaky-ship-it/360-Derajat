<div class="container-fluid p-4">

    {{-- STYLE KHUSUS: RANDOM PENILAI (WARNA DARK MODE DIPERBAIKI) --}}
    <style>
        :root { --polkam-gold: #c38e44; --polkam-gold-hover: #a57635; }
        .text-gold { color: var(--polkam-gold) !important; }
        .bg-gold { background-color: var(--polkam-gold) !important; color: white; }
        .border-gold { border-color: var(--polkam-gold) !important; }
        
        .btn-gold { background-color: var(--polkam-gold); color: white; border: none; font-weight: 600; }
        .btn-gold:hover { background-color: var(--polkam-gold-hover); color: white; }
        
        .form-check-input:checked { background-color: var(--polkam-gold); border-color: var(--polkam-gold); }
        
        /* Card Styles Default (Light Mode) */
        .card-panel { border: 0; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075); border-left: 5px solid var(--polkam-gold); background-color: #fff; }
        .card-history { border: 0; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075); background-color: #fff; }
        
        .live-clock { background: linear-gradient(to right, #f8f9fa, #ffffff); border-left: 3px solid #6c757d; }
        .modal-header-gold { background-color: #f8f9fa; border-bottom: 2px solid var(--polkam-gold); }
        
        .detail-label { font-size: 0.75rem; text-transform: uppercase; color: #6c757d; font-weight: 700; }
        .detail-value { font-weight: 600; color: #212529; }

        /* Mobile Responsive Table */
        @media (max-width: 767px) {
            .table-mobile-card thead { display: none; }
            .table-mobile-card tbody tr { display: flex; flex-wrap: wrap; background-color: #fff; border: 1px solid rgba(0,0,0,0.1); border-radius: 12px; margin-bottom: 1rem; box-shadow: 0 4px 8px rgba(0,0,0,0.05); overflow: hidden; }
            .table-mobile-card tbody td { display: block; width: 100%; border: none !important; padding: 8px 15px; }
            .table-mobile-card tbody td:nth-child(1) { order: 1; background: transparent; border-bottom: 1px solid rgba(0,0,0,0.05) !important; padding-top: 12px; padding-bottom: 12px; font-weight: bold; }
            .table-mobile-card tbody td:nth-child(2) { order: 2; font-size: 0.9rem; }
            .table-mobile-card tbody td:nth-child(2)::before { content: "Periode: "; font-weight: bold; color: #666; }
            .table-mobile-card tbody td:nth-child(3) { order: 3; border-bottom: 1px dashed rgba(0,0,0,0.1) !important; padding-bottom: 12px; }
            .table-mobile-card tbody td:nth-child(3)::before { content: "Status: "; font-weight: bold; color: #666; margin-right: 5px; }
            .table-mobile-card tbody td:nth-child(4) { order: 4; text-align: right !important; background-color: rgba(0,0,0,0.02); padding-top: 10px; padding-bottom: 10px; }
        }
        @media (min-width: 768px) { .w-md-auto { width: auto !important; } }

        /* ========================================= */
        /* DARK MODE FIXES (WARNA HITAM NETRAL)      */
        /* ========================================= */
        
        /* 1. Paksa Background jadi Hitam Netral (Bukan Biru) */
        [data-bs-theme="dark"] .bg-white,
        [data-bs-theme="dark"] .card-panel,
        [data-bs-theme="dark"] .card-history,
        [data-bs-theme="dark"] .card {
            background-color: #1a1a1a !important; /* Hitam Abu Netral */
            border-color: #2d2d2d !important;
            color: #e0e0e0 !important;
        }

        /* 2. Warna Teks */
        [data-bs-theme="dark"] .text-dark { color: #f8f9fa !important; }
        [data-bs-theme="dark"] .text-muted { color: #999 !important; }
        [data-bs-theme="dark"] .detail-value { color: #fff !important; }
        
        /* 3. Input & Form (Abu Gelap Netral) */
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select,
        [data-bs-theme="dark"] .input-group-text,
        [data-bs-theme="dark"] .bg-light {
            background-color: #252525 !important; /* Abu Gelap Solid */
            border-color: #333 !important;
            color: #e0e0e0 !important;
        }
        
        /* 4. List Group & Clock */
        [data-bs-theme="dark"] .list-group-item {
            background-color: #1a1a1a !important;
            border-color: #333 !important;
            color: #e0e0e0 !important;
        }
        [data-bs-theme="dark"] .live-clock {
            background: linear-gradient(to right, #252525, #1a1a1a) !important;
            border-left-color: #555 !important;
        }
        
        /* 5. Modal & Table Hover */
        [data-bs-theme="dark"] .modal-content {
            background-color: #1a1a1a !important;
            border-color: #333 !important;
        }
        [data-bs-theme="dark"] .modal-header-gold,
        [data-bs-theme="dark"] .modal-footer {
            background-color: #252525 !important;
            border-color: #333 !important;
        }
        [data-bs-theme="dark"] .table-hover tbody tr:hover {
            background-color: rgba(255,255,255,0.03) !important;
        }

        /* 6. Fix Mobile Table di Dark Mode */
        @media (max-width: 767px) {
            [data-bs-theme="dark"] .table-mobile-card tbody tr {
                background-color: #1a1a1a !important;
                border-color: #333 !important;
            }
            [data-bs-theme="dark"] .table-mobile-card tbody td:nth-child(4) {
                background-color: rgba(255,255,255,0.03) !important;
            }
        }
    </style>

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <div class="p-2 me-2"><i class="bi bi-shuffle fs-3 text-gold"></i></div>
            <div><h2 class="h4 mb-0 text-dark">Random Penilai</h2></div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($sikluses->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <div class="mb-3"><i class="bi bi-calendar-x text-warning" style="font-size: 4rem;"></i></div>
                <h3 class="h5 fw-bold text-dark">Data Siklus Tidak Ditemukan</h3>
                <a href="{{ route('admin.siklus-semester') }}" class="btn btn-gold px-4 shadow-sm mt-3">Buat Siklus Baru</a>
            </div>
        </div>
    @else

        {{-- SIKLUS SELECTOR --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="row align-items-center gy-2">
                    <div class="col-12 col-md-auto text-gold fw-bold">
                        <i class="bi bi-calendar-range me-2"></i>Pilih Siklus:
                    </div>
                    <div class="col-12 col-md-5">
                        <select wire:model.live="siklus_id" class="form-select border-gold bg-light">
                            @foreach($sikluses as $siklus)
                                <option value="{{ $siklus->id }}">
                                    {{ $siklus->tahun_ajaran }} - {{ $siklus->semester }} 
                                    ({{ $siklus->status }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- KOLOM KIRI (KONFIGURASI) --}}
            <div class="col-12 col-lg-5">
                <div class="card card-panel h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                            <i class="bi bi-sliders me-2 text-gold"></i>Konfigurasi
                        </h5>

                        {{-- [TAMPILAN CHECKLIST SYARAT] --}}
                        @if(!$isReadyToGenerate && !$isSessionExists)
                            <div class="alert alert-danger border-danger shadow-sm rounded-3">
                                <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-octagon-fill me-2"></i>Prasyarat Belum Lengkap!</h6>
                                <p class="small mb-2">Sistem tidak dapat memproses sampai semua syarat terpenuhi:</p>
                                
                                <ul class="list-group list-group-flush small rounded bg-white">
                                    {{-- 1. CEK SIKLUS --}}
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Status Siklus "Aktif"</span>
                                        @if($statusCheck['siklus_aktif']) 
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @else 
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </li>

                                    {{-- 2. CEK BOBOT --}}
                                    <li class="list-group-item d-flex flex-column align-items-start">
                                        <div class="d-flex justify-content-between w-100 align-items-center">
                                            <span>Bobot Kompetensi (Total 100%)</span>
                                            @if($statusCheck['bobot_100']) 
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            @else 
                                                <a href="{{ route('admin.kompetensi') }}" class="btn btn-xs btn-outline-danger py-0" style="font-size: 0.7rem;">Perbaiki</a>
                                            @endif
                                        </div>
                                        @if(!$statusCheck['bobot_100'])
                                            <small class="text-danger mt-1">Saat ini: <strong>{{ $totalBobotCurrent }}%</strong> (Harus pas 100)</small>
                                        @endif
                                    </li>

                                    {{-- 3. CEK PERTANYAAN --}}
                                    <li class="list-group-item d-flex flex-column align-items-start">
                                        <div class="d-flex justify-content-between w-100 align-items-center">
                                            <span>Kelengkapan Pertanyaan</span>
                                            @if($statusCheck['pertanyaan_lengkap']) 
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            @else 
                                                <a href="{{ route('admin.pertanyaan') }}" class="btn btn-xs btn-outline-danger py-0" style="font-size: 0.7rem;">Lengkapi</a>
                                            @endif
                                        </div>
                                        @if(!$statusCheck['pertanyaan_lengkap'] && !empty($missingKompetensiNames))
                                            <div class="mt-1 text-danger fst-italic" style="font-size: 0.7rem;">
                                                Kompetensi tanpa soal: {{ implode(', ', $missingKompetensiNames) }}
                                            </div>
                                        @endif
                                    </li>

                                    {{-- 4. CEK SKEMA --}}
                                    <li class="list-group-item d-flex flex-column align-items-start">
                                        <div class="d-flex justify-content-between w-100 align-items-center">
                                            <span>Skema Penilaian (Level 1-5)</span>
                                            @if($statusCheck['skema_lengkap']) 
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            @else 
                                                <a href="{{ route('admin.skema-penilaian') }}" class="btn btn-xs btn-outline-danger py-0" style="font-size: 0.7rem;">Buat Skema</a>
                                            @endif
                                        </div>
                                        @if(!$statusCheck['skema_lengkap'] && !empty($missingLevels))
                                            <div class="mt-1 text-danger fst-italic" style="font-size: 0.7rem;">
                                                Level belum dicakup: {{ implode(', ', $missingLevels) }}
                                            </div>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        @endif

                        {{-- LIVE CLOCK --}}
                        <div class="live-clock p-3 mb-4 rounded shadow-sm">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Waktu Saat Ini</small>
                                    <div class="fs-5 fw-bold text-dark mt-1 lh-1">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-secondary font-monospace fs-6" id="liveTime">{{ \Carbon\Carbon::now()->format('H:i') }} WIB</span>
                                </div>
                            </div>
                        </div>

                        <form wire:submit.prevent="generate">
                            @if($isSessionExists)
                                {{-- TAMPILAN TERKUNCI --}}
                                <div class="text-center py-2">
                                    <div class="mb-3">
                                        <i class="bi {{ $isExpired ? 'bi-x-circle text-danger' : 'bi-check-circle text-success' }}" style="font-size: 4rem;"></i>
                                    </div>
                                    <h5 class="fw-bold {{ $isExpired ? 'text-danger' : 'text-success' }}">
                                        {{ $isExpired ? 'Masa Penilaian Berakhir' : 'Penilaian Sedang Berjalan' }}
                                    </h5>
                                    <div class="alert alert-warning border-warning d-inline-block px-3 py-2 rounded-3 small mb-4 text-start w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted me-2">Batas Waktu:</span>
                                            <strong class="text-dark text-end">{{ \Carbon\Carbon::parse($batas_waktu)->isoFormat('D MMM Y, HH:mm') }} WIB</strong>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-secondary w-100 py-2 rounded shadow-sm opacity-75" disabled>
                                        <i class="bi bi-lock-fill me-2"></i>Form Terkunci
                                    </button>
                                </div>
                            @elseif(!$isReadyToGenerate)
                                {{-- TAMPILAN BELUM SIAP --}}
                                <div class="text-center py-3 opacity-50">
                                    <i class="bi bi-slash-circle fs-1 mb-2 d-block"></i>
                                    <span class="small fw-bold">Tombol terkunci karena prasyarat belum lengkap.</span>
                                </div>
                            @else
                                {{-- FORM INPUT --}}
                                <div class="mb-3">
                                    <label class="fw-bold text-muted small mb-1">Tentukan Batas Waktu (Deadline)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-gold"></i></span>
                                        <input type="datetime-local" wire:model="batas_waktu" min="{{ now()->format('Y-m-d\TH:i') }}" class="form-control border-start-0 border-end-0 ps-0 text-center fw-bold @error('batas_waktu') is-invalid @enderror">
                                        <span class="input-group-text bg-light text-muted fw-bold border-start-0">WIB</span>
                                    </div>
                                    @error('batas_waktu') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="fw-bold text-muted small mb-1">Jumlah Sampel Rekan</label>
                                    <div class="input-group">
                                        <input type="number" wire:model="limit_rekan" class="form-control" placeholder="Contoh: 3">
                                        <span class="input-group-text bg-light text-muted">Orang</span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="fw-bold text-muted small mb-2 d-block">Filter Kategori</label>
                                    <div class="d-flex flex-wrap gap-2 p-3 bg-light rounded border-0">
                                        @foreach(['Atasan', 'Bawahan', 'Rekan', 'Diri Sendiri'] as $kategori)
                                            <div class="form-check me-2">
                                                <input class="form-check-input" type="checkbox" value="{{ $kategori }}" wire:model="pilihan_kategori" id="cat_{{$kategori}}">
                                                <label class="form-check-label small" for="cat_{{$kategori}}">{{ $kategori }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-gold w-100 py-2 shadow-sm" wire:loading.attr="disabled">
                                    <span wire:loading.remove><i class="bi bi-magic me-2"></i>Mulai Random Penilai</span>
                                    <span wire:loading><span class="spinner-border spinner-border-sm me-2"></span> Memproses...</span>
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN (RIWAYAT) --}}
            <div class="col-12 col-lg-7">
                <div class="card card-history h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-gold"></i>Riwayat Generate</h5>
                    </div>
                    <div class="card-body px-0">
                        <div class="">
                            <table class="table table-hover align-middle mb-0 table-mobile-card">
                                <thead class="bg-light text-secondary small text-uppercase">
                                    <tr>
                                        <th class="ps-4">Siklus</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($histories as $history)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-bold text-dark">{{ $history->siklus->tahun_ajaran }}</div>
                                            <div class="small text-gold">{{ $history->siklus->semester }}</div>
                                        </td>
                                        <td>
                                            <div class="small text-dark fw-bold">
                                                {{ \Carbon\Carbon::parse($history->tanggal_mulai)->format('d M') }} s/d {{ \Carbon\Carbon::parse($history->batas_waktu)->format('d M') }}
                                            </div>
                                        </td>
                                        <td>
                                            @php $isExpiredHistory = \Carbon\Carbon::now() > $history->batas_waktu; @endphp
                                            @if($history->status == 'Open' && !$isExpiredHistory)
                                                <span class="badge bg-success-subtle text-success px-3 rounded-pill border">Aktif</span>
                                            @elseif($isExpiredHistory)
                                                <span class="badge bg-danger-subtle text-danger px-3 rounded-pill">Expired</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary px-3 rounded-pill">Selesai</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <button wire:click="showDetail({{ $history->id }})" data-bs-toggle="modal" data-bs-target="#detailModal" class="btn btn-sm btn-outline-secondary rounded-pill px-3 w-100 w-md-auto">Detail</button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-5 text-muted">Belum ada riwayat.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="px-4 mt-3">{{ $histories->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL DETAIL --}}
    <div wire:ignore.self class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header modal-header-gold px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-clipboard-data me-2 text-gold"></i>Detail Hasil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    @if($selectedHistory)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row text-center gy-2">
                                <div class="col-4 border-end"><div class="detail-label">Siklus</div><div class="detail-value">{{ $selectedHistory->siklus->tahun_ajaran }}</div></div>
                                <div class="col-4 border-end"><div class="detail-label">Total Data</div><div class="detail-value">{{ $selectedHistory->alokasis->count() }}</div></div>
                                <div class="col-4"><div class="detail-label">Status</div><div class="detail-value">{{ \Carbon\Carbon::now() > $selectedHistory->batas_waktu ? 'Berakhir' : 'Aktif' }}</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive bg-white rounded shadow-sm" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="bg-light sticky-top">
                                <tr class="small text-muted">
                                    <th class="ps-3 py-2">TARGET</th>
                                    <th>JABATAN</th>
                                    <th>PENILAI</th>
                                    <th class="text-center">SEBAGAI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedHistory->alokasis as $alokasi)
                                <tr class="small">
                                    <td class="ps-3 fw-bold">{{ $alokasi->targetUser->name ?? '-' }}</td>
                                    <td>{{ $alokasi->targetJabatan->nama_jabatan ?? '-' }}</td>
                                    <td>
                                        @if($alokasi->sebagai == 'Diri Sendiri')
                                            <span class="text-muted fst-italic">Diri Sendiri</span>
                                        @else
                                            <span class="text-secondary" title="Nama disensor untuk privasi">
                                                <i class="bi bi-shield-lock-fill me-1 small"></i>
                                                {{ substr($alokasi->user->name ?? '?', 0, 1) }}*******
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center"><span class="badge bg-light text-dark border rounded-pill">{{ $alokasi->sebagai }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-top-0 bg-light"><button type="button" class="btn btn-secondary rounded-pill btn-sm" data-bs-dismiss="modal">Tutup</button></div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        setInterval(() => {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
            const el = document.getElementById('liveTime');
            if(el) el.innerText = timeString;
        }, 1000);
    </script>
    @endpush
</div>