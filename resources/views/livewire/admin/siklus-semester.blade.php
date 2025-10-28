<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-repeat fs-1 text-custom-brown me-3"></i>
            <h2 class="h3 mb-0 text-dark">Siklus Semester</h2>
        </div>
        
        <button type="button" class="btn btn-success shadow-sm" 
                wire:click="showTambahModal">
            <i class="bi bi-plus-lg me-2"></i>
            Tambah Data
        </button>
        </div>

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

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
             <div class="d-flex justify-content-between align-items-center">
                 <h5 class="mb-0 fw-bold">
                     <i class="bi bi-table me-2"></i>
                     Daftar Siklus Semester
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
                            <th scope="col" class="text-center" style="width: 50px;">No</th>
                            <th scope="col">Tahun Ajaran</th>
                            <th scope="col">Semester</th>
                            <th scope="col" style="width: 25%;">Penilaian (%)</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarSiklus as $index => $siklus)
                        <tr wire:key="{{ $siklus->id }}">
                            <td class="text-center fw-bold">{{ $daftarSiklus->firstItem() + $index }}</td>
                            <td>{{ $siklus->tahun_ajaran }}</td>
                            <td>{{ $siklus->semester }}</td>
                            <td>
                                <small class="d-block">Diri Sendiri: {{ $siklus->persen_diri }}%</small>
                                <small class="d-block">Atasan: {{ $siklus->persen_atasan }}%</small>
                                <small class="d-block">Rekan: {{ $siklus->persen_rekan }}%</small>
                                <small class="d-block">Bawahan: {{ $siklus->persen_bawahan }}%</small>
                                </td>
                            <td>
                                @if ($siklus->status == 'Aktif')
                                    <span class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> Aktif</span>
                                @else
                                    <span class="fw-bold text-danger"><i class="bi bi-x-circle-fill me-1"></i> Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary border-0" title="Edit" 
                                        wire:click="edit({{ $siklus->id }})">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger border-0" title="Hapus" 
                                        wire:click="confirmDelete({{ $siklus->id }})">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                                </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted p-4">
                                Data tidak ditemukan atau belum ada.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             @if($daftarSiklus->hasPages())
             <div class="card-footer bg-white py-3">
                 {{ $daftarSiklus->links() }}
             </div>
             @endif
        </div>
    </div>

    {{-- Properti showModal akan mengontrol tampilan modal --}}
    <div wire:ignore.self class="modal fade" 
     id="siklusModal" tabindex="-1" aria-labelledby="siklusModalLabel" 
     aria-hidden="true">
         
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                {{-- Form di-handle oleh Livewire --}}
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

                        <div class="row mb-3">
                            <label class="form-label mb-1">Persentase Penilaian (%) <span class="text-danger">*</span></label>
                            <small class="text-muted d-block mb-2">Total persentase keempat komponen harus 100%.</small>
                            
                            <div class="col-md-3">
                                <label for="persen_diri" class="form-label small">Diri Sendiri</label>
                                <input type="number" id="persen_diri" class="form-control @error('persen_diri') is-invalid @enderror @error('persen_total') is-invalid @enderror" wire:model.live="persen_diri" min="0" max="100">
                                @error('persen_diri') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="persen_atasan" class="form-label small">Atasan</label>
                                <input type="number" id="persen_atasan" class="form-control @error('persen_atasan') is-invalid @enderror @error('persen_total') is-invalid @enderror" wire:model.live="persen_atasan" min="0" max="100">
                                @error('persen_atasan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                             <div class="col-md-3">
                                <label for="persen_rekan" class="form-label small">Rekan Sejawat</label>
                                <input type="number" id="persen_rekan" class="form-control @error('persen_rekan') is-invalid @enderror @error('persen_total') is-invalid @enderror" wire:model.live="persen_rekan" min="0" max="100">
                                @error('persen_rekan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                             <div class="col-md-3">
                                <label for="persen_bawahan" class="form-label small">Bawahan</label>
                                <input type="number" id="persen_bawahan" class="form-control @error('persen_bawahan') is-invalid @enderror @error('persen_total') is-invalid @enderror" wire:model.live="persen_bawahan" min="0" max="100">
                                @error('persen_bawahan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                             @error('persen_total') <div class="col-12 text-danger small mt-2">{{ $message }}</div> @enderror
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
        
        // 1. Ambil elemen modal dan inisialisasi Bootstrap Modal
        const modalElement = document.getElementById('siklusModal');
        const siklusModal = new bootstrap.Modal(modalElement);

        // 2. Aktifkan listener untuk BUKA modal
        @this.on('open-modal', () => {
            siklusModal.show();
        });

        // 3. Aktifkan listener untuk TUTUP modal
        @this.on('close-modal', () => {
            siklusModal.hide();
        });

        // 4. Biarkan listener untuk konfirmasi hapus:
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
                if (result.isConfirmed) {
                    @this.dispatch('deleteConfirmed'); 
                }
            });
        });

        // 5. Biarkan listener ini untuk reset form saat modal ditutup paksa:
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', event => {
                if (typeof @this !== 'undefined') { 
                    @this.call('closeModal'); 
                }
            });
        }
    });
</script>
@endpush