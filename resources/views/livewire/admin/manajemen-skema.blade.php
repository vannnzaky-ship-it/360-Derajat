<div class="container-fluid p-4">

    {{-- CUSTOM STYLES & RESPONSIVE FIXES --}}
    <style>
        :root { --polkam-gold: #c38e44; --polkam-gold-hover: #a57635; }
        .text-gold { color: var(--polkam-gold) !important; }
        .bg-gold { background-color: var(--polkam-gold) !important; color: white; }
        .border-gold { border-color: var(--polkam-gold) !important; }
        .btn-green { background-color: #198754; color: white; border: none; font-weight: 600; } 
        .btn-green:hover { background-color: #1a8452; color: white; }
        .btn-gold { background-color: var(--polkam-gold); color: white; border: none; font-weight: 600; }
        .btn-gold:hover { background-color: var(--polkam-gold-hover); color: white; }
        .form-check-input:checked { background-color: var(--polkam-gold); border-color: var(--polkam-gold); }
        .card-skema { border-left: 5px solid var(--polkam-gold); transition: transform 0.2s; }
        .card-skema:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .progress-diri { background-color: #0dcaf0; }
        .progress-atasan { background-color: #198754; }
        .progress-rekan { background-color: #ffc107; color: black; }
        .progress-bawahan { background-color: #dc3545; }

        /* Mobile Fixes */
        @media (max-width: 767px) {
            .btn-action-mobile { width: 40px; height: 40px; } /* Tombol lebih besar di mobile */
            .card-skema .card-body { padding: 1rem; }
            .badge-level { font-size: 0.75rem; padding: 0.35em 0.65em; }
        }
        @media (min-width: 768px) {
            .w-md-auto { width: auto !important; }
            .btn-action-mobile { width: 32px; height: 32px; } /* Normal di desktop */
        }
    </style>

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <div class="p-2 me-3">
                <i class="bi bi-diagram-3-fill fs-3 text-gold"></i>
            </div>
            <div>
                <h2 class="h4 fw-bold mb-0 text-dark">Skema Penilaian</h2>
            </div>
        </div>
        
        {{-- TOMBOL TAMBAH --}}
        <div class="w-100 w-md-auto">
            @if($siklus_list->isEmpty())
                <button class="btn btn-secondary shadow-sm px-4 w-100 w-md-auto" disabled style="cursor: not-allowed; opacity: 0.7;">
                    <i class="bi bi-lock-fill me-2"></i>Tambah Skema
                </button>
            @else
                <button class="btn btn-green shadow-sm px-4 w-100 w-md-auto" wire:click="showTambahModal">
                    <i class="bi bi-plus-lg me-2"></i>Tambah Skema
                </button>
            @endif
        </div>
    </div>

    {{-- ALERT PERINGATAN JIKA KOSONG (SIMPLE) --}}
    @if($siklus_list->isEmpty())
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-warning mt-1"></i>
            <div>
                <strong class="d-block text-dark">Belum ada Siklus Semester!</strong>
                <span class="text-muted small">Harap tambahkan data semester terlebih dahulu pada menu <b>Siklus Semester</b> agar dapat membuat skema penilaian.</span>
            </div>
        </div>
    @endif

    {{-- ALERT NOTIFIKASI --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- SIKLUS SELECTOR --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center gy-2">
                <div class="col-12 col-md-auto text-gold fw-bold">
                    <i class="bi bi-calendar-check me-2"></i>Siklus Aktif:
                </div>
                <div class="col-12 col-md-6">
                    <select wire:model.live="siklus_id_aktif" class="form-select border-gold bg-light" 
                            @if($siklus_list->isEmpty()) disabled @endif>
                        
                        @if($siklus_list->isEmpty())
                            <option value="">Data Siklus Kosong</option>
                        @else
                            <option value="">-- Pilih Siklus --</option>
                            @foreach($siklus_list as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->tahun_ajaran }} - {{ $s->semester }} 
                                    @if($s->status == 'Aktif') (AKTIF) @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-12 col-md text-md-end text-muted small">
                    <i class="bi bi-info-circle me-1"></i>Pilih siklus untuk melihat/mengedit skema.
                </div>
            </div>
        </div>
    </div>

    {{-- LIST SKEMA (GRID RESPONSIVE) --}}
    <div class="card border-0 shadow-sm bg-transparent shadow-none">
        <div class="card-header bg-transparent border-0 px-0 pt-0 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-list-task me-2 text-gold"></i>Daftar Skema Tersedia
            </h5>
            @if($siklus_id_aktif)
                <span class="badge bg-white text-dark shadow-sm border align-self-start align-self-md-center">Total: {{ count($daftar_skema) }} Skema</span>
            @endif
        </div>

        <div class="row g-3">
            @forelse($daftar_skema as $skema)
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm card-skema bg-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="w-100 me-2">
                                    <h4 class="h6 fw-bold text-dark mb-2 text-break d-flex align-items-center gap-2">
                                        {{ $skema->nama_skema }}
                                        @if($skema->is_locked)
                                            <i class="bi bi-lock-fill text-muted small" title="Terkunci: Penilaian Berjalan/Selesai"></i>
                                        @endif
                                    </h4>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($skema->level_target as $lvl)
                                            <span class="badge bg-secondary opacity-75 fw-normal rounded-pill badge-level">
                                                Level {{ $lvl }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                
                                {{-- BUTTON GROUP (EDIT & DELETE) --}}
                                <div class="d-flex gap-1 flex-shrink-0">
                                    
                                    {{-- Tombol Edit --}}
                                    <button class="btn btn-sm text-primary hover-bg-light rounded-circle btn-action-mobile d-flex align-items-center justify-content-center p-0" 
                                            wire:click="edit({{ $skema->id }})" title="Edit Skema">
                                        <i class="bi bi-pencil-square fs-5"></i>
                                    </button>

                                    {{-- Tombol Hapus (Conditional Lock) --}}
                                    @if($skema->is_locked)
                                        {{-- TAMPILAN TERKUNCI --}}
                                        <button class="btn btn-sm text-muted hover-bg-light rounded-circle btn-action-mobile d-flex align-items-center justify-content-center p-0" 
                                                onclick="Swal.fire({
                                                    icon: 'info',
                                                    title: 'Skema Terkunci',
                                                    text: 'Skema ini tidak bisa dihapus karena penilaian periode ini sudah dimulai atau selesai.',
                                                    confirmButtonColor: '#c38e44'
                                                })"
                                                title="Terkunci (Tidak bisa dihapus)">
                                            <i class="bi bi-lock-fill fs-5"></i>
                                        </button>
                                    @else
                                        {{-- TAMPILAN BISA HAPUS --}}
                                        <button class="btn btn-sm text-danger hover-bg-light rounded-circle btn-action-mobile d-flex align-items-center justify-content-center p-0" 
                                                wire:click="hapus({{ $skema->id }})"
                                                onclick="confirm('Hapus skema ini?') || event.stopImmediatePropagation()"
                                                title="Hapus Skema">
                                            <i class="bi bi-trash-fill fs-5"></i>
                                        </button>
                                    @endif

                                </div>
                            </div>

                            <div class="progress rounded-pill" style="height: 25px; font-weight: 600; font-size: 0.7rem;">
                                @if($skema->persen_diri > 0)
                                    <div class="progress-bar progress-diri overflow-hidden text-truncate px-1" style="width: {{ $skema->persen_diri }}%">Diri {{ $skema->persen_diri }}%</div>
                                @endif
                                @if($skema->persen_atasan > 0)
                                    <div class="progress-bar progress-atasan overflow-hidden text-truncate px-1" style="width: {{ $skema->persen_atasan }}%">Ats {{ $skema->persen_atasan }}%</div>
                                @endif
                                @if($skema->persen_rekan > 0)
                                    <div class="progress-bar progress-rekan overflow-hidden text-truncate px-1" style="width: {{ $skema->persen_rekan }}%">Rek {{ $skema->persen_rekan }}%</div>
                                @endif
                                @if($skema->persen_bawahan > 0)
                                    <div class="progress-bar progress-bawahan overflow-hidden text-truncate px-1" style="width: {{ $skema->persen_bawahan }}%">Bwh {{ $skema->persen_bawahan }}%</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-white rounded shadow-sm border-0">
                        <i class="bi bi-inbox text-gold opacity-50" style="font-size: 3rem;"></i>
                        @if($siklus_list->isEmpty())
                            <h6 class="mt-3 fw-bold text-danger">Data Siklus Kosong</h6>
                            <p class="text-muted small px-3">Anda harus membuat Siklus Semester terlebih dahulu di menu "Siklus Semester".</p>
                        @else
                            <h6 class="mt-3 fw-bold text-muted">Belum ada skema</h6>
                            <p class="text-muted small px-3">Silakan pilih siklus di atas lalu klik tombol "Tambah Skema".</p>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- MODAL FORM --}}
    <div wire:ignore.self class="modal fade" id="skemaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gold text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi {{ $isEditMode ? 'bi-pencil-square' : 'bi-plus-circle-fill' }} me-2"></i>
                        {{ $isEditMode ? 'Edit Skema' : 'Buat Skema Baru' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body p-3 p-md-4">
                    
                    {{-- [LOGIKA TAMBAHAN] Cek Full tapi tetap izinkan Edit --}}
                    @if(!$isFull || $isEditMode)
                        <form wire:submit="simpan">
                            
                            <div class="mb-3">
                                <label class="fw-bold mb-1 small text-uppercase text-muted">Nama Skema / Aturan</label>
                                <input type="text" class="form-control" wire:model="nama_skema" 
                                       placeholder="Contoh: Skema Pimpinan">
                                @error('nama_skema') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold mb-2 small text-uppercase text-muted">Berlaku untuk Level:</label>
                                <div class="border rounded p-3 bg-light">
                                    <div class="row">
                                        @foreach($masterLevel as $key => $label)
                                            @php
                                                // Cek apakah level ini sudah ada di usedLevels
                                                // Pastikan tipe datanya sama (string/integer)
                                                $isDisabled = in_array((string)$key, array_map('strval', $usedLevels));
                                            @endphp

                                            <div class="col-12 col-sm-6">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="{{ $key }}" 
                                                        wire:model="selected_levels" 
                                                        id="lvl_{{ $key }}"
                                                        @if($isDisabled) disabled @endif> {{-- Disable jika sudah terpakai --}}
                                                    
                                                    <label class="form-check-label {{ $isDisabled ? 'text-muted text-decoration-line-through' : 'text-secondary' }}" 
                                                        for="lvl_{{ $key }}">
                                                        {{ $label }}
                                                        @if($isDisabled) 
                                                            <span class="badge bg-light text-muted border ms-1" style="font-size: 0.65rem;">Terpakai</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('selected_levels') 
                                    <div class="alert alert-warning mt-2 py-2 small d-flex align-items-center">
                                        <i class="bi bi-exclamation-triangle me-2"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold mb-3 small text-uppercase text-muted">Distribusi Bobot (%)</label>
                                @foreach([
                                    'p_diri' => 'Diri Sendiri',
                                    'p_atasan' => 'Atasan',
                                    'p_rekan' => 'Rekan',
                                    'p_bawahan' => 'Bawahan'
                                ] as $field => $label)
                                    <div class="row g-2 mb-2 align-items-center">
                                        <div class="col-4 col-sm-3 text-muted small fw-bold">{{ $label }}</div>
                                        <div class="col-8 col-sm-9">
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control" wire:model="{{ $field }}">
                                                <span class="input-group-text bg-light">%</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @error('total_persen') 
                                    <div class="alert alert-danger mt-3 py-2 text-center fw-bold small">
                                        <i class="bi bi-x-circle me-1"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-gold px-4 shadow-sm">
                                    {{ $isEditMode ? 'Update' : 'Simpan' }}
                                </button>
                            </div>
                        </form>

                    @else
                        {{-- TAMPILAN JIKA PENUH --}}
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="fw-bold text-dark">Semua Level Terisi!</h4>
                            <p class="text-muted small px-3">
                                Semua level jabatan (Level 1 s/d 5) pada siklus ini sudah memiliki skema penilaian.
                            </p>
                            <div class="alert alert-warning d-inline-block mt-2 small">
                                <i class="bi bi-info-circle me-1"></i> Klik tombol Edit (Pensil) pada skema yang ada untuk mengubah.
                            </div>
                            <div class="mt-4">
                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        const modalElement = document.getElementById('skemaModal');
        const modal = modalElement ? new bootstrap.Modal(modalElement) : null;

        @this.on('open-modal', () => { if(modal) modal.show(); });
        @this.on('close-modal', () => { if(modal) modal.hide(); });
    });
</script>
@endpush