<div class="container-fluid p-4">

    {{-- STYLE CUSTOM: FORMULIR PENILAIAN + DARK MODE FIX LENGKAP --}}
    <style>
        /* --- Default Light Mode Colors --- */
        .text-gold { color: #C38E44 !important; }
        .bg-gold { background-color: #C38E44 !important; }
        .bg-gold-subtle { background-color: #fdf3e3 !important; }
        
        /* Avatar Icon */
        .avatar-circle {
            width: 54px; height: 54px;
            background-color: #fdf3e3; color: #C38E44;
            display: flex; align-items: center; justify-content: center;
            border-radius: 16px; font-size: 1.5rem;
            box-shadow: 0 4px 10px rgba(195, 142, 68, 0.15);
        }

        /* Card Style */
        .card-section {
            border: none; border-radius: 16px; background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            transition: all 0.3s ease; overflow: hidden; margin-bottom: 1.5rem;
        }
        .card-section:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }

        /* Accordion Header */
        .accordion-header-custom { cursor: pointer; transition: background-color 0.2s; }
        .accordion-header-custom:hover { background-color: #f8f9fa; } 
        .accordion-header-custom[aria-expanded="true"] .chevron-icon { transform: rotate(180deg); }
        .chevron-icon { transition: transform 0.3s ease; }

        /* Table Style */
        .table-modern { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-modern thead th {
            font-weight: 600; text-transform: uppercase; font-size: 0.75rem;
            letter-spacing: 0.5px; color: #888; background-color: #f8f9fa;
            border-bottom: 1px solid #eee; padding: 1rem 1.5rem;
        }
        .table-modern tbody td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #f0f0f0; }

        /* Timer Aesthetic */
        .timer-box-small { 
            background: #fdf8f3; border: 1px solid #f1e4d1; color: #a68b53; 
            padding: 10px 15px; border-radius: 10px; display: inline-flex; gap: 10px; margin-top: 10px; 
        }
        .timer-val { font-weight: bold; font-size: 1.1rem; color: #8E652E; }
        .timer-lbl { font-size: 0.6rem; text-transform: uppercase; display: block; text-align: center; }

        /* ========================================= */
        /* DARK MODE FIXES (SOLUSI LAYAR PUTIH)      */
        /* ========================================= */
        
        /* 1. Paksa Semua Background Putih/Terang jadi Gelap */
        [data-bs-theme="dark"] .bg-white,
        [data-bs-theme="dark"] .bg-light {
            background-color: #1e1e1e !important; /* Hitam Abu */
            color: #e0e0e0 !important;
            border-color: #333 !important;
        }

        /* 2. Fix Teks Warna */
        [data-bs-theme="dark"] .text-dark { color: #f8f9fa !important; }
        [data-bs-theme="dark"] .text-secondary, 
        [data-bs-theme="dark"] .text-muted { color: #adb5bd !important; }

        /* 3. Fix Card & Accordion Background */
        [data-bs-theme="dark"] .card-section {
            background-color: #1e1e1e !important;
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }
        [data-bs-theme="dark"] .accordion-header-custom:hover {
            background-color: #2c2c2c !important;
        }
        [data-bs-theme="dark"] .card-body.border-top {
            border-top-color: #333 !important;
        }
        
        /* 4. Fix Avatar & Timer Icons */
        [data-bs-theme="dark"] .avatar-circle {
            background-color: rgba(195, 142, 68, 0.2); /* Gold Transparan */
            color: #C38E44;
            box-shadow: none;
        }
        [data-bs-theme="dark"] .bg-gold-subtle {
            background-color: rgba(195, 142, 68, 0.2) !important;
            color: #C38E44 !important;
        }
        [data-bs-theme="dark"] .bg-info.bg-opacity-10 {
            background-color: rgba(13, 202, 240, 0.15) !important;
            color: #0dcaf0 !important;
        }
        [data-bs-theme="dark"] .timer-box-small {
            background-color: #2c2c2c;
            border-color: #444;
            color: #ccc;
        }
        [data-bs-theme="dark"] .timer-val { color: #C38E44; }

        /* 5. Fix Tabel Header & Body */
        [data-bs-theme="dark"] .table-modern thead th {
            background-color: #252525;
            color: #ccc;
            border-bottom-color: #333;
        }
        [data-bs-theme="dark"] .table-modern tbody td {
            border-bottom-color: #333;
            color: #e0e0e0;
        }

        /* 6. FIX KHUSUS: BADGE/LABEL JABATAN YANG PUTIH */
        /* Ini akan memaksa elemen pill/badge di dalam tabel menjadi gelap */
        [data-bs-theme="dark"] .table-modern tbody td .badge,
        [data-bs-theme="dark"] .table-modern tbody td .rounded-pill,
        [data-bs-theme="dark"] .table-modern tbody td span[class*="bg-white"],
        [data-bs-theme="dark"] .table-modern tbody td span[class*="bg-light"],
        [data-bs-theme="dark"] .table-modern tbody td div[class*="bg-white"],
        [data-bs-theme="dark"] .table-modern tbody td div[class*="bg-light"] {
            background-color: #2c2c2c !important; /* Abu Gelap Solid */
            color: #e0e0e0 !important; /* Teks Terang */
            border: 1px solid #444 !important;
        }
    </style>

    {{-- HEADER --}}
    <div class="d-flex align-items-center mb-5">
        <div class="avatar-circle me-3 flex-shrink-0"><i class="bi bi-file-text-fill"></i></div>
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Formulir Penilaian</h1>
            <p class="text-secondary mb-0">Daftar rekan kerja yang perlu Anda nilai pada periode ini.</p>
        </div>
    </div>

    {{-- LOGIKA ALERT & TIMER --}}
    @if($sessionInfo)
        @php
            $isExpired = now() > $sessionInfo->batas_waktu;
            $carbonDate = \Carbon\Carbon::parse($sessionInfo->batas_waktu);
        @endphp

        @if($isExpired)
            <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center mb-5 p-4">
                {{-- Fix ikon background putih di dark mode --}}
                <div class="bg-white text-danger rounded-circle p-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                    <i class="bi bi-lock-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Masa Penilaian Berakhir</h5>
                    <p class="mb-0 opacity-75">Batas waktu: <strong>{{ $carbonDate->isoFormat('D MMMM Y HH:mm') }}</strong>. Formulir telah ditutup otomatis.</p>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm mb-5 rounded-4 overflow-hidden bg-white">
                <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-stopwatch-fill fs-3"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Sisa Waktu Penilaian</h6>
                            <p class="mb-0 text-secondary small">Harap selesaikan sebelum: <strong>{{ $carbonDate->isoFormat('dddd, D MMMM Y, HH:mm') }} WIB</strong></p>
                        </div>
                    </div>
                    {{-- TIMER DISPLAY --}}
                    <div class="timer-box-small shadow-sm" x-data="timerData('{{ $sessionInfo->batas_waktu }}')" x-init="start()">
                        <div><span class="timer-val" x-text="days">00</span><span class="timer-lbl">Hari</span></div>
                        <div><span class="timer-val" x-text="hours">00</span><span class="timer-lbl">Jam</span></div>
                        <div><span class="timer-val" x-text="minutes">00</span><span class="timer-lbl">Mnt</span></div>
                        <div><span class="timer-val" x-text="seconds">00</span><span class="timer-lbl">Dtk</span></div>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- SECTIONS (Diri, Atasan, Rekan, Bawahan) --}}
    @foreach(['Diri Sendiri' => $diri, 'Atasan' => $atasan, 'Rekan Sejawat' => $rekan, 'Bawahan' => $bawahan] as $title => $data)
    <div class="card card-section">
        {{-- Header Accordion --}}
        <div class="accordion-header-custom p-4 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapse{{ Str::slug($title) }}" role="button" aria-expanded="false">
            <div class="d-flex align-items-center">
                {{-- Ikon Kategori --}}
                <div class="bg-gold-subtle text-gold rounded-3 p-2 me-3">
                    <i class="bi {{ $title == 'Diri Sendiri' ? 'bi-person-circle' : ($title == 'Atasan' ? 'bi-person-workspace' : ($title == 'Bawahan' ? 'bi-person-lines-fill' : 'bi-people-fill')) }} fs-4"></i>
                </div>
                {{-- Judul Kategori --}}
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Penilaian {{ $title }}</h6>
                    <small class="text-muted">Total: {{ $data->count() }} orang</small>
                </div>
            </div>
            
            {{-- Badge Status & Chevron --}}
            <div class="d-flex align-items-center gap-3">
                <span class="badge rounded-pill px-3 py-2 {{ $data->where('status_nilai', 'Belum')->count() > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                    {{ $data->where('status_nilai', 'Belum')->count() }} Belum Dinilai
                </span>
                <i class="bi bi-chevron-down text-muted chevron-icon"></i>
            </div>
        </div>

        {{-- Isi Accordion (Tabel) --}}
        <div class="collapse" id="collapse{{ Str::slug($title) }}" wire:ignore.self>
            <div class="card-body p-0 border-top">
                @if($data->isEmpty())
                    <div class="text-center py-5 text-muted small">Tidak ada data untuk kategori ini.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-modern mb-0 w-100">
                            <thead>
                                <tr>
                                    <th>Nama Pegawai</th>
                                    <th>Jabatan</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $alokasi)
                                    @include('livewire.karyawan.partials.row-tabel', ['alokasi' => $alokasi])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach

    {{-- Script Timer --}}
    <script>
        function timerData(deadline) {
            return {
                days: '00', hours: '00', minutes: '00', seconds: '00',
                endTime: new Date(deadline).getTime(),
                start() { this.update(); setInterval(() => this.update(), 1000); },
                update() {
                    let diff = this.endTime - new Date().getTime();
                    if (diff <= 0) { this.days = this.hours = this.minutes = this.seconds = '00'; return; }
                    this.days = Math.floor(diff / 86400000).toString().padStart(2, '0');
                    this.hours = Math.floor((diff % 86400000) / 3600000).toString().padStart(2, '0');
                    this.minutes = Math.floor((diff % 3600000) / 60000).toString().padStart(2, '0');
                    this.seconds = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
                }
            }
        }
    </script>
</div>