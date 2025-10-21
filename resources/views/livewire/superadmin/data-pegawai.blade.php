<div>
    {{-- Layout Admin Anda akan ada di sini --}}
    <div class="container py-4">
        <h1 class="h3 mb-3">Manajemen Data Pegawai</h1>

        @if (session()->has('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <input wire:model.live.debounce.300ms="search" type="text" class="form-control w-25" placeholder="Cari nama, email, NIP...">
                <button wire:click="create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Pegawai
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>NIP</th>
                                <th>Jabatan</th>
                                <th>Peran (Roles)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pegawaiList as $pegawai)
                                <tr wire:key="{{ $pegawai->id }}">
                                    <td>{{ $pegawai->user->name }}</td>
                                    <td>{{ $pegawai->user->email }}</td>
                                    <td>{{ $pegawai->nip }}</td>
                                    <td>{{ $pegawai->jabatan->nama_jabatan }}</td>
                                    <td>
                                        @foreach ($pegawai->user->roles as $role)
                                            <span class="badge bg-secondary">{{ $role->label }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <td>
                                            {{-- Tombol akan selalu tampil karena Superadmin & diri sendiri sudah disembunyikan --}}
                                            <button wire:click="edit({{ $pegawai->id }})" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-fill"></i> Edit
                                            </button>
                                            <button 
                                                wire:click="$dispatch('confirmDelete', { id: {{ $pegawai->user_id }} })" 
                                                class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash-fill"></i> Hapus
                                            </button>
                                        </td>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $pegawaiList->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade @if($showModal) show d-block @endif" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit="store">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEditMode ? 'Edit' : 'Tambah' }} Data Pegawai</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror">
                                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP</label>
                                <input type="text" wire:model="nip" class="form-control @error('nip') is-invalid @enderror">
                                @error('nip') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan</label>
                                <select wire:model="jabatan_id" class="form-select @error('jabatan_id') is-invalid @enderror">
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach ($jabatanList as $jabatan)
                                        <option value="{{ $jabatan->id }}">{{ $jabatan->nama_jabatan }}</option>
                                    @endforeach
                                </select>
                                @error('jabatan_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="{{ $isEditMode ? 'Kosongkan jika tidak ingin diubah' : '' }}">
                            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Peran (Bisa pilih lebih dari satu)</label>
                            @error('selectedRoles') <div class="d-block invalid-feedback mb-2">{{ $message }}</div> @enderror
                            <div class="d-flex gap-3">
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Script untuk konfirmasi delete (Contoh pakai SweetAlert) --}}
    {{-- Anda perlu install SweetAlert2 untuk ini --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('confirmDelete', (event) => {
                Swal.fire({
                    title: 'Anda yakin?',
                    text: "Data pegawai (dan akun loginnya) akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('destroy', event.id);
                    }
                });
            });
        });
    </script>
</div>