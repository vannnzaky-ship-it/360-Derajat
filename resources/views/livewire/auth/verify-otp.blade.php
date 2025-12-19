<div>
    <div class="container-fluid g-0">
        <div class="row g-0 min-vh-100">
            <div class="col-lg-5 d-none d-lg-flex justify-content-center align-items-center position-relative bg-custom-brown">
                <img src="/images/logo-polkam.png" alt="Logo" class="position-absolute" style="opacity: 0.1; left: 0; transform: translateX(-50%); height: 80%; width: auto;">
                <img src="/images/logo-polkam2.png" alt="Logo Utama" class="position-relative z-1" style="max-width: 430px;">
            </div>

            <div class="col-12 col-lg-7 d-flex justify-content-center align-items-center py-5">
                <div class="w-100 px-3">
                    <div class="card shadow-lg border-0 rounded-2 bg-custom-form-bg mx-auto" style="max-width: 32rem;">
                        <div class="card-body p-5">
                            
                            {{-- Notifikasi Sukses Kirim Ulang --}}
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show text-center small" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <h2 class="fs-5 card-subtitle mb-2 text-center fw-bold text-custom-dark">VERIFIKASI OTP</h2>
                            <p class="text-center text-muted small mb-4">
                                Kode OTP dikirim ke <strong>{{ $email }}</strong>.<br>
                                Masukkan kode & password baru Anda.
                            </p>

                            <form wire:submit="resetPassword">
                                {{-- Input OTP --}}
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Kode OTP</label>
                                    <input type="text" wire:model="otp" maxlength="6"
                                           class="form-control text-center fw-bold @error('otp') is-invalid @enderror" 
                                           style="font-size: 20px; letter-spacing: 5px; background-color: #EFEAEA; border-color: #d8d8d8;" 
                                           placeholder="XXXXXX">
                                    @error('otp') <div class="invalid-feedback text-center">{{ $message }}</div> @enderror
                                </div>

                                <hr>

                                {{-- Password Baru --}}
                                <div class="mb-3">
                                    <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" 
                                           placeholder="Password Baru" style="background-color: #EFEAEA; border-color: #d8d8d8;">
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- Konfirmasi Password --}}
                                <div class="mb-3">
                                    <input type="password" wire:model="password_confirmation" class="form-control" 
                                           placeholder="Ulangi Password Baru" style="background-color: #EFEAEA; border-color: #d8d8d8;">
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-custom-brown btn-lg fw-bold text-custom-dark">
                                        Ubah Password
                                        {{-- Loading Indicator saat submit --}}
                                        <div wire:loading wire:target="resetPassword" class="spinner-border spinner-border-sm ms-2"></div>
                                    </button>
                                </div>
                            </form>

                            {{-- BAGIAN LOGIKA COUNTDOWN (ALPINE JS) --}}
                            <div class="text-center mt-4" 
                                 x-data="{ 
                                    timer: 60, 
                                    interval: null,
                                    startTimer() {
                                        this.timer = 60;
                                        this.interval = setInterval(() => {
                                            if (this.timer > 0) {
                                                this.timer--;
                                            } else {
                                                clearInterval(this.interval);
                                            }
                                        }, 1000);
                                    }
                                 }" 
                                 x-init="startTimer()">
                                
                                <p class="small text-muted mb-1">Tidak menerima kode?</p>
                                
                                {{-- Tombol ini akan disable jika timer > 0 --}}
                                <button type="button" 
                                        wire:click="resendOtp" 
                                        x-on:click="startTimer()"
                                        :disabled="timer > 0"
                                        class="btn btn-link text-decoration-none small fw-bold p-0"
                                        :class="timer > 0 ? 'text-muted' : 'text-primary'"
                                        style="font-size: 14px;">
                                    
                                    {{-- Teks berubah dinamis --}}
                                    <span x-show="timer > 0">
                                        Kirim Ulang dalam <span x-text="timer"></span> detik
                                    </span>
                                    
                                    <span x-show="timer === 0">
                                        Kirim Ulang Kode OTP
                                    </span>

                                </button>
                                
                                {{-- Loading Indicator saat kirim ulang --}}
                                <div wire:loading wire:target="resendOtp" class="spinner-border spinner-border-sm ms-1 text-primary" role="status"></div>
                            </div>

                            <div class="text-center mt-3 border-top pt-3">
                                <a href="{{ route('password.request') }}" class="text-muted small text-decoration-none">
                                    <i class="bi bi-arrow-left"></i> Ganti Email
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>