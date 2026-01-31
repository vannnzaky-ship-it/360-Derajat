<div class="container-fluid p-4">

    {{-- CUSTOM STYLES & DARK MODE FIX --}}
    <style>
        /* 1. Global Colors (Light Mode) */
        :root { --polkam-gold: #c38e44; --polkam-gold-hover: #a57635; }
        .text-gold { color: var(--polkam-gold) !important; }
        .bg-gold { background-color: var(--polkam-gold) !important; color: white; }
        .border-gold { border-color: var(--polkam-gold) !important; }
        
        /* 2. Custom Buttons */
        .btn-green { background-color: #198754; color: white; border: none; font-weight: 600; font-size: 0.9rem; } 
        .btn-green:hover { background-color: #1a8452; color: white; }
        .btn-gold { background-color: var(--polkam-gold); color: white; border: none; font-weight: 600; }
        .btn-gold:hover { background-color: var(--polkam-gold-hover); color: white; }

        /* 3. Modal Compact Style */
        .modal-dialog-compact { 
            max-width: 500px; 
            margin-top: 50px; 
            margin-bottom: 2rem; 
        }
        
        /* Typography Form Kecil */
        .form-label-sm { 
            font-size: 0.75rem; font-weight: 700; margin-bottom: 3px; 
            color: #666; text-transform: uppercase; letter-spacing: 0.5px; 
        }
        .form-control-sm, .form-select-sm, .input-group-text-sm, .form-check-label-sm { 
            font-size: 0.85rem; 
        }

        /* 4. Card & Progress Bar Styles */
        .card-skema { border-left: 4px solid var(--polkam-gold); transition: transform 0.2s; }
        .card-skema:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.1)!important; }
        .progress-diri { background-color: #0dcaf0; }
        .progress-atasan { background-color: #198754; }
        .progress-rekan { background-color: #ffc107; color: black; }
        .progress-bawahan { background-color: #dc3545; }
        .badge-level { font-size: 0.7rem; padding: 0.35em 0.65em; }

        /* Mobile Fixes */
        @media (max-width: 767px) {
            .btn-action-mobile { width: 35px; height: 35px; }
            .modal-dialog-compact { margin: 10px; max-width: 100%; }
        }
        @media (min-width: 768px) {
            .w-md-auto { width: auto !important; }
            .btn-action-mobile { width: 32px; height: 32px; }
        }

        /* 1. Global Backgrounds & Text */
        [data-bs-theme="dark"] .bg-white {
            background-color: #1e1e1e !important;
            color: #e0e0e0 !important;
        }
        [data-bs-theme="dark"] .text-dark { color: #f8f9fa !important; }
        [data-bs-theme="dark"] .text-secondary { color: #adb5bd !important; }
        [data-bs-theme="dark"] .text-muted { color: #999 !important; }

        /* 2. Card Styles */
        [data-bs-theme="dark"] .card {
            background-color: #1e1e1e !important;
            border-color: #333 !important;
        }
        [data-bs-theme="dark"] .card-skema:hover {
            background-color: #252525 !important;
            box-shadow: 0 .5rem 1rem rgba(0,0,0,0.5) !important;
        }
        
        /* 3. Input Select & Form Control */
        [data-bs-theme="dark"] .form-select,
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .input-group-text {
            background-color: #2c2c2c !important;
            border-color: #444 !important;
            color: #e0e0e0 !important;
        }
        [data-bs-theme="dark"] .bg-light {
            background-color: #2c2c2c !important;
            color: #e0e0e0 !important;
            border-color: #444 !important;
        }
        
        /* 4. Modal Styles */
        [data-bs-theme="dark"] .modal-content {
            background-color: #1e1e1e !important;
            border: 1px solid #444 !important;
        }
        [data-bs-theme="dark"] .modal-header,
        [data-bs-theme="dark"] .modal-footer {
            border-color: #333 !important;
        }
        [data-bs-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        [data-bs-theme="dark"] .form-label-sm { color: #ccc !important; }

        /* 5. Checkbox & Radio Labels */
        [data-bs-theme="dark"] .form-check-label { color: #e0e0e0 !important; }
        [data-bs-theme="dark"] .form-check-label.text-muted { color: #666 !important; }

        /* 6. Progress Bar Container */
        [data-bs-theme="dark"] .progress.bg-light {
            background-color: #333 !important;
            border-color: #444 !important;
        }
    </style>

    {{-- HEADER HALAMAN --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <div class="p-2 me-2">
                <i class="bi bi-diagram-3-fill fs-2 text-gold"></i>
            </div>
            <div>
                <h2 class="h3 mb-0 text-dark">Skema Penilaian</h2>
            </div>
        </div>
        
        <div class="w-100 w-md-auto">
            @if($siklus_list->isEmpty())
                <button class="btn btn-secondary shadow-sm px-4 w-100 w-md-auto" disabled>
                    <i class="bi bi-lock-fill me-2"></i>Tambah Skema
                </button>
            @else
                <button type="button" class="btn btn-success shadow-sm px-4 w-100 w-md-auto" wire:click="showTambahModal">
                    <i class="bi bi-plus-lg me-2"></i>Tambah Skema
                </button>
            @endif
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if($siklus_list->isEmpty())
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4 py-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-5 text-warning"></i>
            <div class="small">
                <strong>Data Siklus Kosong!</strong> Harap tambahkan data semester terlebih dahulu.
            </div>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm py-2" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm py-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTER SIKLUS --}}
    <div class="card border-0 shadow-sm mb-4 bg-white">
        <div class="card-body py-3">
            <div class="row align-items-center gy-2">
                <div class="col-12 col-md-auto text-gold fw-bold small text-uppercase">
                    <i class="bi bi-calendar-check me-2"></i>Siklus Aktif:
                </div>
                <div class="col-12 col-md-5">
                    <select wire:model.live="siklus_id_aktif" class="form-select form-select-sm border-gold bg-light" 
                            @if($siklus_list->isEmpty()) disabled @endif>
                        @if($siklus_list->isEmpty())
                            <option value="">Tidak ada data</option>
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
                <div class="col-12 col-md text-md-end text-muted" style="font-size: 0.75rem;">
                    Total Skema: <strong class="text-dark">{{ count($daftar_skema) }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- LIST SKEMA --}}
    <div class="row g-3">
        @forelse($daftar_skema as $skema)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm card-skema bg-white h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="w-100 me-2">
                                <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $skema->nama_skema }}">
                                    {{ $skema->nama_skema }}
                                    @if($skema->is_locked)
                                        <i class="bi bi-lock-fill text-muted ms-1" style="font-size: 0.7rem;"></i>
                                    @endif
                                </h6>
                                <div class="d-flex flex-wrap gap-1 mb-2">
                                    @foreach($skema->level_target as $lvl)
                                        <span class="badge bg-secondary opacity-75 fw-normal rounded-pill badge-level">Lvl {{ $lvl }}</span>
                                    @endforeach
                                </div>
                            </div>
                            
                            {{-- Action Buttons --}}
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-outline-primary border-0 rounded-circle btn-action-mobile d-flex align-items-center justify-content-center" 
                                        wire:click="edit({{ $skema->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                @if($skema->is_locked)
                                    <button class="btn btn-sm btn-outline-secondary border-0 rounded-circle btn-action-mobile d-flex align-items-center justify-content-center" 
                                            onclick="Swal.fire({icon: 'info', title: 'Terkunci', text: 'Skema sedang digunakan dalam penilaian.', confirmButtonColor: '#c38e44'})">
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-danger border-0 rounded-circle btn-action-mobile d-flex align-items-center justify-content-center" 
                                            wire:click="hapus({{ $skema->id }})"
                                            onclick="confirm('Hapus skema ini?') || event.stopImmediatePropagation()">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Progress Bar Bobot --}}
                        <div class="progress rounded-pill bg-light border" style="height: 20px; font-weight: 700; font-size: 0.65rem;">
                            @if($skema->persen_diri > 0)
                                <div class="progress-bar progress-diri" style="width: {{ $skema->persen_diri }}%">Diri {{ $skema->persen_diri }}%</div>
                            @endif
                            @if($skema->persen_atasan > 0)
                                <div class="progress-bar progress-atasan" style="width: {{ $skema->persen_atasan }}%">Ats {{ $skema->persen_atasan }}%</div>
                            @endif
                            @if($skema->persen_rekan > 0)
                                <div class="progress-bar progress-rekan" style="width: {{ $skema->persen_rekan }}%">Rek {{ $skema->persen_rekan }}%</div>
                            @endif
                            @if($skema->persen_bawahan > 0)
                                <div class="progress-bar progress-bawahan" style="width: {{ $skema->persen_bawahan }}%">Bwh {{ $skema->persen_bawahan }}%</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded shadow-sm">
                    <i class="bi bi-inbox text-gold opacity-25" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2 small">
                        @if($siklus_list->isEmpty()) Data siklus belum tersedia. @else Belum ada skema pada siklus ini. @endif
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- MODAL FORM (COMPACT & BLUR) --}}
    <div wire:ignore.self class="modal fade" id="skemaModal" tabindex="-1" aria-hidden="true" 
         style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(5px);"> 
        
        <div class="modal-dialog modal-dialog-centered modal-dialog-compact">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                
                {{-- HEADER (Style Clean) --}}
                <div class="modal-header py-2 px-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 text-warning rounded p-1 me-2">
                            <i class="bi bi-diagram-3-fill fs-6"></i>
                        </div>
                        <h6 class="modal-title fw-bold m-0 text-dark" id="skemaModalLabel">
                            {{ $isEditMode ? 'Edit Skema' : 'Buat Skema' }}
                        </h6>
                    </div>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-3">
                    
                    {{-- CEK JIKA FULL & BUKAN EDIT MODE --}}
                    @if($isFull && !$isEditMode)
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle-fill text-success mb-3" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold text-dark">Semua Level Terisi!</h6>
                            <p class="text-muted small px-3 mb-0">
                                Semua level jabatan (1-5) pada siklus ini sudah memiliki skema. Edit skema yang ada jika ingin mengubah.
                            </p>
                        </div>
                        <div class="modal-footer bg-light border-top py-2 px-3 justify-content-center">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    
                    @else
                        {{-- FORM INPUT --}}
                        <form wire:submit="simpan">
                            
                            {{-- 1. Nama Skema --}}
                            <div class="mb-3">
                                <label class="form-label-sm">Nama Skema <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" wire:model="nama_skema" placeholder="Contoh: Skema Pimpinan">
                                @error('nama_skema') <div class="text-danger small mt-1" style="font-size: 0.7rem;">{{ $message }}</div> @enderror
                            </div>

                            {{-- 2. Level Target (Checkboxes Compact) --}}
                            <div class="mb-3">
                                <label class="form-label-sm">Berlaku untuk Level <span class="text-danger">*</span></label>
                                <div class="border rounded p-2 bg-light">
                                    <div class="row g-2">
                                        @foreach($masterLevel as $key => $label)
                                            @php
                                                // Cek disable jika level sudah dipakai (kecuali sedang diedit)
                                                $isDisabled = in_array((string)$key, array_map('strval', $usedLevels));
                                            @endphp
                                            <div class="col-6">
                                                <div class="form-check small mb-0">
                                                    <input class="form-check-input" type="checkbox" value="{{ $key }}" 
                                                           wire:model="selected_levels" id="lvl_{{ $key }}"
                                                           @if($isDisabled) disabled @endif>
                                                    <label class="form-check-label form-check-label-sm {{ $isDisabled ? 'text-muted text-decoration-line-through' : 'text-dark' }}" for="lvl_{{ $key }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('selected_levels') 
                                    <div class="text-danger small mt-1" style="font-size: 0.7rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- 3. Distribusi Bobot (Grid 2x2 Compact) --}}
                            <div class="mb-2">
                                <label class="form-label-sm">Distribusi Bobot (%) <span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    @php
                                        $fields = [
                                            'p_diri' => 'Diri Sendiri', 'p_atasan' => 'Atasan',
                                            'p_rekan' => 'Rekan', 'p_bawahan' => 'Bawahan'
                                        ];
                                    @endphp
                                    @foreach($fields as $field => $label)
                                        <div class="col-6">
                                            <label class="small text-muted mb-0" style="font-size: 0.7rem;">{{ $label }}</label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control form-control-sm" wire:model="{{ $field }}" placeholder="0">
                                                <span class="input-group-text bg-white text-muted px-1">%</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('total_persen') 
                                    <div class="alert alert-danger mt-2 py-1 px-2 small mb-0 d-flex align-items-center" style="font-size: 0.75rem;">
                                        <i class="bi bi-exclamation-circle me-2"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- FOOTER COMPACT --}}
                            <div class="modal-footer bg-light border-top py-2 px-3 mt-3 mx-n3 mb-n3">
                                <button type="button" class="btn btn-sm btn-secondary border" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-sm btn-gold text-white px-3 shadow-sm">
                                    {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Data' }}
                                </button>
                            </div>
                        </form>
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
        const modal = modalElement ? new bootstrap.Modal(modalElement, { backdrop: 'static', keyboard: false }) : null;

        @this.on('open-modal', () => { if(modal) modal.show(); });
        @this.on('close-modal', () => { if(modal) modal.hide(); });
    });
</script>
@endpush