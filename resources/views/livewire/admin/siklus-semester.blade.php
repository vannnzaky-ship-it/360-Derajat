<div class="container-fluid p-4">

    {{-- STYLE KHUSUS & DARK MODE FIX --}}
    <style>
        /* --- 1. GLOBAL STYLE (LIGHT) --- */
        .text-custom-brown { color: #C38E44; }
        .bg-custom-brown { background-color: #C38E44 !important; }
        
        /* --- 2. STYLE MODAL COMPACT --- */
        .modal-dialog-compact { 
            max-width: 500px; 
            margin-top: 60px; 
            margin-bottom: 2rem; 
        }
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

            /* Mapping Label Mobile */
            tbody td:nth-child(2) { /* Tahun */
                background: linear-gradient(to right, rgba(195, 142, 68, 0.1), transparent);
                border-bottom: 1px solid rgba(0,0,0,0.05) !important;
                font-weight: bold; font-size: 1.1rem; color: #333; order: 1;
            }
            tbody td:nth-child(2)::before { content: "Tahun Ajaran "; color: #666; font-size: 0.9rem; font-weight: normal; }

            tbody td:nth-child(3) { /* Semester */
                order: 2; width: 50%; float: left; border-bottom: 1px dashed rgba(0,0,0,0.05) !important;
            }
            tbody td:nth-child(3)::before { content: "Semester"; display: block; font-size: 0.7rem; font-weight: 700; color: #aaa; text-transform: uppercase; }

            tbody td:nth-child(4) { /* Status */
                order: 2; width: 50%; float: right; text-align: right; border-bottom: 1px dashed rgba(0,0,0,0.05) !important;
            }
            tbody td:nth-child(1) { display: none; } 

            tbody td:nth-child(5) { /* Aksi */
                order: 3; background-color: rgba(0,0,0,0.02); border-top: 1px solid rgba(0,0,0,0.05) !important;
                display: flex; justify-content: flex-end; gap: 10px; padding: 10px 15px;
            }
        }
        @media (min-width: 768px) { .w-md-auto { width: auto !important; } }

        
        /* 1. Global Backgrounds & Text */
        [data-bs-theme="dark"] .bg-white {
            background-color: #1e1e1e !important;
            color: #e0e0e0 !important;
        }
        [data-bs-theme="dark"] .text-dark { color: #f8f9fa !important; }
        [data-bs-theme="dark"] .text-muted { color: #adb5bd !important; }
        
        /* 2. Input Fields & Search */
        [data-bs-theme="dark"] .input-group-text, 
        [data-bs-theme="dark"] .bg-light {
            background-color: #2c2c2c !important; /* Abu Gelap */
            border-color: #444 !important;
            color: #ccc !important;
        }
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #2c2c2c !important;
            border-color: #444 !important;
            color: #fff !important;
        }
        [data-bs-theme="dark"] .form-control::placeholder { color: #777 !important; }

        /* 3. Card Styles */
        [data-bs-theme="dark"] .card {
            background-color: #1e1e1e !important;
            border: 1px solid #333 !important;
        }
        
        /* 4. Table Styles */
        [data-bs-theme="dark"] .table { color: #e0e0e0 !important; }
        [data-bs-theme="dark"] .table-hover tbody tr:hover {
            color: #e0e0e0;
            background-color: rgba(255,255,255,0.05);
        }
        
        /* 5. Modal Styles */
        [data-bs-theme="dark"] .modal-content {
            background-color: #1e1e1e !important;
            border: 1px solid #444 !important;
        }
        [data-bs-theme="dark"] .modal-header,
        [data-bs-theme="dark"] .modal-footer {
            border-color: #333 !important;
        }
        [data-bs-theme="dark"] .form-label-sm { color: #ccc !important; }
        [data-bs-theme="dark"] .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

        /* 6. Mobile View Fixes in Dark Mode */
        @media (max-width: 767px) {
            [data-bs-theme="dark"] tbody tr {
                background-color: #1e1e1e !important;
                border-color: #333 !important;
            }
            [data-bs-theme="dark"] tbody td:nth-child(2) {
                background: linear-gradient(to right, rgba(195, 142, 68, 0.2), transparent) !important;
                color: #fff !important;
                border-bottom-color: #333 !important;
            }
            [data-bs-theme="dark"] tbody td:nth-child(3),
            [data-bs-theme="dark"] tbody td:nth-child(4) {
                border-bottom-color: #333 !important;
            }
            [data-bs-theme="dark"] tbody td:nth-child(5) {
                background-color: rgba(255,255,255,0.05) !important;
                border-top-color: #333 !important;
            }
        }

        /* 7. SWEETALERT (POPUP HAPUS) DARK MODE CSS */
        /* Ini untuk memastikan CSS dasar juga gelap */
        [data-bs-theme="dark"] .swal2-popup {
            background-color: #1e1e1e !important;
            color: #e0e0e0 !important;
            border: 1px solid #333;
        }
        [data-bs-theme="dark"] .swal2-title { color: #f8f9fa !important; }
        [data-bs-theme="dark"] .swal2-html-container { color: #adb5bd !important; }
    </style>
    
    {{-- HEADER HALAMAN --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
       <div class="d-flex align-items-center">
            <i class="bi bi-calendar-range-fill fs-1 text-custom-brown me-3"></i>
            <h2 class="h3 mb-0 text-dark">Siklus Semester</h2>
       </div>
       <button type="button" class="btn btn-success shadow-sm w-100 w-md-auto" wire:click="showTambahModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Siklus
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
        <div class="card-header bg-white py-3 border-bottom-0">
             <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                 <h5 class="mb-0 fw-bold d-flex align-items-center text-dark">
                     <i class="bi bi-table me-2"></i> Daftar Siklus Semester
                 </h5>
                 <div class="input-group w-100 w-md-auto" style="max-width: 400px;">
                      <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                      <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Tahun/Semester..." wire:model.live.debounce.300ms="search">
                 </div>
             </div>
        </div>
        
        <div class="card-body p-0">
            <div class="">
                <table class="table table-hover mb-0 align-middle">
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
                            <td class="text-center fw-bold">{{ $daftarSiklus->firstItem() + $index }}</td>
                            <td>{{ $siklus->tahun_ajaran }}</td>
                            <td>{{ $siklus->semester }}</td>
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
                            <td class="text-center">
                                <div class="d-flex justify-content-end justify-content-md-center gap-1">
                                    <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $siklus->id }})" title="Edit">
                                        <i class="bi bi-pencil-square"></i> <span class="d-md-none ms-1">Edit</span>
                                    </button>

                                    @if($siklus->penilaianSession)
                                        @php
                                            $batasWaktu = \Carbon\Carbon::parse($siklus->penilaianSession->batas_waktu);
                                            $isOngoing = now()->lessThan($batasWaktu);
                                            $tglIndo = $batasWaktu->translatedFormat('d F Y H:i');
                                        @endphp

                                        @if($isOngoing)
                                            <button class="btn btn-sm btn-secondary border-0" 
                                                    {{-- Gunakan JS Swal manual disini juga untuk konsistensi --}}
                                                    onclick="showLockedAlert('{{ $tglIndo }}')">
                                                <i class="bi bi-eye-slash-fill"></i> <span class="d-md-none ms-1">Terkunci</span>
                                            </button>
                                        @else
                                            <a href="{{ route('admin.rekap-siklus', $siklus->id) }}" class="btn btn-sm btn-info text-white border-0" title="Lihat Rekap">
                                                <i class="bi bi-eye-fill"></i> <span class="d-md-none ms-1">Rekap</span>
                                            </a>
                                        @endif
                                    @else
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
             @if($daftarSiklus->hasPages())
             <div class="card-footer bg-white py-3 border-top-0">
                 {{ $daftarSiklus->links() }}
             </div>
             @endif
        </div>
    </div>

    {{-- MODAL TAMBAH/EDIT SIKLUS --}}
    <div wire:ignore.self class="modal fade" id="siklusModal" tabindex="-1" aria-labelledby="siklusModalLabel" aria-hidden="true"
         style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(5px);">
        
        <div class="modal-dialog modal-dialog-centered modal-dialog-compact">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                <form wire:submit="saveSiklus"> 
                    
                    <div class="modal-header py-2 px-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 text-warning rounded p-1 me-2">
                                <i class="bi bi-calendar-event-fill fs-6"></i>
                            </div>
                            <h6 class="modal-title fw-bold m-0 text-dark" id="siklusModalLabel">
                                {{ $isEditMode ? 'Edit' : 'Tambah' }} Siklus
                            </h6>
                        </div>
                        <button type="button" class="btn-close btn-sm" wire:click="closeModal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <label for="tahun_ajaran" class="form-label-sm">Tahun Ajaran <span class="text-danger">*</span></label>
                                <input type="text" id="tahun_ajaran" 
                                    class="form-control form-control-sm @error('tahun_ajaran') is-invalid @enderror" 
                                    wire:model="tahun_ajaran" 
                                    placeholder="Contoh: 2025/2026"
                                    maxlength="9">
                                @error('tahun_ajaran') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-6">
                                <label for="semester" class="form-label-sm">Semester <span class="text-danger">*</span></label>
                                <select id="semester" class="form-select form-select-sm @error('semester') is-invalid @enderror" wire:model="semester">
                                    <option value="">-- Pilih --</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                                @error('semester') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 mt-0 mb-2">
                                <div class="text-muted" style="font-size: 0.65rem; margin-top: -2px;">Format Tahun: TAHUN/TAHUN (cth: 2025/2026)</div>
                            </div>

                            <div class="col-12">
                                <label for="status" class="form-label-sm">Status Siklus <span class="text-danger">*</span></label>
                                <select id="status" class="form-select form-select-sm @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                                @error('status') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top py-2 px-3">
                        <button type="button" class="btn btn-sm btn-secondary border" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary fw-bold text-white px-3" style="background-color: #C38E44; border-color: #C38E44;">
                             {{ $isEditMode ? 'Simpan' : 'Simpan' }}
                             <div wire:loading wire:target="saveSiklus" class="spinner-border spinner-border-sm ms-1" role="status"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Fungsi bantuan untuk alert "Terkunci" agar bisa dipanggil di HTML
    function showLockedAlert(dateStr) {
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        Swal.fire({
            icon: 'info', 
            title: 'Penilaian Sedang Berjalan', 
            text: 'Hasil rekap baru dapat dilihat setelah ' + dateStr + ' WIB.', 
            confirmButtonColor: '#c38e44',
            background: isDark ? '#1e1e1e' : '#fff',
            color: isDark ? '#e9ecef' : '#545454'
        });
    }

    document.addEventListener('livewire:initialized', () => {
        const modalElement = document.getElementById('siklusModal');
        const siklusModal = modalElement ? new bootstrap.Modal(modalElement, { backdrop: 'static', keyboard: false }) : null;

        @this.on('open-modal', () => { if(siklusModal) siklusModal.show(); });
        @this.on('close-modal', () => { if(siklusModal) siklusModal.hide(); });

        // SWEETALERT HAPUS DENGAN DETEKSI DARK MODE
        @this.on('show-delete-confirmation', () => {
            // Cek apakah tema gelap aktif di tag <html>
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

            Swal.fire({
                title: 'Hapus Data?',
                text: "Data siklus semester ini akan dihapus permanen!",
                icon: 'warning', 
                showCancelButton: true, 
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6', 
                confirmButtonText: 'Ya, hapus!', 
                cancelButtonText: 'Batal',
                // Konfigurasi Warna Manual
                background: isDark ? '#1e1e1e' : '#fff',
                color: isDark ? '#e9ecef' : '#545454'
            }).then((result) => { 
                if (result.isConfirmed) { @this.dispatch('deleteConfirmed'); } 
            });
        });

        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', event => {
                if (typeof @this !== 'undefined') { @this.call('closeModal'); }
            });
        }
    });
</script>
@endpush