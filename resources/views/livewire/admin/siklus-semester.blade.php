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

            /* --- BAGIAN 1: HEADER KARTU (TAHUN AJARAN) --- */
            /* Kolom 2 (Tahun Ajaran) jadi Judul */
            tbody td:nth-child(2) {
                background: linear-gradient(to right, rgba(195, 142, 68, 0.1), transparent);
                border-bottom: 1px solid rgba(0,0,0,0.05) !important;
                font-weight: bold; font-size: 1.1rem; color: #333;
                order: 1;
            }
            tbody td:nth-child(2)::before {
                content: "Tahun Ajaran "; color: #666; font-size: 0.9rem; font-weight: normal;
            }

            /* --- BAGIAN 2: SEMESTER & STATUS (GRID) --- */
            /* Kolom 3 (Semester) */
            tbody td:nth-child(3) {
                order: 2; width: 50%; float: left;
                border-bottom: 1px dashed rgba(0,0,0,0.05) !important;
            }
            tbody td:nth-child(3)::before {
                content: "Semester"; display: block; font-size: 0.7rem; font-weight: 700; color: #aaa; text-transform: uppercase;
            }

            /* Kolom 4 (Status) */
            tbody td:nth-child(4) {
                order: 2; width: 50%; float: right; text-align: right;
                border-bottom: 1px dashed rgba(0,0,0,0.05) !important;
            }

            /* --- BAGIAN 3: NOMOR URUT (Hidden) --- */
            tbody td:nth-child(1) { display: none; }

            /* --- BAGIAN 4: AKSI (FOOTER) --- */
            /* Kolom 5 (Action) */
            tbody td:nth-child(5) {
                order: 3;
                background-color: rgba(0,0,0,0.02);
                border-top: 1px solid rgba(0,0,0,0.05) !important;
                display: flex; justify-content: flex-end; gap: 10px;
                padding: 10px 15px;
            }
        }

        /* Desktop Fix */
        @media (min-width: 768px) { .w-md-auto { width: auto !important; } }
    </style>
    
    {{-- HEADER HALAMAN --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
       <div class="d-flex align-items-center">
            <i class="bi bi-calendar-range-fill fs-1 text-custom-brown me-3"></i>
            <h2 class="h3 mb-0 text-dark">Siklus Semester</h2>
       </div>
       <button type="button" class="btn btn-success shadow-sm w-100 w-md-auto" wire:click="showTambahModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Data
       </button>
    </div>

    {{-- ALERT MESSAGES --}}
    @if (session()->has('message')) 
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error')) 
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABEL DATA --}}
    <div class="card shadow-sm border-0 rounded-3">
        {{-- Card Header & Search --}}
        <div class="card-header bg-white py-3 border-bottom-0">
             <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                 <h5 class="mb-0 fw-bold d-flex align-items-center">
                     <i class="bi bi-table me-2"></i> Daftar Siklus Semester
                 </h5>
                 <div class="input-group w-100 w-md-auto" style="max-width: 400px;">
                     <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                     <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Tahun/Semester..." wire:model.live.debounce.300ms="search">
                 </div>
             </div>
        </div>
        
        <div class="card-body p-0">
            <div class=""> {{-- Hapus table-responsive agar card view mobile bekerja --}}
                <table class="table table-hover mb-0 align-middle">
                    {{-- HEADER TABEL (Hanya Desktop) --}}
                    <thead class="bg-custom-brown text-white">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($daftarSiklus as $index => $siklus)
                        <tr wire:key="{{ $siklus->id }}">
                            {{-- 1. No --}}
                            <td class="text-center fw-bold">{{ $daftarSiklus->firstItem() + $index }}</td>
                            
                            {{-- 2. Tahun Ajaran --}}
                            <td>{{ $siklus->tahun_ajaran }}</td>
                            
                            {{-- 3. Semester --}}
                            <td>{{ $siklus->semester }}</td>
                            
                            {{-- 4. Status --}}
                            <td class="text-center">
                                @if ($siklus->status == 'Aktif')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1 rounded-pill">
                                        <i class="bi bi-x-circle-fill me-1"></i> Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            
                            {{-- 5. Aksi --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-end justify-content-md-center gap-1">
                                    {{-- Tombol Edit --}}
                                    <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $siklus->id }})" title="Edit">
                                        <i class="bi bi-pencil-square"></i> <span class="d-md-none ms-1">Edit</span>
                                    </button>

                                    {{-- LOGIKA TOMBOL HAPUS VS MATA --}}
                                    @if($siklus->penilaianSession)
                                        @php
                                            $batasWaktu = \Carbon\Carbon::parse($siklus->penilaianSession->batas_waktu);
                                            $isOngoing = now()->lessThan($batasWaktu);
                                            $tglIndo = $batasWaktu->translatedFormat('d F Y H:i');
                                        @endphp

                                        @if($isOngoing)
                                            {{-- Sedang Berjalan (Terkunci) --}}
                                            <button class="btn btn-sm btn-secondary border-0" 
                                                    onclick="Swal.fire({
                                                        icon: 'info',
                                                        title: 'Penilaian Sedang Berjalan',
                                                        text: 'Hasil rekap baru dapat dilihat setelah batas waktu berakhir pada {{ $tglIndo }} WIB.',
                                                        confirmButtonColor: '#c38e44'
                                                    })">
                                                <i class="bi bi-eye-slash-fill"></i> <span class="d-md-none ms-1">Terkunci</span>
                                            </button>
                                        @else
                                            {{-- Sudah Selesai (Lihat Rekap) --}}
                                            <a href="{{ route('admin.rekap-siklus', $siklus->id) }}" class="btn btn-sm btn-info text-white border-0" title="Lihat Rekap">
                                                <i class="bi bi-eye-fill"></i> <span class="d-md-none ms-1">Rekap</span>
                                            </a>
                                        @endif
                                    @else
                                        {{-- Belum Ada Sesi (Hapus) --}}
                                        <button class="btn btn-sm btn-outline-danger" wire:click="confirmDelete({{ $siklus->id }})" title="Hapus">
                                            <i class="bi bi-trash"></i> <span class="d-md-none ms-1">Hapus</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted p-5">
                                <div class="d-flex flex-column align-items-center opacity-50">
                                    <i class="bi bi-calendar-x display-1"></i>
                                    <p class="mt-2">Data siklus belum tersedia.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
             @if($daftarSiklus->hasPages())
             <div class="card-footer bg-white py-3 border-top-0">
                 {{ $daftarSiklus->links() }}
             </div>
             @endif
        </div>
    </div>

    {{-- MODAL TAMBAH/EDIT SIKLUS --}}
    <div wire:ignore.self class="modal fade" id="siklusModal" tabindex="-1" aria-labelledby="siklusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit="saveSiklus"> 
                    <div class="modal-header">
                        <h5 class="modal-title" id="siklusModalLabel">
                            {{ $isEditMode ? 'Edit' : 'Tambah' }} Siklus Semester
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="tahun_ajaran" class="form-label fw-bold small text-uppercase text-muted">Tahun Ajaran <span class="text-danger">*</span></label>
                                <input type="number" id="tahun_ajaran" class="form-control @error('tahun_ajaran') is-invalid @enderror" wire:model="tahun_ajaran" placeholder="Contoh: 2024">
                                @error('tahun_ajaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="semester" class="form-label fw-bold small text-uppercase text-muted">Semester <span class="text-danger">*</span></label>
                                <select id="semester" class="form-select @error('semester') is-invalid @enderror" wire:model="semester">
                                    <option value="">-- Pilih Semester --</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                                @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                         <div class="mb-3">
                            <label for="status" class="form-label fw-bold small text-uppercase text-muted">Status Siklus <span class="text-danger">*</span></label>
                            <select id="status" class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                             @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #C38E44; border-color: #C38E44;">
                             {{ $isEditMode ? 'Update Data' : 'Simpan Data' }}
                             <div wire:loading wire:target="saveSiklus" class="spinner-border spinner-border-sm ms-1" role="status">
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
    document.addEventListener('livewire:initialized', () => {
        const modalElement = document.getElementById('siklusModal');
        const siklusModal = modalElement ? new bootstrap.Modal(modalElement) : null;

        @this.on('open-modal', () => { if(siklusModal) siklusModal.show(); });
        @this.on('close-modal', () => { if(siklusModal) siklusModal.hide(); });

        @this.on('show-delete-confirmation', () => {
            Swal.fire({
                title: 'Hapus Data?',
                text: "Data siklus semester ini akan dihapus permanen!",
                icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
            }).then((result) => { if (result.isConfirmed) { @this.dispatch('deleteConfirmed'); } });
        });

        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', event => {
                if (typeof @this !== 'undefined') { @this.call('closeModal'); }
            });
        }
    });
</script>
@endpush