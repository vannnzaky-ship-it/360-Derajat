<div>
    <style>
        :root { --theme-brown: #c59d5f; --theme-brown-dark: #a68b53; }
        .text-theme { color: var(--theme-brown) !important; }
        .bg-theme { background-color: var(--theme-brown) !important; }
        .card-modern { border: none; border-left: 5px solid var(--theme-brown); border-radius: 12px; background: #fff; transition: all 0.3s ease; }
        .btn-theme { background-color: var(--theme-brown); color: #fff; border: none; transition: 0.3s; }
        .btn-theme:hover { background-color: var(--theme-brown-dark); color: #fff; transform: translateY(-2px); }
        .btn-disabled { background-color: #e9ecef; color: #6c757d; border: 1px solid #dee2e6; cursor: not-allowed; }
        .timer-box { background: #fdf8f3; border: 1px solid #f1e4d1; color: var(--theme-brown-dark); padding: 10px 20px; border-radius: 10px; display: inline-flex; gap: 15px; }
        .timer-unit { text-align: center; }
        .timer-value { display: block; font-size: 1.2rem; font-weight: bold; line-height: 1; }
        .timer-label { font-size: 0.7rem; text-transform: uppercase; }
    </style>

    <div class="container-fluid p-4">
        <div class="mb-4">
            <h1 class="h3 fw-bold text-dark">Selamat Datang, <span class="text-theme">{{ $namaUser }}</span>!</h1>
            <p class="text-muted">Selamat beraktivitas di Sistem Penilaian 360 Derajat.</p>
        </div>

        <div class="card card-modern shadow-sm mb-5">
            <div class="card-body p-4">
                @if(!$adaSesi)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title fw-bold m-0 text-secondary"><i class="bi bi-clock-history me-2"></i>Status Penilaian</h5>
                        <span class="badge bg-secondary rounded-pill">Non-Aktif / Berakhir</span>
                    </div>
                    <div class="alert alert-warning border-0 shadow-sm rounded-3 mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Siklus untuk semester ini tidak ada atau sudah melewati batas waktu. Silahkan kembali lagi saat ada pemberitahuan dari <strong>Admin/BPM</strong>.
                    </div>
                @else
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                        <div>
                            <h5 class="card-title fw-bold m-0 text-dark">Progress Pengisian Penilaian</h5>
                            <small class="text-muted">Selesaikan sebelum waktu habis.</small>
                        </div>
                        <div class="timer-box shadow-sm" x-data="timerData('{{ $deadline }}')" x-init="start()">
                            <div class="timer-unit"><span class="timer-value" x-text="days">00</span><span class="timer-label">Hari</span></div>
                            <div class="timer-unit"><span class="timer-value" x-text="hours">00</span><span class="timer-label">Jam</span></div>
                            <div class="timer-unit"><span class="timer-value" x-text="minutes">00</span><span class="timer-label">Menit</span></div>
                            <div class="timer-unit"><span class="timer-value" x-text="seconds">00</span><span class="timer-label">Detik</span></div>
                        </div>
                    </div>
                    <div class="progress mb-3" style="height: 25px; border-radius: 20px; background-color: #f0f0f0; overflow: hidden;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated {{ $persentase == 100 ? 'bg-success' : 'bg-theme' }}" 
                             style="width: {{ $persentase }}%;">{{ $persentase }}%</div>
                    </div>
                    <p class="text-muted small">Anda telah menyelesaikan <strong>{{ $sudahSelesai }}</strong> dari <strong>{{ $totalTugas }}</strong> formulir.</p>
                @endif
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-center gap-4 py-2">
            <div class="card card-modern card-menu text-center shadow-sm h-100" style="width: 18rem;">
                <div class="card-body d-flex flex-column justify-content-center p-4">
                    <i class="bi bi-ui-checks display-4 {{ $adaSesi ? 'text-theme' : 'text-secondary opacity-50' }} mb-3"></i>
                    <h5 class="card-title fw-bold mt-2 {{ $adaSesi ? 'text-dark' : 'text-secondary' }}">Mulai Menilai</h5>
                    <p class="card-text text-muted small mb-4">Isi formulir penilaian untuk rekan, atasan, dan diri sendiri.</p>
                    @if($adaSesi)
                        <a href="{{ url('karyawan/penilaian') }}" class="btn btn-theme rounded-pill w-100 mt-auto">Mulai</a>
                    @else
                        <button class="btn btn-disabled rounded-pill w-100 mt-auto" disabled>Tutup</button>
                    @endif
                </div>
            </div>
            <div class="card card-modern card-menu text-center shadow-sm h-100" style="width: 18rem;">
                <div class="card-body d-flex flex-column justify-content-center p-4">
                    <i class="bi bi-clipboard-data display-4 text-theme mb-3"></i>
                    <h5 class="card-title fw-bold mt-2">Lihat Raport</h5>
                    <p class="card-text text-muted small mb-4">Lihat hasil akhir penilaian kinerja Anda semester ini.</p>
                    <a href="{{ url('karyawan/raport') }}" class="btn btn-outline-theme rounded-pill w-100 mt-auto">Lihat</a>
                </div>
            </div>
        </div>
    </div>
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