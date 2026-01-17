<div class="container-fluid p-4">

    {{-- CSS CUSTOM --}}
    <style>
        /* --- 1. GLOBAL STYLE --- */
        .text-custom-brown { color: #C38E44; }
        .bg-custom-brown { background-color: #C38E44 !important; }
        
        /* --- 2. STYLE MODAL COMPACT (Supaya Ramping) --- */
        .modal-dialog-compact { 
            max-width: 500px; /* Ukuran Compact */
            margin-top: 60px; 
            margin-bottom: 2rem; 
        }
        
        /* Typography Form Kecil */
        .form-label-sm { 
            font-size: 0.75rem; 
            font-weight: 700; 
            margin-bottom: 4px; 
            color: #666; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }
        .form-control-sm, .form-select-sm, .input-group-text-sm { font-size: 0.85rem; }

        /* --- 3. RESPONSIF MOBILE CARD VIEW --- */
        @media (max-width: 767px) {
            thead { display: none; }
            tbody tr {
                display: flex; flex-wrap: wrap; background-color: #fff;
                border: 1px solid rgba(0,0,0,0.1); border-radius: 12px;
                margin-bottom: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: hidden;
            }
            tbody td { border: none !important; padding: 10px 15px; width: 100%; display: block; }

            /* Header Kartu (Kompetensi) */
            tbody td:nth-child(2) {
                background: linear-gradient(to right, rgba(195, 142, 68, 0.1), transparent);
                border-bottom: 1px solid rgba(0,0,0,0.05) !important;
                font-weight: bold; font-size: 1rem; color: #333; order: 1;
            }
            tbody td:nth-child(2)::before { content: "Kategori: "; font-weight: normal; color: #555; font-size: 0.9rem; }

            /* Pertanyaan */
            tbody td:nth-child(3) {
                order: 2; font-size: 0.95rem; color: #444; line-height: 1.5; padding-top: 15px; padding-bottom: 5px;
            }

            /* Penilai */
            tbody td:nth-child(4) {
                order: 3; font-size: 0.85rem; color: #666; padding-top: 5px; padding-bottom: 10px; border-bottom: 1px dashed rgba(0,0,0,0.1) !important;
            }
            tbody td:nth-child(4)::before { content: "Penilai: "; font-weight: bold; color: #C38E44; }

            /* Status */
            tbody td:nth-child(5) {
                order: 4; text-align: right; padding-top: 10px;
            }
            tbody td:nth-child(1) { display: none; } /* No Hidden */

            /* Aksi */
            tbody td:nth-child(6) {
                order: 5; background-color: rgba(0,0,0,0.02); border-top: 1px solid rgba(0,0,0,0.05) !important;
                display: flex; justify-content: flex-end; gap: 10px; padding: 10px 15px;
            }
        }
        @media (min-width: 768px) { .w-md-auto { width: auto !important; } }
    </style>

    {{-- HEADER HALAMAN --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <i class="bi bi-question-circle-fill fs-1 text-custom-brown me-3"></i>
            <h2 class="h3 mb-0 text-dark">Manajemen Pertanyaan</h2>
        </div>
        <button type="button" class="btn btn-success shadow-sm w-100 w-md-auto" wire:click="showTambahModal"> 
            <i class="bi bi-plus-lg me-2"></i> Tambah Pertanyaan
        </button>
    </div>

    {{-- NOTIFIKASI --}}
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

    {{-- TABEL DATA --}}
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
            <div class=""> 
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
                            <td>
                                <span class="fw-medium text-dark">{{ $pert->kompetensi->nama_kompetensi ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $pert->teks_pertanyaan }}</td>
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
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" title="Edit" 
                                        wire:click="edit({{ $pert->id }})"> 
                                    <i class="bi bi-pencil-square"></i> <span class="d-md-none ms-1">Edit</span>
                                </button>
                                
                                @if($pert->penilaian_skors_count == 0)
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus" 
                                            wire:click="confirmDelete({{ $pert->id }})">
                                        <i class="bi bi-trash"></i> <span class="d-md-none ms-1">Hapus</span>
                                    </button>
                                @endif
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

    {{-- MODAL FORM (COMPACT & BLUR) --}}
    <div wire:ignore.self class="modal fade" id="pertanyaanModal" tabindex="-1" aria-labelledby="pertanyaanModalLabel" aria-hidden="true"
         style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(5px);"> 
        
        {{-- Menggunakan 'modal-dialog-compact' --}}
        <div class="modal-dialog modal-dialog-centered modal-dialog-compact">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                <form wire:submit="savePertanyaan"> 
                    
                    {{-- HEADER --}}
                    <div class="modal-header py-2 px-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 text-warning rounded p-1 me-2">
                                <i class="bi bi-question-lg fs-6"></i>
                            </div>
                            <h6 class="modal-title fw-bold m-0" id="pertanyaanModalLabel">
                                {{ $isEditMode ? 'Edit Pertanyaan' : 'Tambah Pertanyaan' }}
                            </h6>
                        </div>
                        <button type="button" class="btn-close btn-sm" wire:click="closeModal" aria-label="Close"></button>
                    </div>

                    {{-- BODY COMPACT --}}
                    <div class="modal-body p-3">
                        
                        <fieldset @if($isEditMode) disabled @endif>
                            {{-- 1. Kompetensi --}}
                            <div class="mb-3">
                                <label for="kompetensi_id" class="form-label-sm">Kompetensi (Kriteria) <span class="text-danger">*</span></label>
                                <select id="kompetensi_id" class="form-select form-select-sm @error('kompetensi_id') is-invalid @enderror" wire:model="kompetensi_id">
                                    <option value="">-- Pilih Kompetensi --</option>
                                    @foreach ($kompetensiList as $komp)
                                        <option value="{{ $komp->id }}">{{ $komp->nama_kompetensi }} (Bobot: {{ $komp->bobot }}%)</option>
                                    @endforeach
                                </select>
                                @error('kompetensi_id') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            {{-- 2. Teks Pertanyaan --}}
                            <div class="mb-3">
                                <label for="teks_pertanyaan" class="form-label-sm">Teks Pertanyaan <span class="text-danger">*</span></label>
                                <textarea id="teks_pertanyaan" class="form-control form-control-sm @error('teks_pertanyaan') is-invalid @enderror" 
                                          wire:model="teks_pertanyaan" rows="3" placeholder="Masukkan teks pertanyaan..."></textarea>
                                @error('teks_pertanyaan') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            {{-- 3. Tipe Penilai (Checkbox) --}}
                            <div class="mb-3">
                                <label class="form-label-sm">Tipe Penilai <span class="text-danger">*</span></label>
                                @error('penilai_checkbox') <div class="text-danger small mb-1" style="font-size: 0.7rem;">{{ $message }}</div> @enderror
                                
                                <div class="border rounded p-2 bg-light">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="form-check small mb-0">
                                                <input class="form-check-input" type="checkbox" wire:model="untuk_diri" id="check_diri">
                                                <label class="form-check-label" for="check_diri" style="font-size: 0.75rem;">Diri Sendiri</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check small mb-0">
                                                <input class="form-check-input" type="checkbox" wire:model="untuk_atasan" id="check_atasan">
                                                <label class="form-check-label" for="check_atasan" style="font-size: 0.75rem;">Atasan</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check small mb-0">
                                                <input class="form-check-input" type="checkbox" wire:model="untuk_rekan" id="check_rekan">
                                                <label class="form-check-label" for="check_rekan" style="font-size: 0.75rem;">Rekan</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check small mb-0">
                                                <input class="form-check-input" type="checkbox" wire:model="untuk_bawahan" id="check_bawahan">
                                                <label class="form-check-label" for="check_bawahan" style="font-size: 0.75rem;">Bawahan</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        {{-- 4. Status (Bisa diedit kapan saja) --}}
                        <div class="mb-0">
                            <label for="status_pert" class="form-label-sm">Status <span class="text-danger">*</span></label>
                            <select id="status_pert" class="form-select form-select-sm @error('status') is-invalid @enderror" wire:model="status">
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                            @error('status') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="modal-footer bg-light border-top py-2 px-3">
                        <button type="button" class="btn btn-sm btn-secondary border" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary fw-bold text-white px-3" style="background-color: #C38E44; border-color: #C38E44;">
                             {{ $isEditMode ? 'Update Status' : 'Simpan Data' }}
                             <div wire:loading wire:target="savePertanyaan" class="spinner-border spinner-border-sm ms-1"></div>
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