<div class="container-fluid p-4">

    {{-- CSS CUSTOM: RESPONSIVE CARD VIEW --}}
    <style>
        /* --- 1. GLOBAL STYLE --- */
        .text-custom-brown { color: #C38E44; }
        .bg-custom-brown { background-color: #C38E44 !important; }
        
        /* --- 2. RESPONSIF MOBILE CARD VIEW --- */
        @media (max-width: 767px) {
            /* Sembunyikan Header Tabel Desktop */
            thead { display: none; }

            /* Ubah TR menjadi Kartu */
            tbody tr {
                display: flex;
                flex-wrap: wrap;
                background-color: #fff;
                border: 1px solid rgba(0,0,0,0.1);
                border-radius: 12px;
                margin-bottom: 1rem;
                box-shadow: 0 4px 10px rgba(0,0,0,0.05);
                overflow: hidden;
            }

            /* Hapus border default TD */
            tbody td { border: none !important; padding: 10px 15px; width: 100%; display: block; }

            /* --- BAGIAN 1: HEADER KARTU (KOMPETENSI) --- */
            /* Kolom 2 (Kompetensi) jadi Judul */
            tbody td:nth-child(2) {
                background: linear-gradient(to right, rgba(195, 142, 68, 0.1), transparent);
                border-bottom: 1px solid rgba(0,0,0,0.05) !important;
                font-weight: bold; font-size: 1rem; color: #000; /* Hitam Pekat */
                order: 1;
            }
            tbody td:nth-child(2)::before {
                content: "Kategori: "; font-weight: normal; color: #333; /* Label Hitam */
                font-size: 0.9rem;
            }

            /* --- BAGIAN 2: TEKS PERTANYAAN --- */
            /* Kolom 3 (Pertanyaan) */
            tbody td:nth-child(3) {
                order: 2; font-size: 0.95rem; color: #444; line-height: 1.5;
                padding-top: 15px; padding-bottom: 5px;
            }

            /* --- BAGIAN 3: INFO PENILAI & STATUS --- */
            /* Kolom 4 (Tipe Penilai) */
            tbody td:nth-child(4) {
                order: 3; font-size: 0.85rem; color: #666;
                padding-top: 5px; padding-bottom: 10px;
                border-bottom: 1px dashed rgba(0,0,0,0.1) !important;
            }
            tbody td:nth-child(4)::before {
                content: "Penilai: "; font-weight: bold; color: #C38E44;
            }

            /* Kolom 5 (Status) */
            tbody td:nth-child(5) {
                order: 4; text-align: right; /* Badge di kanan */
                padding-top: 10px;
            }

            /* --- BAGIAN 4: NOMOR URUT (Hidden) --- */
            tbody td:nth-child(1) { display: none; }

            /* --- BAGIAN 5: AKSI (FOOTER) --- */
            /* Kolom 6 (Action) */
            tbody td:nth-child(6) {
                order: 5;
                background-color: rgba(0,0,0,0.02);
                border-top: 1px solid rgba(0,0,0,0.05) !important;
                display: flex; justify-content: flex-end; gap: 10px;
                padding: 10px 15px;
            }
        }

        /* Desktop Fix */
        @media (min-width: 768px) { .w-md-auto { width: auto !important; } }
    </style>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <i class="bi bi-question-circle-fill fs-1 text-custom-brown me-3"></i>
            <h2 class="h3 mb-0 text-dark">Manajemen Pertanyaan</h2>
        </div>
        <button type="button" class="btn btn-success shadow-sm w-100 w-md-auto" wire:click="showTambahModal"> 
            <i class="bi bi-plus-lg me-2"></i> Tambah Pertanyaan
        </button>
    </div>

    @if (session()->has('message')) 
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('message') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error')) 
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 border-bottom-0">
             <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                 <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bi bi-table me-2"></i>Daftar Pertanyaan
                 </h5>
                 <div class="input-group w-100 w-md-auto" style="max-width: 400px;">
                      <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                      <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Pertanyaan..." wire:model.live.debounce.300ms="search">
                 </div>
             </div>
        </div>
        
        <div class="card-body p-0">
            <div class=""> {{-- Hapus table-responsive agar card view berfungsi --}}
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-custom-brown text-white">
                        <tr>
                            <th scope="col" class="text-center" style="width: 50px;">No</th>
                            <th scope="col" style="width: 20%;">Kompetensi</th>
                            <th scope="col">Teks Pertanyaan</th>
                            <th scope="col" style="width: 20%;">Tipe Penilai</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($daftarPertanyaan as $index => $pert)
                        <tr wire:key="{{ $pert->id }}">
                            <td class="text-center fw-bold">{{ $daftarPertanyaan->firstItem() + $index }}</td>
                            
                            {{-- Kompetensi (UBAH JADI HITAM / TEXT-DARK) --}}
                            <td>
                                <span class="fw-medium text-dark">{{ $pert->kompetensi->nama_kompetensi ?? 'N/A' }}</span>
                            </td>
                            
                            {{-- Teks Pertanyaan --}}
                            <td>{{ $pert->teks_pertanyaan }}</td>
                            
                            {{-- Tipe Penilai --}}
                            <td>
                                @php 
                                    $penilai = [];
                                    if($pert->untuk_diri) $penilai[] = 'Diri';
                                    if($pert->untuk_atasan) $penilai[] = 'Atasan';
                                    if($pert->untuk_rekan) $penilai[] = 'Rekan';
                                    if($pert->untuk_bawahan) $penilai[] = 'Bawahan';
                                @endphp
                                <small class="text-muted"><i class="bi bi-people-fill me-1"></i> {{ implode(', ', $penilai) }}</small>
                            </td>
                            
                            {{-- Status --}}
                            <td class="text-center">
                                @if ($pert->status == 'Aktif')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1 rounded-pill">
                                        <i class="bi bi-x-circle-fill me-1"></i> Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Action --}}
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary" title="Edit Status" 
                                        wire:click="edit({{ $pert->id }})"> 
                                    <i class="bi bi-pencil-square"></i> <span class="d-md-none ms-1">Edit</span>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-1" title="Hapus" 
                                        wire:click="confirmDelete({{ $pert->id }})">
                                    <i class="bi bi-trash"></i> <span class="d-md-none ms-1">Hapus</span>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted p-5">
                                <div class="d-flex flex-column align-items-center opacity-50">
                                    <i class="bi bi-inbox display-1"></i>
                                    <p class="mt-2">Data pertanyaan belum tersedia.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             @if($daftarPertanyaan->hasPages())
             <div class="card-footer bg-white py-3 border-top-0 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                 <div class="d-flex align-items-center">
                    <span class="text-muted me-2 small">Per Halaman:</span>
                    <select class="form-select form-select-sm" wire:model.live="perPage" style="width: 70px;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                 </div>
                 <div class="w-100 w-md-auto">
                     {{ $daftarPertanyaan->links() }}
                 </div>
             </div>
             @endif
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="pertanyaanModal" tabindex="-1" aria-labelledby="pertanyaanModalLabel" aria-hidden="true"> 
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit="savePertanyaan"> 
                    <div class="modal-header">
                        <h5 class="modal-title" id="pertanyaanModalLabel">
                            {{ $isEditMode ? 'Edit Status Pertanyaan' : 'Tambah Pertanyaan Baru' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        
                        {{-- Hanya bisa diubah saat TAMBAH --}}
                        <fieldset @if($isEditMode) disabled @endif> 
                            <div class="mb-3">
                                <label for="kompetensi_id" class="form-label fw-bold small text-uppercase text-muted">Kompetensi (Kriteria) <span class="text-danger">*</span></label>
                                <select id="kompetensi_id" class="form-select @error('kompetensi_id') is-invalid @enderror" wire:model="kompetensi_id">
                                    <option value="">-- Pilih Kompetensi --</option>
                                    @foreach ($kompetensiList as $komp)
                                        <option value="{{ $komp->id }}">{{ $komp->nama_kompetensi }} (Bobot: {{ $komp->bobot }}%)</option>
                                    @endforeach
                                </select>
                                @error('kompetensi_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="teks_pertanyaan" class="form-label fw-bold small text-uppercase text-muted">Teks Pertanyaan <span class="text-danger">*</span></label>
                                <textarea id="teks_pertanyaan" class="form-control @error('teks_pertanyaan') is-invalid @enderror" wire:model="teks_pertanyaan" rows="3" placeholder="Masukkan teks pertanyaan..."></textarea>
                                @error('teks_pertanyaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">Tipe Penilai <span class="text-danger">*</span></label>
                                @error('penilai_checkbox') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                                <div class="d-flex flex-wrap gap-3 p-3 bg-light rounded border">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="untuk_diri" id="check_diri">
                                        <label class="form-check-label" for="check_diri">Diri Sendiri</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="untuk_atasan" id="check_atasan">
                                        <label class="form-check-label" for="check_atasan">Atasan</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="untuk_rekan" id="check_rekan">
                                        <label class="form-check-label" for="check_rekan">Rekan Sejawat</label>
                                    </div>
                                     <div class="form-check">
                                        <input class="form-check-input" type="checkbox" wire:model="untuk_bawahan" id="check_bawahan">
                                        <label class="form-check-label" for="check_bawahan">Bawahan</label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                         {{-- Status bisa diubah saat TAMBAH maupun EDIT --}}
                         <div class="mb-3">
                            <label for="status_pert" class="form-label fw-bold small text-uppercase text-muted">Status <span class="text-danger">*</span></label>
                            <select id="status_pert" class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                             @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #C38E44; border-color: #C38E44;">
                             {{ $isEditMode ? 'Update Status' : 'Simpan Pertanyaan' }}
                             <div wire:loading wire:target="savePertanyaan" class="spinner-border spinner-border-sm ms-1" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const pertanyaanModalElement = document.getElementById('pertanyaanModal');
    const pertanyaanModal = pertanyaanModalElement ? new bootstrap.Modal(pertanyaanModalElement, { keyboard: false, backdrop: 'static' }) : null;

    document.addEventListener('livewire:initialized', () => {
        @this.on('open-pertanyaan-modal', () => { if (pertanyaanModal) pertanyaanModal.show(); });
        @this.on('close-pertanyaan-modal', () => { if (pertanyaanModal) pertanyaanModal.hide(); });
        @this.on('show-delete-confirmation-pertanyaan', () => { 
            Swal.fire({
                title: 'Hapus Pertanyaan?', text: "Data tidak dapat dikembalikan!", icon: 'warning', 
                showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', 
                confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
            }).then((result) => { if (result.isConfirmed) @this.dispatch('deleteConfirmedPertanyaan'); });
        });
    });

    if (pertanyaanModalElement) { 
         pertanyaanModalElement.addEventListener('hidden.bs.modal', event => {
           if (typeof @this !== 'undefined') @this.call('resetForm'); 
         });
    }
</script>
@endpush