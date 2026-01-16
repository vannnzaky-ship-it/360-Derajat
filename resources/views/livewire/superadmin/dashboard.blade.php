<div>
    {{-- CSS CUSTOM: EFEK FLOATING/MUNCUL --}}
    <style>
        /* Style Global */
        body { background-color: #f8f9fa; }

        /* Class khusus untuk Card yang "Muncul" */
        .card-floating {
            border: none;
            border-radius: 1rem; /* Sudut membulat */
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); /* Bayangan awal halus */
            transition: all 0.3s ease; /* Animasi halus */
            position: relative;
            overflow: hidden;
        }

        /* Efek saat mouse diarahkan (Hover) */
        .card-floating:hover {
            transform: translateY(-5px); /* Naik ke atas 5px */
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); /* Bayangan makin tebal */
        }

        /* Khusus Card Kalender (Gradient) */
        .card-gradient {
            background: linear-gradient(135deg, #C38E44 0%, #a07232 100%);
            color: white;
        }
        
        /* Ikon Background Besar */
        .bg-icon-overlay {
            position: absolute;
            right: -20px;
            bottom: -20px;
            font-size: 8rem;
            opacity: 0.05;
            transform: rotate(-15deg);
            pointer-events: none;
        }
    </style>

    <div class="container-fluid p-4">
        
        {{-- SECTION 1: Welcome Header --}}
        <div class="mb-4 text-center text-md-start">
            <h2 class="fw-bold text-dark">
                Selamat Datang, <span style="color: #C38E44;">{{ $user->name }}</span>!
            </h2>
            <p class="text-secondary">Selamat beraktivitas di Panel Super Administrator.</p>
        </div>

        {{-- SECTION 2: Info Box (Status Sistem) --}}
        {{-- Menggunakan class 'card-floating' agar ikut muncul saat di hover --}}
        <div class="card card-floating mb-4" style="border-left: 5px solid #C38E44 !important;">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3">
                    <div class="p-3 rounded-circle" style="background-color: #FFF4E5; color: #C38E44; flex-shrink: 0;">
                        <i class="bi bi-info-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">Status Sistem</h5>
                        <p class="mb-0 text-secondary small">
                            Semua sistem berjalan normal. Anda memiliki akses penuh untuk mengelola Admin dan Pegawai.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 3: Statistik Cards --}}
        <div class="row g-4">
            
            {{-- Card 1: Total Pegawai --}}
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card card-floating h-100">
                    <div class="card-body p-4 position-relative">
                        {{-- Header Card --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-uppercase small fw-bold text-secondary">Total Pegawai</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded">Terdaftar</span>
                        </div>
                        
                        {{-- Isi Angka --}}
                        <div class="d-flex align-items-center">
                            <h2 class="fw-bold mb-0 me-3 display-5">{{ $total_pegawai }}</h2>
                            {{-- Ikon Kecil --}}
                            <div class="ms-auto text-primary opacity-25 d-block d-xl-none">
                                <i class="bi bi-people-fill fs-1"></i>
                            </div>
                        </div>

                        {{-- Footer Link --}}
                        <div class="mt-4 pt-3 border-top position-relative" style="z-index: 2;">
                            <a href="{{ route('superadmin.data-pegawai') }}" class="text-decoration-none small fw-bold text-primary d-flex align-items-center">
                                Lihat Detail <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>

                        {{-- Ikon Background Besar (Hiasan) --}}
                        <i class="bi bi-people-fill bg-icon-overlay text-primary"></i>
                    </div>
                </div>
            </div>
            
            {{-- Card 2: Administrator --}}
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card card-floating h-100">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-uppercase small fw-bold text-secondary">Administrator</span>
                            <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 rounded" style="color: #C38E44 !important; background-color: #FFF4E5 !important;">Aktif</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <h2 class="fw-bold mb-0 me-3 display-5">{{ $total_admin }}</h2>
                             <div class="ms-auto opacity-25 d-block d-xl-none" style="color: #C38E44;">
                                <i class="bi bi-shield-lock-fill fs-1"></i>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-top position-relative" style="z-index: 2;">
                            <a href="{{ route('superadmin.manajemen-admin') }}" class="text-decoration-none small fw-bold d-flex align-items-center" style="color: #C38E44;">
                                Kelola Akses <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>

                        {{-- Ikon Background Besar --}}
                        <i class="bi bi-shield-lock-fill bg-icon-overlay" style="color: #C38E44;"></i>
                    </div>
                </div>
            </div>

            {{-- Card 3: Kalender --}}
            <div class="col-12 col-xl-4">
                <div class="card card-floating card-gradient h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center position-relative">
                        <i class="bi bi-calendar-check mb-3 fs-1 text-white-50"></i>
                        <h4 class="fw-bold">{{ now()->translatedFormat('l, d F Y') }}</h4>
                        <p class="mb-0 opacity-75 small">Sistem Penilaian 360 Derajat</p>
                        
                        {{-- Ikon Background Besar --}}
                         <i class="bi bi-calendar2-week bg-icon-overlay text-white opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>