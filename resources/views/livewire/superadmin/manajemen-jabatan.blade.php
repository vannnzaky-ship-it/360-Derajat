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
    </style>

    <div class="container py-4">
        <h1 class="h3 mb-3">Manajemen Struktur & Jabatan</h1>

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
                                                                <button wire:click="delete({{ $jabatan->id }})" onclick="return confirm('Yakin hapus?') || event.stopImmediatePropagation()" class="btn btn-sm btn-link text-decoration-none text-danger p-0">
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
    <div wire:ignore.self class="modal fade" id="jabatanModal" tabindex="-1" aria-labelledby="jabatanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="jabatanModalLabel">{{ $isEdit ? 'Edit Jabatan' : 'Tambah Jabatan' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                            <input type="text" wire:model="nama_jabatan" class="form-control @error('nama_jabatan') is-invalid @enderror" placeholder="Contoh: Kepala Bagian Keuangan">
                            @error('nama_jabatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

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

                        <div class="mb-3 p-3 border rounded bg-special-section">
                            <label class="form-label fw-bold" style="color: #C38E44;">Tingkatan Level <span class="text-danger">*</span></label>
                            <select wire:model.live="level" class="form-select @error('level') is-invalid @enderror">
                                <option value="">-- Pilih Tingkatan Level --</option>
                                @foreach($opsiLevel as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="form-text mt-2 small"><i class="bi bi-info-circle"></i> Memilih level ini akan otomatis memfilter pilihan <strong>Atasan Langsung</strong>.</div>
                            @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Atasan Langsung (Posisi Induk)</label>
                            <select wire:model="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">-- Tidak Ada (Level Puncak/Direktur) --</option>
                                @if($parentOptions->isEmpty())
                                    <option disabled>{{ !$level ? 'Silakan pilih Level Jabatan terlebih dahulu.' : 'Tidak ada atasan yang cocok untuk level ini.' }}</option>
                                @else
                                    @foreach($parentOptions as $groupBidang => $listJabatan)
                                        <optgroup label="{{ $groupBidang }}">
                                            @foreach($listJabatan as $opt) <option value="{{ $opt->id }}">{{ $opt->nama_jabatan }}</option> @endforeach
                                        </optgroup>
                                    @endforeach
                                @endif
                            </select>
                            @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label">Urutan Tampil</label>
                                <input type="number" wire:model="urutan" class="form-control" placeholder="0">
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label d-block">Status Jabatan</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" wire:model="status" id="statusSwitch">
                                    <label class="form-check-label" for="statusSwitch">{{ $status ? 'Aktif' : 'Non-Aktif' }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 p-2 rounded border bg-special-section">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="is_singleton" id="singletonCheck">
                                <label class="form-check-label fw-medium" for="singletonCheck">Jabatan Tunggal (Singleton)</label>
                                <div class="form-text" style="font-size: 0.85em">Centang untuk jabatan pimpinan (hanya 1 orang).</div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #C38E44; border-color: #C38E44;">
                            {{ $isEdit ? 'Update' : 'Simpan' }}
                            <div wire:loading wire:target="{{ $isEdit ? 'update' : 'store' }}" class="spinner-border spinner-border-sm ms-1" role="status"></div>
                        </button>
                    </div>
                </form>
            </div>
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
    </script>
    @endscript
</div>