<div>
    <style>
        /* --- STYLE UMUM (Sama seperti sebelumnya) --- */
        .accordion-button:not(.collapsed) {
            color: #C38E44;
            background-color: rgba(195, 142, 68, 0.1);
            box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
        }
        .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23C38E44'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }
        .accordion-button:focus {
            border-color: #C38E44;
            box-shadow: 0 0 0 0.25rem rgba(195, 142, 68, 0.25);
        }
        .bg-custom-brown {
            background-color: #C38E44 !important;
            color: white;
        }

        /* --- MODE GELAP FIX --- */
        [data-bs-theme="dark"] .accordion-button { background-color: #212529; color: #e9ecef; }
        [data-bs-theme="dark"] .accordion-button:not(.collapsed) { background-color: rgba(195, 142, 68, 0.2); color: #e0b675; box-shadow: none; }
        [data-bs-theme="dark"] .input-group-text { background-color: #343a40; border-color: #495057; color: #e9ecef; }
        [data-bs-theme="dark"] .bg-special-section { background-color: #2b3035 !important; border-color: #495057 !important; }

        /* === CSS AJAIB: UBAH TABEL JADI KARTU DI HP (MOBILE VIEW) === 
           Ini akan menghilangkan scroll samping dan membuat data turun ke bawah 
        */
        @media (max-width: 767px) {
            /* 1. Sembunyikan Header Tabel */
            #accordionJabatan thead { display: none; }

            /* 2. Ubah Baris (TR) menjadi Box/Kartu */
            #accordionJabatan tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid rgba(0,0,0,0.1);
                border-radius: 0.5rem;
                background-color: var(--bs-body-bg); /* Ikut warna background tema */
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            /* 3. Ubah Sel (TD) menjadi Baris Flex */
            #accordionJabatan tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid rgba(0,0,0,0.05);
                padding: 0.75rem 1rem !important; /* Padding nyaman */
                text-align: right;
            }
            
            #accordionJabatan tbody td:last-child { border-bottom: 0; }

            /* 4. Munculkan Label Kolom Secara Otomatis via CSS */
            /* Kolom 1: Nama Jabatan */
            #accordionJabatan tbody td:nth-child(1)::before { 
                content: "Jabatan"; 
                font-weight: bold; 
                text-transform: uppercase; 
                font-size: 0.75rem; 
                color: #C38E44;
                margin-right: 1rem;
            }
            /* Kolom 2: Atasan */
            #accordionJabatan tbody td:nth-child(2)::before { content: "Atasan"; font-weight: 600; font-size: 0.85rem; opacity: 0.7; }
            /* Kolom 3: Level */
            #accordionJabatan tbody td:nth-child(3)::before { content: "Level"; font-weight: 600; font-size: 0.85rem; opacity: 0.7; }
            /* Kolom 4: Status */
            #accordionJabatan tbody td:nth-child(4)::before { content: "Status"; font-weight: 600; font-size: 0.85rem; opacity: 0.7; }
            /* Kolom 5: Urutan */
            #accordionJabatan tbody td:nth-child(5)::before { content: "Urutan"; font-weight: 600; font-size: 0.85rem; opacity: 0.7; }
            /* Kolom 6: Aksi */
            #accordionJabatan tbody td:nth-child(6)::before { content: "Aksi"; font-weight: 600; font-size: 0.85rem; opacity: 0.7; }

            /* Fix khusus untuk kolom pertama agar rata kiri sedikit */
            #accordionJabatan tbody td:nth-child(1) {
                flex-direction: column;
                align-items: flex-start;
                text-align: left;
                background-color: rgba(195, 142, 68, 0.05); /* Sedikit warna beda untuk judul */
            }
            #accordionJabatan tbody td:nth-child(1)::before { margin-bottom: 0.25rem; }
        }

        /* Desktop Fix */
        @media (min-width: 768px) {
            .w-md-auto { width: auto !important; }
        }
        /* --- CSS TAMBAHAN UNTUK MODAL COMPACT (Copy dari referensi) --- */
    .modal-dialog-compact { max-width: 550px; margin-top: 60px; margin-bottom: 2rem; }
    .modal-body-compact { max-height: 70vh; overflow-y: auto; padding: 15px 20px !important; }
    
    /* Typography Form Kecil */
    .form-label-sm { font-size: 0.75rem; font-weight: 700; margin-bottom: 2px; color: #555; }
    .form-control-sm, .form-select-sm { font-size: 0.85rem; }
    
    /* Pemisah Seksi */
    .section-divider { 
        font-size: 0.65rem; font-weight: 800; color: #c38e44; 
        letter-spacing: 1px; text-transform: uppercase; 
        margin: 15px 0 10px 0; border-bottom: 1px solid #eee; padding-bottom: 3px; 
    }
    
    /* Style Khusus Checkbox Singleton */
    .bg-singleton { background-color: #fff8e1; border: 1px solid #ffe082; }
    </style>

    <div class="container py-4">
        <div class="d-flex align-items-center">
            <div class="p-2 me-2">
                <i class="bi bi-people-fill fs-2 text-gold " style="color: #C38E44;"></i>
            </div>
            <div>
                <h2 class="h3 mb-0 text-dark">Manajemen Struktur</h2>
            </div>
        </div>
        {{-- Error & Notif Block (Sama seperti sebelumnya) --}}
        @if ($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul></div>
        @endif
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('message') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="card shadow border-0 rounded-3">
            {{-- Header Card --}}
            <div class="card-header py-3 border-bottom-0 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="input-group w-100 w-md-auto">
                    <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
                    <input wire:model.live="search" type="text" class="form-control border-start-0" placeholder="Cari jabatan...">
                </div>
                <button wire:click="resetInput" data-bs-toggle="modal" data-bs-target="#jabatanModal" class="btn btn-success w-100 w-md-auto">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Jabatan
                </button>
            </div>

            <div class="card-body p-4 bg-body-tertiary">
                @if($groupedJabatans->isEmpty())
                    <div class="text-center py-5"><i class="bi bi-inbox display-4 text-muted"></i><p class="mt-2 text-muted">Data tidak ditemukan.</p></div>
                @else
                
                <div class="accordion" id="accordionJabatan">
                    @foreach($groupedJabatans as $namaBidang => $listJabatan)
                        <div class="accordion-item mb-3 border-0 shadow-sm rounded overflow-hidden">
                            <h2 class="accordion-header" id="heading-{{ Str::slug($namaBidang) }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ Str::slug($namaBidang) }}">
                                    <span class="badge bg-custom-brown me-2 rounded-pill">{{ $listJabatan->count() }}</span> 
                                    {{ $namaBidang ? $namaBidang : 'Lainnya' }}
                                </button>
                            </h2>

                            <div id="collapse-{{ Str::slug($namaBidang) }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#accordionJabatan">
                                <div class="accordion-body p-0">
                                    {{-- Hapus table-responsive di sini agar CSS card view bekerja optimal --}}
                                    <div class=""> 
                                        <table class="table table-hover mb-0 align-middle table-striped">
                                            <thead class="text-secondary">
                                                <tr style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    <th class="ps-4">Nama Jabatan</th>
                                                    <th>Atasan Langsung</th>
                                                    <th class="text-center">Level</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Urutan</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($listJabatan as $jabatan)
                                                    <tr wire:key="jabatan-{{ $jabatan->id }}">
                                                        {{-- Hapus ps-4 disini, diganti padding CSS --}}
                                                        <td>
                                                            <div class="fw-medium">{{ $jabatan->nama_jabatan }}</div>
                                                            @if($jabatan->is_singleton)
                                                                <small class="d-flex align-items-center" style="color: #C38E44 !important;">
                                                                    <i class="bi bi-person-fill me-1"></i> Tunggal
                                                                </small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($jabatan->parent)
                                                                <div class="badge border fw-normal text-body" style="background-color: rgba(var(--bs-body-bg-rgb), 0.5);">
                                                                    <i class="bi bi-arrow-return-right text-muted me-1"></i> {{ $jabatan->parent->nama_jabatan }}
                                                                </div>
                                                            @else
                                                                <span class="text-muted small fst-italic opacity-50">- Puncak -</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($jabatan->level == 1) <span class="badge bg-danger">Direktur</span>
                                                            @elseif($jabatan->level == 2) <span class="badge bg-warning text-dark">Wadir</span>
                                                            @elseif($jabatan->level == 3) <span class="badge bg-primary">Ka Unit</span>
                                                            @elseif($jabatan->level == 4) <span class="badge bg-info text-dark">Kasi</span>
                                                            @elseif($jabatan->level == 5) <span class="badge bg-secondary">Staff</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($jabatan->status == 'Aktif' || $jabatan->status == 1)
                                                                <span class="badge bg-success-subtle text-success rounded-pill px-2">Aktif</span>
                                                            @else
                                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">Non-Aktif</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center text-muted">{{ $jabatan->urutan }}</td>
                                                        <td class="text-center">
                                                            <div class="btn-group">
                                                                <button wire:click="edit({{ $jabatan->id }})" class="btn btn-sm btn-link text-decoration-none p-0 me-3">
                                                                    <i class="bi bi-pencil-square fs-5" style="color: #C38E44;"></i>
                                                                </button>
                                                                <button wire:click="confirmDelete({{ $jabatan->id }})" class="btn btn-sm btn-link text-decoration-none text-danger p-0">
                                                                    <i class="bi bi-trash fs-5"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    {{-- MODAL BARU (STYLE COMPACT & BLUR) --}}
<div wire:ignore.self class="modal fade" id="jabatanModal" tabindex="-1" aria-labelledby="jabatanModalLabel" aria-hidden="true"
     style="background-color: rgba(0,0,0,0.6); backdrop-filter: blur(2px); z-index: 1055;">
    
    <div class="modal-dialog modal-dialog-centered modal-dialog-compact">
        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            
            {{-- HEADER --}}
            <div class="modal-header py-2 px-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded p-1 me-2">
                        {{-- Icon Diagram/Struktur --}}
                        <i class="bi bi-diagram-3-fill fs-6"></i>
                    </div>
                    <h6 class="modal-title fw-bold m-0" id="jabatanModalLabel">
                        {{ $isEdit ? 'Edit Jabatan' : 'Tambah Jabatan' }}
                    </h6>
                </div>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body modal-body-compact">
                
                {{-- SEKSI 1: IDENTITAS --}}
                <div class="section-divider mt-0">Identitas Jabatan</div>
                
                <div class="row g-2">
                    {{-- Nama Jabatan (Full Width) --}}
                    <div class="col-12">
                        <label class="form-label-sm">Nama Jabatan <span class="text-danger">*</span></label>
                        <input type="text" wire:model="nama_jabatan" class="form-control form-control-sm @error('nama_jabatan') is-invalid @enderror" placeholder="Contoh: Kepala Bagian...">
                        @error('nama_jabatan') <span class="text-danger small" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Bidang (Col 6) --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label-sm">Bidang / Kelompok <span class="text-danger">*</span></label>
                        <select wire:model.live="bidang" class="form-select form-select-sm @error('bidang') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @foreach($opsiBidang as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('bidang') <span class="text-danger small" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Level (Col 6) --}}
                    <div class="col-12 col-md-6">
                        <label class="form-label-sm">Tingkatan Level <span class="text-danger">*</span></label>
                        <select wire:model.live="level" class="form-select form-select-sm @error('level') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @foreach($opsiLevel as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('level') <span class="text-danger small" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- SEKSI 2: HIERARKI --}}
                <div class="section-divider">Hierarki & Posisi</div>

                <div class="row g-2">
                    {{-- Atasan Langsung (Full Width) --}}
                    <div class="col-12">
                        <label class="form-label-sm">Atasan Langsung <span class="text-muted fw-normal fst-italic">(Parent)</span></label>
                        {{-- PERHATIKAN PENAMBAHAN wire:key DI BAWAH INI --}}
                        <select 
                            wire:model="parent_id" 
                            wire:key="parent-select-{{ $level ?? '0' }}-{{ $jabatanId ?? 'new' }}"
                            class="form-select form-select-sm @error('parent_id') is-invalid @enderror"
                        >
                            <option value="">-- Tidak Ada (Puncak) --</option>
                            
                            @if($parentOptions->isEmpty())
                                <option disabled>{{ !$level ? 'Pilih Level dahulu.' : 'Tidak ada opsi.' }}</option>
                            @else
                                @foreach($parentOptions as $groupBidang => $listJabatan)
                                    <optgroup label="{{ $groupBidang }}">
                                        @foreach($listJabatan as $opt) 
                                            <option value="{{ $opt->id }}">{{ $opt->nama_jabatan }}</option> 
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            @endif
                        </select>
                        @error('parent_id') <span class="text-danger small" style="font-size: 0.65rem;">{{ $message }}</span> @enderror
                    </div>

                    {{-- Urutan (Col 6) --}}
                    <div class="col-6">
                        <label class="form-label-sm">Urutan</label>
                        <input type="number" wire:model="urutan" class="form-control form-control-sm" placeholder="0">
                    </div>

                    {{-- Status (Col 6 - Toggle Style) --}}
                    <div class="col-6 d-flex flex-column justify-content-end pb-1">
                        <div class="form-check form-switch ps-0">
                            <div class="d-flex align-items-center">
                                <label class="form-label-sm mb-0 me-3">Status:</label>
                                <input class="form-check-input ms-0 me-2" type="checkbox" wire:model="status" id="statusSwitch">
                                <label class="form-check-label small fw-bold {{ $status ? 'text-success' : 'text-muted' }}" for="statusSwitch">
                                    {{ $status ? 'Aktif' : 'Non-Aktif' }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEKSI 3: KONFIGURASI KHUSUS --}}
                <div class="mt-3 p-2 rounded bg-singleton">
                    <div class="form-check d-flex align-items-start">
                        <input class="form-check-input mt-1 me-2" type="checkbox" wire:model="is_singleton" id="singletonCheck" style="cursor: pointer;">
                        <div>
                            <label class="form-check-label fw-bold text-dark" style="font-size: 0.8rem; cursor: pointer;" for="singletonCheck">
                                Jabatan Tunggal (Singleton)
                            </label>
                            <div class="text-secondary lh-1" style="font-size: 0.7rem;">
                                Centang jika jabatan ini hanya boleh diisi oleh 1 orang saja (contoh: Direktur).
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer py-2 px-3 border-top bg-light bg-opacity-50">
                <button type="button" class="btn btn-sm btn-secondary border shadow-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-sm text-white px-3 fw-bold shadow-sm" style="background-color: #c38e44;">
                    <span wire:loading.remove wire:target="{{ $isEdit ? 'update' : 'store' }}">
                        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Data' }}
                    </span>
                    <span wire:loading wire:target="{{ $isEdit ? 'update' : 'store' }}">
                        <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                    </span>
                </button>
            </div>

        </form>
    </div>
</div>
    @script
    <script>
        window.addEventListener('close-modal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('jabatanModal'));
            if (modal) { modal.hide(); }
        });
        window.addEventListener('open-modal', event => {
            new bootstrap.Modal(document.getElementById('jabatanModal')).show();
        });

        document.addEventListener('livewire:initialized', () => {
        
        // Mendengarkan event 'show-delete-confirmation' dari PHP
        @this.on('show-delete-confirmation', (id) => {
            Swal.fire({
                title: 'Hapus Jabatan?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', // Warna merah untuk hapus
                cancelButtonColor: '#3085d6', // Warna biru/standar untuk batal
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                // Jika user klik tombol "Ya, Hapus!"
                if (result.isConfirmed) {
                    // Panggil method 'destroy' di PHP dengan parameter ID
                    @this.call('destroy', id);
                }
            });
        });

    });
    </script>
    @endscript
</div>