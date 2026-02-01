<div class="container-fluid p-4">
    <style>
        /* --- VARIABLES --- */
        :root { --primary-gold: #c38e44; --gold: #FFD700; --silver: #C0C0C0; --bronze: #CD7F32; }
        .text-gold { color: var(--primary-gold) !important; }
        .bg-gold { background-color: var(--primary-gold) !important; color: white; }

        /* --- BUTTON FIX --- */
        .btn-responsive { width: 100%; }
        @media (min-width: 768px) {
            .btn-responsive { width: auto !important; }
        }

        /* --- DESKTOP PODIUM (CSS RAMPING) --- */
        .podium-container { 
            display: flex; 
            justify-content: center; 
            align-items: flex-end; 
            gap: 20px; 
            margin-bottom: 50px; 
            padding-top: 30px;
            max-width: 900px; 
            margin-left: auto; 
            margin-right: auto;
        }

        .rank-card {
            background: white; border-radius: 15px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); text-align: center;
            position: relative; display: flex; flex-direction: column;
            transition: transform 0.3s; border: 1px solid rgba(0,0,0,0.05);
        }
        .rank-card:hover { transform: translateY(-5px); }
        
        .rank-card-1 { min-height: 350px; border-top: 8px solid var(--gold); transform: scale(1.05); z-index: 2; }
        .rank-card-2 { min-height: 310px; border-top: 8px solid var(--silver); }
        .rank-card-3 { min-height: 290px; border-top: 8px solid var(--bronze); }

        .rank-1-wrapper { width: 34%; order: 2; }
        .rank-2-wrapper { width: 32%; order: 1; }
        .rank-3-wrapper { width: 32%; order: 3; }

        .rank-badge-floating {
            width: 45px; height: 45px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1.3rem; color: white;
            position: absolute; top: -22px; left: 50%; transform: translateX(-50%);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2); z-index: 3;
        }
        .badge-1 { background: var(--gold); border: 3px solid white; }
        .badge-2 { background: var(--silver); border: 3px solid white; }
        .badge-3 { background: var(--bronze); border: 3px solid white; }

        .rank-avatar {
            width: 80px; height: 80px; border-radius: 50%; object-fit: cover;
            background: #f8f9fa; color: #555; font-size: 2rem; font-weight: bold;
            display: flex; align-items: center; justify-content: center;
            border: 4px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin: 35px auto 10px auto;
        }
        .rank-name { font-size: 1rem; font-weight: 700; color: #333; margin-bottom: 2px; padding: 0 10px; }
        .rank-jabatan { font-size: 0.75rem; color: #888; margin-bottom: 10px; padding: 0 10px; min-height: 30px; line-height: 1.2; }
        .badge-suara { font-size: 0.7rem; background: rgba(0,0,0,0.04); color: #666; padding: 4px 10px; border-radius: 20px; margin-bottom: 10px; display: inline-block; font-weight: 600; }
        
        .rank-score-box { margin-top: auto; background: #fafafa; padding: 12px; border-radius: 0 0 15px 15px; border-top: 1px solid #eee; }
        .rank-score-val { font-size: 1.4rem; font-weight: 800; color: #333; }
        .rank-score-lbl { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; color: #aaa; }

        /* --- MOBILE & LIST STYLES --- */
        .leaderboard-item {
            display: flex; align-items: center; background: #fff;
            padding: 15px; margin-bottom: 15px; border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 10px rgba(0,0,0,0.03);
            position: relative; overflow: hidden;
        }
        .m-rank-1 { border: 2px solid var(--gold); background: linear-gradient(to right, rgba(255, 215, 0, 0.05), #fff); }
        .m-rank-2 { border-left: 6px solid var(--silver); }
        .m-rank-3 { border-left: 6px solid var(--bronze); }
        .m-rank-number { width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; margin-right: 12px; flex-shrink: 0; color: white; }
        .m-bg-1 { background-color: var(--gold); }
        .m-bg-2 { background-color: var(--silver); }
        .m-bg-3 { background-color: var(--bronze); }
        .m-user-avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; margin-right: 12px; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); flex-shrink: 0; }
        .m-user-info { flex-grow: 1; min-width: 0; }
        .m-user-name { font-weight: 700; font-size: 0.95rem; margin-bottom: 2px; color: #333; }
        .m-user-jabatan { font-size: 0.75rem; color: #888; }
        .m-score-box { text-align: right; margin-left: 10px; flex-shrink: 0; }
        .m-score-val { font-size: 1.1rem; font-weight: 800; line-height: 1; color: #333; }
        .m-trophy { position: absolute; right: -5px; top: -5px; font-size: 3rem; color: var(--gold); opacity: 0.1; transform: rotate(15deg); pointer-events: none; }

        .mobile-list-card {
            background: #fff; border-radius: 10px; padding: 15px; margin-bottom: 10px;
            border: 1px solid rgba(0,0,0,0.08); display: flex; align-items: center;
            justify-content: space-between;
        }
        .ml-rank { font-weight: bold; color: #aaa; width: 30px; }
        .ml-info { flex-grow: 1; padding-right: 10px; min-width: 0; }
        .ml-name { font-weight: 600; color: #333; font-size: 0.95rem; }
        .ml-role { font-size: 0.75rem; color: #888; }
        .ml-score { font-weight: bold; color: var(--primary-gold); font-size: 1rem; }

        /* ========================================= */
        /* DARK MODE FIXES                           */
        /* ========================================= */
        [data-bs-theme="dark"] .bg-white {
            background-color: #1e1e1e !important;
            color: #e0e0e0 !important;
        }
        [data-bs-theme="dark"] .text-dark { color: #f8f9fa !important; }
        [data-bs-theme="dark"] .text-secondary { color: #adb5bd !important; }
        [data-bs-theme="dark"] .text-muted { color: #999 !important; }

        /* Card Podium & List */
        [data-bs-theme="dark"] .rank-card, 
        [data-bs-theme="dark"] .leaderboard-item, 
        [data-bs-theme="dark"] .mobile-list-card,
        [data-bs-theme="dark"] .card { 
            background-color: #1e1e1e !important; 
            border-color: #333 !important; 
        }
        
        /* Text Colors inside Card */
        [data-bs-theme="dark"] .rank-name, 
        [data-bs-theme="dark"] .rank-score-val, 
        [data-bs-theme="dark"] .m-user-name, 
        [data-bs-theme="dark"] .m-score-val, 
        [data-bs-theme="dark"] .ml-name { 
            color: #fff !important; 
        }
        [data-bs-theme="dark"] .rank-jabatan, 
        [data-bs-theme="dark"] .m-user-jabatan, 
        [data-bs-theme="dark"] .ml-role { 
            color: #adb5bd !important; 
        }
        
        /* Score Box Background */
        [data-bs-theme="dark"] .rank-score-box { 
            background-color: #2c2c2c !important; 
            border-top: 1px solid #333 !important; 
        }
        
        /* Mobile Gradient Fix */
        [data-bs-theme="dark"] .m-rank-1 { 
            background: linear-gradient(to right, rgba(255, 215, 0, 0.1), #1e1e1e) !important; 
        }
        
        /* Table Styles */
        [data-bs-theme="dark"] .card-header, 
        [data-bs-theme="dark"] .table thead { 
            background-color: #2c2c2c !important; 
            color: #adb5bd !important; 
            border-bottom-color: #333 !important;
        }
        [data-bs-theme="dark"] .table tbody td {
            border-bottom-color: #333 !important;
            color: #e0e0e0 !important;
        }
        [data-bs-theme="dark"] .table-hover tbody tr:hover { 
            color: #fff; 
            background-color: rgba(255,255,255,.05) !important; 
        }
        
        /* Input & Search */
        [data-bs-theme="dark"] .input-group-text, 
        [data-bs-theme="dark"] .bg-light {
            background-color: #2c2c2c !important;
            border-color: #444 !important;
            color: #ccc !important;
        }
        [data-bs-theme="dark"] .form-control {
            background-color: #2c2c2c !important;
            border-color: #444 !important;
            color: #fff !important;
        }
        [data-bs-theme="dark"] .form-control::placeholder {
            color: #777 !important;
        }
        
        /* Avatar & Badges */
        [data-bs-theme="dark"] .rank-avatar {
            background-color: #2c2c2c !important;
            border-color: #444 !important;
            color: #e0e0e0 !important;
        }
        [data-bs-theme="dark"] .m-user-avatar {
            border-color: #444 !important;
        }
        [data-bs-theme="dark"] .badge.bg-light {
            background-color: #2c2c2c !important;
            color: #e0e0e0 !important;
            border-color: #444 !important;
        }
    </style>

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 gap-3">
        <div>
            <a href="{{ route('admin.siklus-semester') }}" class="text-decoration-none text-muted small mb-1 d-block">
                <i class="bi bi-arrow-left"></i> Kembali ke Siklus
            </a>
            <div class="d-flex align-items-center">
                <div class="p-2 me-2">
                    <i class="bi bi bi-journal-arrow-down fs-2 text-gold " style="color: #C38E44;"></i>
                </div>
                <div>
                    <h2 class="h3 mb-0 text-dark">Rekap Penilaian</h2>
            </div>
        </div>
            <p class="text-muted mb-0">Periode: <span class="fw-bold text-gold">{{ $siklus->tahun_ajaran }} {{ $siklus->semester }}</span></p>
        </div>
        
        <div class="dropdown btn-responsive">
            <button class="btn btn-white border shadow-sm dropdown-toggle px-4 py-2 rounded-3 text-secondary fw-bold btn-responsive" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-download me-2"></i> Unduh
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2 w-100">
                <li><button class="dropdown-item py-2" wire:click="exportPdf"><i class="bi bi-file-earmark-pdf text-danger me-2"></i> PDF</button></li>
                <li><button class="dropdown-item py-2" wire:click="exportExcel"><i class="bi bi-file-earmark-excel text-success me-2"></i> Excel</button></li>
            </ul>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TAMPILAN 3 BESAR (PODIUM RAMPING) --}}
    {{-- ============================================================ --}}
    
    @if(count($dataPegawai) > 0)
        
        <div class="d-none d-md-flex podium-container">
            {{-- JUARA 2 --}}
            @if(isset($dataPegawai[1]))
            <div class="rank-2-wrapper">
                <div class="rank-card rank-card-2">
                    <div class="rank-badge-floating badge-2">2</div>
                    <div class="rank-avatar">
                        @if(!empty($dataPegawai[1]['foto']))
                            <img src="{{ asset('storage/' . $dataPegawai[1]['foto']) }}" class="w-100 h-100 rounded-circle object-fit-cover">
                        @else {{ substr($dataPegawai[1]['nama'], 0, 1) }} @endif
                    </div>
                    <div class="rank-name">{{ Str::limit($dataPegawai[1]['nama'], 20) }}</div>
                    <div class="rank-jabatan">{{ Str::limit($dataPegawai[1]['jabatan'], 30) }}</div>
                    <div><span class="badge-suara"><i class="bi bi-people-fill me-1"></i> {{ $dataPegawai[1]['total_penilai'] }} Penilai</span></div>
                    <div class="rank-score-box">
                        <div class="rank-score-val">{{ number_format($dataPegawai[1]['skor_akhir'], 2) }}</div>
                        <div class="rank-score-lbl">Total Skor</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- JUARA 1 --}}
            @if(isset($dataPegawai[0]))
            <div class="rank-1-wrapper">
                <div class="rank-card rank-card-1">
                    <div class="rank-badge-floating badge-1"><i class="bi bi-trophy-fill"></i></div>
                    <div class="rank-avatar">
                        @if(!empty($dataPegawai[0]['foto']))
                            <img src="{{ asset('storage/' . $dataPegawai[0]['foto']) }}" class="w-100 h-100 rounded-circle object-fit-cover">
                        @else {{ substr($dataPegawai[0]['nama'], 0, 1) }} @endif
                    </div>
                    <div class="rank-name fs-5">{{ Str::limit($dataPegawai[0]['nama'], 20) }}</div>
                    <div class="rank-jabatan">{{ Str::limit($dataPegawai[0]['jabatan'], 35) }}</div>
                    <div><span class="badge-suara bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25"><i class="bi bi-people-fill me-1"></i> {{ $dataPegawai[0]['total_penilai'] }} Penilai</span></div>
                    <div class="rank-score-box" style="background: rgba(255, 215, 0, 0.05);">
                        <div class="rank-score-val text-gold">{{ number_format($dataPegawai[0]['skor_akhir'], 2) }}</div>
                        <div class="rank-score-lbl text-warning">Total Skor</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- JUARA 3 --}}
            @if(isset($dataPegawai[2]))
            <div class="rank-3-wrapper">
                <div class="rank-card rank-card-3">
                    <div class="rank-badge-floating badge-3">3</div>
                    <div class="rank-avatar">
                        @if(!empty($dataPegawai[2]['foto']))
                            <img src="{{ asset('storage/' . $dataPegawai[2]['foto']) }}" class="w-100 h-100 rounded-circle object-fit-cover">
                        @else {{ substr($dataPegawai[2]['nama'], 0, 1) }} @endif
                    </div>
                    <div class="rank-name">{{ Str::limit($dataPegawai[2]['nama'], 20) }}</div>
                    <div class="rank-jabatan">{{ Str::limit($dataPegawai[2]['jabatan'], 30) }}</div>
                    <div><span class="badge-suara"><i class="bi bi-people-fill me-1"></i> {{ $dataPegawai[2]['total_penilai'] }} Penilai</span></div>
                    <div class="rank-score-box">
                        <div class="rank-score-val">{{ number_format($dataPegawai[2]['skor_akhir'], 2) }}</div>
                        <div class="rank-score-lbl">Total Skor</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- B. MOBILE LIST (Sama seperti sebelumnya) --}}
        <div class="d-block d-md-none mb-5">
            <h6 class="fw-bold text-muted text-uppercase mb-3 ps-1 small">3 Besar Terbaik</h6>
            @foreach(array_slice($dataPegawai, 0, 3) as $index => $row)
                @php 
                    $rank = $index + 1; $mClass = 'm-rank-'.$rank; $bgClass = 'm-bg-'.$rank;
                @endphp
                <div class="leaderboard-item {{ $mClass }}">
                    @if($rank == 1) <i class="bi bi-trophy-fill m-trophy"></i> @endif
                    <div class="m-rank-number {{ $bgClass }}">{{ $rank }}</div>
                    <div class="m-user-avatar d-flex align-items-center justify-content-center bg-light text-secondary fw-bold border">{{ substr($row['nama'], 0, 1) }}</div>
                    <div class="m-user-info">
                        <div class="m-user-name text-truncate">{{ $row['nama'] }}</div>
                        <div class="m-user-jabatan text-truncate">{{ $row['jabatan'] }}</div>
                    </div>
                    <div class="m-score-box">
                        <div class="m-score-val">{{ number_format($row['skor_akhir'], 2) }}</div>
                        <div class="m-score-lbl">SKOR</div>
                    </div>
                </div>
            @endforeach
        </div>

    @endif

    {{-- TABEL LENGKAP --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mt-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <h6 class="mb-0 fw-bold text-dark">Daftar Peringkat Lengkap</h6>
            <div class="input-group btn-responsive" style="max-width: 300px;">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search small"></i></span>
                <input type="text" class="form-control bg-light border-start-0 form-control-sm" placeholder="Cari Pegawai..." wire:model.live.debounce="300ms">
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="py-3 text-center ps-4" width="5%">#</th>
                            <th class="py-3">Pegawai</th>
                            <th class="py-3">Jabatan</th>
                            <th class="text-center py-3">Skor Akhir</th>
                            <th class="text-center py-3">Validitas</th>
                            <th class="text-center py-3">Predikat</th>
                            <th class="text-center py-3" width="10%">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($dataPegawai as $index => $row)
                            <tr>
                                <td class="text-center ps-4">
                                    <div class="badge rounded-circle bg-light text-secondary border" style="width: 28px; height: 28px; display:flex; align-items:center; justify-content:center;">{{ $index + 1 }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $row['nama'] }}</div>
                                    <div class="small text-muted">{{ $row['nip'] }}</div>
                                </td>
                                <td><span class="text-secondary small">{{ Str::limit($row['jabatan'], 40) }}</span></td>
                                <td class="text-center">
                                    @php $skorTampil = $row['skor_akhir'] <= 5 ? $row['skor_akhir'] * 20 : $row['skor_akhir']; @endphp
                                    <span class="fw-bold fs-6 text-dark">{{ number_format($skorTampil, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-secondary border rounded-pill fw-normal px-3"><i class="bi bi-person-check-fill me-1"></i> {{ $row['total_penilai'] }} Suara</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $class = match($row['predikat']) {
                                            'Sangat Baik' => 'bg-success bg-opacity-10 text-success',
                                            'Baik' => 'bg-primary bg-opacity-10 text-primary',
                                            'Cukup' => 'bg-warning bg-opacity-10 text-warning',
                                            default => 'bg-danger bg-opacity-10 text-danger'
                                        };
                                    @endphp
                                    <span class="badge {{ $class }} rounded-pill fw-normal px-3">{{ $row['predikat'] }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.detail-nilai', ['siklusId' => $siklus->id, 'userId' => $row['user_id']]) }}" class="btn btn-sm btn-light border rounded-3 px-3 text-muted w-100"><i class="bi bi-chevron-right"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-5">Data tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- MOBILE CARD LIST --}}
            <div class="d-md-none p-3 bg-light">
                @forelse($dataPegawai as $index => $row)
                    @php $skorTampil = $row['skor_akhir'] <= 5 ? $row['skor_akhir'] * 20 : $row['skor_akhir']; @endphp
                    <div class="mobile-list-card" onclick="window.location='{{ route('admin.detail-nilai', ['siklusId' => $siklus->id, 'userId' => $row['user_id']]) }}'" style="cursor: pointer;">
                        <div class="ml-rank">{{ $index + 1 }}</div>
                        <div class="ml-info">
                            <div class="ml-name text-truncate">{{ $row['nama'] }}</div>
                            <div class="ml-role text-truncate">{{ Str::limit($row['jabatan'], 30) }}</div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.65rem;">{{ $row['total_penilai'] }} Suara</span>
                                <span class="badge bg-light text-dark border fw-normal" style="font-size: 0.65rem;">{{ $row['predikat'] }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="ml-score">{{ number_format($skorTampil, 2) }}</div>
                            <i class="bi bi-chevron-right text-muted small"></i>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">Data tidak ditemukan.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>