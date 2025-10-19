<div>
    {{-- Layout Admin Anda --}}
    <div class="container py-4">
        <h1 class="h3 mb-3">Manajemen Akses Superadmin</h1>
        <p class="text-muted">Halaman ini digunakan untuk menunjuk atau mencabut hak akses Superadmin dari seorang pengguna.</p>

        @if (session()->has('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header">
                <input wire:model.live.debounce.300ms="search" type="text" class="form-control w-25" placeholder="Cari nama atau email...">
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Pengguna</th>
                                <th>Email</th>
                                <th>Peran Saat Ini</th>
                                <th>Status Superadmin</th>
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
                                            <span class="badge bg-secondary">{{ $role->label }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($user->hasRole('superadmin'))
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button 
                                            wire:click="toggleSuperadmin({{ $user->id }})" 
                                            class="btn btn-sm {{ $user->hasRole('superadmin') ? 'btn-danger' : 'btn-success' }}">
                                            {{ $user->hasRole('superadmin') ? 'Cabut Akses' : 'Jadikan Superadmin' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data.</td>
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