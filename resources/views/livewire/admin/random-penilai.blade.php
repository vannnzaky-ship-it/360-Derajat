<div class="container-fluid p-4">

    {{-- CUSTOM STYLES --}}
    <style>
        :root { --polkam-gold: #c38e44; --polkam-gold-hover: #a57635; }
        .text-gold { color: var(--polkam-gold) !important; }
        .bg-gold { background-color: var(--polkam-gold) !important; color: white; }
        .border-gold { border-color: var(--polkam-gold) !important; }
        .btn-gold { background-color: var(--polkam-gold); color: white; border: none; font-weight: 600; }
        .btn-gold:hover { background-color: var(--polkam-gold-hover); color: white; }
        .form-check-input:checked { background-color: var(--polkam-gold); border-color: var(--polkam-gold); }
        .card-panel { border: 0; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075); border-left: 5px solid var(--polkam-gold); }
        .card-history { border: 0; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075); }
        .table-hover tbody tr:hover { background-color: #fdf8f3; }
        
        /* Jam Digital */
        .live-clock {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            border-left: 3px solid #6c757d;
        }

        /* Modal Detail Styles */
        .modal-header-gold {
            background-color: #f8f9fa;
            border-bottom: 2px solid var(--polkam-gold);
        }
        .detail-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            font-weight: 700;
        }
        .detail-value {
            font-weight: 600;
            color: #212529;
        }
    </style>

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="p-2 me-3">
                <i class="bi bi-shuffle fs-3 text-gold"></i>
            </div>
            <div>
                <h2 class="h4 fw-bold mb-0 text-dark">Random Penilai</h2>
                <p class="text-muted small mb-0">Generate acak penilai untuk siklus berjalan</p>
            </div>
        </div>
    </div>

    {{-- SIKLUS SELECTOR --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-auto text-gold fw-bold">
                    <i class="bi bi-calendar-range me-2"></i>Pilih Siklus:
                </div>
                <div class="col-md-5">
                    <select wire:model.live="siklus_id" class="form-select border-gold bg-light">
                        @foreach($sikluses as $siklus)
                            <option value="{{ $siklus->id }}">
                                {{ $siklus->tahun_ajaran }} - {{ $siklus->semester }}
                                @if($siklus->penilaianSession) (Data Tersedia) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md text-end text-muted small">
                    <i class="bi bi-info-circle me-1"></i>Pilih siklus untuk mengatur parameter generate.
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        {{-- KOLOM KIRI: KONFIGURASI & WAKTU --}}
        <div class="col-md-5">
            <div class="card card-panel h-100 bg-white">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                        <i class="bi bi-sliders me-2 text-gold"></i>Konfigurasi
                    </h5>

                    {{-- JAM DIGITAL --}}
                    <div class="live-clock p-3 mb-4 rounded shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">Waktu Saat Ini</small>
                                <div class="fs-5 fw-bold text-dark mt-1">
                                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                                </div>
                            </div>
                            <div class="text-end">
                                <i class="bi bi-clock text-secondary mb-1 d-block h5"></i>
                                <span class="badge bg-secondary font-monospace" id="liveTime">
                                    {{ \Carbon\Carbon::now()->format('H:i') }} WIB
                                </span>
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="generate">
                        
                        @if($isSessionExists)
                            
                            {{-- TAMPILAN STATUS (SUDAH ADA SESI) --}}
                            <div class="text-center py-2">
                                <div class="mb-3">
                                    <i class="bi {{ $isExpired ? 'bi-x-circle text-danger' : 'bi-check-circle text-success' }}" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="fw-bold {{ $isExpired ? 'text-danger' : 'text-success' }}">
                                    {{ $isExpired ? 'Masa Penilaian Berakhir' : 'Penilaian Sedang Berjalan' }}
                                </h5>
                                <p class="text-muted small mb-4">
                                    {{ $isExpired ? 'Siklus ini sudah selesai dan ditutup.' : 'Data acak penilai untuk siklus ini sudah aktif.' }}
                                </p>
                                
                                <div class="alert alert-warning border-warning d-inline-block px-4 py-2 rounded-3 small mb-4 text-start w-100">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted"><i class="bi bi-hourglass-split me-1"></i> Batas Waktu:</span>
                                        <strong class="text-dark">{{ \Carbon\Carbon::parse($batas_waktu)->isoFormat('D MMMM Y, HH:mm') }} WIB</strong>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-secondary w-100 py-2 rounded shadow-sm opacity-75" disabled>
                                    <i class="bi bi-lock-fill me-2"></i> Form Terkunci
                                </button>
                            </div>

                        @else

                            {{-- FORM INPUT (GENERATE BARU) --}}
                            <div class="mb-3">
                                <label class="fw-bold text-muted small mb-1">Tentukan Batas Waktu (Deadline)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-gold"></i></span>
                                    <input type="datetime-local" wire:model="batas_waktu" class="form-control border-start-0 ps-0 @error('batas_waktu') is-invalid @enderror">
                                </div>
                                @error('batas_waktu') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-muted small mb-1">Jumlah Sampel Rekan (Maksimal)</label>
                                <div class="input-group">
                                    <input type="number" wire:model="limit_rekan" class="form-control" placeholder="Cth: 5">
                                    <span class="input-group-text bg-light text-muted">Orang</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold text-muted small mb-2 d-block">Filter Kategori yang Digenerate</label>
                                <div class="d-flex flex-wrap gap-2 p-3 bg-light rounded border-0">
                                    @foreach(['Atasan', 'Bawahan', 'Rekan', 'Diri Sendiri'] as $kategori)
                                        <div class="form-check me-2">
                                            <input class="form-check-input" type="checkbox" value="{{ $kategori }}" wire:model="pilihan_kategori" id="cat_{{$kategori}}">
                                            <label class="form-check-label small" for="cat_{{$kategori}}">{{ $kategori }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('pilihan_kategori') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            <button type="submit" class="btn btn-gold w-100 py-2 shadow-sm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="generate">
                                    <i class="bi bi-magic me-2"></i>Mulai Random Penilai
                                </span>
                                <span wire:loading wire:target="generate">
                                    <span class="spinner-border spinner-border-sm me-2"></span> Memproses...
                                </span>
                            </button>

                        @endif
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: HISTORY TABLE --}}
        <div class="col-md-7">
            <div class="card card-history h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-clock-history me-2 text-gold"></i>Riwayat Generate
                    </h5>
                    <span class="badge bg-light text-secondary border">Total: {{ $histories->total() }}</span>
                </div>
                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="ps-4 border-0">Siklus</th>
                                    <th class="border-0">Periode</th>
                                    <th class="border-0">Status</th>
                                    <th class="text-end pe-4 border-0">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($histories as $history)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark">{{ $history->siklus->tahun_ajaran }}</div>
                                        <div class="small text-muted text-gold">{{ $history->siklus->semester }}</div>
                                    </td>
                                    <td>
                                        <div class="small text-dark fw-bold">
                                            {{ \Carbon\Carbon::parse($history->tanggal_mulai)->format('d M') }} 
                                            <span class="text-muted fw-normal">s/d</span> 
                                            {{ \Carbon\Carbon::parse($history->batas_waktu)->format('d M') }}
                                        </div>
                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                            {{ \Carbon\Carbon::parse($history->batas_waktu)->format('Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        @php $isExpiredHistory = \Carbon\Carbon::now() > $history->batas_waktu; @endphp
                                        @if($history->status == 'Open' && !$isExpiredHistory)
                                            <span class="badge bg-success-subtle text-success px-3 rounded-pill border border-success-subtle">
                                                <i class="bi bi-circle-fill me-1 small" style="font-size: 6px; vertical-align: middle;"></i> Aktif
                                            </span>
                                        @elseif($isExpiredHistory)
                                            <span class="badge bg-danger-subtle text-danger px-3 rounded-pill">Expired</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary px-3 rounded-pill">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        {{-- TOMBOL DETAIL --}}
                                        {{-- Memicu fungsi showDetail di Controller & Membuka Modal --}}
                                        <button wire:click="showDetail({{ $history->id }})" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailModal"
                                                class="btn btn-sm btn-outline-secondary rounded-pill px-3" 
                                                style="font-size: 0.8rem;">
                                            Detail <i class="bi bi-chevron-right ms-1"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="bi bi-inbox text-gold opacity-50" style="font-size: 3rem;"></i>
                                        <p class="text-muted small mt-2 mb-0">Belum ada riwayat generate.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 mt-3">
                        {{ $histories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL (POP-UP) --}}
    <div wire:ignore.self class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                
                <div class="modal-header modal-header-gold px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="detailModalLabel">
                        <i class="bi bi-clipboard-data me-2 text-gold"></i>Detail Hasil Generate
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-light">
                    
                    {{-- Loading State --}}
                    <div wire:loading wire:target="showDetail" class="text-center py-5 w-100">
                        <div class="spinner-border text-gold" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2 small">Mengambil data...</p>
                    </div>

                    {{-- Konten Detail --}}
                    @if($selectedHistory)
                    <div wire:loading.remove wire:target="showDetail">
                        
                        {{-- Ringkasan --}}
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-4 border-end">
                                        <div class="detail-label">Siklus</div>
                                        <div class="detail-value text-gold">
                                            {{ $selectedHistory->siklus->tahun_ajaran }} {{ $selectedHistory->siklus->semester }}
                                        </div>
                                    </div>
                                    <div class="col-4 border-end">
                                        <div class="detail-label">Total Penilaian</div>
                                        <div class="detail-value">{{ $selectedHistory->alokasis->count() }} Data</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="detail-label">Status</div>
                                        <div class="detail-value">
                                            @if(\Carbon\Carbon::now() > $selectedHistory->batas_waktu)
                                                <span class="text-danger">Berakhir</span>
                                            @else
                                                <span class="text-success">Aktif</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Rincian --}}
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="fw-bold mb-0 text-dark small text-uppercase">Daftar Alokasi Penilaian</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead class="bg-light sticky-top">
                                            <tr style="font-size: 0.75rem;">
                                                <th class="ps-3 py-2 text-muted">TARGET (Dinilai)</th>
                                                <th class="py-2 text-muted">JABATAN</th>
                                                <th class="py-2 text-muted">PENILAI</th>
                                                <th class="py-2 text-muted text-center">SEBAGAI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($selectedHistory->alokasis as $alokasi)
                                            <tr style="font-size: 0.85rem;">
                                                <td class="ps-3 fw-bold text-dark">
                                                    {{ $alokasi->targetUser->name ?? 'User Terhapus' }}
                                                </td>
                                                <td>
                                                    <span class="text-muted small">
                                                        {{ $alokasi->targetJabatan->nama_jabatan ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $alokasi->user->name ?? 'User Terhapus' }}
                                                </td>
                                                <td class="text-center">
                                                    @if($alokasi->sebagai == 'Atasan')
                                                        <span class="badge bg-info-subtle text-info-emphasis rounded-pill px-2">Atasan</span>
                                                    @elseif($alokasi->sebagai == 'Bawahan')
                                                        <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2">Bawahan</span>
                                                    @elseif($alokasi->sebagai == 'Rekan')
                                                        <span class="badge bg-success-subtle text-success-emphasis rounded-pill px-2">Rekan</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">Diri Sendiri</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted small">
                                                    Tidak ada data alokasi.
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endif

                </div>
                <div class="modal-footer border-top-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
            const element = document.getElementById('liveTime');
            if(element) element.innerText = timeString;
        }
        setInterval(updateClock, 1000);
    </script>
    @endpush
</div>