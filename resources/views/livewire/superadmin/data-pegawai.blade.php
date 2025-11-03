<div>
    <div class="container py-4">
        <h1 class="h3 mb-3">Manajemen Data Pegawai</h1>

        {{-- Notifikasi --}}
        @if (session()->has('message')) {{-- ... --}} @endif
        @if (session()->has('error')) {{-- ... --}} @endif

        <div class="card shadow border-0 rounded-3">
            <div class="card-header bg-white py-3 border-bottom-0 d-flex justify-content-between align-items-center">
                 <div class="input-group w-auto">
                     <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Cari nama, email, NIP, jabatan...">
                 </div>
                <button wire:click="showTambahModal" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Pegawai
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-custom-brown text-white">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>NIP</th>
                                <th>Jabatan</th>
                                <th>Peran</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pegawaiList as $index => $pegawai)
                                <tr wire:key="pegawai-{{ $pegawai->id }}">
                                    <td class="text-center fw-bold">{{ $pegawaiList->firstItem() + $index }}</td>
                                    <td>{{ $pegawai->user->name }}</td>
                                    <td>{{ $pegawai->user->email }}</td>
                                    <td>{{ $pegawai->nip }}</td>
                                    <td>
                                        {{-- Tampilkan multiple jabatan --}}
                                        @forelse ($pegawai->jabatans as $jabatan)
                                            <span class="badge bg-secondary me-1 mb-1">{{ $jabatan->nama_jabatan }}</span>
                                        @empty
                                            <span class="text-muted small">(-)</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @foreach ($pegawai->user->roles as $role)
                                            <span class="badge bg-info text-dark me-1 mb-1">{{ $role->label }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="edit({{ $pegawai->id }})" class="btn btn-sm btn-outline-primary border-0" title="Edit">
                                           <i class="bi bi-pencil-fill"></i> 
                                        </button>
                                        <button 
                                            wire:click="confirmDelete({{ $pegawai->user_id }})" 
                                            class="btn btn-sm btn-outline-danger border-0" title="Hapus">
                                            <i class="bi bi-trash-fill"></i> 
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted p-4">Tidak ada data pegawai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                 @if($pegawaiList->hasPages())
                 <div class="card-footer bg-white py-3 border-top-0">
                     {{ $pegawaiList->links() }}
                 </div>
                 @endif
            </div>
        </div>
    </div>

    <!-- Modal Form Pegawai -->
    <div class="modal fade @if($showModal) show d-block @endif" 
         id="pegawaiModal" tabindex="-1" aria-labelledby="pegawaiModalLabel" 
         aria-hidden="{{ !$showModal ? 'true' : 'false' }}" 
         style="@if($showModal) background-color: rgba(0,0,0,0.5); @endif">
         
        <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 600px;">
            <div class="modal-content">
                <form wire:submit="store">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pegawaiModalLabel">{{ $isEditMode ? 'Edit' : 'Tambah' }} Data Pegawai</h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror">
                                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP <span class="text-danger">*</span></label>
                                <input type="text" wire:model="nip" class="form-control @error('nip') is-invalid @enderror">
                                @error('nip') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                             <div class="col-md-6 mb-3">
                                <label class="form-label">Password @if(!$isEditMode)<span class="text-danger">*</span>@endif</label>
                                <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="{{ $isEditMode ? 'Kosongkan jika tidak diubah' : '' }}">
                                @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            {{-- ==== INPUT JABATAN (CHECKBOX) ==== --}}
                            <div class="col-md-7 mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                @error('selectedJabatans') <div class="d-block invalid-feedback mb-1">{{ $message }}</div> @enderror
                                @if ($errors->has('selectedJabatans.*')) <div class="d-block invalid-feedback mb-1">ID Jabatan tidak valid.</div> @endif
                                
                                {{-- Kita buat sedikit lebih tinggi agar nyaman --}}
                                <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto; background-color: var(--bs-tertiary-bg) !important;">
                                    @if(!empty($groupedJabatans))
                                    <x-jabatan-checkbox-group 
                                        :groupedJabatans="$groupedJabatans" 
                                        :parent_id="null" 
                                        :level="0"
                                        :takenSingletons="$takenSingletonJabatans"
                                        :selectedJabatans="$selectedJabatans ?? []" /> 
                                    @else
                                        <p class="text-muted small mb-0">Memuat data jabatan...</p> 
                                    @endif
                                </div>
                            </div>
                            {{-- ====================================== --}}

                            {{-- ==== INPUT PERAN (CHECKBOX) ==== --}}
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Peran <span class="text-danger">*</span></label>
                                @error('selectedRoles') <div class="d-block invalid-feedback mb-2">{{ $message }}</div> @enderror

                                {{-- Kita bungkus dalam box juga agar seragam --}}
                                <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto; background-color: var(--bs-tertiary-bg) !important;">
                                    {{-- Kita ubah jadi flex-column agar rapi ke bawah --}}
                                    <div class="d-flex flex-column gap-2"> 
                                        @foreach ($roleList as $role)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" wire:model="selectedRoles" value="{{ $role->id }}" id="role-{{ $role->id }}">
                                                <label class="form-check-label" for="role-{{ $role->id }}">
                                                    {{ $role->label }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            {{-- ====================================== --}}

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                             {{ $isEditMode ? 'Update Data' : 'Simpan Data' }}
                             <div wire:loading wire:target="store" class="spinner-border spinner-border-sm ms-1" role="status"> {{-- Target 'store' --}}
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Script untuk konfirmasi delete --}}
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('confirmDelete', (event) => {
                Swal.fire({ /* ... sweetalert options ... */ }).then((result) => {
                    if (result.isConfirmed) { @this.call('destroy', event.id); }
                });
            });
        });
    </script>
    @endpush
</div>