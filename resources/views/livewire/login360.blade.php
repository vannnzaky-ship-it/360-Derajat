<div>
    <div class="container-fluid g-0">
        <div class="row g-0 min-vh-100">
            
            <div class="col-lg-5 d-none d-lg-flex justify-content-center align-items-center position-relative bg-custom-brown">
    
    {{-- LOGO BACKGROUND YANG DIUBAH --}}
    <img src="/images/logo-polkam.png" 
         alt="Logo Background" 
         class="position-absolute" 
         style="
            opacity: 0.1; {{-- Pastikan opacity berlaku --}}
            left: 0; 
            transform: translateX(-50%); 
            height : 80%;
            max-width: none; {{-- Penting agar tidak terpotong --}}
            object-fit: cover; {{-- Memastikan gambar mengisi area tanpa terdistorsi --}}
            width: auto; {{-- Biarkan width auto agar tinggi yang mengontrol ukuran --}}
         ">
    
    {{-- LOGO UTAMA BIARKAN SEPERTI SEMULA --}}
    <img src="/images/logo-polkam2.png" 
         alt="Logo Politeknik Kampar" 
         class="position-relative z-1" 
         style="max-width: 430px;">
</div>

            <div class="col-12 col-lg-7 d-flex justify-content-center align-items-center py-5">
                
                <div class="w-100 px-3">

                    <h1 class="h5 text-center fw-bold text-custom-dark mb-4">
                        Sistem Penilaian dan Evaluasi <br> Kinerja Karyawan Metode 360 Degree
                    </h1>

                    <div class="card shadow-lg border-0 rounded-2 bg-custom-form-bg mx-auto" style="max-width: 32rem;">
                        <div class="card-body p-5">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            <h2 class="fs-5 card-subtitle mb-4 text-center fw-bold text-custom-dark">
                                SILAHKAN LOGIN
                            </h2>
                            <form wire:submit="login">
                                
                                <div class="mb-3">
                                    <input type="text" 
                                           wire:model="email"
                                           class="form-control @error('email') is-invalid @enderror" style="font-size: 16px; background-color: #EFEAEA; border-color: #d8d8d8;" 
                                           placeholder="Username / Email"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="password" 
                                            wire:model="password"
                                            id="password-field"  class="form-control @error('password') is-invalid @enderror" 
                                            style="font-size: 16px; background-color: #EFEAEA; border-color: #d8d8d8; border-right: 0;" 
                                            placeholder="Password"
                                            required>
                                        
                                        <button class="btn" type="button" id="togglePasswordBtn" 
                                                style="background-color: #EFEAEA; border: 1px solid #d8d8d8; border-left: 0;">
                                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>
                                    
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-custom-brown btn-lg fw-bold text-custom-dark" style="font-size: 16px;" >
                                        Masuk
                                        <div wire:loading wire:target="login" class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </button>
                                </div>

                                <div class="text-center mt-3">
                                    <a href="{{ route('password.request') }}" class="text-muted text-decoration-none small">
                                        Lupa Password ?
                                    </a>
                                </div>

                            </form>

                        </div>
                    </div> </div> </div> </div> </div> </div>