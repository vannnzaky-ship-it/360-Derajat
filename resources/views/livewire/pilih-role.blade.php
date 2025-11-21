<div class="min-vh-100 d-flex justify-content-center align-items-center" style="background-color: #f4f6f9;">
    
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
            overflow: hidden;
            position: relative;
        }

        /* Aksen Atas Gold */
        .role-card-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: var(--primary-gold);
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
            margin: -60px auto 20px; /* Efek floating keluar kartu */
            border: 5px solid #f4f6f9; /* Border warna background body agar terlihat terpotong */
            box-shadow: 0 10px 20px rgba(195, 142, 68, 0.3);
        }

        .page-content {
            flex-grow: 1;
            position: relative; /* Membuatnya menjadi 'parent' untuk posisi logo */
            z-index: 1; /* Memastikan konten tetap di atas logo */
        }

        .page-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('/images/logo-polkam.png');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 55%; /* Atur ukuran logo, misal 50% dari area konten */
            opacity: 0.05; /* Opacity 10% */
            z-index: -1; /* Meletakkan logo di belakang konten */
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                
                {{-- Kartu Pilih Role --}}
                <div class="card role-card-container pt-4 px-4 pb-4 mt-5">
                    
                    {{-- Icon Utama Floating --}}
                    <div class="main-icon">
                        <i class="bi bi-person-bounding-box"></i>
                    </div>

                    <div class="card-body text-center pt-0">
                        <h4 class="fw-bold text-dark mb-2">Selamat Datang!</h4>
                        <p class="text-muted small mb-4 px-3">
                            Akun Anda terhubung dengan beberapa hak akses. <br>
                            Silakan pilih portal untuk melanjutkan.
                        </p>

                        {{-- Alert Error (Jika ada) --}}
                        @if (session('error'))
                            <div class="alert alert-danger border-0 shadow-sm mb-4" style="font-size: 0.9rem;">
                                <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                            </div>
                        @endif

                        {{-- Daftar Tombol Role --}}
                        <div class="d-grid gap-2 text-start">
                            @foreach ($roles as $role)
                                <button wire:click="selectRole('{{ $role->name }}')" class="btn-role-select group">
                                    <div class="d-flex align-items-center">
                                        {{-- Icon kecil berdasarkan nama role --}}
                                        <div class="role-icon-wrapper">
                                            @if($role->name == 'superadmin') <i class="bi bi-shield-lock-fill"></i>
                                            @elseif($role->name == 'admin') <i class="bi bi-laptop"></i>
                                            @elseif($role->name == 'peninjau') <i class="bi bi-eye-fill"></i>
                                            @elseif($role->name == 'karyawan') <i class="bi bi-person-badge-fill"></i>
                                            @else <i class="bi bi-grid-fill"></i>
                                            @endif
                                        </div>
                                        
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold" style="font-size: 1rem;">{{ $role->label }}</span>
                                            <span class="text-muted" style="font-size: 0.75rem;">Masuk sebagai {{ strtolower($role->label) }}</span>
                                        </div>
                                    </div>
                                    
                                    <i class="bi bi-arrow-right role-arrow"></i>
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
                
                {{-- Copyright Footer --}}
                {{-- <div class="text-center mt-4 text-muted small opacity-50">
                    &copy; {{ date('Y') }} Penilaian 360 Derajat
                </div> --}}

            </div>
        </div>
    </div>
</div>