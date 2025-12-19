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
                            <h2 class="fs-5 card-subtitle mb-3 text-center fw-bold text-custom-dark">LUPA PASSWORD?</h2>
                            <p class="text-center text-muted small mb-4">Masukkan email Anda. Kami akan mengirimkan kode OTP.</p>

                            <form wire:submit="sendOtp">
                                <div class="mb-3">
                                    <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" 
                                           style="background-color: #EFEAEA; border-color: #d8d8d8;" placeholder="Email Anda">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-custom-brown btn-lg fw-bold text-custom-dark">
                                        Kirim Kode OTP
                                        <div wire:loading wire:target="sendOtp" class="spinner-border spinner-border-sm ms-2"></div>
                                    </button>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('login') }}" class="text-muted small text-decoration-none">Kembali ke Login</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>