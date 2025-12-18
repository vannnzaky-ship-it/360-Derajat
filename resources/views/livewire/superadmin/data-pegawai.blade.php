<div>
    {{-- CSS CUSTOM: MODAL & DARK MODE --}}
    <style>
        /* 1. UKURAN MODAL & POSISI */
        .modal-dialog-compact {
            max-width: 550px; 
            margin-top: 60px;
            margin-bottom: 2rem;
        }

        /* 2. SCROLLBAR CUSTOM */
        .modal-body-compact {
            max-height: 70vh;
            overflow-y: auto;
            padding: 15px 20px !important;
        }
        .modal-body-compact::-webkit-scrollbar { width: 5px; }
        .modal-body-compact::-webkit-scrollbar-track { background: #f1f1f1; }
        .modal-body-compact::-webkit-scrollbar-thumb { background: #c38e44; border-radius: 10px; }

        /* 3. STYLE FORM COMPACT (Mode Terang Default) */
        .form-label-sm { font-size: 0.75rem; font-weight: 700; margin-bottom: 2px; color: #555; }
        .section-divider { 
            font-size: 0.65rem; font-weight: 800; color: #c38e44; 
            letter-spacing: 1px; text-transform: uppercase; 
            margin: 15px 0 10px 0; border-bottom: 1px solid #eee; padding-bottom: 3px; 
        }
        .accordion-button-custom:not(.collapsed) {
            color: #C38E44 !important;
            background-color: rgba(195, 142, 68, 0.1) !important;
            box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
        }
        .accordion-button-custom:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23C38E44'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }
        
        /* --- KHUSUS DARK MODE --- */
        [data-bs-theme="dark"] .modal-content {
            background-color: #212529; /* Warna dasar modal gelap */
            color: #e9ecef;
            border: 1px solid #495057;
        }
        [data-bs-theme="dark"] .modal-header,
        [data-bs-theme="dark"] .modal-footer {
            border-color: #495057;
            background-color: #212529 !important; /* Hapus putih/light */
        }
        [data-bs-theme="dark"] .form-control {
            background-color: #2b3035;
            border-color: #495057;
            color: #fff;
        }
        [data-bs-theme="dark"] .form-control::placeholder {
            color: #adb5bd;
        }
        [data-bs-theme="dark"] .form-label-sm {
            color: #ced4da; /* Label jadi terang */
        }
        [data-bs-theme="dark"] .bg-white {
            background-color: #2b3035 !important;
        }
        [data-bs-theme="dark"] .list-group-item {
            background-color: #2b3035; /* List jabatan gelap */
            border-color: #495057;
            color: #fff;
        }
        [data-bs-theme="dark"] .accordion-button {
            background-color: #212529;
            color: #fff;
        }
        [data-bs-theme="dark"] .accordion-button:not(.collapsed) {
             background-color: rgba(195, 142, 68, 0.2) !important;
        }
        [data-bs-theme="dark"] .form-check-input {
            background-color: #343a40;
            border-color: #6c757d;
        }
        [data-bs-theme="dark"] .form-check-input:checked {
            background-color: #C38E44;
            border-color: #C38E44;
        }
        /* Scrollbar track gelap */
        [data-bs-theme="dark"] .modal-body-compact::-webkit-scrollbar-track { background: #2b3035; }

        /* Avatar Inisial (Lingkaran Huruf) */
        .avatar-initial {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #C38E44;
            background-color: rgba(195, 142, 68, 0.15);
            border-radius: 50%;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        /* NIP Styling */
        .font-monospace-custom {
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
            letter-spacing: 0.5px;
            background: rgba(0,0,0,0.05);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Hover Effect pada Row Tabel */
        .table-hover-custom tbody tr {
            transition: all 0.2s ease;
        }
        .table-hover-custom tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            z-index: 1;
            position: relative;
            background-color: #fff !important; /* Warna hover di light mode */
        }

        /* Badge yang lebih modern */
        .badge-soft {
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        /* DARK MODE ADJUSTMENTS */
        [data-bs-theme="dark"] .table-hover-custom tbody tr:hover {
            background-color: #2b3035 !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        [data-bs-theme="dark"] .font-monospace-custom {
            background: rgba(255,255,255,0.1);
            color: #e9ecef;
        }
        [data-bs-theme="dark"] .text-muted {
            color: #adb5bd !important;
        }

        /* 1. Badge Jabatan (Style Default / Light Mode) */
        .badge-jabatan {
            /* Gunakan coklat yang lebih gelap (#8f6222) daripada emas (#C38E44) agar teks terbaca jelas di putih */
            color: #8f6222 !important; 
            background-color: #fff3cd !important; /* Kuning/Cream lembut */
            border: 1px solid #ffe69c !important; /* Border kuning halus */
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
        }

        /* 2. Badge Role (Style Default / Light Mode) */
        .badge-role {
            color: #495057 !important; /* Abu tua */
            background-color: #e9ecef !important; /* Abu sedikit lebih tebal dari bg-light biasa */
            border: 1px solid #ced4da !important;
            font-weight: 500;
            font-size: 0.7rem;
            padding: 3px 8px;
        }

        /* --- ADJUSTMENT DARK MODE --- */
        [data-bs-theme="dark"] .badge-jabatan {
            /* Di mode gelap, balikkan jadi emas terang agar kontras dengan background hitam */
            background-color: rgba(195, 142, 68, 0.2) !important;
            color: #e0b675 !important; 
            border-color: rgba(195, 142, 68, 0.3) !important;
        }

        [data-bs-theme="dark"] .badge-role {
            background-color: #343a40 !important;
            color: #adb5bd !important;
            border-color: #495057 !important;
        }
    </style>

    <div class="container py-4">
        <h1 class="h3 mb-3">Manajemen Data Pegawai</h1>

        {{-- Notifikasi --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow border-0 rounded-3">
            {{-- Header Table: Hapus bg-white --}}
            <div class="card-header py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                 <div class="input-group w-auto">
                      <span class="input-group-text"><i class="bi bi-search"></i></span>
                      <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Cari data pegawai...">
                 </div>
                <button wire:click="showTambahModal" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Pegawai
                </button>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive" style="min-height: 400px;">
    <table class="table align-middle border-bottom mb-0 table-hover-custom">
        <thead class="bg-light text-secondary">
            <tr style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
                <th class="ps-4 py-3 border-bottom-0" width="35%">Pegawai</th>
                <th class="py-3 border-bottom-0" width="15%">NIP / ID</th>
                <th class="py-3 border-bottom-0" width="25%">Jabatan & Peran</th>
                <th class="text-center py-3 border-bottom-0" width="15%">Aksi</th>
            </tr>
        </thead>
        <tbody class="border-top-0">
            @forelse ($pegawaiList as $index => $pegawai)
                <tr wire:key="pegawai-{{ $pegawai->id }}" class="border-bottom-0">
                    
                    {{-- KOLOM 1: PROFILE (Gabungan Nama + Email + Avatar) --}}
                    <td class="ps-4 py-3">
                        <div class="d-flex align-items-center">
                            {{-- Avatar Inisial (Ambil huruf pertama nama) --}}
                            <div class="avatar-initial me-3">
                                {{ strtoupper(substr($pegawai->user->name, 0, 1)) }}
                            </div>
                            
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-body mb-0">{{ $pegawai->user->name }}</span>
                                <span class="text-muted small" style="font-size: 0.8rem;">
                                    {{ $pegawai->user->email }}
                                </span>
                                {{-- Tampilkan HP jika ada --}}
                                @if($pegawai->no_hp)
                                    <span class="text-muted small" style="font-size: 0.75rem;">
                                        <i class="bi bi-telephone me-1"></i> {{ $pegawai->no_hp }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- KOLOM 2: NIP (Gaya Monospace) --}}
                    <td class="py-3">
                        <span class="font-monospace-custom text-dark">
                            #{{ $pegawai->nip }}
                        </span>
                    </td>

                    {{-- KOLOM 3: JABATAN & PERAN --}}
                    <td class="py-3">
                        <div class="d-flex flex-column gap-2">
                            {{-- List Jabatan (Menggunakan class baru .badge-jabatan) --}}
                            <div>
                                @forelse ($pegawai->jabatans as $jabatan)
                                    <div class="badge-jabatan mb-1 me-1">
                                        <i class="bi bi-briefcase-fill me-1 opacity-75"></i> 
                                        {{ $jabatan->nama_jabatan }}
                                    </div>
                                @empty
                                    <span class="text-muted small fst-italic opacity-75 px-1">- Belum ada jabatan -</span>
                                @endforelse
                            </div>

                            {{-- List Role (Menggunakan class baru .badge-role) --}}
                            <div class="d-flex flex-wrap gap-1">
                                @foreach ($pegawai->user->roles as $role)
                                    <span class="badge rounded-pill badge-role">
                                        {{ $role->label }}
                                        {{-- Tampilkan bintang kecil jika role Peninjau --}}
                                        @if($role->name === 'peninjau')
                                            <i class="bi bi-star-fill text-warning ms-1" style="font-size: 0.6em;"></i>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </td>

                    {{-- KOLOM 4: AKSI (Floating Buttons) --}}
                    <td class="text-center py-3">
                        <div class="btn-group shadow-sm rounded-3" role="group">
                            <button wire:click="edit({{ $pegawai->id }})" class="btn btn-sm btn-white border text-primary" data-bs-toggle="tooltip" title="Edit Data">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button wire:click="confirmDelete({{ $pegawai->user_id }})" class="btn btn-sm btn-white border text-danger" data-bs-toggle="tooltip" title="Hapus Pegawai">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center opacity-50">
                            <i class="bi bi-people display-4 mb-3"></i>
                            <h6 class="fw-bold">Belum ada data pegawai</h6>
                            <p class="small text-muted">Silakan tambahkan pegawai baru.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
                 @if($pegawaiList->hasPages())
                 <div class="card-footer py-3 border-top-0">
                     {{ $pegawaiList->links() }}
                 </div>
                 @endif
            </div>
        </div>
    </div>

    <div class="modal fade @if($showModal) show d-block @endif" 
         id="pegawaiModal" tabindex="-1" aria-labelledby="pegawaiModalLabel" 
         aria-hidden="{{ !$showModal ? 'true' : 'false' }}" 
         style="@if($showModal) background-color: rgba(0,0,0,0.6); backdrop-filter: blur(2px); z-index: 9999 !important; @endif">

        <div class="modal-dialog modal-dialog-centered modal-dialog-compact">
            
            <form wire:submit="store" class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                
                {{-- HEADER: Hapus bg-white --}}
                <div class="modal-header py-2 px-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 text-warning rounded p-1 me-2">
                            <i class="bi bi-person-plus-fill fs-6"></i>
                        </div>
                        {{-- Hapus text-dark agar font ikut tema --}}
                        <h6 class="modal-title fw-bold m-0" id="pegawaiModalLabel">
                            {{ $isEditMode ? 'Edit Pegawai' : 'Tambah Pegawai' }}
                        </h6>
                    </div>
                    <button type="button" class="btn-close btn-sm" wire:click="closeModal" aria-label="Close"></button>
                </div>

                {{-- BODY --}}
                <div class="modal-body modal-body-compact">
                    
                    {{-- Bagian A: Data Diri --}}
                    <div class="section-divider mt-0">Data Pribadi</div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label-sm">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" class="form-control form-control-sm" placeholder="Nama...">
                            @error('name') <span class="text-danger" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label-sm">NIP / ID <span class="text-danger">*</span></label>
                                <input type="text" wire:model="nip" class="form-control form-control-sm" placeholder="NIP...">
                                @error('nip') <span class="text-danger" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                            </div>
                            
                            {{-- Input No HP Baru --}}
                            <div class="col-md-6">
                                <label class="form-label-sm">No. Handphone <span class="text-muted fst-italic fw-normal">(Opsional)</span></label>
                                <input type="text" wire:model="no_hp" class="form-control form-control-sm" placeholder="Contoh: 08123456789">
                                @error('no_hp') <span class="text-danger" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Email <span class="text-danger">*</span></label>
                            <input type="email" wire:model="email" class="form-control form-control-sm" placeholder="Email...">
                            @error('email') <span class="text-danger" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sm">Password</label>
                            <input type="password" wire:model="password" class="form-control form-control-sm" 
                                   placeholder="{{ $isEditMode ? '(Biarkan kosong)' : 'Password baru...' }}">
                            @error('password') <span class="text-danger" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Bagian B: Jabatan & Peran --}}
                    <div class="section-divider">Akses & Jabatan</div>
                    <div class="row g-3">
                        
                        {{-- Pilih Jabatan --}}
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

                            {{-- Container Scrollable --}}
                            {{-- Gunakan bg-white hanya di default, akan di override CSS dark mode --}}
                            <div class="border rounded p-0 bg-white shadow-sm" style="max-height: 400px; overflow-y: auto;">
                                
                                @if(isset($groupedJabatans) && $groupedJabatans->isNotEmpty())
                                    <div class="accordion accordion-flush" id="accordionJabatanInline">
                                        @foreach($groupedJabatans as $namaBidang => $listJabatan)
                                            <div class="accordion-item border-bottom" wire:key="bidang-{{ Str::slug($namaBidang) }}">
                                                <h2 class="accordion-header" id="head-{{ Str::slug($namaBidang) }}">
                                                    <button class="accordion-button accordion-button-custom collapsed fw-bold py-2" 
                                                            type="button" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#coll-{{ Str::slug($namaBidang) }}">
                                                        <span class="badge me-2 rounded-pill" style="background-color: #C38E44;">
                                                            {{ $listJabatan->count() }}
                                                        </span>
                                                        {{ $namaBidang ? $namaBidang : 'Lainnya' }}
                                                    </button>
                                                </h2>

                                                <div id="coll-{{ Str::slug($namaBidang) }}" 
                                                     class="accordion-collapse collapse" 
                                                     data-bs-parent="#accordionJabatanInline"
                                                     wire:ignore.self>
                                                     
                                                    <div class="accordion-body p-0">
                                                        <ul class="list-group list-group-flush">
                                                            @foreach($listJabatan as $jabatan)
                                                                @php
                                                                    $isTaken = in_array($jabatan->id, $takenSingletonJabatans);
                                                                    $isSelected = in_array($jabatan->id, $selectedJabatans);
                                                                    $paddingLeft = ($jabatan->indent_level ?? 0) * 25; 
                                                                @endphp
                                                                
                                                                <li class="list-group-item py-2 {{ $isTaken && !$isSelected ? 'bg-secondary bg-opacity-10' : '' }}"
                                                                    style="padding-left: {{ $paddingLeft + 20 }}px;"
                                                                    wire:key="jab-{{ $jabatan->id }}">
                                                                    
                                                                    <div class="form-check d-flex align-items-center">
                                                                        @if(($jabatan->indent_level ?? 0) > 0)
                                                                            <span class="text-muted me-1 opacity-50" style="font-size: 0.8em;">↳</span>
                                                                        @endif

                                                                        <input class="form-check-input form-check-input-custom me-2" 
                                                                               type="checkbox" 
                                                                               value="{{ $jabatan->id }}" 
                                                                               wire:model.live="selectedJabatans" 
                                                                               id="chk-{{ $jabatan->id }}"
                                                                               onclick="event.stopPropagation()" 
                                                                               {{ $isTaken && !$isSelected ? 'disabled' : '' }}>
                                                                        
                                                                        <label class="form-check-label w-100 cursor-pointer" 
                                                                               for="chk-{{ $jabatan->id }}"
                                                                               onclick="event.stopPropagation()">
                                                                            {{-- Hapus text-dark, biarkan pewarnaan otomatis --}}
                                                                            <span class="{{ $isTaken && !$isSelected ? 'text-muted text-decoration-line-through' : '' }}">
                                                                                {{ $jabatan->nama_jabatan }}
                                                                            </span>
                                                                            
                                                                            @if($isTaken && !$isSelected)
                                                                                <small class="text-danger ms-1 fst-italic" style="font-size: 0.7em;">(Terisi)</small>
                                                                            @endif
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
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                                        <p class="mb-0 small">Data jabatan belum tersedia.</p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="form-text mt-2 small opacity-75">
                                <i class="bi bi-info-circle"></i> Klik nama <strong>Bidang</strong> untuk membuka daftar jabatan.
                            </div>
                        </div>

                        {{-- Pilih Peran (UPDATED: Indikator Peninjau) --}}
                        <div class="col-12">
                            <label class="form-label-sm opacity-75 mb-1">Role Aplikasi <span class="text-danger">*</span></label>
                            @error('selectedRoles') <div class="text-danger fw-bold" style="font-size: 0.65rem;">{{ $message }}</div> @enderror

{{-- List Role --}}
<div class="d-flex gap-2 flex-wrap">
    @foreach ($roleList as $role)
        @php
            $isPeninjau = $role->name === 'peninjau';
            // Cek apakah role ini harus dimatikan (Disabled)
            $isDisabled = $isPeninjau && $peninjauTakenBy;
            
            // Siapkan pesan error untuk JS (dibersihkan dari karakter aneh)
            $pesanError = "Role Peninjau sudah digunakan oleh akun: " . addslashes($peninjauTakenBy ?? '') . ". Hanya diperbolehkan 1 akun Peninjau.";
        @endphp

        {{-- 
            WRAPPER UTAMA
            Kita gunakan logic PHP sederhana untuk onclick agar tidak merusak HTML
        --}}
        <div class="form-check form-check-inline border rounded px-2 py-1 m-0 d-flex align-items-center {{ $isDisabled ? 'bg-secondary bg-opacity-10 border-secondary' : '' }}"
             @if($isDisabled)
                style="cursor: not-allowed;"
                onclick="Swal.fire({
                    icon: 'warning',
                    title: 'Akses Dibatasi',
                    text: '{{ $pesanError }}',
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#c38e44'
                })"
             @endif
        >
            
            {{-- INPUT CHECKBOX --}}
            <input class="form-check-input m-0 me-2" 
                   type="checkbox" 
                   wire:model="selectedRoles" 
                   value="{{ $role->id }}" 
                   id="sm-role-{{ $role->id }}"
                   {{-- Jika disabled, matikan input & matikan pointer events biar klik tembus ke div wrapper --}}
                   @if($isDisabled) disabled style="pointer-events: none;" @endif> 
            
            {{-- LABEL --}}
            <label class="form-check-label d-flex align-items-center" 
                   for="sm-role-{{ $role->id }}" 
                   style="font-size: 0.75rem; cursor: {{ $isDisabled ? 'not-allowed' : 'pointer' }}; @if($isDisabled) pointer-events: none; @endif">
                
                <span class="{{ $isDisabled ? 'text-muted' : '' }}">{{ $role->label }}</span>
                
                {{-- INDIKATOR VISUAL --}}
                @if($isPeninjau)
                    @if($isDisabled)
                        {{-- Tampilan Gembok (Sudah ada orang lain) --}}
                        <span class="badge bg-danger bg-opacity-10 text-danger ms-2" style="font-size: 0.65em;">
                            <i class="bi bi-lock-fill me-1"></i> {{ Str::limit($peninjauTakenBy, 8) }}
                        </span>
                    @else
                        {{-- Tampilan Bintang (Bisa dipilih) --}}
                        <i class="bi bi-star-fill text-warning ms-1" 
                           style="font-size: 0.6rem;" 
                           data-bs-toggle="tooltip" 
                           title="Role Spesial: Hanya 1 Orang"></i>
                    @endif
                @endif
            </label>
        </div>
    @endforeach
</div>
                        </div>

                    </div>
                </div>

                {{-- FOOTER: Hapus bg-light --}}
                <div class="modal-footer py-2 px-3 border-top">
                    <button type="button" class="btn btn-sm btn-secondary border" wire:click="closeModal">Batal</button>
                    <button type="submit" class="btn btn-sm text-white px-3 fw-bold shadow-sm" style="background-color: #c38e44;">
                        <span wire:loading.remove wire:target="store">Simpan</span>
                        <span wire:loading wire:target="store">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    @push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Mendengarkan event dari PHP: 'show-delete-confirmation'
        @this.on('show-delete-confirmation', (id) => {
            Swal.fire({
                title: 'Yakin hapus pegawai?',
                text: "Data akun dan kepegawaian akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Panggil function destroy di PHP
                    @this.call('destroy', id); 
                }
            });
        });
    });
</script>
@endpush
</div>