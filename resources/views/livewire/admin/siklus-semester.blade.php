<div class="container-fluid p-4">
    
    {{-- HEADER HALAMAN --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
       <div class="d-flex align-items-center">
            <h2 class="h3 mb-0 text-dark">Siklus Semester</h2>
       </div>
       <button type="button" class="btn btn-success shadow-sm" wire:click="showTambahModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Data
       </button>
    </div>

    {{-- ALERT MESSAGES --}}
    @if (session()->has('message')) 
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session()->has('error')) 
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABEL DATA --}}
    <div class="card shadow-sm border-0">
        {{-- Card Header & Search --}}
        <div class="card-header bg-white py-3">
             <div class="d-flex justify-content-between align-items-center">
                 <h5 class="mb-0 fw-bold">
                     <i class="bi bi-table me-2"></i> Daftar Siklus Semester
                 </h5>
                 <div class="input-group" style="width: 300px;">
                     <span class="input-group-text"><i class="bi bi-search"></i></span>
                     <input type="text" class="form-control" placeholder="Cari Tahun/Semester..." wire:model.live.debounce.300ms="search">
                 </div>
             </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-custom-brown text-white">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarSiklus as $index => $siklus)
                        <tr wire:key="{{ $siklus->id }}">
                            <td class="text-center">{{ $daftarSiklus->firstItem() + $index }}</td>
                            <td>{{ $siklus->tahun_ajaran }}</td>
                            <td>{{ $siklus->semester }}</td>
                            <td>
                                @if ($siklus->status == 'Aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{-- Tombol Edit (Selalu Ada) --}}
                                <button class="btn btn-sm btn-outline-primary border-0 me-1" wire:click="edit({{ $siklus->id }})">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>

                                {{-- LOGIKA TOMBOL HAPUS VS MATA --}}
                                @if($siklus->penilaianSession)
                                    {{-- JIKA SUDAH DIPAKAI: Tombol Link ke Halaman Baru --}}
                                    {{-- Pastikan route 'admin.rekap-siklus' sudah ada di web.php --}}
                                    <a href="{{ route('admin.rekap-siklus', $siklus->id) }}" 
                                       class="btn btn-sm btn-info text-white border-0" 
                                       title="Lihat Rekap Nilai (Halaman Baru)">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                @else
                                    {{-- JIKA BELUM DIPAKAI: Tombol Hapus --}}
                                    <button class="btn btn-sm btn-outline-danger border-0" 
                                            title="Hapus" 
                                            wire:click="confirmDelete({{ $siklus->id }})">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center p-4">Data kosong.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
             @if($daftarSiklus->hasPages())
             <div class="card-footer bg-white py-3">
                 {{ $daftarSiklus->links() }}
             </div>
             @endif
        </div>
    </div>

    {{-- MODAL TAMBAH/EDIT SIKLUS (TETAP ADA) --}}
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
                            <div class="col-md-6">
                                <label for="tahun_ajaran" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                <input type="number" id="tahun_ajaran" class="form-control @error('tahun_ajaran') is-invalid @enderror" wire:model="tahun_ajaran" placeholder="Contoh: 2024">
                                @error('tahun_ajaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                                <select id="semester" class="form-select @error('semester') is-invalid @enderror" wire:model="semester">
                                    <option value="">-- Pilih Semester --</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                                @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                         <div class="mb-3">
                            <label for="status" class="form-label">Status Siklus <span class="text-danger">*</span></label>
                            <select id="status" class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                             @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-primary">
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
        // 1. Inisialisasi Modal CRUD
        const modalElement = document.getElementById('siklusModal');
        const siklusModal = new bootstrap.Modal(modalElement);

        // 2. Listener Buka/Tutup Modal CRUD
        @this.on('open-modal', () => { siklusModal.show(); });
        @this.on('close-modal', () => { siklusModal.hide(); });

        // 3. Listener Konfirmasi Hapus
        @this.on('show-delete-confirmation', () => {
            Swal.fire({
                title: 'Anda yakin?',
                text: "Data siklus semester ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) { @this.dispatch('deleteConfirmed'); }
            });
        });

        // 4. Reset form saat modal ditutup paksa
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', event => {
                if (typeof @this !== 'undefined') { @this.call('closeModal'); }
            });
        }
    });
</script>
@endpush