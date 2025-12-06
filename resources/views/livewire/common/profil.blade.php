<div>
    <style>
        /* --- STYLE DASAR (Light Mode) --- */
        :root {
            --primary-gold: #c38e44;
            --primary-gold-hover: #a67636;
            --soft-bg: #f8f9fa;
        }
        .text-gold { color: var(--primary-gold) !important; }
        .bg-gold { background-color: var(--primary-gold) !important; }
        
        .btn-gold {
            background-color: var(--primary-gold);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-gold:hover {
            background-color: var(--primary-gold-hover);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(195, 142, 68, 0.3);
        }

        .card-minimal {
            border: 1px solid #eee;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.03);
            transition: transform 0.3s ease;
            background: white;
        }
        .card-accent-top { border-top: 4px solid var(--primary-gold); }

        /* Avatar Upload Style */
        .avatar-upload-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto;
        }
        .avatar-preview {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px dashed var(--primary-gold);
            padding: 4px;
            object-fit: cover;
        }
        .avatar-edit-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: var(--primary-gold);
            color: white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            transition: all 0.2s;
        }
        .avatar-edit-btn:hover {
            background: var(--primary-gold-hover);
            transform: scale(1.1);
        }
        .delete-photo-btn {
            position: absolute;
            bottom: 5px;
            left: 5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            transition: all 0.2s;
        }
        
        .info-box {
            background-color: #fafafa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
        }

        /* Form Styles */
        .form-control:focus {
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 0.25rem rgba(195, 142, 68, 0.25);
        }
        .input-group-text { background-color: #fff; border-right: none; color: #999; }
        .form-control { border-left: none; padding-left: 0; }


        /* --- KHUSUS DARK MODE --- */
        [data-bs-theme="dark"] .card-minimal {
            background-color: #212529; /* Card Gelap */
            border-color: #373b3e;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        [data-bs-theme="dark"] .text-dark {
            color: #f8f9fa !important; /* Text hitam jadi putih */
        }
        [data-bs-theme="dark"] .info-box {
            background-color: #2b3035; /* Kotak info jadi abu gelap */
        }
        [data-bs-theme="dark"] .input-group-text {
            background-color: #2b3035;
            border-color: #495057;
            color: #adb5bd;
        }
        [data-bs-theme="dark"] .form-control {
            background-color: #2b3035;
            border-color: #495057;
            color: #fff;
        }
        [data-bs-theme="dark"] .form-control::placeholder {
            color: #6c757d;
        }
        [data-bs-theme="dark"] .avatar-edit-btn, 
        [data-bs-theme="dark"] .delete-photo-btn {
            border-color: #212529; /* Border tombol jadi gelap */
        }
        [data-bs-theme="dark"] .alert-success {
            background-color: #2b3035 !important;
            color: #fff;
            border-color: #373b3e;
        }
    </style>

    <div class="container-fluid px-4 py-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">Pengaturan Akun</h2>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" 
                 style="border-left: 5px solid var(--primary-gold) !important;" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill text-gold fs-4 me-3"></i>
                    <div>
                        <strong class="d-block text-dark">Berhasil!</strong>
                        <span class="text-muted">{{ session('message') }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            
            {{-- KOLOM KIRI: FOTO & INFO --}}
            <div class="col-lg-4">
                <div class="card card-minimal h-100">
                    <div class="card-body text-center pt-5 pb-4">
                        
                        {{-- Bagian Upload Foto --}}
                        <div class="avatar-upload-container mb-3">
                            @if ($photo) 
                                <img src="{{ $photo->temporaryUrl() }}" class="avatar-preview">
                            @elseif ($existingPhoto)
                                <img src="{{ asset('storage/' . $existingPhoto) }}" class="avatar-preview">
                            @else
                                <img src="/images/avatar.jpg" class="avatar-preview">
                            @endif

                            <label for="photoUpload" class="avatar-edit-btn" title="Ganti Foto">
                                <i class="bi bi-camera-fill" style="font-size: 14px;"></i>
                            </label>
                            <input type="file" id="photoUpload" wire:model="photo" class="d-none" accept="image/*">

                            @if ($existingPhoto && !$photo)
                                <button wire:click="deletePhoto" 
                                        wire:confirm="Apakah Anda yakin ingin menghapus foto profil ini?"
                                        class="delete-photo-btn" title="Hapus Foto">
                                    <i class="bi bi-trash-fill" style="font-size: 14px;"></i>
                                </button>
                            @endif

                            <div wire:loading wire:target="photo" class="position-absolute top-50 start-50 translate-middle">
                                <div class="spinner-border text-gold" role="status" style="width: 1.5rem; height: 1.5rem;"></div>
                            </div>
                        </div>

                        {{-- Tombol Simpan Foto --}}
                        @if ($photo)
                            <div class="mb-4 fade-in">
                                <button wire:click="savePhoto" class="btn btn-gold btn-sm w-100 mb-2">
                                    <i class="bi bi-check-lg me-1"></i> Simpan Foto
                                </button>
                                <button wire:click="$set('photo', null)" class="btn btn-outline-secondary btn-sm w-100 border">
                                    Batal
                                </button>
                            </div>
                        @endif

                        {{-- Info User --}}
                        <h4 class="fw-bold mb-1 text-dark">{{ $name }}</h4>

                        @php
                            $urlSegment = request()->segment(1);
                            $displayRole = match($urlSegment) {
                                'superadmin' => 'Super Admin',
                                'admin'      => 'Administrator',
                                'peninjau'   => 'Peninjau',
                                'karyawan'   => 'Karyawan',
                                default      => auth()->user()->roles->first()->label ?? 'Pengguna'
                            };
                        @endphp

                        {{-- Badge Role (Update class agar adaptif) --}}
                        <span class="badge bg-opacity-10 text-gold border border-warning rounded-pill px-3 py-2 mb-4 profile-role" 
                              style="background-color: rgba(195, 142, 68, 0.1);">
                            {{ $displayRole }}
                        </span>

                        <div class="text-start mt-3 px-2">
                            {{-- Gunakan class 'info-box' agar bisa di-style dark mode --}}
                            <div class="info-box">
                                <small class="text-muted d-block text-uppercase" style="font-size: 10px; letter-spacing: 1px;">NIP / User ID</small>
                                <span class="fw-bold text-dark">{{ $nip }}</span>
                            </div>
                            <div class="info-box">
                                <small class="text-muted d-block text-uppercase" style="font-size: 10px; letter-spacing: 1px;">Jabatan</small>
                                <span class="fw-bold text-dark">{{ $jabatan }}</span>
                            </div>
                            <div class="info-box">
                                <small class="text-muted d-block text-uppercase" style="font-size: 10px; letter-spacing: 1px;">Email</small>
                                <span class="fw-bold text-dark text-break">{{ $email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: GANTI PASSWORD --}}
            <div class="col-lg-8">
                <div class="card card-minimal card-accent-top h-100">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 45px; height: 45px; color: var(--primary-gold); background-color: rgba(195, 142, 68, 0.1);">
                                <i class="bi bi-lock-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">Ganti Password</h5>
                                <small class="text-muted">Perbarui kata sandi Anda secara berkala.</small>
                            </div>
                        </div>

                        <form wire:submit.prevent="updatePassword">
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">PASSWORD SAAT INI</label>
                                <div class="input-group">
                                    <span class="input-group-text border-end-0 rounded-start-3 ps-3"><i class="bi bi-key"></i></span>
                                    <input type="password" wire:model="current_password" class="form-control border-start-0 rounded-end-3 ps-2 py-2 @error('current_password') is-invalid @enderror" placeholder="Password Saat Ini">
                                </div>
                                @error('current_password') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label text-muted small fw-bold">PASSWORD BARU</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0 rounded-start-3 ps-3"><i class="bi bi-shield-lock"></i></span>
                                        <input type="password" wire:model="new_password" class="form-control border-start-0 rounded-end-3 ps-2 py-2 @error('new_password') is-invalid @enderror" placeholder="Min. 6 karakter">
                                    </div>
                                    @error('new_password') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label text-muted small fw-bold">KONFIRMASI PASSWORD</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0 rounded-start-3 ps-3"><i class="bi bi-check2-circle"></i></span>
                                        <input type="password" wire:model="new_password_confirmation" class="form-control border-start-0 rounded-end-3 ps-2 py-2" placeholder="Konfirmasi Password">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-gold">
                                    <span wire:loading.remove wire:target="updatePassword">Simpan Password Baru</span>
                                    <span wire:loading wire:target="updatePassword">
                                        <span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>