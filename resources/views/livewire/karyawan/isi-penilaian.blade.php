<div class="container p-4" style="max-width: 900px;">
    
    <style>
        :root { --polkam-gold: #c38e44; --polkam-gold-hover: #a57635; --polkam-soft: #fdf8f3; }
        .text-gold { color: var(--polkam-gold) !important; }
        .bg-soft-gold { background-color: var(--polkam-soft) !important; }
        .btn-gold { background-color: var(--polkam-gold); color: white; border: none; font-weight: 600; transition: 0.3s; }
        .btn-gold:hover { background-color: var(--polkam-gold-hover); color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(195,142,68,0.3); }
        .card-elegant { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.05); transition: all 0.3s ease; }
        .rating-box { position: relative; }
        .rating-input { position: absolute; opacity: 0; width: 0; height: 0; }
        .rating-label { display: flex; align-items: center; justify-content: center; width: 45px; height: 45px; border-radius: 50%; border: 2px solid #e9ecef; background-color: white; color: #6c757d; font-weight: bold; cursor: pointer; transition: all 0.2s; }
        .rating-label:hover { border-color: var(--polkam-gold); color: var(--polkam-gold); background-color: var(--polkam-soft); }
        .rating-input:checked + .rating-label { background-color: var(--polkam-gold); border-color: var(--polkam-gold); color: white; transform: scale(1.1); box-shadow: 0 4px 10px rgba(195,142,68,0.4); }
        .timer-mini { background: #fdf8f3; border: 1px solid #f1e4d1; padding: 6px 15px; border-radius: 50px; color: #a57635; font-weight: bold; font-size: 0.9rem; }
    </style>

    {{-- Header Kartu --}}
    <div class="card card-elegant rounded-4 mb-4">
        <div class="card-body p-4 bg-white rounded-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <a href="{{ route('karyawan.penilaian') }}" class="btn btn-light rounded-circle me-3 shadow-sm text-secondary" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-arrow-left fs-5"></i>
                    </a>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Form Evaluasi Kinerja</h5>
                        <div class="text-muted small">Menilai: <strong class="text-gold">{{ $alokasi->target->name }}</strong> <span class="badge bg-secondary opacity-75 ms-1">{{ $alokasi->sebagai }}</span></div>
                    </div>
                </div>
                
                {{-- TIMER MINI --}}
                @if(isset($deadline))
                <div class="timer-mini shadow-sm" x-data="timerData('{{ $deadline }}')" x-init="start()">
                    <i class="bi bi-clock-history me-1"></i> <span x-text="days + ':' + hours + ':' + minutes + ':' + seconds"></span>
                </div>
                @endif
            </div>

            <div class="alert bg-soft-gold border-0 d-flex align-items-center mb-0 text-dark rounded-3">
                <div class="me-3 text-gold"><i class="bi bi-shield-check fs-2"></i></div>
                <div>
                    <h6 class="fw-bold mb-1">Instruksi Penilaian</h6>
                    <p class="small mb-0 opacity-75">Skala 1 (Sangat Buruk) hingga 5 (Sangat Baik). Semua pertanyaan wajib diisi.</p>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="simpan">
        @foreach($kompetensis as $kompetensi)
            @if($kompetensi->pertanyaans->count() > 0)
                <div class="card card-elegant rounded-4 mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center">
                        <div class="rounded-circle bg-soft-gold text-gold d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;"><i class="bi bi-star-fill"></i></div>
                        <h6 class="mb-0 fw-bold text-dark">{{ $kompetensi->nama_kompetensi }}</h6>
                    </div>

                    <div class="card-body p-0">
                        @foreach($kompetensi->pertanyaans as $index => $tanya)
                            <div class="p-4 border-bottom {{ $loop->last ? 'border-0' : '' }}">
                                <div class="row align-items-center">
                                    <div class="col-md-7 mb-3 mb-md-0">
                                        <p class="fw-medium mb-1 text-dark">{{ $tanya->teks_pertanyaan }}</p>
                                        {{-- VALIDASI ERROR PER PERTANYAAN --}}
                                        @error('jawaban.'.$tanya->id) 
                                            <span class="text-danger small animate__animated animate__fadeIn">
                                                <i class="bi bi-exclamation-circle me-1"></i> Wajib diisi
                                            </span> 
                                        @enderror
                                    </div>

                                    <div class="col-md-5 d-flex flex-column align-items-md-end">
                                        <div style="width: 100%; max-width: 260px;">
                                            <div class="d-flex justify-content-between gap-2">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <div class="rating-box">
                                                        <input type="radio" class="rating-input" name="q_{{ $tanya->id }}" id="q_{{ $tanya->id }}_{{ $i }}" value="{{ $i }}" wire:model="jawaban.{{ $tanya->id }}">
                                                        <label class="rating-label shadow-sm" for="q_{{ $tanya->id }}_{{ $i }}">{{ $i }}</label>
                                                    </div>
                                                @endfor
                                            </div>
                                            <div class="d-flex justify-content-between px-1 mt-2 small text-muted" style="font-size: 0.7rem;"><span>Buruk</span><span>Baik</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Tombol Submit --}}
        <div class="d-grid pb-5 pt-2">
            <button type="submit" class="btn btn-gold btn-lg fw-bold rounded-pill py-3 shadow">
                <i class="bi bi-send-fill me-2"></i> Kirim Hasil Penilaian
            </button>
            <div class="text-center mt-3 text-muted small">
                <i class="bi bi-lock-fill me-1"></i> Penilaian tidak dapat diubah setelah dikirim.
            </div>
        </div>
    </form>

    <script>
        function timerData(deadline) {
            return {
                days: '0', hours: '00', minutes: '00', seconds: '00',
                endTime: new Date(deadline).getTime(),
                start() { this.update(); setInterval(() => this.update(), 1000); },
                update() {
                    let diff = this.endTime - new Date().getTime();
                    if (diff <= 0) { this.days = '0'; this.hours = '00'; this.minutes = '00'; this.seconds = '00'; return; }
                    this.days = Math.floor(diff / 86400000);
                    this.hours = Math.floor((diff % 86400000) / 3600000).toString().padStart(2, '0');
                    this.minutes = Math.floor((diff % 3600000) / 60000).toString().padStart(2, '0');
                    this.seconds = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
                }
            }
        }
    </script>
</div>