<div class="min-vh-100 d-flex justify-content-center align-items-center py-5" style="background-color: #f4f6f9;">
    
    {{-- Custom CSS untuk halaman ini --}}
    <style>
        :root {
            --primary-gold: #c38e44;
            --primary-gold-soft: rgba(195, 142, 68, 0.1);
        }

        /* Kartu Utama */
        .role-card-container {
            border: none;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            background: white;
            /* PENTING: Ubah hidden jadi visible agar icon tidak kepotong */
            overflow: visible !important; 
            position: relative;
            margin-top: 40px; /* Tambah jarak atas agar icon punya ruang */
        }

        /* Aksen Atas Gold - Kita ganti cara buatnya agar tidak merusak overflow */
        .card-top-accent {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: var(--primary-gold);
            border-radius: 24px 24px 0 0;
        }

        /* Tombol Peran (Role Button) */
        .btn-role-select {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 16px 20px;
            background-color: #fff;
            border: 2px solid #f1f1f1;
            border-radius: 12px;
            color: #444;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 12px;
            text-align: left;
            position: relative;
        }

        /* Efek Hover Keren */
        .btn-role-select:hover {
            border-color: var(--primary-gold);
            background-color: var(--primary-gold-soft);
            color: var(--primary-gold);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(195, 142, 68, 0.15);
        }

        /* Icon Panah di kanan tombol */
        .role-arrow {
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
            color: var(--primary-gold);
        }

        .btn-role-select:hover .role-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        /* Icon Role di kiri */
        .role-icon-wrapper {
            width: 40px;
            height: 40px;
            min-width: 40px; /* Menjaga agar icon tidak gepeng di HP */
            border-radius: 10px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .btn-role-select:hover .role-icon-wrapper {
            background-color: var(--primary-gold);
            color: white;
        }

        /* Header Icon */
        .main-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #c38e44 0%, #eebb77 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            /* Posisi Absolute agar pasti di tengah garis kartu */
            position: absolute; 
            top: -40px; 
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid #f4f6f9; 
            box-shadow: 0 10px 20px rgba(195, 142, 68, 0.3);
            z-index: 10;
        }

        /* Responsive Text untuk HP */
        @media (max-width: 576px) {
            .btn-role-select {
                padding: 12px 15px;
            }
            h4 { font-size: 1.25rem; }
            p { font-size: 0.85rem; }
        }

        .page-bg-logo {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: url('/images/logo-polkam.png');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 55%;
            opacity: 0.05;
            z-index: 0;
            pointer-events: none;
        }
    </style>

    {{-- Background Logo --}}
    <div class="page-bg-logo"></div>

    <div class="container" style="position: relative; z-index: 2;">
        <div class="row justify-content-center">
            {{-- Ubah col-md-6 jadi lebih fleksibel untuk HP (col-11) --}}
            <div class="col-11 col-sm-8 col-md-6 col-lg-5">
                
                {{-- Kartu Pilih Role --}}
                <div class="card role-card-container px-3 pb-4">
                    
                    {{-- Garis Aksen Gold (Pengganti ::before) --}}
                    <div class="card-top-accent"></div>

                    {{-- Icon Utama Floating --}}
                    <div class="main-icon">
                        <i class="bi bi-person-bounding-box"></i>
                    </div>

                    <div class="card-body text-center pt-5 mt-2">
                        <h4 class="fw-bold text-dark mb-2">Selamat Datang!</h4>
                        <p class="text-muted small mb-4 px-2">
                            Akun Anda terhubung dengan beberapa hak akses. <br class="d-none d-sm-block">
                            Silakan pilih portal untuk melanjutkan.
                        </p>

                        {{-- Alert Error (Jika ada) --}}
                        @if (session('error'))
                            <div class="alert alert-danger border-0 shadow-sm mb-4 text-start" style="font-size: 0.9rem;">
                                <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                            </div>
                        @endif

                        {{-- Daftar Tombol Role --}}
                        <div class="d-grid gap-2 text-start">
                            @foreach ($roles as $role)
                                <button wire:click="selectRole('{{ $role->name }}')" class="btn-role-select group">
                                    <div class="d-flex align-items-center w-100">
                                        {{-- Icon kecil berdasarkan nama role --}}
                                        <div class="role-icon-wrapper flex-shrink-0">
                                            @if($role->name == 'superadmin') <i class="bi bi-shield-lock-fill"></i>
                                            @elseif($role->name == 'admin') <i class="bi bi-laptop"></i>
                                            @elseif($role->name == 'peninjau') <i class="bi bi-eye-fill"></i>
                                            @elseif($role->name == 'karyawan') <i class="bi bi-person-badge-fill"></i>
                                            @else <i class="bi bi-grid-fill"></i>
                                            @endif
                                        </div>
                                        
                                        <div class="d-flex flex-column flex-grow-1">
                                            <span class="fw-bold text-truncate" style="font-size: 1rem;">{{ $role->label }}</span>
                                            <span class="text-muted text-truncate" style="font-size: 0.75rem;">Masuk sebagai {{ strtolower($role->label) }}</span>
                                        </div>
                                        
                                        <i class="bi bi-arrow-right role-arrow ms-2"></i>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        {{-- Footer Logout --}}
                        <div class="mt-4 pt-3 border-top">
                            <a href="/logout" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="text-muted text-decoration-none small d-inline-flex align-items-center hover-gold">
                                <i class="bi bi-box-arrow-left me-2"></i> Bukan akun Anda? Keluar
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>