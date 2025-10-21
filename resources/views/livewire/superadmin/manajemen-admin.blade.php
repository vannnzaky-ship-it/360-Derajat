<div>
    <div class="container-fluid p-4">
        <h1 class="h3 mb-3">Manajemen Akses Administrator</h1>
        <p class="text-muted">Halaman ini digunakan untuk menunjuk atau mencabut hak akses Administrator dari seorang pengguna.</p>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow border-0 rounded-3">
            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                <input wire:model.live.debounce.300ms="search" type="text" class="form-control w-auto" placeholder="Cari nama atau email...">
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nama Pengguna</th>
                                <th>Email</th>
                                <th>Peran Saat Ini</th>
                                <th>Status Admin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr wire:key="{{ $user->id }}">
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach ($user->roles as $role)
                                            {{-- Jangan tampilkan role 'karyawan' jika ada role lain --}}
                                            @if($user->roles->count() == 1 || $role->name != 'karyawan')
                                                <span class="badge bg-secondary me-1">{{ $role->label }}</span>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($user->hasRole('admin'))
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button 
                                            wire:click="toggleAdmin({{ $user->id }})" 
                                            class="btn btn-sm {{ $user->hasRole('admin') ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                            {{ $user->hasRole('admin') ? 'Cabut Akses' : 'Jadikan Admin' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-3 text-muted">Tidak ada pengguna lain ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>