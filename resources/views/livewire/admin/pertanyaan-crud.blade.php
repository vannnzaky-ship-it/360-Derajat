<div class="container-fluid p-4">

    <!-- Header & Tombol Tambah -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-question-circle-fill fs-1 text-custom-brown me-3"></i>
            <h2 class="h3 mb-0 text-dark">Manajemen Pertanyaan</h2>
        </div>
        <button type="button" class="btn btn-success shadow-sm" wire:click="showTambahModal"> 
            <i class="bi bi-plus-lg me-2"></i> Tambah Pertanyaan
        </button>
    </div>

    <!-- Notifikasi -->
    @if (session()->has('message')) {{-- ... --}} @endif
    @if (session()->has('error')) {{-- ... --}} @endif

    <!-- Tabel Data -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 border-bottom-0">
             <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                 <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-table me-2"></i>Daftar Pertanyaan</h5>
                 <div class="input-group" style="width: 300px;">
                     <span class="input-group-text"><i class="bi bi-search"></i></span>
                     <input type="text" class="form-control" placeholder="Cari Pertanyaan/Kompetensi..." wire:model.live.debounce.300ms="search">
                 </div>
             </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-custom-brown text-white">
                        <tr>
                            <th scope="col" class="text-center" style="width: 50px;">No</th>
                            <th scope="col" style="width: 15%;">Kompetensi</th>
                            <th scope="col">Teks Pertanyaan</th>
                            <th scope="col" style="width: 20%;">Tipe Penilai</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarPertanyaan as $index => $pert)
                        <tr wire:key="{{ $pert->id }}">
                            <td class="text-center fw-bold">{{ $daftarPertanyaan->firstItem() + $index }}</td>
                            <td>{{ $pert->kompetensi->nama_kompetensi ?? 'N/A' }}</td>
                            <td>{{ $pert->teks_pertanyaan }}</td>
                            <td>
                                {{-- Tampilkan tipe penilai --}}
                                @php $penilai = []; @endphp
                                @if($pert->untuk_diri) @php $penilai[] = 'Diri'; @endphp @endif
                                @if($pert->untuk_atasan) @php $penilai[] = 'Atasan'; @endphp @endif
                                @if($pert->untuk_rekan) @php $penilai[] = 'Rekan'; @endphp @endif
                                @if($pert->untuk_bawahan) @php $penilai[] = 'Bawahan'; @endphp @endif
                                <small>{{ implode(', ', $penilai) }}</small>
                            </td>
                            <td>
                                @if ($pert->status == 'Aktif')
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Aktif</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i> Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary border-0" title="Edit Status" 
                                        wire:click="edit({{ $pert->id }})"> 
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger border-0" title="Hapus" 
                                        wire:click="confirmDelete({{ $pert->id }})">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted p-4">
                                Data pertanyaan tidak ditemukan atau belum ada.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             @if($daftarPertanyaan->hasPages())
             <div class="card-footer bg-white py-3 border-top-0 d-flex justify-content-between align-items-center">
                 <div>
                    <span class="text-muted me-2">Per Halaman:</span>
                    <select class="form-select form-select-sm d-inline-block" wire:model.live="perPage" style="width: 70px;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                 </div>
                 {{ $daftarPertanyaan->links() }}
             </div>
             @endif
        </div>
    </div>

    <!-- ==== MODAL TAMBAH/EDIT PERTANYAAN ==== -->
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
                                <label for="kompetensi_id" class="form-label">Kompetensi (Kriteria) <span class="text-danger">*</span></label>
                                <select id="kompetensi_id" class="form-select @error('kompetensi_id') is-invalid @enderror" wire:model="kompetensi_id">
                                    <option value="">-- Pilih Kompetensi --</option>
                                    @foreach ($kompetensiList as $komp)
                                        <option value="{{ $komp->id }}">{{ $komp->nama_kompetensi }} (Bobot: {{ $komp->bobot }}%)</option>
                                    @endforeach
                                </select>
                                @error('kompetensi_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="teks_pertanyaan" class="form-label">Teks Pertanyaan (Sub Kriteria) <span class="text-danger">*</span></label>
                                <textarea id="teks_pertanyaan" class="form-control @error('teks_pertanyaan') is-invalid @enderror" wire:model="teks_pertanyaan" rows="3" placeholder="Masukkan teks pertanyaan..."></textarea>
                                @error('teks_pertanyaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tipe Penilai (Ditujukan Kepada) <span class="text-danger">*</span></label>
                                @error('penilai_checkbox') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                                <div class="d-flex flex-wrap gap-3">
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
                            <label for="status_pert" class="form-label">Status Pertanyaan <span class="text-danger">*</span></label>
                            <select id="status_pert" class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                             @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-primary">
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
    <!-- ============================== -->
</div>

@push('scripts')
<script>
    // Inisialisasi objek modal Bootstrap
    const pertanyaanModalElement = document.getElementById('pertanyaanModal');
    const pertanyaanModal = pertanyaanModalElement ? new bootstrap.Modal(pertanyaanModalElement, { keyboard: false, backdrop: 'static' }) : null;

    document.addEventListener('livewire:initialized', () => {
        // Listener Buka Modal
        @this.on('open-pertanyaan-modal', () => { if (pertanyaanModal) pertanyaanModal.show(); });
        // Listener Tutup Modal
        @this.on('close-pertanyaan-modal', () => { if (pertanyaanModal) pertanyaanModal.hide(); });
        // Listener Konfirmasi Hapus
        @this.on('show-delete-confirmation-pertanyaan', () => { 
            Swal.fire({
                title: 'Anda yakin?', text: "Pertanyaan ini akan dihapus permanen!", icon: 'warning', 
                showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', 
                confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
            }).then((result) => { if (result.isConfirmed) @this.dispatch('deleteConfirmedPertanyaan'); });
        });
    });

    // Reset form saat modal ditutup paksa
    if (pertanyaanModalElement) { 
         pertanyaanModalElement.addEventListener('hidden.bs.modal', event => {
           if (typeof @this !== 'undefined') @this.call('resetForm'); 
         });
    }
</script>
@endpush
