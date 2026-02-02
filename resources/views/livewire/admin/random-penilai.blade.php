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

        /* DARK MODE FIXES */
        [data-bs-theme="dark"] .bg-white,
        [data-bs-theme="dark"] .card-panel,
        [data-bs-theme="dark"] .card-history,
        [data-bs-theme="dark"] .card {
            background-color: #1a1a1a !important;
            border-color: #2d2d2d !important;
            color: #e0e0e0 !important;
        }
        [data-bs-theme="dark"] .text-dark { color: #f8f9fa !important; }
        [data-bs-theme="dark"] .text-muted { color: #999 !important; }
        [data-bs-theme="dark"] .detail-value { color: #fff !important; }
        
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select,
        [data-bs-theme="dark"] .input-group-text,
        [data-bs-theme="dark"] .bg-light {
            background-color: #252525 !important;
            border-color: #333 !important;
            color: #e0e0e0 !important;
        }
        
        [data-bs-theme="dark"] .list-group-item {
            background-color: #1a1a1a !important;
            border-color: #333 !important;
            color: #e0e0e0 !important;
        }
        [data-bs-theme="dark"] .live-clock {
            background: linear-gradient(to right, #252525, #1a1a1a) !important;
            border-left-color: #555 !important;
        }
        
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

        /* SweetAlert Dark Mode */
        [data-bs-theme="dark"] div.swal2-popup {
            background-color: #1e1e1e !important;
            color: #e0e0e0 !important;
            border: 1px solid #333 !important;
        }
        [data-bs-theme="dark"] h2.swal2-title { color: #f8f9fa !important; }
        [data-bs-theme="dark"] div.swal2-html-container { color: #adb5bd !important; }
        [data-bs-theme="dark"] button.swal2-close { color: #fff !important; }
        [data-bs-theme="dark"] div.swal2-icon.swal2-warning {
            border-color: #c38e44 !important; color: #c38e44 !important;
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
    
    @if(session('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
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

                        {{-- [CHECKLIST PRASYARAT - DETAIL LENGKAP] --}}
                        {{-- Logic: Tampilkan hanya jika BELUM ada sesi (karena kalau sudah ada, sudah pasti syarat terpenuhi) --}}
                        @if(!$isSessionExists)
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold text-muted small text-uppercase mb-0">Status Persyaratan</h6>
                                        @if($isReadyToGenerate)
                                            <span class="badge bg-success-subtle text-success">Siap Generate</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Belum Lengkap</span>
                                        @endif
                                    </div>

                                    <ul class="list-group shadow-sm small">
                                        {{-- 1. CEK SIKLUS --}}
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>1. Status Siklus "Aktif"</span>
                                            @if($statusCheck['siklus_aktif']) 
                                                <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                            @else 
                                                <span class="badge bg-danger">Non-Aktif</span>
                                            @endif
                                        </li>

                                        {{-- 2. CEK BOBOT --}}
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>2. Bobot Kompetensi (100%)</span>
                                                @if($statusCheck['bobot_100']) 
                                                    <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                                @else 
                                                    <a href="{{ route('admin.kompetensi') }}" class="btn btn-xs btn-outline-danger py-0" style="font-size: 0.65rem;">Perbaiki</a>
                                                @endif
                                            </div>
                                            {{-- Detail Error Bobot --}}
                                            @if(!$statusCheck['bobot_100'])
                                                <div class="mt-1 text-danger fw-bold" style="font-size: 0.7rem;">
                                                    <i class="bi bi-info-circle me-1"></i>Saat ini: {{ $totalBobotCurrent }}%
                                                </div>
                                            @endif
                                        </li>

                                        {{-- 3. CEK PERTANYAAN --}}
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>3. Kelengkapan Soal</span>
                                                @if($statusCheck['pertanyaan_lengkap']) 
                                                    <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                                @else 
                                                    <a href="{{ route('admin.pertanyaan') }}" class="btn btn-xs btn-outline-danger py-0" style="font-size: 0.65rem;">Lengkapi</a>
                                                @endif
                                            </div>
                                            {{-- Detail Error Pertanyaan --}}
                                            @if(!$statusCheck['pertanyaan_lengkap'] && !empty($missingKompetensiNames))
                                                <div class="mt-1 text-danger fst-italic" style="font-size: 0.7rem;">
                                                    <i class="bi bi-exclamation-circle me-1"></i>Kosong: {{ implode(', ', $missingKompetensiNames) }}
                                                </div>
                                            @endif
                                        </li>

                                        {{-- 4. CEK SKEMA --}}
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>4. Skema Level (1-5)</span>
                                                @if($statusCheck['skema_lengkap']) 
                                                    <i class="bi bi-check-circle-fill text-success fs-6"></i>
                                                @else 
                                                    <a href="{{ route('admin.skema-penilaian') }}" class="btn btn-xs btn-outline-danger py-0" style="font-size: 0.65rem;">Buat Skema</a>
                                                @endif
                                            </div>
                                            {{-- Detail Error Skema --}}
                                            @if(!$statusCheck['skema_lengkap'] && !empty($missingLevels))
                                                <div class="mt-1 text-danger fst-italic" style="font-size: 0.7rem;">
                                                    <i class="bi bi-exclamation-circle me-1"></i>Kurang Level: {{ implode(', ', $missingLevels) }}
                                                </div>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif

                        {{-- [JAM HIDUP] --}}
                        <div class="live-clock p-3 mb-4 rounded shadow-sm">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Waktu Server</small>
                                    <div class="fs-5 fw-bold text-dark mt-1 lh-1">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-secondary font-monospace fs-6" id="liveTime">
                                        {{ \Carbon\Carbon::now()->format('H:i') }} WIB
                                    </span>
                                </div>
                            </div>
                        </div>

                        <form wire:submit.prevent="generate">
                            @if($isSessionExists)
                                {{-- === TAMPILAN STATUS (JIKA SESI SUDAH ADA) === --}}
                                <div class="text-center py-2">
                                    <div class="mb-3">
                                        {{-- LOGIKA ICON --}}
                                        @if($existingSession->status == 'Diperpanjang' && !$isExpired)
                                            <i class="bi bi-hourglass-split text-primary" style="font-size: 4rem;"></i>
                                        @elseif(!$isExpired)
                                            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                                        @else
                                            <i class="bi bi-x-circle text-danger" style="font-size: 4rem;"></i>
                                        @endif
                                    </div>

                                    {{-- LOGIKA TEKS STATUS --}}
                                    @if($existingSession->status == 'Diperpanjang' && !$isExpired)
                                        <h5 class="fw-bold text-primary">Penilaian Diperpanjang</h5>
                                        <p class="text-muted small">Waktu penilaian telah ditambah.</p>
                                    @elseif(!$isExpired)
                                        <h5 class="fw-bold text-success">Penilaian Sedang Berjalan</h5>
                                        <p class="text-muted small">Menunggu batas waktu berakhir.</p>
                                    @else
                                        <h5 class="fw-bold text-danger">Masa Penilaian Berakhir</h5>
                                        <p class="text-muted small">Sesi telah ditutup otomatis.</p>
                                    @endif

                                    {{-- [INFO BATAS WAKTU - SINKRON DENGAN EDIT] --}}
                                    <div class="alert alert-warning border-warning d-inline-block px-3 py-2 rounded-3 small mb-4 text-start w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted me-2 small text-uppercase fw-bold">Batas Waktu:</span>
                                            <strong class="text-dark text-end fs-6">
                                                {{-- Ini otomatis terupdate karena properti $batas_waktu direfresh dari backend --}}
                                                {{ \Carbon\Carbon::parse($batas_waktu)->isoFormat('D MMM Y, HH:mm') }} WIB
                                            </strong>
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
                                    <span class="small d-block text-muted">Lengkapi persyaratan di atas.</span>
                                </div>
                            @else
                                {{-- === FORM INPUT (GENERATE BARU) === --}}
                                <div class="mb-3">
                                    <label class="fw-bold text-muted small mb-1">Tentukan Batas Waktu (Deadline)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-gold"></i></span>
                                        <input type="datetime-local" wire:model.live="batas_waktu" min="{{ now()->format('Y-m-d\TH:i') }}" class="form-control border-start-0 border-end-0 ps-0 text-center fw-bold @error('batas_waktu') is-invalid @enderror">
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
                                                {{ \Carbon\Carbon::parse($history->tanggal_mulai)->translatedFormat('d M') }} s/d 
                                                {{ \Carbon\Carbon::parse($history->batas_waktu)->translatedFormat('d M Y') }}
                                            </div>
                                            <small class="text-muted" style="font-size: 0.7rem;">
                                                Pukul: {{ \Carbon\Carbon::parse($history->batas_waktu)->format('H:i') }} WIB
                                            </small>
                                        </td>
                                        <td>
                                            @php 
                                                $isExpiredHistory = \Carbon\Carbon::now() > $history->batas_waktu;
                                            @endphp

                                            @if($history->status == 'Diperpanjang' && !$isExpiredHistory)
                                                <span class="badge bg-primary-subtle text-primary px-3 rounded-pill border border-primary-subtle">
                                                    <i class="bi bi-hourglass-split me-1"></i>Diperpanjang
                                                </span>
                                            @elseif(($history->status == 'Open' || $history->status == 'Aktif') && !$isExpiredHistory)
                                                <span class="badge bg-success-subtle text-success px-3 rounded-pill border border-success-subtle">
                                                    <i class="bi bi-check-circle me-1"></i>Berjalan
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary px-3 rounded-pill border">
                                                    Selesai
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                
                                                {{-- LOGIKA TOMBOL EDIT --}}
                                                @if($history->siklus->status == 'Aktif')
                                                    {{-- SIKLUS AKTIF --}}
                                                    <button wire:click="editSession({{ $history->id }})" 
                                                            data-bs-toggle="modal" data-bs-target="#editTimeModal"
                                                            class="btn btn-sm btn-gold text-white px-3" 
                                                            title="Perpanjang/Edit Waktu">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                @else
                                                    {{-- SIKLUS TIDAK AKTIF (SweetAlert) --}}
                                                    <button type="button" 
                                                            onclick="Swal.fire({
                                                                icon: 'warning',
                                                                title: 'Siklus Tidak Aktif!',
                                                                text: 'Harap aktifkan Siklus {{ $history->siklus->tahun_ajaran }} {{ $history->siklus->semester }} terlebih dahulu di menu Siklus & Semester agar bisa mengedit sesi ini.',
                                                                confirmButtonColor: '#c38e44',
                                                                background: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#1e1e1e' : '#fff',
                                                                color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#e0e0e0' : '#545454'
                                                            })"
                                                            class="btn btn-sm btn-secondary text-white px-3 opacity-50" 
                                                            title="Siklus Non-Aktif">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                @endif

                                                <button wire:click="showDetail({{ $history->id }})" 
                                                        data-bs-toggle="modal" data-bs-target="#detailModal" 
                                                        class="btn btn-sm btn-outline-secondary px-3">
                                                    Detail
                                                </button>
                                            </div>
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
    
    {{-- MODAL EDIT WAKTU --}}
    <div wire:ignore.self class="modal fade" id="editTimeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-clock-history me-2 text-gold"></i>Edit Waktu Penilaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="updateSession">
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 d-flex align-items-center small mb-3">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <div>
                                Mengubah waktu ke masa depan akan otomatis membuka kembali sesi ini (Status: <strong>Open/Diperpanjang</strong>).
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold text-muted small mb-1">Batas Waktu Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-gold"></i></span>
                                <input type="datetime-local" wire:model="editBatasWaktu" class="form-control border-start-0 ps-0 fw-bold">
                            </div>
                            @error('editBatasWaktu') <span class="text-danger small mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light text-muted" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-gold px-4">
                            <span wire:loading.remove wire:target="updateSession">Simpan Perubahan</span>
                            <span wire:loading wire:target="updateSession">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- Pastikan SweetAlert sudah diload di Layout Utama, jika belum bisa uncomment baris bawah --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Script Jam Berjalan (Real-time Clock)
        setInterval(() => {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const timeString = `${hours}:${minutes} WIB`;
            
            const el = document.getElementById('liveTime');
            if(el) el.innerText = timeString;
        }, 1000);

        // Listener untuk menutup modal dari Livewire
        window.addEventListener('close-modal', event => {
            var el = document.getElementById('editTimeModal');
            var modal = bootstrap.Modal.getInstance(el);
            if (modal) {
                modal.hide();
            }
        });

        // Listener untuk SweetAlert dari Backend
        window.addEventListener('show-alert', event => {
            Swal.fire({
                icon: event.detail[0].type,
                title: event.detail[0].title,
                text: event.detail[0].text,
                confirmButtonColor: '#c38e44',
                // CSS Fix untuk Dark Mode agar konsisten
                background: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#1e1e1e' : '#fff',
                color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#e0e0e0' : '#545454'
            });
        });
    </script>
    @endpush
</div>