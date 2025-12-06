<div>
    {{-- CSS Custom untuk Accordion & Mode Gelap --}}
    <style>
        /* --- STYLE UMUM (Mode Terang) --- */
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

        /* --- STYLE KHUSUS MODE GELAP --- */
        [data-bs-theme="dark"] .accordion-button {
            background-color: #212529; /* Gelap */
            color: #e9ecef;
        }
        /* Saat Accordion Aktif di Mode Gelap */
        [data-bs-theme="dark"] .accordion-button:not(.collapsed) {
            background-color: rgba(195, 142, 68, 0.2); /* Coklat transparan gelap */
            color: #e0b675; /* Emas yang lebih terang agar terbaca */
            box-shadow: none;
        }
        /* Memperbaiki warna input search di mode gelap */
        [data-bs-theme="dark"] .input-group-text {
            background-color: #343a40;
            border-color: #495057;
            color: #e9ecef;
        }
        /* Memperbaiki area dalam Modal yang sebelumnya putih */
        [data-bs-theme="dark"] .bg-special-section {
            background-color: #2b3035 !important; /* Abu gelap sedikit terang */
            border-color: #495057 !important;
        }
    </style>

    <div class="container py-4">
        <h1 class="h3 mb-3">Manajemen Struktur & Jabatan</h1>

        {{-- Error Validasi Global --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Notifikasi --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow border-0 rounded-3">
            {{-- Header: Pencarian & Tombol Tambah --}}
            {{-- HAPUS bg-white agar ikut tema --}}
            <div class="card-header py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                <div class="input-group w-auto">
                    {{-- HAPUS bg-light --}}
                    <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
                    <input wire:model.live="search" type="text" class="form-control border-start-0" placeholder="Cari jabatan...">
                </div>
                
                <button wire:click="resetInput" data-bs-toggle="modal" data-bs-target="#jabatanModal" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Jabatan
                </button>
            </div>

            {{-- BODY: ACCORDION VIEW --}}
            {{-- GANTI bg-light dengan bg-body-tertiary (adaptif) atau hapus background --}}
            <div class="card-body p-4 bg-body-tertiary">
                
                @if($groupedJabatans->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <p class="mt-2 text-muted">Data tidak ditemukan.</p>
                    </div>
                @else
                
                <div class="accordion" id="accordionJabatan">
                    
                    {{-- Loop per Bidang (Group) --}}
                    @foreach($groupedJabatans as $namaBidang => $listJabatan)
                        <div class="accordion-item mb-3 border-0 shadow-sm rounded overflow-hidden">
                            
                            {{-- Header Accordion --}}
                            <h2 class="accordion-header" id="heading-{{ Str::slug($namaBidang) }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ Str::slug($namaBidang) }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                    <span class="badge bg-custom-brown me-2 rounded-pill">{{ $listJabatan->count() }}</span> 
                                    {{ $namaBidang ? $namaBidang : 'Lainnya' }}
                                </button>
                            </h2>

                            {{-- Body Accordion (Isi Tabel) --}}
                            <div id="collapse-{{ Str::slug($namaBidang) }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#accordionJabatan">
                                <div class="accordion-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0 align-middle table-striped">
                                            {{-- HAPUS bg-white pada thead --}}
                                            <thead class="text-secondary">
                                                <tr style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    <th class="ps-4" width="35%">Nama Jabatan</th>
                                                    <th width="25%">Atasan Langsung</th>
                                                    <th class="text-center" width="10%">Level</th>
                                                    <th class="text-center" width="10%">Status</th>
                                                    <th class="text-center" width="10%">Urutan</th>
                                                    <th class="text-center" width="10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($listJabatan as $jabatan)
                                                    <tr wire:key="jabatan-{{ $jabatan->id }}">
                                                        <td class="ps-4">
                                                            {{-- HAPUS text-dark disini --}}
                                                            <div class="fw-medium">{{ $jabatan->nama_jabatan }}</div>
                                                            @if($jabatan->is_singleton)
                                                                <small class="d-flex align-items-center" style="color: #C38E44 !important;">
                                                                    <i class="bi bi-person-fill me-1"></i> Tunggal
                                                                </small>
                                                            @endif
                                                        </td>
                                                        
                                                        <td>
                                                            @if($jabatan->parent)
                                                                {{-- Hapus bg-light dan text-dark, ganti dengan badge-light custom --}}
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
                                                                <button wire:click="edit({{ $jabatan->id }})" class="btn btn-sm btn-link text-decoration-none p-0 me-3" title="Edit">
                                                                    <i class="bi bi-pencil-square fs-5" style="color: #C38E44;"></i>
                                                                </button>
                                                                <button wire:click="delete({{ $jabatan->id }})" onclick="return confirm('Yakin hapus?') || event.stopImmediatePropagation()" class="btn btn-sm btn-link text-decoration-none text-danger p-0" title="Hapus">
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

    {{-- MODAL FORM --}}
    <div wire:ignore.self class="modal fade" id="jabatanModal" tabindex="-1" aria-labelledby="jabatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="jabatanModalLabel">{{ $isEdit ? 'Edit Jabatan' : 'Tambah Jabatan' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        {{-- 1. Nama Jabatan --}}
                        <div class="mb-3">
                            <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                            <input type="text" wire:model="nama_jabatan" class="form-control @error('nama_jabatan') is-invalid @enderror" placeholder="Contoh: Kepala Bagian Keuangan">
                            @error('nama_jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- 2. Bidang --}}
                        <div class="mb-3">
                            <label class="form-label">Bidang / Kelompok <span class="text-danger">*</span></label>
                            <select wire:model.live="bidang" class="form-select @error('bidang') is-invalid @enderror">
                                <option value="">-- Pilih Bidang --</option>
                                @foreach($opsiBidang as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('bidang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- 3. LEVEL JABATAN (Ganti bg-light jadi class custom bg-special-section) --}}
                        <div class="mb-3 p-3 border rounded bg-special-section">
                            <label class="form-label fw-bold" style="color: #C38E44;">Tingkatan Level <span class="text-danger">*</span></label>
                            <select wire:model.live="level" class="form-select @error('level') is-invalid @enderror">
                                <option value="">-- Pilih Tingkatan Level --</option>
                                @foreach($opsiLevel as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            
                            <div class="form-text mt-2 small">
                                <i class="bi bi-info-circle"></i> Memilih level ini akan otomatis memfilter pilihan <strong>Atasan Langsung</strong> di bawah.
                            </div>
                            @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- 4. Parent / Atasan --}}
                        <div class="mb-3">
                            <label class="form-label">Atasan Langsung (Posisi Induk)</label>
                            <select wire:model="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">-- Tidak Ada (Level Puncak/Direktur) --</option>
                                @if($parentOptions->isEmpty())
                                    <option disabled>
                                        {{ !$level ? 'Silakan pilih Level Jabatan terlebih dahulu.' : 'Tidak ada atasan yang cocok untuk level ini.' }}
                                    </option>
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
                            @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            {{-- 5. Urutan --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Urutan Tampil</label>
                                <input type="number" wire:model="urutan" class="form-control" placeholder="0">
                            </div>

                            {{-- 6. Status Switch --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Status Jabatan</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" wire:model="status" id="statusSwitch">
                                    <label class="form-check-label" for="statusSwitch">
                                        {{ $status ? 'Aktif' : 'Non-Aktif' }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- 7. Singleton Checkbox (Ganti bg-white/light jadi bg-special-section) --}}
                        <div class="mb-3 p-2 rounded border bg-special-section">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="is_singleton" id="singletonCheck">
                                <label class="form-check-label fw-medium" for="singletonCheck">
                                    Jabatan Tunggal (Singleton)
                                </label>
                                <div class="form-text" style="font-size: 0.85em">
                                    Centang untuk jabatan pimpinan (hanya 1 orang). 
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #C38E44; border-color: #C38E44;">
                            {{ $isEdit ? 'Update' : 'Simpan' }}
                            <div wire:loading wire:target="{{ $isEdit ? 'update' : 'store' }}" class="spinner-border spinner-border-sm ms-1" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script Modal --}}
    @script
    <script>
        window.addEventListener('close-modal', event => {
            var myModalEl = document.getElementById('jabatanModal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            if (modal) { modal.hide(); }
        });

        window.addEventListener('open-modal', event => {
            var myModalEl = document.getElementById('jabatanModal');
            var modal = new bootstrap.Modal(myModalEl);
            modal.show();
        });
    </script>
    @endscript
</div>