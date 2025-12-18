<div class="container-fluid p-4">
    
    {{-- CSS KHUSUS (PREMIUM LOOK) --}}
    <style>
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

        /* Card Style Professional */
        .card-pro {
            border: 1px solid #eee;
            border-left: 5px solid #c38e44; /* Aksen Emas di Kiri */
            border-radius: 12px;
            background: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-pro:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        /* Card Locked (Terkunci) */
        .card-locked {
            border-left: 5px solid #dcdcdc; /* Aksen Abu */
            background: #fcfcfc;
            opacity: 0.85;
        }
        .card-locked:hover {
            transform: none;
            box-shadow: none;
        }

        /* Badge Semester Custom */
        .badge-semester {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 6px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .badge-ganjil {
            background-color: #fff8e1;
            color: #f57f17;
            border: 1px solid #ffe0b2;
        }
        .badge-genap {
            background-color: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
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