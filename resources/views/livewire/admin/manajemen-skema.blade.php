<div class="container-fluid p-4">

    {{-- CUSTOM STYLES --}}
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
    </style>

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="p-2 me-3">
                <i class="bi bi-diagram-3-fill fs-3 text-gold"></i>
            </div>
            <div>
                <h2 class="h4 fw-bold mb-0 text-dark">Skema Penilaian</h2>
            </div>
        </div>
        
        {{-- TOMBOL TAMBAH --}}
        <div>
            @if($siklus_list->isEmpty())
                {{-- Tombol Disabled (Non-aktif) --}}
                <button class="btn btn-secondary shadow-sm px-4" disabled style="cursor: not-allowed; opacity: 0.7;">
                    <i class="bi bi-lock-fill me-2"></i>Tambah Skema
                </button>
            @else
                {{-- Tombol Aktif --}}
                <button class="btn btn-green shadow-sm px-4" wire:click="showTambahModal">
                    <i class="bi bi-plus-lg me-2"></i>Tambah Skema
                </button>
            @endif
        </div>
    </div>

    {{-- ALERT PERINGATAN JIKA KOSONG (SIMPLE) --}}
    @if($siklus_list->isEmpty())
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-warning"></i>
            <div>
                <strong class="d-block text-dark">Belum ada Siklus Semester!</strong>
                <span class="text-muted">Harap tambahkan data semester terlebih dahulu pada menu <b>Siklus Semester</b> agar dapat membuat skema penilaian.</span>
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
            <div class="row align-items-center">
                <div class="col-md-auto text-gold fw-bold">
                    <i class="bi bi-calendar-check me-2"></i>Siklus Aktif:
                </div>
                <div class="col-md-6">
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
                <div class="col-md text-end text-muted small">
                    <i class="bi bi-info-circle me-1"></i>Pilih siklus untuk melihat/mengedit skema.
                </div>
            </div>
        </div>
    </div>

    {{-- LIST SKEMA (Full Width) --}}
    <div class="card border-0 shadow-sm bg-transparent shadow-none">
        <div class="card-header bg-transparent border-0 px-0 pt-0 d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-list-task me-2 text-gold"></i>Daftar Skema Tersedia
            </h5>
            @if($siklus_id_aktif)
                <span class="badge bg-white text-dark shadow-sm border">Total: {{ count($daftar_skema) }} Skema</span>
            @endif
        </div>

        <div class="row">
            @forelse($daftar_skema as $skema)
                <div class="col-md-6">
                    <div class="card mb-3 border-0 shadow-sm card-skema bg-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="h5 fw-bold text-dark mb-2">{{ $skema->nama_skema }}</h4>
                                    <div class="mb-3">
                                        @foreach($skema->level_target as $lvl)
                                            <span class="badge bg-secondary opacity-75 fw-normal me-1 rounded-pill px-3">
                                                Level {{ $lvl }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                
                                {{-- BUTTON GROUP (EDIT & DELETE) --}}
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm text-primary hover-bg-light rounded-circle" 
                                            wire:click="edit({{ $skema->id }})" title="Edit Skema"
                                            style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-pencil-square fs-5"></i>
                                    </button>
                                    <button class="btn btn-sm text-danger hover-bg-light rounded-circle" 
                                            wire:click="hapus({{ $skema->id }})"
                                            onclick="confirm('Hapus skema ini?') || event.stopImmediatePropagation()"
                                            title="Hapus Skema"
                                            style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-trash-fill fs-5"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="progress rounded-pill" style="height: 25px; font-weight: 600; font-size: 0.75rem;">
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
                <div class="col-12 text-center py-5 bg-white rounded shadow-sm border-0">
                    <i class="bi bi-inbox text-gold opacity-50" style="font-size: 4rem;"></i>
                    @if($siklus_list->isEmpty())
                        <h6 class="mt-3 fw-bold text-danger">Data Siklus Kosong</h6>
                        <p class="text-muted small">Anda harus membuat Siklus Semester terlebih dahulu di menu "Siklus Semester".</p>
                    @else
                        <h6 class="mt-3 fw-bold text-muted">Belum ada skema</h6>
                        <p class="text-muted small">Silakan pilih siklus di atas lalu klik tombol "Tambah Skema".</p>
                    @endif
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
                
                <div class="modal-body p-4">
                    {{-- 
                        LOGIKA TAMPILAN MODAL:
                        Tampilkan FORM jika:
                        1. Belum Penuh ($isFull == false)
                        2. ATAU Sedang Edit ($isEditMode == true)
                    --}}
                    @if(!$isFull || $isEditMode)
                        <form wire:submit="simpan">
                            
                            <div class="mb-3">
                                <label class="fw-bold mb-1">Nama Skema / Aturan</label>
                                <input type="text" class="form-control" wire:model="nama_skema" 
                                       placeholder="Contoh: Skema Pimpinan (Ada Bawahan)">
                                @error('nama_skema') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold mb-2">Berlaku untuk Level:</label>
                                <div class="border rounded p-3">
                                    @foreach($masterLevel as $key => $label)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value="{{ $key }}" 
                                                   wire:model="selected_levels" id="lvl_{{ $key }}">
                                            <label class="form-check-label text-secondary" for="lvl_{{ $key }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('selected_levels') 
                                    <div class="alert alert-warning mt-2 py-2 small d-flex align-items-center">
                                        <i class="bi bi-exclamation-triangle me-2"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold mb-3">Distribusi Bobot (%)</label>
                                <div class="row g-2 mb-2 align-items-center">
                                    <div class="col-3 text-muted small fw-bold">Diri Sendiri</div>
                                    <div class="col-9"><div class="input-group input-group-sm"><input type="number" class="form-control" wire:model="p_diri"><span class="input-group-text">%</span></div></div>
                                </div>
                                <div class="row g-2 mb-2 align-items-center">
                                    <div class="col-3 text-muted small fw-bold">Atasan</div>
                                    <div class="col-9"><div class="input-group input-group-sm"><input type="number" class="form-control" wire:model="p_atasan"><span class="input-group-text">%</span></div></div>
                                </div>
                                <div class="row g-2 mb-2 align-items-center">
                                    <div class="col-3 text-muted small fw-bold">Rekan</div>
                                    <div class="col-9"><div class="input-group input-group-sm"><input type="number" class="form-control" wire:model="p_rekan"><span class="input-group-text">%</span></div></div>
                                </div>
                                <div class="row g-2 mb-2 align-items-center">
                                    <div class="col-3 text-muted small fw-bold">Bawahan</div>
                                    <div class="col-9"><div class="input-group input-group-sm"><input type="number" class="form-control" wire:model="p_bawahan"><span class="input-group-text">%</span></div></div>
                                </div>

                                @error('total_persen') 
                                    <div class="alert alert-danger mt-3 py-2 text-center fw-bold small">
                                        <i class="bi bi-x-circle me-1"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-gold px-4 shadow-sm">
                                    {{ $isEditMode ? 'Update Perubahan' : 'Simpan Skema' }}
                                </button>
                            </div>
                        </form>

                    @else
                        {{-- TAMPILAN JIKA PENUH DAN BUKAN EDIT --}}
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="fw-bold text-dark">Semua Level Sudah Terisi!</h4>
                            <p class="text-muted">
                                Semua level jabatan (Level 1 s/d 5) pada siklus ini sudah memiliki skema penilaian.<br>
                                Anda tidak perlu menambahkan skema baru lagi.
                            </p>
                            <div class="alert alert-warning d-inline-block mt-2">
                                <i class="bi bi-info-circle me-1"></i> Jika ingin mengubah, silakan klik tombol Edit (Pensil) pada skema yang ada.
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
        const modal = new bootstrap.Modal(modalElement);

        @this.on('open-modal', () => { modal.show(); });
        @this.on('close-modal', () => { modal.hide(); });
    });
</script>
@endpush