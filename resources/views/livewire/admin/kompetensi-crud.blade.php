<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-bar-chart-fill fs-1 text-custom-brown me-3"></i>
            <h2 class="h3 mb-0 text-dark">Manajemen Kompetensi</h2>
        </div>
        <button type="button" class="btn btn-success shadow-sm" wire:click="showTambahModal"> 
            <i class="bi bi-plus-lg me-2"></i> Tambah Kompetensi
        </button>
    </div>

    @if (session()->has('message')) {{-- ... --}} @endif
    @if (session()->has('error')) {{-- ... --}} @endif

    <div class="alert {{ $totalBobotAktif == 100 ? 'alert-success' : 'alert-warning' }}" role="alert">
        <i class="bi {{ $totalBobotAktif == 100 ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }} me-2"></i>
        Total bobot kompetensi yang berstatus Aktif saat ini: {{ $totalBobotAktif }}%. 
        @if($totalBobotAktif != 100)
            Harap sesuaikan agar totalnya menjadi 100%.
        @endif
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 border-bottom-0">
             <div class="d-flex justify-content-between align-items-center">
                 <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-table me-2"></i>Daftar Kompetensi</h5>
                 <div class="input-group" style="width: 300px;">
                     <span class="input-group-text"><i class="bi bi-search"></i></span>
                     <input type="text" class="form-control" placeholder="Cari Nama Kompetensi..." wire:model.live.debounce.300ms="search">
                 </div>
             </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-custom-brown text-white">
                        <tr>
                            <th scope="col" class="text-center" style="width: 50px;">No</th>
                            <th scope="col">Nama Kompetensi</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" class="text-center">Bobot (%)</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarKompetensi as $index => $komp)
                        <tr wire:key="{{ $komp->id }}">
                            <td class="text-center fw-bold">{{ $daftarKompetensi->firstItem() + $index }}</td>
                            <td>{{ $komp->nama_kompetensi }}</td>
                            <td><small>{{ $komp->deskripsi ?: '-' }}</small></td>
                            <td class="text-center">{{ $komp->bobot }}%</td>
                            <td>
                                @if ($komp->status == 'Aktif')
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Aktif</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i> Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary border-0" title="Edit" 
                                        wire:click="edit({{ $komp->id }})"> 
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger border-0" title="Hapus" 
                                        wire:click="confirmDelete({{ $komp->id }})">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted p-4">
                                Data kompetensi tidak ditemukan atau belum ada.
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
                            <label for="nama_kompetensi" class="form-label">Nama Kompetensi <span class="text-danger">*</span></label>
                            <input type="text" id="nama_kompetensi" class="form-control @error('nama_kompetensi') is-invalid @enderror" wire:model="nama_kompetensi" placeholder="Contoh: Kepribadian">
                            @error('nama_kompetensi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                            <textarea id="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" wire:model="deskripsi" rows="3" placeholder="Penjelasan singkat mengenai kompetensi..."></textarea>
                            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="bobot" class="form-label">Bobot (%) <span class="text-danger">*</span></label>
                                <input type="number" id="bobot" class="form-control @error('bobot') is-invalid @enderror @error('bobot_total') is-invalid @enderror" wire:model="bobot" min="0" max="100" placeholder="0-100">
                                @error('bobot') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('bobot_total') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror {{-- Tampilkan error total --}}
                            </div>
                            <div class="col-md-6">
                                <label for="status_komp" class="form-label">Status <span class="text-danger">*</span></label>
                                <select id="status_komp" class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-primary">
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
    // Inisialisasi objek modal Bootstrap sekali saja
    const kompetensiModalElement = document.getElementById('kompetensiModal');
    const kompetensiModal = kompetensiModalElement ? new bootstrap.Modal(kompetensiModalElement, { keyboard: false, backdrop: 'static' }) : null;

    document.addEventListener('livewire:initialized', () => {
        // Listener Buka Modal
        @this.on('open-kompetensi-modal', () => { if (kompetensiModal) kompetensiModal.show(); });
        // Listener Tutup Modal
        @this.on('close-kompetensi-modal', () => { if (kompetensiModal) kompetensiModal.hide(); });
        // Listener Konfirmasi Hapus
        @this.on('show-delete-confirmation-kompetensi', () => { 
            Swal.fire({
                title: 'Anda yakin?',
                text: "Kompetensi ini akan dihapus permanen!",
                icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
            }).then((result) => { if (result.isConfirmed) @this.dispatch('deleteConfirmedKompetensi'); });
        });
    });

    // Reset form saat modal ditutup paksa
    if (kompetensiModalElement) { 
         kompetensiModalElement.addEventListener('hidden.bs.modal', event => {
           if (typeof @this !== 'undefined') @this.call('resetForm'); 
         });
    }
</script>
@endpush