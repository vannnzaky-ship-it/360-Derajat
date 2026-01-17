<div class="container-fluid p-4">

    {{-- STYLE KHUSUS: MENYAMAKAN DENGAN MANAJEMEN JABATAN --}}
    <style>
        /* 1. Warna Utama */
        .text-custom-brown { color: #C38E44; }
        .bg-custom-brown { background-color: #C38E44 !important; }

        /* 2. Mengatur Lebar Modal AGAR TIDAK LEBAR (Compact) */
        .modal-dialog-compact { 
            max-width: 500px; /* KUNCI: Membatasi lebar agar ramping */
            margin-top: 60px; 
            margin-bottom: 2rem; 
        }

        /* 3. Typography Form Kecil (Agar muat di modal kecil) */
        .form-label-sm { 
            font-size: 0.75rem; 
            font-weight: 700; 
            margin-bottom: 4px; 
            color: #666; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }
        .form-control-sm, .form-select-sm, .input-group-text-sm { font-size: 0.85rem; }

        /* 4. Responsif Mobile Card View (Tabel) */
        @media (max-width: 767px) {
            thead { display: none; }
            tbody tr {
                display: flex; flex-wrap: wrap; background-color: #fff;
                border: 1px solid rgba(0,0,0,0.1); border-radius: 12px;
                margin-bottom: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: hidden;
            }
            tbody td { border: none !important; padding: 10px 15px; width: 100%; display: block; }
            
            tbody td:nth-child(2) { /* Nama */
                background: linear-gradient(to right, rgba(195, 142, 68, 0.1), transparent);
                border-bottom: 1px solid rgba(0,0,0,0.05) !important;
                font-weight: bold; font-size: 1.1rem; color: #333; order: 1;
            }
            tbody td:nth-child(3) { /* Deskripsi */
                order: 3; font-size: 0.9rem; color: #666; padding: 5px 15px;
            }
            tbody td:nth-child(3)::before { content: "Deskripsi:"; display: block; font-size: 0.75rem; font-weight: 700; color: #aaa; margin-bottom: 2px; }
            tbody td:nth-child(4) { /* Bobot */
                order: 2; width: 50%; float: left; border-bottom: 1px dashed rgba(0,0,0,0.05) !important;
            }
            tbody td:nth-child(4)::before { content: "Bobot"; display: block; font-size: 0.7rem; font-weight: 700; color: #aaa; text-transform: uppercase; }
            tbody td:nth-child(5) { /* Status */
                order: 2; width: 50%; float: right; text-align: right; border-bottom: 1px dashed rgba(0,0,0,0.05) !important;
            }
            tbody td:nth-child(1) { display: none; }
            tbody td:nth-child(6) { /* Aksi */
                order: 4; background-color: rgba(0,0,0,0.02); border-top: 1px solid rgba(0,0,0,0.05) !important;
                display: flex; justify-content: flex-end; gap: 10px; padding: 10px 15px;
            }
        }
        @media (min-width: 768px) { .w-md-auto { width: auto !important; } }
    </style>

    {{-- HEADER HALAMAN --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center">
            <i class="bi bi-bar-chart-fill fs-1 text-custom-brown me-3"></i>
            <h2 class="h3 mb-0 text-dark">Manajemen Kompetensi</h2>
        </div>
        <button type="button" class="btn btn-success shadow-sm w-100 w-md-auto" wire:click="showTambahModal"> 
            <i class="bi bi-plus-lg me-2"></i> Tambah Kompetensi
        </button>
    </div>

    {{-- NOTIFIKASI --}}
    @if (session()->has('message')) 
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('message') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ALERT TOTAL BOBOT --}}
    <div class="alert {{ $totalBobotAktif == 100 ? 'alert-success' : 'alert-warning' }} d-flex align-items-start" role="alert">
        <i class="bi {{ $totalBobotAktif == 100 ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }} me-2 mt-1"></i>
        <div>
            Total bobot aktif: <strong>{{ $totalBobotAktif }}%</strong>. 
            @if($totalBobotAktif != 100)
                <span class="small">Harap sesuaikan menjadi <strong>100%</strong>.</span>
            @endif
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 border-bottom-0">
             <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                 <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bi bi-table me-2"></i>Daftar Kompetensi
                 </h5>
                 <div class="input-group w-100 w-md-auto" style="max-width: 400px;">
                      <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                      <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari..." wire:model.live.debounce.300ms="search">
                 </div>
             </div>
        </div>

        <div class="card-body p-0">
            <div class="">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-custom-brown text-white">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama Kompetensi</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Bobot (%)</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($daftarKompetensi as $index => $komp)
                        <tr wire:key="row-{{ $komp->id }}">
                            <td class="text-center fw-bold">{{ $daftarKompetensi->firstItem() + $index }}</td>
                            <td>{{ $komp->nama_kompetensi }}</td>
                            <td>
                                @if($komp->deskripsi) <small class="text-muted">{{ Str::limit($komp->deskripsi, 60) }}</small>
                                @else <span class="text-muted small fst-italic">-</span> @endif
                            </td>
                            <td class="text-center fw-bold text-primary">{{ $komp->bobot }}%</td>
                            <td class="text-center">
                                @if ($komp->status == 'Aktif')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill">Aktif</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary me-1" wire:click="edit({{ $komp->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                @if($komp->pertanyaans_count == 0)
                                    <button class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $komp->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted p-5">Data tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             @if($daftarKompetensi->hasPages())
             <div class="card-footer bg-white py-3 border-top-0">
                 {{ $daftarKompetensi->links() }}
             </div>
             @endif
        </div>
    </div>

    {{-- MODAL FORM YANG BENAR (UKURAN COMPACT/KECIL) --}}
    <div wire:ignore.self class="modal fade" id="kompetensiModal" tabindex="-1" aria-hidden="true" 
         style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(5px);"> 
        
        {{-- PENTING: Gunakan 'modal-dialog-compact' yang sudah didefinisikan di CSS atas --}}
        <div class="modal-dialog modal-dialog-centered modal-dialog-compact">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                <form wire:submit="saveKompetensi"> 
                    
                    {{-- HEADER --}}
                    <div class="modal-header py-2 px-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 text-warning rounded p-1 me-2">
                                <i class="bi bi-star-fill fs-6"></i>
                            </div>
                            <h6 class="modal-title fw-bold m-0" id="kompetensiModalLabel">
                                {{ $isEditMode ? 'Edit' : 'Tambah' }} Kompetensi
                            </h6>
                        </div>
                        <button type="button" class="btn-close btn-sm" wire:click="closeModal" aria-label="Close"></button>
                    </div>

                    {{-- BODY COMPACT --}}
                    <div class="modal-body p-3">
                        
                        {{-- 1. Nama --}}
                        <div class="mb-3">
                            <label class="form-label-sm">Nama Kompetensi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm @error('nama_kompetensi') is-invalid @enderror" 
                                   wire:model="nama_kompetensi" placeholder="Contoh: Kepribadian">
                            @error('nama_kompetensi') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>

                        {{-- 2. Deskripsi --}}
                        <div class="mb-3">
                            <label class="form-label-sm">Deskripsi <span class="fw-normal text-muted text-lowercase">(opsional)</span></label>
                            <textarea class="form-control form-control-sm @error('deskripsi') is-invalid @enderror" 
                                      wire:model="deskripsi" rows="3" placeholder="Penjelasan singkat..."></textarea>
                            @error('deskripsi') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                        </div>

                        {{-- 3. Grid Bobot & Status (Kecil & Rapi) --}}
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label-sm">Bobot (%) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control form-control-sm @error('bobot') is-invalid @enderror" 
                                           wire:model="bobot" min="0" max="100" placeholder="0">
                                    <span class="input-group-text bg-light text-muted">%</span>
                                </div>
                                @error('bobot') <div class="text-danger small mt-1" style="font-size: 0.7rem;">{{ $message }}</div> @enderror
                                @error('bobot_total') <div class="text-danger small mt-1 d-block" style="font-size: 0.7rem;">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-6">
                                <label class="form-label-sm">Status <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                                @error('status') <div class="text-danger small mt-1" style="font-size: 0.7rem;">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- FOOTER COMPACT --}}
                    <div class="modal-footer bg-light border-top py-2 px-3">
                        <button type="button" class="btn btn-sm btn-secondary border" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary fw-bold text-white px-3" 
                                style="background-color: #C38E44; border-color: #C38E44;">
                             {{ $isEditMode ? 'Simpan' : 'Simpan' }}
                             <div wire:loading wire:target="saveKompetensi" class="spinner-border spinner-border-sm ms-1"></div>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    const modalEl = document.getElementById('kompetensiModal');
    const modalObj = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });

    document.addEventListener('livewire:initialized', () => {
        @this.on('open-kompetensi-modal', () => { modalObj.show(); });
        @this.on('close-kompetensi-modal', () => { modalObj.hide(); });
        
        @this.on('show-delete-confirmation-kompetensi', () => { 
            Swal.fire({
                title: 'Hapus Kompetensi?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', 
                confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
            }).then((res) => { if (res.isConfirmed) @this.dispatch('deleteConfirmedKompetensi'); });
        });
    });

    modalEl.addEventListener('hidden.bs.modal', () => {
        @this.call('resetForm');
    });
</script>
@endpush