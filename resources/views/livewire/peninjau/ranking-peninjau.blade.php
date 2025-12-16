<div class="container-fluid p-4">
    <style>
        /* --- PODIUM LAYOUT --- */
        .podium-container {
            display: flex;
            justify-content: center;
            align-items: flex-end; /* Ratakan bawah */
            gap: 25px;
            margin-bottom: 50px;
            padding-top: 20px;
        }

        /* --- CARD BASE --- */
        .rank-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            text-align: center;
            position: relative;
            padding: 0;
            overflow: visible; /* Agar avatar bisa keluar dikit jika mau */
            display: flex;
            flex-direction: column;
            border: 1px solid #f0f0f0;
        }

        /* --- UKURAN & POSISI (FIX PENYET) --- */
        /* Juara 1 (Tengah) */
        .rank-1-wrapper {
            order: 2;
            width: 280px; /* Lebih lebar */
            z-index: 2;
        }
        .rank-card-1 {
            min-height: 320px; /* Tinggi proporsional */
            border-top: 8px solid #FFD700; /* Aksen Emas */
            transform: scale(1.05); /* Sedikit lebih besar */
        }

        /* Juara 2 (Kiri) */
        .rank-2-wrapper {
            order: 1;
            width: 240px;
        }
        .rank-card-2 {
            min-height: 280px; /* Lebih rendah dari rank 1 */
            border-top: 8px solid #C0C0C0; /* Aksen Perak */
        }

        /* Juara 3 (Kanan) */
        .rank-3-wrapper {
            order: 3;
            width: 240px;
        }
        .rank-card-3 {
            min-height: 260px; /* Paling rendah */
            border-top: 8px solid #CD7F32; /* Aksen Perunggu */
        }

        /* --- COMPONENTS --- */
        .rank-badge-floating {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1.2rem;
            color: white;
            position: absolute;
            top: -20px; left: 50%; transform: translateX(-50%);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 3;
        }
        .badge-1 { background: #FFD700; border: 3px solid white; }
        .badge-2 { background: #C0C0C0; border: 3px solid white; }
        .badge-3 { background: #CD7F32; border: 3px solid white; }

        .rank-avatar-box {
            margin-top: 35px;
            margin-bottom: 15px;
            display: flex; justify-content: center;
        }
        .rank-avatar {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: #f8f9fa;
            color: #555;
            font-size: 2rem; font-weight: bold;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #eee;
        }
        /* Border warna avatar sesuai juara */
        .rank-1-wrapper .rank-avatar { border-color: #FFD700; color: #b8860b; background-color: #fffbf0; }
        .rank-2-wrapper .rank-avatar { border-color: #C0C0C0; color: #7f8c8d; }
        .rank-3-wrapper .rank-avatar { border-color: #CD7F32; color: #a0522d; }

        .rank-name { font-size: 1.1rem; font-weight: 700; color: #333; margin-bottom: 5px; padding: 0 15px; }
        .rank-jabatan { font-size: 0.75rem; color: #888; margin-bottom: 20px; padding: 0 15px; min-height: 30px; }
        
        .rank-score-box {
            margin-top: auto; /* Dorong ke bawah */
            background: #fafafa;
            padding: 15px;
            border-radius: 0 0 15px 15px; /* Radius bawah mengikuti card */
            border-top: 1px solid #eee;
        }
        .rank-score-val { font-size: 1.5rem; font-weight: 800; color: #333; }
        .rank-score-lbl { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #aaa; }

        /* Dark Mode */
        [data-bs-theme="dark"] .rank-card { background: #212529; border-color: #373b3e; }
        [data-bs-theme="dark"] .rank-name { color: #f8f9fa; }
        [data-bs-theme="dark"] .rank-score-box { background: #2b3035; border-color: #373b3e; }
        [data-bs-theme="dark"] .rank-score-val { color: #f8f9fa; }
        [data-bs-theme="dark"] .rank-avatar { background: #2b3035; }
    </style>

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <a href="{{ route('peninjau.laporan') }}" class="text-decoration-none text-muted small mb-1 d-block"><i class="bi bi-arrow-left"></i> Kembali</a>
            <h2 class="h3 fw-bold text-dark mb-1">Peringkat Kinerja</h2>
            <p class="text-muted mb-0">Periode: <span class="fw-bold" style="color: #c38e44;">{{ $siklus->tahun_ajaran }} {{ $siklus->semester }}</span></p>
        </div>
        <div class="dropdown">
            <button class="btn btn-white border shadow-sm dropdown-toggle px-4 py-2 rounded-3 text-secondary fw-bold" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-download me-2"></i> Unduh
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                <li><button class="dropdown-item py-2" wire:click="exportPdf"><i class="bi bi-file-earmark-pdf text-danger me-2"></i> PDF</button></li>
                <li><button class="dropdown-item py-2" wire:click="exportExcel"><i class="bi bi-file-earmark-excel text-success me-2"></i> Excel</button></li>
            </ul>
        </div>
    </div>

    {{-- 🏆 PODIUM 3 BESAR (STATIK / HANYA TAMPILAN) --}}
    @if(count($dataPegawai) > 0)
        <div class="podium-container">
            
            {{-- 🥈 RANK 2 --}}
            @if(isset($dataPegawai[1]))
            <div class="rank-2-wrapper">
                <div class="rank-card rank-card-2">
                    <div class="rank-badge-floating badge-2">2</div>
                    <div class="rank-avatar-box">
                        <div class="rank-avatar">
                            {{ substr($dataPegawai[1]['nama'], 0, 1) }}
                        </div>
                    </div>
                    <div class="rank-name">{{ Str::limit($dataPegawai[1]['nama'], 20) }}</div>
                    <div class="rank-jabatan">{{ Str::limit($dataPegawai[1]['jabatan'], 30) }}</div>
                    <div class="rank-score-box">
                        @php $skor2 = $dataPegawai[1]['skor_akhir'] <= 5 ? $dataPegawai[1]['skor_akhir'] * 20 : $dataPegawai[1]['skor_akhir']; @endphp
                        <div class="rank-score-val">{{ number_format($skor2, 0) }}</div>
                        <div class="rank-score-lbl">Total Skor</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- 🥇 RANK 1 --}}
            @if(isset($dataPegawai[0]))
            <div class="rank-1-wrapper">
                <div class="rank-card rank-card-1">
                    <div class="rank-badge-floating badge-1">
                        <i class="bi bi-trophy-fill" style="font-size: 0.9rem;"></i>
                    </div>
                    <div class="rank-avatar-box">
                        <div class="rank-avatar">
                            {{ substr($dataPegawai[0]['nama'], 0, 1) }}
                        </div>
                    </div>
                    <div class="rank-name fs-5">{{ Str::limit($dataPegawai[0]['nama'], 20) }}</div>
                    <div class="rank-jabatan">{{ Str::limit($dataPegawai[0]['jabatan'], 35) }}</div>
                    <div class="rank-score-box" style="background: #fffdf5;">
                        @php $skor1 = $dataPegawai[0]['skor_akhir'] <= 5 ? $dataPegawai[0]['skor_akhir'] * 20 : $dataPegawai[0]['skor_akhir']; @endphp
                        <div class="rank-score-val" style="color: #d4a017;">{{ number_format($skor1, 0) }}</div>
                        <div class="rank-score-lbl text-warning">Total Skor</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- 🥉 RANK 3 --}}
            @if(isset($dataPegawai[2]))
            <div class="rank-3-wrapper">
                <div class="rank-card rank-card-3">
                    <div class="rank-badge-floating badge-3">3</div>
                    <div class="rank-avatar-box">
                        <div class="rank-avatar">
                            {{ substr($dataPegawai[2]['nama'], 0, 1) }}
                        </div>
                    </div>
                    <div class="rank-name">{{ Str::limit($dataPegawai[2]['nama'], 20) }}</div>
                    <div class="rank-jabatan">{{ Str::limit($dataPegawai[2]['jabatan'], 30) }}</div>
                    <div class="rank-score-box">
                        @php $skor3 = $dataPegawai[2]['skor_akhir'] <= 5 ? $dataPegawai[2]['skor_akhir'] * 20 : $dataPegawai[2]['skor_akhir']; @endphp
                        <div class="rank-score-val">{{ number_format($skor3, 0) }}</div>
                        <div class="rank-score-lbl">Total Skor</div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    @endif

    {{-- 📋 TABEL DAFTAR LENGKAP --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mt-5">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark">Daftar Peringkat Lengkap</h6>
            <div class="input-group" style="max-width: 250px;">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search small"></i></span>
                <input type="text" class="form-control bg-light border-start-0 form-control-sm" placeholder="Cari..." wire:model.live.debounce="300ms">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="py-3 text-center ps-4" width="8%">#</th>
                        <th class="py-3">Pegawai</th>
                        <th class="py-3">Jabatan</th>
                        <th class="text-center py-3">Skor</th>
                        <th class="text-center py-3">Predikat</th>
                        <th class="text-center py-3" width="10%">Detail</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($dataPegawai as $index => $row)
                        <tr>
                            <td class="text-center ps-4">
                                <div class="badge rounded-circle bg-light text-secondary border" style="width: 28px; height: 28px; display:flex; align-items:center; justify-content:center;">
                                    {{ $index + 1 }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row['nama'] }}</div>
                                <div class="small text-muted">{{ $row['nip'] }}</div>
                            </td>
                            <td><span class="text-secondary small">{{ Str::limit($row['jabatan'], 40) }}</span></td>
                            <td class="text-center">
                                @php
                                    $skorTampil = $row['skor_akhir'] <= 5 ? $row['skor_akhir'] * 20 : $row['skor_akhir'];
                                @endphp
                                <span class="fw-bold text-dark">{{ number_format($skorTampil, 0) }}</span>
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
                                <a href="{{ route('peninjau.laporan.detail', ['siklusId' => $siklus->id, 'userId' => $row['user_id']]) }}" 
                                   class="btn btn-sm btn-light border rounded-3 px-3 text-muted" title="Lihat Detail">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>