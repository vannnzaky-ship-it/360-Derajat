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
        .live-clock { background: linear-gradient(to right, #f8f9fa, #ffffff); border-left: 3px solid #6c757d; }
        .modal-header-gold { background-color: #f8f9fa; border-bottom: 2px solid var(--polkam-gold); }
        .detail-label { font-size: 0.75rem; text-transform: uppercase; color: #6c757d; font-weight: 700; }
        .detail-value { font-weight: 600; color: #212529; }

        /* Dark Mode Styles */
        [data-bs-theme="dark"] .bg-white,
        [data-bs-theme="dark"] .card { background-color: #212529 !important; border-color: #373b3e !important; color: #ffffff !important; }
        [data-bs-theme="dark"] .bg-light { background-color: #2c3034 !important; border-color: #373b3e !important; color: #e0e0e0 !important; }
        [data-bs-theme="dark"] .text-dark { color: #ffffff !important; }
        [data-bs-theme="dark"] .text-muted { color: #adb5bd !important; }
        [data-bs-theme="dark"] .form-select,
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .input-group-text { background-color: #2b3035 !important; border-color: #495057 !important; color: #ffffff !important; }
        [data-bs-theme="dark"] .live-clock { background: linear-gradient(to right, #2c3034, #212529) !important; border-left-color: #adb5bd !important; border: 1px solid #373b3e; }
        [data-bs-theme="dark"] .table { color: #e0e0e0 !important; border-color: #373b3e !important; }
        [data-bs-theme="dark"] .table thead th { background-color: #2c3034 !important; color: #adb5bd !important; }
        [data-bs-theme="dark"] .table tbody td { background-color: #212529 !important; border-bottom: 1px solid #373b3e !important; }
        [data-bs-theme="dark"] .modal-content { background-color: #212529 !important; border: 1px solid #495057 !important; }
        [data-bs-theme="dark"] .modal-header-gold { background-color: #2c3034 !important; border-bottom: 2px solid var(--polkam-gold) !important; }
        [data-bs-theme="dark"] .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
    </style>

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="p-2 me-3"><i class="bi bi-shuffle fs-3 text-gold"></i></div>
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
                <div class="col-md-auto text-gold fw-bold"><i class="bi bi-calendar-range me-2"></i>Pilih Siklus:</div>
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
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- KOLOM KIRI --}}
        <div class="col-md-5">
            <div class="card card-panel h-100 bg-white">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                        <i class="bi bi-sliders me-2 text-gold"></i>Konfigurasi
                    </h5>

                    <div class="live-clock p-3 mb-4 rounded shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Waktu Saat Ini</small>
                                <div class="fs-5 fw-bold text-dark mt-1">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</div>
                            </div>
                            <div class="text-end">
                                <i class="bi bi-clock text-secondary mb-1 d-block h5"></i>
                                <span class="badge bg-secondary font-monospace" id="liveTime">{{ \Carbon\Carbon::now()->format('H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="generate">
                        @if($isSessionExists)
                            <div class="text-center py-2">
                                <div class="mb-3">
                                    <i class="bi {{ $isExpired ? 'bi-x-circle text-danger' : 'bi-check-circle text-success' }}" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="fw-bold {{ $isExpired ? 'text-danger' : 'text-success' }}">
                                    {{ $isExpired ? 'Masa Penilaian Berakhir' : 'Penilaian Sedang Berjalan' }}
                                </h5>
                                <div class="alert alert-warning border-warning d-inline-block px-4 py-2 rounded-3 small mb-4 text-start w-100">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Batas Waktu:</span>
                                        <strong class="text-dark">{{ \Carbon\Carbon::parse($batas_waktu)->isoFormat('D MMMM Y, HH:mm') }} WIB</strong>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-secondary w-100 py-2 rounded shadow-sm opacity-75" disabled>Form Terkunci</button>
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="fw-bold text-muted small mb-1">Tentukan Batas Waktu (Deadline)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-gold"></i></span>
                                    {{-- BERUBAH: Min diset ke waktu sekarang agar hari ini bisa dipilih --}}
                                    <input type="datetime-local" 
                                           wire:model="batas_waktu" 
                                           min="{{ now()->format('Y-m-d\TH:i') }}"
                                           class="form-control border-start-0 ps-0 @error('batas_waktu') is-invalid @enderror">
                                </div>
                                @error('batas_waktu') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold text-muted small mb-1">Jumlah Sampel Rekan</label>
                                <div class="input-group">
                                    <input type="number" wire:model="limit_rekan" class="form-control">
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

        {{-- KOLOM KANAN --}}
        <div class="col-md-7">
            <div class="card card-history h-100 bg-white">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-gold"></i>Riwayat Generate</h5>
                </div>
                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
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
                                        <button wire:click="showDetail({{ $history->id }})" data-bs-toggle="modal" data-bs-target="#detailModal" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Detail</button>
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

    {{-- MODAL DETAIL --}}
    <div wire:ignore.self class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header modal-header-gold px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-clipboard-data me-2 text-gold"></i>Detail Hasil Generate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    @if($selectedHistory)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4 border-end"><div class="detail-label">Siklus</div><div class="detail-value">{{ $selectedHistory->siklus->tahun_ajaran }}</div></div>
                                <div class="col-4 border-end"><div class="detail-label">Total Penilaian</div><div class="detail-value">{{ $selectedHistory->alokasis->count() }} Data</div></div>
                                <div class="col-4"><div class="detail-label">Status</div><div class="detail-value">{{ \Carbon\Carbon::now() > $selectedHistory->batas_waktu ? 'Berakhir' : 'Aktif' }}</div></div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive bg-white rounded shadow-sm">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="bg-light">
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
                                    <td>{{ $alokasi->user->name ?? '-' }}</td>
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