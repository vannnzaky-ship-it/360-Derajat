<div class="container-fluid p-4">
    
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Laporan Hasil Penilaian</h3>
            <p class="text-muted mb-0">Daftar siklus penilaian yang telah dilaksanakan.</p>
        </div>
    </div>

    {{-- KARTU TABEL (SAMA PERSIS DENGAN SIKLUS SEMESTER) --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    {{-- Header Table --}}
                    <thead class="bg-custom-brown text-white" style="background-color: #c38e44; color: white;">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="30%">Tahun Ajaran</th>
                            <th width="20%">Semester</th>
                            <th width="20%">Status</th>
                            <th class="text-center" width="25%">Action</th>
                        </tr>
                    </thead>
                    
                    {{-- Body Table --}}
                    <tbody>
                        @forelse ($sikluses as $index => $siklus)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="fw-bold text-dark">{{ $siklus->tahun_ajaran }}</td>
                            <td>
                                @if($siklus->semester == 'Ganjil')
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3">Ganjil</span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3">Genap</span>
                                @endif
                            </td>
                            <td>
                                @if ($siklus->status == 'Aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Selesai</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{-- Tombol Mata (Lihat Ranking) --}}
                                {{-- Menggunakan style btn-info text-white agar sama persis dengan referensi --}}
                                <a href="{{ route('peninjau.laporan.ranking', $siklus->id) }}" 
                                   class="btn btn-sm btn-info text-white border-0 px-3" 
                                   title="Lihat Laporan Ranking">
                                    <i class="bi bi-eye-fill me-1"></i> Lihat Laporan
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="opacity-50 mb-2"><i class="bi bi-clipboard-x fs-1"></i></div>
                                Belum ada laporan hasil penilaian yang tersedia.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>