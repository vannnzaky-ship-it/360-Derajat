<div class="container-fluid p-4">
    {{-- BAGIAN ATAS --}}
    <div class="mb-4">
        <h1 class="h3 fw-bold text-dark">Formulir Penilaian Kinerja</h1>
        <p class="text-muted">Daftar rekan kerja yang perlu Anda nilai pada periode aktif ini.</p>
    </div>

    {{-- HELPER FUNCTION --}}
    {{-- (Livewire tidak support helper function PHP dalam blade secara native tanpa error variable scope --}}
    {{-- Jadi kita pakai pendekatan @include manual seperti perbaikan terakhir agar stabil) --}}

    {{-- ALERT BATAS WAKTU --}}
    @if($sessionInfo)
        @php
            $isExpired = now() > $sessionInfo->batas_waktu;
            $carbonDate = \Carbon\Carbon::parse($sessionInfo->batas_waktu);
        @endphp

        @if($isExpired)
            <div class="alert alert-danger d-flex align-items-center mb-4 rounded-3 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
                <div>
                    <h5 class="alert-heading fw-bold mb-1">Masa Penilaian Berakhir</h5>
                    <p class="mb-0">
                        Batas waktu penilaian adalah <strong>{{ $carbonDate->isoFormat('D MMMM Y HH:mm') }}</strong>. 
                        Formulir penilaian telah ditutup otomatis.
                    </p>
                </div>
            </div>
        @else
            <div class="alert alert-info d-flex align-items-center mb-4 rounded-3 shadow-sm border-0 bg-white border-start border-4 border-info">
                <i class="bi bi-stopwatch-fill fs-3 me-3 text-info"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Sisa Waktu Penilaian</h6>
                    <p class="mb-0 small text-muted">
                        Silakan selesaikan penilaian sebelum: <br>
                        <span class="fw-bold text-dark fs-5">{{ $carbonDate->isoFormat('dddd, D MMMM Y') }}</span> 
                        pukul <span class="fw-bold text-danger">{{ $carbonDate->format('H:i') }} WIB</span>
                    </p>
                </div>
            </div>
        @endif
    @endif

    {{-- 1. SECTION DIRI SENDIRI --}}
    <div class="card shadow-sm border-0 mb-3 rounded-4 overflow-hidden">
        <a class="list-group-item list-group-item-action d-flex justify-content-between p-3 bg-white" data-bs-toggle="collapse" href="#collapseDiri">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-circle fs-4 me-3 text-custom-brown"></i>
                <span class="fw-bold">Penilaian Diri Sendiri</span>
            </div>
            <span class="badge {{ $diri->where('status_nilai', 'Belum')->count() > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill">
                {{ $diri->where('status_nilai', 'Belum')->count() }} Belum
            </span>
        </a>
        <div class="collapse show" id="collapseDiri">
            @if($diri->isEmpty())
                <div class="text-center p-4 text-muted">Tidak ada data diri sendiri untuk dinilai.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-fixed-layout">
                        <thead class="table-header-fix">
                            <tr>
                                <th class="py-2 px-3 small text-uppercase">Nama Pegawai</th>
                                <th class="py-2 px-3 small text-uppercase">Jabatan</th>
                                <th class="py-2 px-3 small text-uppercase text-center">Status</th>
                                <th class="py-2 px-3 small text-uppercase text-end">Aksi</th>
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

    {{-- 2. SECTION ATASAN --}}
    <div class="card shadow-sm border-0 mb-3 rounded-4 overflow-hidden">
        <a class="list-group-item list-group-item-action d-flex justify-content-between p-3 bg-white" data-bs-toggle="collapse" href="#collapseAtasan">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-workspace fs-4 me-3 text-custom-brown"></i>
                <span class="fw-bold">Penilaian Atasan</span>
            </div>
            <span class="badge {{ $atasan->where('status_nilai', 'Belum')->count() > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill">
                {{ $atasan->where('status_nilai', 'Belum')->count() }} Belum
            </span>
        </a>
        <div class="collapse" id="collapseAtasan">
            @if($atasan->isEmpty())
                <div class="text-center p-4 text-muted">Tidak ada data atasan untuk dinilai.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-fixed-layout">
                        <thead class="table-header-fix">
                            <tr>
                                <th class="py-2 px-3 small text-uppercase">Nama Pegawai</th>
                                <th class="py-2 px-3 small text-uppercase">Jabatan</th>
                                <th class="py-2 px-3 small text-uppercase text-center">Status</th>
                                <th class="py-2 px-3 small text-uppercase text-end">Aksi</th>
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

    {{-- 3. SECTION REKAN --}}
    <div class="card shadow-sm border-0 mb-3 rounded-4 overflow-hidden">
        <a class="list-group-item list-group-item-action d-flex justify-content-between p-3 bg-white" data-bs-toggle="collapse" href="#collapseRekan">
            <div class="d-flex align-items-center">
                <i class="bi bi-people-fill fs-4 me-3 text-custom-brown"></i>
                <span class="fw-bold">Penilaian Rekan Sejawat</span>
            </div>
            <span class="badge {{ $rekan->where('status_nilai', 'Belum')->count() > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill">
                {{ $rekan->where('status_nilai', 'Belum')->count() }} Belum
            </span>
        </a>
        <div class="collapse" id="collapseRekan">
            @if($rekan->isEmpty())
                <div class="text-center p-4 text-muted">Tidak ada data rekan sejawat untuk dinilai.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-fixed-layout">
                        <thead class="table-header-fix">
                            <tr>
                                <th class="py-2 px-3 small text-uppercase">Nama Pegawai</th>
                                <th class="py-2 px-3 small text-uppercase">Jabatan</th>
                                <th class="py-2 px-3 small text-uppercase text-center">Status</th>
                                <th class="py-2 px-3 small text-uppercase text-end">Aksi</th>
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

    {{-- 4. SECTION BAWAHAN --}}
    <div class="card shadow-sm border-0 mb-3 rounded-4 overflow-hidden">
        <a class="list-group-item list-group-item-action d-flex justify-content-between p-3 bg-white" data-bs-toggle="collapse" href="#collapseBawahan">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-lines-fill fs-4 me-3 text-custom-brown"></i>
                <span class="fw-bold">Penilaian Bawahan</span>
            </div>
            <span class="badge {{ $bawahan->where('status_nilai', 'Belum')->count() > 0 ? 'bg-danger' : 'bg-success' }} rounded-pill">
                {{ $bawahan->where('status_nilai', 'Belum')->count() }} Belum
            </span>
        </a>
        <div class="collapse" id="collapseBawahan">
            @if($bawahan->isEmpty())
                <div class="text-center p-4 text-muted">Tidak ada data bawahan untuk dinilai.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-fixed-layout">
                        <thead class="table-header-fix">
                            <tr>
                                <th class="py-2 px-3 small text-uppercase">Nama Pegawai</th>
                                <th class="py-2 px-3 small text-uppercase">Jabatan</th>
                                <th class="py-2 px-3 small text-uppercase text-center">Status</th>
                                <th class="py-2 px-3 small text-uppercase text-end">Aksi</th>
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

    {{-- STYLE DIPINDAHKAN KE DALAM DIV UTAMA (SEBELUM PENUTUP DIV) --}}
    <style>
        .table-header-fix th { background-color: #C38E44 !important; color: white !important; }
        .table-fixed-layout { table-layout: fixed; width: 100%; }
        /* Atur lebar kolom agar rapi */
        .table-fixed-layout th:nth-child(1) { width: 35%; }
        .table-fixed-layout th:nth-child(2) { width: 30%; }
        .table-fixed-layout th:nth-child(3) { width: 15%; }
        .table-fixed-layout th:nth-child(4) { width: 20%; }
        
        .text-custom-brown { color: #C38E44; }
        .btn-custom-brown { background-color: #C38E44; border-color: #C38E44; color: white; }
        .btn-custom-brown:hover { background-color: #a8793a; color: white; }
    </style>

</div> {{-- INI PENUTUP DIV ROOT --}}