<div class="container-fluid p-4">

    <style>
        .text-gold { color: #C38E44 !important; }
        .bg-gold { background-color: #C38E44 !important; }
        .bg-gold-subtle { background-color: #fdf3e3 !important; }
        
        .avatar-circle {
            width: 54px;
            height: 54px;
            background-color: #fdf3e3;
            color: #C38E44;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            font-size: 1.5rem;
            box-shadow: 0 4px 10px rgba(195, 142, 68, 0.15);
        }

        .card-section {
            border: none;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .card-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        .accordion-header-custom {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .accordion-header-custom:hover {
            background-color: #fcfcfc;
        }
        /* Rotasi icon chevron saat accordion aktif */
        .accordion-header-custom[aria-expanded="true"] .chevron-icon {
            transform: rotate(180deg);
        }
        .chevron-icon {
            transition: transform 0.3s ease;
        }

        /* Table Styling */
        .table-modern thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #888;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
            padding: 1rem 1.5rem;
        }
        .table-modern tbody td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }
        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }
    </style>

    {{-- === BAGIAN HEADER === --}}
    <div class="d-flex align-items-center mb-5">
        <div class="avatar-circle me-3 flex-shrink-0">
            <i class="bi bi-file-text-fill"></i>
        </div>
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Formulir Penilaian</h1>
            <p class="text-secondary mb-0">Daftar rekan kerja yang perlu Anda nilai pada periode ini.</p>
        </div>
    </div>

    {{-- === LOGIKA ALERT & TIMER === --}}
    @if($sessionInfo)
        @php
            $isExpired = now() > $sessionInfo->batas_waktu;
            $carbonDate = \Carbon\Carbon::parse($sessionInfo->batas_waktu);
        @endphp

        @if($isExpired)
            <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-center mb-5 p-4">
                <div class="bg-white text-danger rounded-circle p-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                    <i class="bi bi-lock-fill fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Masa Penilaian Berakhir</h5>
                    <p class="mb-0 text-dark opacity-75">
                        Batas waktu: <strong>{{ $carbonDate->isoFormat('D MMMM Y HH:mm') }}</strong>. 
                        Formulir telah ditutup otomatis.
                    </p>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm mb-5 rounded-4 overflow-hidden bg-white">
                <div class="card-body p-4 d-flex align-items-center position-relative">
                    <div class="position-absolute start-0 top-0 bottom-0 bg-info" style="width: 6px;"></div>
                    
                    <div class="me-4 text-center ps-2">
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="bi bi-stopwatch-fill fs-3"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Sisa Waktu Penilaian</h6>
                        <p class="mb-0 text-secondary small">
                            Harap selesaikan sebelum: <br>
                            <span class="fw-bold text-dark fs-6">{{ $carbonDate->isoFormat('dddd, D MMMM Y') }}</span> 
                            pukul <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1">{{ $carbonDate->format('H:i') }} WIB</span>
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- === 1. SECTION DIRI SENDIRI === --}}
    <div class="card card-section">
        <div class="accordion-header-custom p-4 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapseDiri" role="button" aria-expanded="false" aria-controls="collapseDiri">
            <div class="d-flex align-items-center">
                <div class="bg-gold-subtle text-gold rounded-3 p-2 me-3">
                    <i class="bi bi-person-circle fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Penilaian Diri Sendiri</h6>
                    <small class="text-muted">Evaluasi kinerja Anda sendiri</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge rounded-pill px-3 py-2 {{ $diri->where('status_nilai', 'Belum')->count() > 0 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' }}">
                    {{ $diri->where('status_nilai', 'Belum')->count() }} Belum Dinilai
                </span>
                <i class="bi bi-chevron-down text-muted chevron-icon"></i>
            </div>
        </div>

        <div class="collapse" id="collapseDiri" wire:ignore.self>
            <div class="card-body p-0 border-top">
                @if($diri->isEmpty())
                    <div class="text-center py-5">
                        <div class="bg-light rounded-circle p-3 d-inline-block mb-3">
                            <i class="bi bi-slash-circle text-muted fs-3"></i>
                        </div>
                        <p class="text-muted mb-0">Tidak ada data.</p>
                    </div>
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
                                @foreach($diri as $alokasi)
                                    @include('livewire.karyawan.partials.row-tabel', ['alokasi' => $alokasi])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- === 2. SECTION ATASAN === --}}
    <div class="card card-section">
        <div class="accordion-header-custom p-4 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapseAtasan" role="button" aria-expanded="false">
            <div class="d-flex align-items-center">
                <div class="bg-gold-subtle text-gold rounded-3 p-2 me-3">
                    <i class="bi bi-person-workspace fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Penilaian Atasan</h6>
                    <small class="text-muted">Berikan penilaian kepada atasan</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge rounded-pill px-3 py-2 {{ $atasan->where('status_nilai', 'Belum')->count() > 0 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' }}">
                    {{ $atasan->where('status_nilai', 'Belum')->count() }} Belum Dinilai
                </span>
                <i class="bi bi-chevron-down text-muted chevron-icon"></i>
            </div>
        </div>
        
        <div class="collapse" id="collapseAtasan" wire:ignore.self>
            <div class="card-body p-0 border-top">
                @if($atasan->isEmpty())
                    <div class="text-center py-5">
                        <div class="bg-light rounded-circle p-3 d-inline-block mb-3">
                            <i class="bi bi-slash-circle text-muted fs-3"></i>
                        </div>
                        <p class="text-muted mb-0">Tidak ada data atasan.</p>
                    </div>
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
                                @foreach($atasan as $alokasi)
                                    @include('livewire.karyawan.partials.row-tabel', ['alokasi' => $alokasi])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- === 3. SECTION REKAN SEJAWAT === --}}
    <div class="card card-section">
        <div class="accordion-header-custom p-4 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapseRekan" role="button" aria-expanded="false">
            <div class="d-flex align-items-center">
                <div class="bg-gold-subtle text-gold rounded-3 p-2 me-3">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Penilaian Rekan Sejawat</h6>
                    <small class="text-muted">Berikan penilaian kepada teman satu divisi</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge rounded-pill px-3 py-2 {{ $rekan->where('status_nilai', 'Belum')->count() > 0 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' }}">
                    {{ $rekan->where('status_nilai', 'Belum')->count() }} Belum Dinilai
                </span>
                <i class="bi bi-chevron-down text-muted chevron-icon"></i>
            </div>
        </div>

        <div class="collapse" id="collapseRekan" wire:ignore.self>
            <div class="card-body p-0 border-top">
                @if($rekan->isEmpty())
                    <div class="text-center py-5">
                        <div class="bg-light rounded-circle p-3 d-inline-block mb-3">
                            <i class="bi bi-slash-circle text-muted fs-3"></i>
                        </div>
                        <p class="text-muted mb-0">Tidak ada data rekan sejawat.</p>
                    </div>
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
                                @foreach($rekan as $alokasi)
                                    @include('livewire.karyawan.partials.row-tabel', ['alokasi' => $alokasi])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- === 4. SECTION BAWAHAN === --}}
    <div class="card card-section">
        <div class="accordion-header-custom p-4 d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#collapseBawahan" role="button" aria-expanded="false">
            <div class="d-flex align-items-center">
                <div class="bg-gold-subtle text-gold rounded-3 p-2 me-3">
                    <i class="bi bi-person-lines-fill fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Penilaian Bawahan</h6>
                    <small class="text-muted">Berikan penilaian kepada staff Anda</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge rounded-pill px-3 py-2 {{ $bawahan->where('status_nilai', 'Belum')->count() > 0 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' }}">
                    {{ $bawahan->where('status_nilai', 'Belum')->count() }} Belum Dinilai
                </span>
                <i class="bi bi-chevron-down text-muted chevron-icon"></i>
            </div>
        </div>

        <div class="collapse" id="collapseBawahan" wire:ignore.self>
            <div class="card-body p-0 border-top">
                @if($bawahan->isEmpty())
                    <div class="text-center py-5">
                        <div class="bg-light rounded-circle p-3 d-inline-block mb-3">
                            <i class="bi bi-slash-circle text-muted fs-3"></i>
                        </div>
                        <p class="text-muted mb-0">Tidak ada data bawahan.</p>
                    </div>
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
                                @foreach($bawahan as $alokasi)
                                    @include('livewire.karyawan.partials.row-tabel', ['alokasi' => $alokasi])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>