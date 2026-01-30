<div>
    <style>
    /* --- 1. STYLE GLOBAL & TABLE (DEFAULT LIGHT) --- */
    body { background-color: #f8f9fa; }
    
    .table-floating { border-collapse: separate; border-spacing: 0 15px; }
    
    .row-floating { 
        background-color: #ffffff; /* Default Putih */
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.05); 
        transition: transform 0.2s, background-color 0.3s;
    }
    .row-floating:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
    }

    .row-floating td:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
    .row-floating td:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }

    .avatar-circle {
        width: 45px; height: 45px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: #fff; background-color: #C38E44;
        font-size: 1.1rem; flex-shrink: 0;
        border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .avatar-circle img { width: 100%; height: 100%; object-fit: cover; }

    /* --- 2. STYLE MODAL --- */
    .modal-dialog-compact { max-width: 550px; margin-top: 60px; margin-bottom: 2rem; }
    .modal-body-compact { max-height: 70vh; overflow-y: auto; padding: 15px 20px !important; }
    .form-label-sm { font-size: 0.75rem; font-weight: 700; margin-bottom: 2px; color: #555; }
    .section-divider { 
        font-size: 0.65rem; font-weight: 800; color: #c38e44; 
        letter-spacing: 1px; text-transform: uppercase; 
        margin: 15px 0 10px 0; border-bottom: 1px solid #eee; padding-bottom: 3px; 
    }
    .accordion-button-custom:not(.collapsed) {
        color: #C38E44 !important; background-color: rgba(195, 142, 68, 0.1) !important;
    }
    .badge-jabatan {
        color: #8f6222 !important; background-color: #fff3cd !important;
        border: 1px solid #ffe69c !important; padding: 4px 8px;
        border-radius: 6px; font-weight: 600; font-size: 0.7rem;
        display: inline-flex; align-items: center; gap: 4px;
    }

    /* --- 3. RESPONSIVE MOBILE --- */
    @media (max-width: 768px) {
        .table-floating thead { display: none; }
        .table-floating, .table-floating tbody, .table-floating tr, .table-floating td { display: block; width: 100%; }
        .table-floating tr { margin-bottom: 1rem; border-radius: 12px; }
        
        .row-floating td:first-child { border-radius: 12px 12px 0 0; background: linear-gradient(to bottom, #fdfbf7, #fff); }
        .row-floating td:last-child { border-radius: 0 0 12px 12px; border-top: 1px solid #eee; }
        
        .row-floating td { padding: 10px 15px; text-align: left; }
    }

    /* ========================================= */
    /* FIX DARK MODE (TAMBAHAN PENTING)      */
    /* ========================================= */
    
    /* 1. Global Background & Text */
    [data-bs-theme="dark"] body, 
    [data-bs-theme="dark"] .container-fluid {
        background-color: #121212 !important; /* Hitam Gelap */
        color: #e0e0e0 !important;
    }
    
    /* 2. Baris Tabel Mengambang */
    [data-bs-theme="dark"] .row-floating {
        background-color: #1e1e1e !important; /* Abu Gelap */
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.5);
    }
    
    /* 3. Text Colors Fix */
    [data-bs-theme="dark"] .text-dark { color: #f8f9fa !important; }
    [data-bs-theme="dark"] .text-secondary { color: #adb5bd !important; }
    [data-bs-theme="dark"] .text-muted { color: #6c757d !important; }

    /* 4. Input Search & Background Putih Lainnya */
    [data-bs-theme="dark"] .bg-white {
        background-color: #1e1e1e !important;
        color: #e0e0e0 !important;
        border-color: #333 !important;
    }
    [data-bs-theme="dark"] .input-group-text {
        background-color: transparent !important;
        border-color: #333 !important;
    }
    [data-bs-theme="dark"] .form-control {
        background-color: transparent !important;
        color: #fff !important;
        border-color: #444 !important;
    }
    [data-bs-theme="dark"] .form-control::placeholder {
        color: #6c757d;
    }

    /* 5. Avatar & Badge Fixes */
    [data-bs-theme="dark"] .avatar-circle {
        border-color: #333 !important; 
    }
    [data-bs-theme="dark"] .badge.bg-light {
        background-color: #2d2d2d !important;
        color: #e0e0e0 !important;
        border-color: #444 !important;
    }

    /* 6. Mobile View Fixes for Dark Mode */
    [data-bs-theme="dark"] .row-floating td:first-child {
        background: linear-gradient(to bottom, #2c2c2c, #1e1e1e) !important;
    }
    [data-bs-theme="dark"] .row-floating td:last-child {
        border-top: 1px solid #333 !important;
    }

    /* 7. Modal Dark Mode */
    [data-bs-theme="dark"] .modal-content {
        background-color: #1e1e1e !important;
        border: 1px solid #333 !important;
    }
    [data-bs-theme="dark"] .modal-header, 
    [data-bs-theme="dark"] .modal-footer {
        border-color: #333 !important;
    }
    [data-bs-theme="dark"] .form-label-sm {
        color: #bbb !important;
    }
    [data-bs-theme="dark"] .section-divider {
        border-bottom-color: #333 !important;
    }
    [data-bs-theme="dark"] .list-group-item {
        background-color: #1e1e1e !important;
        color: #e0e0e0 !important;
        border-color: #333 !important;
    }
    [data-bs-theme="dark"] .accordion-button {
        background-color: #1e1e1e !important;
        color: #e0e0e0 !important;
        box-shadow: none !important;
    }
</style>

    <div class="container-fluid p-4" style="background-color: #f8f9fa; min-height: 100vh;">
        
        {{-- HEADER PAGE --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5">
            <div class="mb-3 mb-md-0 text-center text-md-start">
                <h1 class="h3 text-dark mb-1">
                    <i class="bi bi-people-fill me-2" style="color: #C38E44;"></i>Manajemen Data Pegawai
                </h1>
            </div>

            <div class="d-flex flex-column flex-md-row gap-2 col-12 col-md-auto">
                <div class="input-group shadow-sm rounded-pill bg-white" style="min-width: 280px;">
                    <span class="input-group-text bg-transparent border-0 ps-3">
                        <i class="bi bi-search text-secondary"></i>
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control bg-transparent border-0 py-2" placeholder="Cari nama atau NIP...">
                </div>

                <button wire:click="showTambahModal" class="btn shadow-sm rounded-pill px-4 fw-bold text-white d-flex align-items-center justify-content-center gap-2" style="background-color: #C38E44;">
                    <i class="bi bi-plus-lg"></i> <span>Tambah</span>
                </button>
            </div>
        </div>

        {{-- ALERT MESSAGE --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle p-1 me-2 d-flex justify-content-center align-items-center" style="width: 24px; height: 24px;">
                        <i class="bi bi-check small"></i>
                    </div>
                    <div class="fw-bold">{{ session('message') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- TABEL DATA --}}
        <div class="table-responsive">
            <table class="table table-borderless align-middle table-floating">
                <thead>
                    <tr class="text-secondary small text-uppercase" style="letter-spacing: 0.5px;">
                        <th class="fw-bold ps-4" style="width: 30%;">Profil Pegawai</th>
                        <th class="fw-bold text-center" style="width: 15%;">NRP / ID</th>
                        <th class="fw-bold text-center" style="width: 35%;">Jabatan & Peran</th>
                        <th class="fw-bold text-center" style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pegawaiList as $pegawai)
                        <tr wire:key="pegawai-{{ $pegawai->id }}" class="row-floating">
                            
                            {{-- 1. PROFIL --}}
                            <td class="py-3 ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">
                                        @if($pegawai->user->profile_photo_path)
                                            <img src="{{ asset('storage/' . $pegawai->user->profile_photo_path) }}" alt="Avatar">
                                        @else
                                            {{ strtoupper(substr($pegawai->user->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $pegawai->user->name }}</span>
                                        <span class="text-secondary small">{{ $pegawai->user->email }}</span>
                                        
                                        {{-- NO HP & ICON (DITAMPILKAN KEMBALI) --}}
                                        @if($pegawai->no_hp)
                                            <span class="text-muted small mt-1">
                                                <i class="bi bi-whatsapp text-success me-1"></i>{{ $pegawai->no_hp }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- 2. NIP --}}
                            <td class="py-3 text-center">
                                <span class="d-md-none fw-bold text-secondary small d-block mb-1">NIP/NRP</span>
                                <span class="badge bg-light text-dark border fw-bold" style="font-family: monospace; font-size: 0.9rem;">
                                    {{ $pegawai->nip }}
                                </span>
                            </td>

                            {{-- 3. JABATAN & PERAN --}}
                            <td class="py-3 text-center">
                                <span class="d-md-none fw-bold text-secondary small d-block mb-1">JABATAN</span>
                                <div class="d-flex flex-column gap-2 align-items-center">
                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                        @forelse ($pegawai->jabatans as $jabatan)
                                            <div class="badge-jabatan">
                                                <i class="bi bi-briefcase-fill opacity-50"></i> {{ $jabatan->nama_jabatan }}
                                            </div>
                                        @empty
                                            <span class="text-muted small fst-italic">- Tidak ada jabatan -</span>
                                        @endforelse
                                    </div>
                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                        @foreach ($pegawai->user->roles as $role)
                                            <span class="badge rounded-pill border bg-light text-secondary fw-normal px-2">
                                                {{ $role->label }}
                                                @if($role->name === 'peninjau') <i class="bi bi-star-fill text-warning ms-1"></i> @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </td>

                            {{-- 4. AKSI --}}
                            <td class="py-3 text-center">
                                <div class="d-flex justify-content-end justify-content-md-center w-100 gap-2">
                                    <button wire:click="edit({{ $pegawai->id }})" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold border-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button wire:click="confirmDelete({{ $pegawai->user_id }})" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold border-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center opacity-50">
                                    <div class="bg-white p-3 rounded-circle shadow-sm mb-3">
                                        <i class="bi bi-search display-6 text-secondary"></i>
                                    </div>
                                    <h6 class="fw-bold text-secondary">Data tidak ditemukan</h6>
                                    <p class="small text-muted">Coba kata kunci lain atau tambahkan data baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($pegawaiList->hasPages())
            <div class="d-flex justify-content-center mt-4">
                 {{ $pegawaiList->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    {{-- MODAL (TETAP SAMA) --}}
    <div class="modal fade @if($showModal) show d-block @endif" 
         id="pegawaiModal" tabindex="-1" aria-labelledby="pegawaiModalLabel" 
         aria-hidden="{{ !$showModal ? 'true' : 'false' }}" 
         style="@if($showModal) background-color: rgba(0,0,0,0.6); backdrop-filter: blur(2px); z-index: 9999 !important; @endif">

        <div class="modal-dialog modal-dialog-centered modal-dialog-compact">
            <form wire:submit="store" class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                <div class="modal-header py-2 px-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 text-warning rounded p-1 me-2">
                            <i class="bi bi-person-plus-fill fs-6"></i>
                        </div>
                        <h6 class="modal-title fw-bold m-0" id="pegawaiModalLabel">
                            {{ $isEditMode ? 'Edit Pegawai' : 'Tambah Pegawai' }}
                        </h6>
                    </div>
                    <button type="button" class="btn-close btn-sm" wire:click="closeModal" aria-label="Close"></button>
                </div>

                <div class="modal-body modal-body-compact">
                    {{-- A. Data Diri --}}
                    <div class="section-divider mt-0">Data Pribadi</div>
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label-sm">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" class="form-control form-control-sm" placeholder="Nama...">
                            @error('name') <span class="text-danger" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                             <label class="form-label-sm">NRP <span class="text-danger">*</span></label>
                             <input type="text" wire:model="nip" class="form-control form-control-sm" placeholder="NRP...">
                             @error('nip') <span class="text-danger" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                             <label class="form-label-sm">No. Handphone <span class="text-muted fst-italic fw-normal">(Opsional)</span></label>
                             <input type="text" wire:model="no_hp" class="form-control form-control-sm" placeholder="08...">
                             @error('no_hp') <span class="text-danger" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-sm">Email <span class="text-danger">*</span></label>
                            <input type="email" wire:model="email" class="form-control form-control-sm" placeholder="Email...">
                            @error('email') <span class="text-danger" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label-sm">Password</label>
                            <input type="password" wire:model="password" class="form-control form-control-sm" 
                                   placeholder="{{ $isEditMode ? '(Biarkan kosong)' : 'Password...' }}">
                            @error('password') <span class="text-danger" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- B. Jabatan & Peran (LOGIC ACCORDION) --}}
                    <div class="section-divider">Akses & Jabatan</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold opacity-75 mb-2" style="font-size: 0.8rem">
                                Pilih Jabatan Struktural <span class="text-danger">*</span>
                            </label>
                            @error('selectedJabatans') 
                                <div class="alert alert-danger py-2 px-3 mb-2 d-flex align-items-center" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div style="font-size: 0.8rem;">{{ $message }}</div>
                                </div> 
                            @enderror

                            <div class="border rounded p-0 bg-white shadow-sm" style="max-height: 400px; overflow-y: auto;">
                                @if(isset($groupedJabatans) && $groupedJabatans->isNotEmpty())
                                    <div class="accordion accordion-flush" id="accordionJabatanInline">
                                        @foreach($groupedJabatans as $namaBidang => $listJabatan)
                                            <div class="accordion-item border-bottom" wire:key="bidang-{{ Str::slug($namaBidang) }}">
                                                <h2 class="accordion-header" id="head-{{ Str::slug($namaBidang) }}">
                                                    <button class="accordion-button accordion-button-custom collapsed fw-bold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#coll-{{ Str::slug($namaBidang) }}">
                                                        <span class="badge me-2 rounded-pill" style="background-color: #C38E44;">{{ $listJabatan->count() }}</span>
                                                        {{ $namaBidang ? $namaBidang : 'Lainnya' }}
                                                    </button>
                                                </h2>
                                                <div id="coll-{{ Str::slug($namaBidang) }}" class="accordion-collapse collapse" data-bs-parent="#accordionJabatanInline" wire:ignore.self>
                                                    <div class="accordion-body p-0">
                                                        <ul class="list-group list-group-flush">
                                                            @foreach($listJabatan as $jabatan)
                                                                @php
                                                                    $isTaken = in_array($jabatan->id, $takenSingletonJabatans);
                                                                    $isSelected = in_array($jabatan->id, $selectedJabatans);
                                                                    $paddingLeft = ($jabatan->indent_level ?? 0) * 25; 
                                                                @endphp
                                                                <li class="list-group-item py-2 {{ $isTaken && !$isSelected ? 'bg-secondary bg-opacity-10' : '' }}" style="padding-left: {{ $paddingLeft + 20 }}px;" wire:key="jab-{{ $jabatan->id }}">
                                                                    <div class="form-check d-flex align-items-center">
                                                                        @if(($jabatan->indent_level ?? 0) > 0)<span class="text-muted me-1 opacity-50" style="font-size: 0.8em;">↳</span>@endif
                                                                        <input class="form-check-input me-2" type="checkbox" value="{{ $jabatan->id }}" wire:model.live="selectedJabatans" id="chk-{{ $jabatan->id }}" onclick="event.stopPropagation()" {{ $isTaken && !$isSelected ? 'disabled' : '' }}>
                                                                        <label class="form-check-label w-100 cursor-pointer" for="chk-{{ $jabatan->id }}" onclick="event.stopPropagation()">
                                                                            <span class="{{ $isTaken && !$isSelected ? 'text-muted text-decoration-line-through' : '' }}">{{ $jabatan->nama_jabatan }}</span>
                                                                            @if($isTaken && !$isSelected)<small class="text-danger ms-1 fst-italic" style="font-size: 0.7em;">(Terisi)</small>@endif
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted"><p class="mb-0 small">Data jabatan belum tersedia.</p></div>
                                @endif
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label-sm opacity-75 mb-1">Role Aplikasi <span class="text-danger">*</span></label>
                            @error('selectedRoles') <div class="text-danger fw-bold" style="font-size: 0.65rem;">{{ $message }}</div> @enderror
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach ($roleList as $role)
                                    @php
                                        $isPeninjau = $role->name === 'peninjau';
                                        $isDisabled = $isPeninjau && $peninjauTakenBy;
                                        $pesanError = "Role Peninjau sudah digunakan oleh akun: " . addslashes($peninjauTakenBy ?? '') . ".";
                                    @endphp
                                    <div class="form-check form-check-inline border rounded px-2 py-1 m-0 d-flex align-items-center {{ $isDisabled ? 'bg-secondary bg-opacity-10 border-secondary' : '' }}"
                                       @if($isDisabled) onclick="Swal.fire({icon: 'warning', title: 'Akses Dibatasi', text: '{{ $pesanError }}', confirmButtonColor: '#c38e44'})" @endif>
                                        <input class="form-check-input m-0 me-2" type="checkbox" wire:model="selectedRoles" value="{{ $role->id }}" id="sm-role-{{ $role->id }}" @if($isDisabled) disabled style="pointer-events: none;" @endif> 
                                        <label class="form-check-label d-flex align-items-center" for="sm-role-{{ $role->id }}" style="font-size: 0.75rem; cursor: {{ $isDisabled ? 'not-allowed' : 'pointer' }}; @if($isDisabled) pointer-events: none; @endif">
                                            <span class="{{ $isDisabled ? 'text-muted' : '' }}">{{ $role->label }}</span>
                                            @if($isPeninjau && $isDisabled) 
                                                <span class="badge bg-danger bg-opacity-10 text-danger ms-2" style="font-size: 0.65em;"><i class="bi bi-lock-fill me-1"></i> {{ Str::limit($peninjauTakenBy, 8) }}</span>
                                            @elseif($isPeninjau)
                                                <i class="bi bi-star-fill text-warning ms-1" style="font-size: 0.6rem;"></i> 
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2 px-3 border-top">
                    <button type="button" class="btn btn-sm btn-secondary border" wire:click="closeModal">Batal</button>
                    <button type="submit" class="btn btn-sm text-white px-3 fw-bold shadow-sm" style="background-color: #c38e44;">
                        <span wire:loading.remove wire:target="store">Simpan</span>
                        <span wire:loading wire:target="store"><span class="spinner-border spinner-border-sm me-1"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('show-delete-confirmation', (id) => {
                Swal.fire({
                    title: 'Hapus Pegawai?',
                    text: "Data tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) { @this.call('destroy', id); }
                });
            });
        });
    </script>
    @endpush
</div>