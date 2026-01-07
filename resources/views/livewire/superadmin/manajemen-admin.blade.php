<div>
    <div class="container-fluid p-4">
        
        {{-- Header Judul dengan Ikon --}}
        <div class="mb-4">
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="bi bi-shield-lock me-2"style="color: #C38E44;"></i>Manajemen Akses Administrator
            </h1>
            <p class="text-secondary mb-0">Halaman ini digunakan untuk menunjuk atau mencabut hak akses Administrator dari seorang pengguna.</p>
        </div>

        {{-- Alert Message --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Card Utama --}}
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            
            {{-- Bagian Pencarian --}}
            {{-- PERBAIKAN DI SINI: Saya hapus 'pb-0' dan ubah jadi p-4 agar jaraknya rata --}}
            <div class="card-header bg-white border-bottom-0 p-4">
                <div class="row">
                    {{-- Saya buat col-12 agar dia full width dan terlihat rapi di tengah area putih --}}
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input wire:model.live.debounce.300ms="search" type="text" class="form-control bg-light border-start-0 rounded-end-pill py-2" placeholder="Cari nama atau email...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0"> 
                {{-- PERBAIKAN: Padding dipindah ke dalam agar tabel mepet rapi tapi header punya jarak --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="py-3 ps-4 text-uppercase small text-secondary fw-bold">Nama Pengguna</th>
                                <th class="py-3 text-uppercase small text-secondary fw-bold">Email</th>
                                <th class="py-3 text-uppercase small text-secondary fw-bold">Peran Saat Ini</th>
                                <th class="py-3 text-uppercase small text-secondary fw-bold">Status Admin</th>
                                <th class="py-3 text-end pe-4 text-uppercase small text-secondary fw-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr wire:key="{{ $user->id }}" class="border-bottom-0">
                                    <td class="ps-4 py-3 fw-semibold text-dark">{{ $user->name }}</td>
                                    <td class="py-3 text-muted">{{ $user->email }}</td>
                                    <td class="py-3">
                                        @foreach ($user->roles as $role)
                                            @if($user->roles->count() == 1 || $role->name != 'karyawan')
                                                <span class="badge bg-secondary rounded-pill fw-normal px-3 py-2 me-1">{{ $role->label }}</span>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="py-3">
                                        @if ($user->hasRole('admin'))
                                            <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-2">
                                                <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger rounded-pill px-3 py-2">
                                                <i class="bi bi-x-circle-fill me-1"></i> Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-end pe-4">
                                        <button 
                                            wire:click="toggleAdmin({{ $user->id }})" 
                                            class="btn btn-sm {{ $user->hasRole('admin') ? 'btn-outline-danger' : 'btn-outline-success' }} rounded-pill px-3 fw-medium">
                                            {{ $user->hasRole('admin') ? 'Cabut Akses' : 'Jadikan Admin' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-inbox fs-1 mb-2 opacity-50"></i>
                                            <p class="mb-0">Tidak ada pengguna lain ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Footer Pagination --}}
            <div class="card-footer bg-white border-top-0 p-4 pt-3">
                 {{ $users->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>
</div>