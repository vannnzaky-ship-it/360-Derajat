<style>
    :root {
        --theme-brown: #c59d5f;        /* Warna utama sesuai logo */
        --theme-brown-dark: #a68b53;   /* Warna saat tombol di-hover */
        --theme-bg-soft: #fbf8f3;      /* Background kartu yang lembut */
    }

    /* Override Warna Bootstrap Custom */
    .text-theme { color: var(--theme-brown) !important; }
    .bg-theme { background-color: var(--theme-brown) !important; }
    .bg-theme-success { background-color: #198754 !important; } /* Hijau untuk 100% */
    
    .btn-theme {
        background-color: var(--theme-brown);
        color: #fff;
        border: none;
        box-shadow: 0 4px 6px rgba(197, 157, 95, 0.2);
        transition: 0.3s;
    }
    .btn-theme:hover {
        background-color: var(--theme-brown-dark);
        color: #fff;
        transform: translateY(-2px);
    }

    .btn-outline-theme {
        color: var(--theme-brown);
        border: 2px solid var(--theme-brown);
        background: transparent;
        transition: 0.3s;
    }
    .btn-outline-theme:hover {
        background-color: var(--theme-brown);
        color: #fff;
    }

    /* Styling Kartu agar lebih modern */
    .card-modern {
        border: none;
        border-radius: 15px;
        background: #fff;
        transition: all 0.3s ease;
    }
    
    /* Efek Hover pada Kartu Menu */
    .card-menu:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }

    /* Styling Progress Bar */
    .progress-modern {
        background-color: #e9ecef;
        border-radius: 20px;
        overflow: hidden;
    }
</style>

<div class="container-fluid p-4">
    
    {{-- 1. HEADER / WELCOME --}}
    <div class="mb-4">
        <h1 class="h3 fw-bold text-dark">Selamat Datang, <span class="text-theme">{{ $namaUser }}</span>!</h1>
        <p class="text-muted">Selamat beraktivitas di Sistem Penilaian 360.</p>
    </div>

    {{-- 2. PROGRESS BAR SECTION (DENGAN LOGIKA SESI) --}}
    <div class="card card-modern shadow-sm mb-5">
        <div class="card-body p-4">
            
            @if(!$adaSesi)
                {{-- TAMPILAN JIKA TIDAK ADA SESI (Siklus Belum Dimulai) --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title fw-bold m-0 text-secondary">
                        <i class="bi bi-clock-history me-2"></i>Status Penilaian
                    </h5>
                    <span class="badge bg-secondary rounded-pill">Non-Aktif</span>
                </div>

                <p class="card-text text-muted mb-3">
                    Siklus penilaian untuk semester mendatang belum dimulai. Progress akan muncul saat periode dibuka.
                </p>

                <div class="progress progress-modern" style="height: 20px;">
                    <div class="progress-bar bg-secondary" role="progressbar" 
                         style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                         0%
                    </div>
                </div>

            @else
                {{-- TAMPILAN JIKA SESI AKTIF (Normal) --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title fw-bold m-0">Progress Pengisian Penilaian</h5>
                    
                    {{-- Badge Status (Berubah Hijau jika 100%) --}}
                    <span class="badge {{ $persentase == 100 ? 'bg-theme-success' : 'bg-theme' }} rounded-pill">
                        {{ $sudahSelesai }} / {{ $totalTugas }} Selesai
                    </span>
                </div>

                {{-- Text Keterangan Dinamis --}}
                <p class="card-text text-muted mb-3">
                    @if($totalTugas == 0)
                        Belum ada data penilaian yang masuk.
                    @elseif($persentase == 100)
                        Terima kasih! Anda telah menyelesaikan seluruh penilaian.
                    @else
                        Anda telah menyelesaikan {{ $sudahSelesai }} dari {{ $totalTugas }} formulir penilaian.
                    @endif
                </p>
                
                {{-- Progress Bar Dinamis --}}
                <div class="progress progress-modern" style="height: 20px;">
                    <div class="progress-bar {{ $persentase == 100 ? 'bg-theme-success' : 'bg-theme' }}" 
                         role="progressbar" 
                         style="width: {{ $persentase }}%;" 
                         aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100">
                         {{ $persentase }}%
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- 3. MENU KARTU (TAMPILAN TETAP SAMA SEPERTI PERMINTAAN) --}}
    <div class="d-flex flex-wrap justify-content-center gap-4 py-2">
        
        {{-- Kartu Mulai Menilai --}}
        <div class="card card-modern card-menu text-center shadow-sm h-100" style="width: 18rem;">
            <div class="card-body d-flex flex-column justify-content-center p-4">
                <div class="mb-3">
                    <i class="bi bi-ui-checks display-4 text-theme"></i>
                </div>
                <h5 class="card-title fw-bold mt-2">Mulai Menilai</h5>
                <p class="card-text text-muted small mb-4">Isi formulir penilaian untuk rekan, atasan, dan diri sendiri.</p>
                
                <a href="{{ url('karyawan/penilaian') }}" class="btn btn-theme rounded-pill w-100 mt-auto">
                    Mulai
                </a>
            </div>
        </div>

        {{-- Kartu Lihat Raport --}}
        <div class="card card-modern card-menu text-center shadow-sm h-100" style="width: 18rem;">
            <div class="card-body d-flex flex-column justify-content-center p-4">
                <div class="mb-3">
                    <i class="bi bi-clipboard-data display-4 text-theme"></i>
                </div>
                <h5 class="card-title fw-bold mt-2">Lihat Raport</h5>
                <p class="card-text text-muted small mb-4">Lihat hasil akhir penilaian kinerja Anda semester ini.</p>
                
                <a href="{{ url('karyawan/raport') }}" class="btn btn-outline-theme rounded-pill w-100 mt-auto">
                    Lihat
                </a>
            </div>
        </div>
    </div>
</div>