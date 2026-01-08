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

            /* --- BAGIAN 1: HEADER KARTU (NAMA KOMPETENSI) --- */
            /* Kolom 2 (Nama) jadi Header Kartu */
            tbody td:nth-child(2) {
                background: linear-gradient(to right, rgba(195, 142, 68, 0.1), transparent);
                border-bottom: 1px solid rgba(0,0,0,0.05) !important;
                font-weight: bold;
                font-size: 1.1rem;
                color: #333;
                order: 1; /* Urutan ke-1 */
            }

            /* --- BAGIAN 2: DESKRIPSI --- */
            /* Kolom 3 (Deskripsi) */
            tbody td:nth-child(3) {
                order: 3;
                font-size: 0.9rem;
                color: #666;
                padding-top: 5px;
                padding-bottom: 5px;
            }
            tbody td:nth-child(3)::before {
                content: "Deskripsi:";
                display: block; font-size: 0.75rem; font-weight: 700; color: #aaa; margin-bottom: 2px;
            }

            /* --- BAGIAN 3: INFO BOBOT & STATUS (GRID) --- */
            /* Kita buat Bobot & Status bersebelahan di baris baru */
            
            /* Kolom 4 (Bobot) */
            tbody td:nth-child(4) {
                order: 2; /* Tampil setelah judul */
                width: 50%; /* Setengah lebar */
                float: left;
                border-bottom: 1px dashed rgba(0,0,0,0.05) !important;
            }
            tbody td:nth-child(4)::before {
                content: "Bobot";
                display: block; font-size: 0.7rem; font-weight: 700; color: #aaa; text-transform: uppercase;
            }

            /* Kolom 5 (Status) */
            tbody td:nth-child(5) {
                order: 2;
                width: 50%;
                float: right;
                text-align: right; /* Badge status rata kanan */
                border-bottom: 1px dashed rgba(0,0,0,0.05) !important;
            }

            /* --- BAGIAN 4: NOMOR URUT --- */
            /* Kolom 1 (No) disembunyikan saja di mobile agar bersih, atau bisa ditaruh kecil */
            tbody td:nth-child(1) { display: none; }

            /* --- BAGIAN 5: AKSI (FOOTER) --- */
            /* Kolom 6 (Action) */
            tbody td:nth-child(6) {
                order: 4;
                background-color: rgba(0,0,0,0.02);
                border-top: 1px solid rgba(0,0,0,0.05) !important;
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                padding: 10px 15px;
            }
        }

        /* Desktop Fix */
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

    {{-- ALERT TOTAL BOBOT --}}
    <div class="alert {{ $totalBobotAktif == 100 ? 'alert-success' : 'alert-warning' }} d-flex align-items-start" role="alert">
        <i class="bi {{ $totalBobotAktif == 100 ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }} me-2 mt-1"></i>
        <div>
            Total bobot kompetensi yang berstatus Aktif saat ini: <strong>{{ $totalBobotAktif }}%</strong>. 
            @if($totalBobotAktif != 100)
                <br><span class="small">Harap sesuaikan agar totalnya menjadi pas <strong>100%</strong>.</span>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        {{-- HEADER CARD (SEARCH) --}}
        <div class="card-header bg-white py-3 border-bottom-0">
             <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                 <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="bi bi-table me-2"></i>Daftar Kompetensi
                 </h5>
                 <div class="input-group w-100 w-md-auto" style="max-width: 400px;">
                      <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                      <input type="text" class="form-control border-start-0 ps-0" placeholder="Cari Kompetensi..." wire:model.live.debounce.300ms="search">
                 </div>
             </div>
        </div>

        <div class="card-body p-0">
            <div class=""> {{-- Hapus table-responsive agar card view berfungsi --}}
                <table class="table table-hover mb-0 align-middle">
                    {{-- HEADER TABEL (Hanya Desktop) --}}
                    <thead class="bg-custom-brown text-white">
                        <tr>
                            <th scope="col" class="text-center" style="width: 50px;">No</th>
                            <th scope="col">Nama Kompetensi</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" class="text-center">Bobot (%)</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse ($daftarKompetensi as $index => $komp)
                        <tr wire:key="{{ $komp->id }}">
                            {{-- 1. No --}}
                            <td class="text-center fw-bold">{{ $daftarKompetensi->firstItem() + $index }}</td>
                            
                            {{-- 2. Nama --}}
                            <td>{{ $komp->nama_kompetensi }}</td>
                            
                            {{-- 3. Deskripsi --}}
                            <td>
                                @if($komp->deskripsi)
                                    <small class="text-muted">{{ Str::limit($komp->deskripsi, 100) }}</small>
                                @else
                                    <span class="text-muted small fst-italic">-</span>
                                @endif
                            </td>
                            
                            {{-- 4. Bobot --}}
                            <td class="text-center fw-bold text-primary">{{ $komp->bobot }}%</td>
                            
                            {{-- 5. Status --}}
                            <td class="text-center">
                                @if ($komp->status == 'Aktif')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 rounded-pill">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1 rounded-pill">
                                        <i class="bi bi-x-circle-fill me-1"></i> Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            
                            {{-- 6. Aksi --}}
                            {{-- ... Bagian CSS dan Header sama ... --}}

                            {{-- DIGANTI BAGIAN KOLOM AKSI (Button Hapus) --}}
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary" title="Edit" 
                                        wire:click="edit({{ $komp->id }})"> 
                                    <i class="bi bi-pencil-square"></i> <span class="d-md-none ms-1">Edit</span>
                                </button>

                                {{-- LOGIKA: Jika pertanyaans_count == 0, tombol hapus muncul. Jika tidak, tombol hilang/lock --}}
                                @if($komp->pertanyaans_count == 0)
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-1" title="Hapus" 
                                            wire:click="confirmDelete({{ $komp->id }})">
                                        <i class="bi bi-trash"></i> <span class="d-md-none ms-1">Hapus</span>
                                    </button>
                                @else
                                    {{-- <span class="d-inline-block ms-1" tabindex="0" data-bs-toggle="tooltip" title="Terkunci: Berisi Pertanyaan">
                                        <button class="btn btn-sm btn-light text-muted border" style="cursor: not-allowed;" disabled>
                                            <i class="bi bi-lock-fill"></i>
                                        </button>
                                    </span> --}}
                                @endif
                            </td>

                            {{-- ... Sisa kode Modal dan Script sama ... --}}
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted p-5">
                                <div class="d-flex flex-column align-items-center opacity-50">
                                    <i class="bi bi-inbox display-1"></i>
                                    <p class="mt-2">Data kompetensi belum tersedia.</p>
                                </div>
                            </td>
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

    {{-- MODAL FORM (Bootstrap Standard Responsive) --}}
    <div wire:ignore.self class="modal fade" id="kompetensiModal" tabindex="-1" aria-labelledby="kompetensiModalLabel" aria-hidden="true"> 
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit="saveKompetensi"> 
                    <div class="modal-header">
                        <h5 class="modal-title" id="kompetensiModalLabel">
                            {{ $isEditMode ? 'Edit' : 'Tambah' }} Kompetensi
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label for="nama_kompetensi" class="form-label fw-bold small text-uppercase text-muted">Nama Kompetensi <span class="text-danger">*</span></label>
                            <input type="text" id="nama_kompetensi" class="form-control @error('nama_kompetensi') is-invalid @enderror" wire:model="nama_kompetensi" placeholder="Contoh: Kepribadian">
                            @error('nama_kompetensi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label fw-bold small text-uppercase text-muted">Deskripsi (Opsional)</label>
                            <textarea id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" wire:model="deskripsi" rows="3" placeholder="Penjelasan singkat..."></textarea>
                            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="bobot" class="form-label fw-bold small text-uppercase text-muted">Bobot (%) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" id="bobot" class="form-control @error('bobot') is-invalid @enderror @error('bobot_total') is-invalid @enderror" wire:model="bobot" min="0" max="100" placeholder="0-100">
                                    <span class="input-group-text bg-light">%</span>
                                    @error('bobot') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    @error('bobot_total') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="status_komp" class="form-label fw-bold small text-uppercase text-muted">Status <span class="text-danger">*</span></label>
                                <select id="status_komp" class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #C38E44; border-color: #C38E44;">
                             {{ $isEditMode ? 'Update Data' : 'Simpan Data' }}
                             <div wire:loading wire:target="saveKompetensi" class="spinner-border spinner-border-sm ms-1" role="status">
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
    const kompetensiModalElement = document.getElementById('kompetensiModal');
    const kompetensiModal = kompetensiModalElement ? new bootstrap.Modal(kompetensiModalElement, { keyboard: false, backdrop: 'static' }) : null;

    document.addEventListener('livewire:initialized', () => {
        @this.on('open-kompetensi-modal', () => { if (kompetensiModal) kompetensiModal.show(); });
        @this.on('close-kompetensi-modal', () => { if (kompetensiModal) kompetensiModal.hide(); });
        @this.on('show-delete-confirmation-kompetensi', () => { 
            Swal.fire({
                title: 'Hapus Kompetensi?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal'
            }).then((result) => { if (result.isConfirmed) @this.dispatch('deleteConfirmedKompetensi'); });
        });
    });

    if (kompetensiModalElement) { 
         kompetensiModalElement.addEventListener('hidden.bs.modal', event => {
           if (typeof @this !== 'undefined') @this.call('resetForm'); 
         });
    }
</script>
@endpush