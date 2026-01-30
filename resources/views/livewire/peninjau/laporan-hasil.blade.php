<div class="container-fluid p-4">
    
 <style>
    /* --- VARIABLES & UTILS --- */
    .text-gold { color: #c38e44 !important; }
    .bg-gold { background-color: #c38e44 !important; color: white; }
    
    .btn-outline-gold {
        color: #c38e44;
        border: 1px solid #c38e44;
        background: transparent;
        transition: all 0.3s;
    }
    .btn-outline-gold:hover {
        background-color: #c38e44;
        color: white;
        box-shadow: 0 5px 15px rgba(195, 142, 68, 0.2);
    }

    /* --- CARD STYLE --- */
    .card-pro {
        border: 1px solid #eee;
        border-left: 5px solid #c38e44;
        border-radius: 12px;
        background: #fff;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-pro:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    .card-locked {
        border-left: 5px solid #dcdcdc;
        background: #fcfcfc;
        opacity: 0.85;
    }

    /* --- BADGE SEMESTER (DEFAULT LIGHT MODE) --- */
    .badge-semester {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 8px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
    }
    
    /* Warna Ganjil (Light Mode) - Kuning/Oranye */
    .badge-ganjil {
        background-color: #fff8e1; 
        color: #f57f17;
        border: 1px solid #ffe0b2;
    }

    /* Warna Genap (Light Mode) - Biru */
    .badge-genap {
        background-color: #e3f2fd;
        color: #1565c0;
        border: 1px solid #bbdefb;
    }

    /* ========================================= */
    /* PERBAIKAN DARK MODE (MASUKKAN INI)      */
    /* ========================================= */
    [data-bs-theme="dark"] .card-pro {
        background-color: #212529 !important; /* Kartu jadi gelap */
        border-color: #373b3e !important;
        border-left-color: #c38e44 !important; /* Tetap emas di kiri */
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }
    
    [data-bs-theme="dark"] .card-locked {
        background-color: #2c3034 !important;
        border-left-color: #495057 !important;
        opacity: 0.7;
    }

    [data-bs-theme="dark"] .text-dark {
        color: #f8f9fa !important; /* Text hitam jadi putih */
    }

    /* FIX BADGE GANJIL DI DARK MODE */
    [data-bs-theme="dark"] .badge-ganjil {
        background-color: rgba(245, 127, 23, 0.15) !important; /* Background Transparan Oranye */
        color: #ffb74d !important; /* Teks Oranye Terang */
        border-color: rgba(255, 183, 77, 0.3) !important;
    }

    /* FIX BADGE GENAP DI DARK MODE */
    [data-bs-theme="dark"] .badge-genap {
        background-color: rgba(21, 101, 192, 0.15) !important; /* Background Transparan Biru */
        color: #64b5f6 !important; /* Teks Biru Terang */
        border-color: rgba(100, 181, 246, 0.3) !important;
    }
    
    [data-bs-theme="dark"] .bg-white {
        background-color: #212529 !important;
        border-color: #373b3e !important;
        color: #e0e0e0 !important;
    }
</style>

    {{-- HEADER SECTION --}}
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1">
                <i class="bi bi-archive me-2 text-gold"></i>Arsip Laporan
            </h3>
            <p class="text-muted mb-0">Rekapitulasi hasil penilaian kinerja pegawai.</p>
        </div>
        
        {{-- BAGIAN KANAN: TOTAL DATA & LEGENDA --}}
        <div class="d-flex align-items-center gap-4">
            
            {{-- 1. Badge Total Data (Ditambahkan Disini) --}}
            <span class="badge bg-white text-secondary border px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-database me-1"></i> Total Laporan: {{ $sikluses->count() }}
            </span>

            {{-- 2. Legenda Status --}}
            <div class="d-none d-md-flex gap-3 border-start ps-3">
                <div class="d-flex align-items-center">
                    <span class="d-inline-block rounded-circle bg-success me-2" style="width: 10px; height: 10px;"></span>
                    <small class="text-muted">Selesai</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block rounded-circle bg-warning me-2" style="width: 10px; height: 10px;"></span>
                    <small class="text-muted">Berjalan</small>
                </div>
            </div>
        </div>
    </div>

    {{-- GRID KARTU --}}
    <div class="row g-4">
        @forelse ($sikluses as $siklus)
            
            @php
                $session = $siklus->penilaianSession;
                $batasWaktu = \Carbon\Carbon::parse($session->batas_waktu);
                // Cek Akses: Selesai atau Expired
                $isAccessible = ($session->status == 'Closed') || (now() > $batasWaktu);
                $tglSelesai = $batasWaktu->translatedFormat('d F Y, H:i');
            @endphp

            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 card-pro {{ $isAccessible ? '' : 'card-locked' }}">
                    <div class="card-body p-4 d-flex flex-column">
                        
                        {{-- Top Section: Tahun & Semester --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Tahun Ajaran</small>
                                <h4 class="fw-bold text-dark mb-0 mt-1">{{ $siklus->tahun_ajaran }}</h4>
                            </div>
                            {{-- Badge Semester (Ganti Ikon Cuaca) --}}
                            @if($siklus->semester == 'Ganjil')
                                <span class="badge-semester badge-ganjil">
                                    <i class="bi bi-1-circle me-1"></i> Ganjil
                                </span>
                            @else
                                <span class="badge-semester badge-genap">
                                    <i class="bi bi-2-circle me-1"></i> Genap
                                </span>
                            @endif
                        </div>

                        {{-- Middle Section: Info Status & Tanggal --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                @if($isAccessible)
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    <span class="text-dark fw-medium small">Status: Final / Selesai</span>
                                @else
                                    <i class="bi bi-hourglass-split text-warning me-2"></i>
                                    <span class="text-dark fw-medium small">Status: Sedang Berjalan</span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bi bi-calendar-event me-2"></i>
                                <span>{{ $batasWaktu->format('d M Y') }}</span>
                            </div>
                        </div>

                        {{-- Bottom Section: Tombol Aksi --}}
                        <div class="mt-auto">
                            @if($isAccessible)
                                <a href="{{ route('peninjau.laporan.ranking', $siklus->id) }}" 
                                   class="btn btn-outline-gold w-100 rounded-3 py-2 fw-semibold">
                                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Lihat Data
                                </a>
                            @else
                                <button type="button" 
                                        class="btn btn-light text-secondary w-100 rounded-3 py-2 fw-semibold border"
                                        onclick="Swal.fire({
                                            icon: 'info',
                                            title: 'Laporan Belum Siap',
                                            text: 'Periode penilaian ini masih berlangsung. Laporan akan tersedia setelah {{ $tglSelesai }} WIB.',
                                            confirmButtonColor: '#c38e44'
                                        })">
                                    <i class="bi bi-lock-fill me-2"></i>Terkunci
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            </div>

        @empty
            <div class="col-12">
                <div class="d-flex flex-column align-items-center justify-content-center py-5 bg-white rounded-4 border border-dashed text-center">
                    <div class="bg-light rounded-circle p-4 mb-3">
                        <i class="bi bi-folder-x text-muted display-4"></i>
                    </div>
                    <h5 class="fw-bold text-secondary">Tidak Ada Data</h5>
                    <p class="text-muted mb-0">Belum ada siklus penilaian yang tercatat di sistem.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>