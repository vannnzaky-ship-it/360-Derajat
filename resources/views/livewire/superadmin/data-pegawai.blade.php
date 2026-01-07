<div>
    {{-- CSS CUSTOM: MODAL, DARK MODE & KARTU TERPISAH (MOBILE) --}}
    <style>
        /* --- 1. GLOBAL STYLE --- */
        .avatar-initial, .avatar-img {
            width: 50px; height: 50px; border-radius: 50%; /* Avatar sedikit lebih besar */
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #C38E44; background-color: rgba(195, 142, 68, 0.15);
            font-size: 1.2rem; object-fit: cover; flex-shrink: 0;
            border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .font-monospace-custom {
            font-family: 'SFMono-Regular', Consolas, monospace;
            background: rgba(0,0,0,0.04); color: #444;
            padding: 3px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: 600;
        }
        .badge-jabatan {
            color: #8f6222 !important; background-color: #fff3cd !important;
            border: 1px solid #ffe69c !important; padding: 5px 10px;
            border-radius: 6px; font-weight: 600; font-size: 0.75rem;
            display: inline-flex; align-items: center; gap: 4px;
        }
        .badge-role {
            color: #495057; background-color: #e9ecef; border: 1px solid #ced4da;
            font-weight: 500; font-size: 0.7rem; padding: 3px 8px;
        }

        /* --- 2. MODAL & FORM --- */
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

        /* --- 3. DARK MODE --- */
        [data-bs-theme="dark"] .modal-content { background-color: #212529; color: #e9ecef; border: 1px solid #495057; }
        [data-bs-theme="dark"] .modal-header, [data-bs-theme="dark"] .modal-footer { background-color: #212529 !important; border-color: #495057; }
        [data-bs-theme="dark"] .form-control { background-color: #2b3035; border-color: #495057; color: #fff; }
        [data-bs-theme="dark"] .avatar-initial { border-color: #2b3035; }
        [data-bs-theme="dark"] .font-monospace-custom { background: rgba(255,255,255,0.1); color: #e9ecef; }
        [data-bs-theme="dark"] .badge-jabatan { background-color: rgba(195, 142, 68, 0.2) !important; color: #e0b675 !important; border-color: rgba(195, 142, 68, 0.3) !important; }
        [data-bs-theme="dark"] .badge-role { background-color: #343a40 !important; color: #adb5bd !important; border-color: #495057 !important; }

        /* --- 4. TAMPILAN MOBILE: KARTU TERPISAH (CARD STACK) --- */
        @media (max-width: 767px) {
            /* Sembunyikan Header Tabel */
            thead { display: none; }

            /* Ubah TABLE jadi BLOCK agar bisa margin */
            table, tbody { display: block; width: 100%; }

            /* Ubah TR menjadi Kartu Individu */
            tbody tr {
                display: block; /* Jadi kotak block */
                background-color: var(--bs-body-bg);
                border: 1px solid rgba(0,0,0,0.1);
                border-radius: 12px;
                margin-bottom: 1.5rem; /* JARAK ANTAR KARTU PEGAWAI */
                box-shadow: 0 4px 10px rgba(0,0,0,0.05); /* Bayangan agar "pop up" */
                position: relative;
                overflow: hidden;
            }

            /* Reset padding default TD */
            tbody td { 
                display: block; 
                padding: 10px 15px;
                border: none;
                border-bottom: 1px solid rgba(0,0,0,0.05);
            }
            tbody td:last-child { border-bottom: 0; }

            /* --- BAGIAN 1: PROFIL & TOMBOL (Header Kartu) --- */
            tbody td:nth-child(1) {
                background: linear-gradient(to bottom, rgba(195, 142, 68, 0.05), transparent);
                padding-top: 15px;
                padding-bottom: 15px;
            }
            /* Kita pindahkan tombol aksi ke sebelah nama menggunakan absolute position di CSS ini */
            /* Tapi karena struktur HTML tabel kaku, kita pakai Flex di dalam TD Profil nanti */
            
            /* --- BAGIAN 2: NIP --- */
            tbody td:nth-child(2)::before {
                content: "NIP / ID PEGAWAI";
                display: block; font-size: 0.65rem; font-weight: 800; color: #aaa; margin-bottom: 4px;
            }

            /* --- BAGIAN 3: JABATAN --- */
            tbody td:nth-child(3)::before {
                content: "JABATAN & PERAN";
                display: block; font-size: 0.65rem; font-weight: 800; color: #aaa; margin-bottom: 6px;
            }

            /* --- BAGIAN 4: AKSI (Footer Kartu) --- */
            tbody td:nth-child(4) {
                background-color: rgba(0,0,0,0.02);
                padding: 10px 15px;
                display: flex; justify-content: flex-end; /* Tombol di kanan bawah */
            }
        }

        /* Desktop Fix */
        @media (min-width: 768px) { .w-md-auto { width: auto !important; } }
    </style>

    <div class="container py-4">
        
        {{-- JUDUL DENGAN IKON BARU --}}
        <h1 class="h3 fw-bold mb-3 text-dark">
            <i class="bi bi-people-fill me-2" style="color: #C38E44;"></i>Manajemen Data Pegawai
        </h1>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('message') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow border-0 rounded-3" style="background: transparent; box-shadow: none !important;"> 
            {{-- Header Global (Pencarian) --}}
            <div class="card-header bg-white py-3 border rounded-3 mb-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 shadow-sm">
                 <div class="input-group w-100 w-md-auto">
                      <span class="input-group-text"><i class="bi bi-search"></i></span>
                      <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Cari pegawai...">
                 </div>
                <button wire:click="showTambahModal" class="btn btn-success w-100 w-md-auto">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Pegawai
                </button>
            </div>
            
            {{-- Container Tabel / Kartu --}}
            <div class=""> 
                {{-- Hapus background putih container utama agar kartu terlihat terpisah di mobile --}}
                <div style="min-height: 400px;">
    <table class="table align-middle border-bottom mb-0 table-hover-custom" style="background: transparent;">
        
        {{-- HEADER TABEL (Hanya Satu Saja) --}}
        {{-- CSS @media di atas akan otomatis menyembunyikan ini di HP --}}
        <thead class="bg-light text-secondary">
             <tr style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">
                <th class="ps-4 py-3 border-bottom-0" width="35%">Pegawai</th>
                <th class="py-3 border-bottom-0" width="15%">NIP / ID</th>
                <th class="py-3 border-bottom-0" width="35%">Jabatan & Peran</th>
                <th class="text-center py-3 border-bottom-0" width="15%">Aksi</th>
            </tr>
        </thead>

        <tbody class="border-top-0">
            @forelse ($pegawaiList as $index => $pegawai)
                <tr wire:key="pegawai-{{ $pegawai->id }}" class="bg-white">
                    
                    {{-- 1. PROFIL --}}
                    <td class="ps-4 py-3">
                        <div class="d-flex align-items-center">
                            {{-- Avatar --}}
                            @if($pegawai->user->profile_photo_path)
                                <img src="{{ asset('storage/' . $pegawai->user->profile_photo_path) }}" alt="Avatar" class="avatar-img me-3">
                            @else
                                <div class="avatar-initial me-3">{{ strtoupper(substr($pegawai->user->name, 0, 1)) }}</div>
                            @endif
                            
                            {{-- Nama & Email --}}
                            <div class="d-flex flex-column" style="min-width: 0;">
                                <span class="fw-bold text-body mb-0" style="font-size: 1rem;">{{ $pegawai->user->name }}</span>
                                <span class="text-muted small text-truncate">{{ $pegawai->user->email }}</span>
                                @if($pegawai->no_hp)
                                    <span class="text-muted small"><i class="bi bi-telephone me-1"></i> {{ $pegawai->no_hp }}</span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- 2. NIP --}}
                    <td class="py-3">
                        <span class="font-monospace-custom">{{ $pegawai->nip }}</span>
                    </td>

                    {{-- 3. JABATAN --}}
                    <td class="py-3">
                        <div class="d-flex flex-column gap-2">
                            {{-- Jabatan --}}
                            <div class="d-flex flex-wrap gap-1">
                                @forelse ($pegawai->jabatans as $jabatan)
                                    <div class="badge-jabatan">
                                        <i class="bi bi-briefcase-fill opacity-50"></i> {{ $jabatan->nama_jabatan }}
                                    </div>
                                @empty
                                    <span class="text-muted small fst-italic px-1">- Tidak ada jabatan -</span>
                                @endforelse
                            </div>
                            {{-- Role --}}
                            <div class="d-flex flex-wrap gap-1">
                                @foreach ($pegawai->user->roles as $role)
                                    <span class="badge rounded-pill badge-role">
                                        {{ $role->label }}
                                        @if($role->name === 'peninjau') <i class="bi bi-star-fill text-warning ms-1"></i> @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </td>

                    {{-- 4. AKSI --}}
                    <td class="text-center py-3">
                        <div class="d-flex justify-content-end justify-content-md-center w-100">
                            <div class="btn-group shadow-sm rounded-3">
                                <button wire:click="edit({{ $pegawai->id }})" class="btn btn-sm btn-light border text-primary px-3" title="Edit">
                                    <i class="bi bi-pencil-square"></i> <span class="d-md-none ms-1 fw-bold">Edit</span>
                                </button>
                                <button wire:click="confirmDelete({{ $pegawai->user_id }})" class="btn btn-sm btn-light border text-danger px-3" title="Hapus">
                                    <i class="bi bi-trash"></i> <span class="d-md-none ms-1 fw-bold">Hapus</span>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="bg-white">
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
                 <div class="py-3">
                      {{ $pegawaiList->links() }}
                 </div>
                 @endif
            </div>
        </div>
    </div>

    {{-- MODAL (TIDAK ADA PERUBAHAN) --}}
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
                             <label class="form-label-sm">NIP / ID <span class="text-danger">*</span></label>
                             <input type="text" wire:model="nip" class="form-control form-control-sm" placeholder="NIP...">
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

                    {{-- B. Jabatan & Peran --}}
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