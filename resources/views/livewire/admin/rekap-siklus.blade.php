<div class="container-fluid p-4">
    
    {{-- CUSTOM STYLES --}}
    <style>
        /* Warna Utama */
        .text-gold { color: #c38e44 !important; }
        .bg-gold { background-color: #c38e44 !important; color: white; }
        
        /* Rank Circle (Pengganti Piala) */
        .rank-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            margin: 0 auto; /* Tengah */
        }
        .rank-1 { background-color: #c38e44; color: white; box-shadow: 0 2px 5px rgba(195, 142, 68, 0.4); } /* Emas Elegan */
        .rank-2 { background-color: #a0a0a0; color: white; } /* Perak */
        .rank-3 { background-color: #cd7f32; color: white; } /* Perunggu */
        .rank-other { background-color: #f0f0f0; color: #888; } /* Abu abu */

        /* Badge Predikat Soft (Tidak Norak) */
        .badge-soft {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        .soft-danger { background-color: #fce8e6; color: #c53030; }
        .soft-warning { background-color: #fff7e6; color: #b7791f; }
        .soft-success { background-color: #def7ec; color: #03543f; }
        .soft-primary { background-color: #ebf8ff; color: #2b6cb0; }
        .soft-secondary { background-color: #f7fafc; color: #4a5568; border: 1px solid #e2e8f0; }

        /* Highlight Baris Juara 1 */
        .row-winner { background-color: #fffbf0; }
    </style>

    {{-- HEADER HALAMAN (CLEAN & SIMPLE) --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark mb-1">Peringkat Kinerja Pegawai</h2>
            <p class="text-muted mb-0">
                Periode Penilaian: <span class="fw-bold" style="color: #c38e44;">{{ $siklus->tahun_ajaran }} {{ $siklus->semester }}</span>
            </p>
        </div>
        
        {{-- DROPDOWN EXPORT --}}
        <div class="dropdown">
            <button class="btn btn-white border shadow-sm dropdown-toggle px-4 py-2 rounded-3 text-secondary fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download me-2"></i> Unduh Laporan
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                <li>
                    <button class="dropdown-item py-2" wire:click="exportPdf">
                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i> Download PDF
                    </button>
                </li>
                <li>
                    <button class="dropdown-item py-2" wire:click="exportExcel">
                        <i class="bi bi-file-earmark-excel text-success me-2"></i> Download Excel
                    </button>
                </li>
            </ul>
        </div>
    </div>

    {{-- KARTU TABEL UTAMA --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        
        {{-- SEARCH BAR --}}
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="mb-0 fw-bold text-dark">Daftar Peringkat</h6>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <div class="input-group ms-auto" style="max-width: 300px;">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0" 
                               placeholder="Cari Pegawai..." 
                               wire:model.live.debounce="300ms">
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="py-3 text-center" width="8%">Rank</th>
                        <th class="py-3 ps-3">Pegawai</th>
                        <th class="py-3">Jabatan</th>
                        <th class="text-center py-3">Skor</th>
                        <th class="text-center py-3">Predikat</th>
                        <th class="text-center py-3" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($dataPegawai as $index => $row)
                    
                    {{-- Highlight Row hanya untuk Rank 1 --}}
                    <tr class="{{ $index == 0 ? 'row-winner' : '' }}">
                        
                        {{-- KOLOM RANKING (LINGKARAN) --}}
                        <td class="text-center">
                            @if($index == 0)
                                <div class="rank-badge rank-1">1</div>
                            @elseif($index == 1)
                                <div class="rank-badge rank-2">2</div>
                            @elseif($index == 2)
                                <div class="rank-badge rank-3">3</div>
                            @else
                                <div class="rank-badge rank-other">{{ $index + 1 }}</div>
                            @endif
                        </td>

                        {{-- KOLOM NAMA --}}
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $row['nama'] }}</div>
                            <div class="small text-muted">{{ $row['nip'] }}</div>
                        </td>

                        {{-- KOLOM JABATAN --}}
                        <td>
                            <span class="text-secondary small">{{ Str::limit($row['jabatan'], 45) }}</span>
                        </td>

                        {{-- KOLOM SKOR --}}
                        <td class="text-center">
                            @if($row['skor_akhir'] > 0)
                                <span class="fw-bold text-dark fs-6">{{ $row['skor_akhir'] }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>

                        {{-- KOLOM PREDIKAT (SOFT BADGE) --}}
                        <td class="text-center">
                            @php
                                $class = match($row['predikat']) {
                                    'Sangat Baik' => 'soft-success',
                                    'Baik' => 'soft-primary',
                                    'Cukup' => 'soft-warning',
                                    'Kurang', 'Sangat Kurang' => 'soft-danger',
                                    default => 'soft-secondary'
                                };
                            @endphp
                            <span class="badge-soft {{ $class }}">
                                {{ $row['predikat'] }}
                            </span>
                        </td>

                        {{-- KOLOM AKSI (SIMPLE) --}}
                        <td class="text-center">
                            <a href="{{ route('admin.detail-nilai', ['siklusId' => $siklus->id, 'userId' => $row['user_id']]) }}" 
                               class="btn btn-sm btn-outline-secondary rounded-3 px-3 border-0"
                               title="Lihat Detail">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted opacity-50 mb-2">
                                <i class="bi bi-clipboard-x fs-1"></i>
                            </div>
                            <h6 class="fw-bold text-dark">Data Belum Tersedia</h6>
                            <p class="small text-muted">Belum ada penilaian yang masuk.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>