<div>
    {{-- CSS CUSTOM: RESPONSIVE TABLE (CARD STACK ON MOBILE) --}}
    <style>
        /* Default Table Style (Desktop) */
        .table-floating { border-collapse: separate; border-spacing: 0 15px; }
        .row-floating { background-color: #fff; box-shadow: 0 .125rem .25rem rgba(0,0,0,.075); transition: transform 0.2s; }
        .row-floating:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,0.1); }
        
        .row-floating td:first-child { border-top-left-radius: 1rem; border-bottom-left-radius: 1rem; }
        .row-floating td:last-child { border-top-right-radius: 1rem; border-bottom-right-radius: 1rem; }

        /* Mobile Responsive Styles */
        @media (max-width: 767.98px) {
            .table-responsive thead { display: none; }
            .table-responsive table, .table-responsive tbody, .table-responsive tr, .table-responsive td { display: block; width: 100%; }

            .table-responsive tr.row-floating {
                margin-bottom: 1rem; border-radius: 1rem;
                box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #dee2e6;
            }

            .table-responsive td {
                text-align: left !important; padding: 10px 20px;
                border-bottom: 1px solid #f0f0f0; display: flex;
                justify-content: space-between; align-items: center;
                flex-wrap: wrap; gap: 10px;
            }
            .table-responsive td:last-child { border-bottom: none; }
            .row-floating td:first-child { border-radius: 1rem 1rem 0 0; background-color: #fcfcfc; }
            .row-floating td:last-child { border-radius: 0 0 1rem 1rem; padding-top: 15px; padding-bottom: 15px; }
            .td-name { justify-content: flex-start !important; }
            .td-aksi { justify-content: flex-end !important; }
        }
    </style>

    <div class="container-fluid p-4" style="background-color: #f8f9fa; min-height: 100vh;">
        
        {{-- Header Judul --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
            <div class="text-center text-md-start">
                <h1 class="h3 fw-bold text-dark mb-1">
                    <i class="bi bi-shield-lock me-2" style="color: #C38E44;"></i>Akses Administrator
                </h1>
                <p class="text-secondary small mb-0">Kelola hak akses Admin dengan mudah dan aman.</p>
            </div>

            {{-- Kolom Pencarian --}}
            <div class="col-12 col-md-5 col-lg-4">
                <div class="input-group shadow-sm rounded-pill bg-white">
                    <span class="input-group-text bg-transparent border-0 ps-3">
                        <i class="bi bi-search text-secondary"></i>
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control bg-transparent border-0 py-2" placeholder="Cari pengguna...">
                </div>
            </div>
        </div>

        {{-- Alert Message --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle p-1 me-2 d-flex justify-content-center align-items-center" style="width: 24px; height: 24px;">
                        <i class="bi bi-check small"></i>
                    </div>
                    <div>{{ session('message') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Tabel Floating Rows --}}
        <div class="table-responsive">
            <table class="table table-borderless align-middle table-floating">
                <thead>
                    <tr class="text-secondary small text-uppercase">
                        <th class="fw-bold text-center" style="width: 25%;">Nama Pengguna</th>
                        <th class="fw-bold text-center" style="width: 20%;">Email</th>
                        <th class="fw-bold text-center" style="width: 15%;">Peran</th>
                        <th class="fw-bold text-center" style="width: 15%;">Status Admin</th>
                        <th class="fw-bold text-center" style="width: 25%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr wire:key="{{ $user->id }}" class="row-floating">
                            
                            {{-- 1. Kolom Nama --}}
                            <td class="py-3 text-center td-name">
                                <div class="d-flex flex-column flex-md-column flex-row align-items-center justify-content-center gap-3 gap-md-0 w-100">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white mb-md-2" 
                                         style="width: 40px; height: 40px; background-color: #C38E44; flex-shrink: 0;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="fw-bold text-dark text-center">{{ $user->name }}</span>
                                </div>
                            </td>

                            {{-- 2. Kolom Email --}}
                            <td class="py-3 text-center text-muted fw-medium">
                                <span class="d-md-none fw-bold small text-secondary me-2">Email:</span>
                                {{ $user->email }}
                            </td>

                            {{-- 3. Kolom Peran (DIPERBAIKI: SEMUA ROLE MUNCUL) --}}
                            <td class="py-3 text-center">
                                <span class="d-md-none fw-bold small text-secondary me-2">Peran:</span>
                                <div class="d-inline-block">
                                {{-- Loop tanpa filter IF --}}
                                @foreach ($user->roles as $role)
                                    <span class="badge rounded-pill fw-normal px-3 py-2 border m-1" 
                                          style="background-color: #f8f9fa; color: #6c757d; border-color: #dee2e6;">
                                        {{ $role->label }}
                                    </span>
                                @endforeach
                                </div>
                            </td>

                            {{-- 4. Kolom Status --}}
                            <td class="py-3 text-center">
                                <span class="d-md-none fw-bold small text-secondary me-2">Status:</span>
                                @if ($user->hasRole('admin'))
                                    <div class="d-inline-flex align-items-center text-success bg-success-subtle px-3 py-2 rounded-pill fw-bold" style="font-size: 0.85rem;">
                                        <span class="spinner-grow spinner-grow-sm me-2" role="status" aria-hidden="true"></span>
                                        Aktif
                                    </div>
                                @else
                                    <div class="d-inline-flex align-items-center text-secondary bg-light px-3 py-2 rounded-pill border fw-medium" style="font-size: 0.85rem;">
                                        <i class="bi bi-dash-circle me-2"></i> Non-Aktif
                                    </div>
                                @endif
                            </td>

                            {{-- 5. Kolom Aksi --}}
                            <td class="py-3 text-center td-aksi">
                                <button 
                                    wire:click="toggleAdmin({{ $user->id }})" 
                                    class="btn btn-sm rounded-pill px-4 py-2 fw-bold transition-all {{ $user->hasRole('admin') ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                    style="border-width: 2px;">
                                    @if($user->hasRole('admin'))
                                        <i class="bi bi-shield-x me-1"></i> Cabut
                                    @else
                                        <i class="bi bi-shield-check me-1"></i> Jadikan
                                    @endif
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted opacity-50">
                                    <i class="bi bi-search fs-1"></i>
                                    <p class="mt-2">Tidak ada pengguna yang cocok.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Footer Pagination --}}
        <div class="d-flex justify-content-center mt-3">
             {{ $users->links('pagination::bootstrap-5') }}
        </div>

    </div>
</div>