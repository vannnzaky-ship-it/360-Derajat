<div>
    {{-- CSS STYLE TETAP SAMA SEPERTI YANG ANDA PUNYA --}}
    <style>
        /* --- STYLE DARI REFERENSI ANDA --- */
        :root {
            --primary-gold: #c38e44;
            --primary-gold-hover: #a67636;
            --soft-bg: #f8f9fa;
        }
        .text-gold { color: var(--primary-gold) !important; }
        .bg-gold { background-color: var(--primary-gold) !important; }
        
        /* Card Style Minimalis */
        .card-minimal {
            border: 1px solid #eee;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.03);
            transition: transform 0.3s ease;
            background: white;
        }
        /* Style Avatar */
        .avatar-display-container {
            width: 120px;
            height: 120px;
            margin: 0 auto;
            position: relative;
        }
        .avatar-preview {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px dashed var(--primary-gold);
            padding: 4px;
            object-fit: cover;
        }
        
        /* Info Box Style */
        .info-box {
            background-color: #fafafa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border: 1px solid transparent; 
        }
        .info-label {
            font-size: 10px; 
            letter-spacing: 1px;
            text-transform: uppercase;
            display: block;
            color: #6c757d;
        }

        /* --- KHUSUS DARK MODE (GLOBAL) --- */
        [data-bs-theme="dark"] .card-minimal {
            background-color: #212529;
            border-color: #373b3e;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        [data-bs-theme="dark"] .text-dark {
            color: #f8f9fa !important;
        }
        [data-bs-theme="dark"] .info-box {
            background-color: #2b3035;
            border-color: #373b3e;
        }
        [data-bs-theme="dark"] .info-label {
            color: #adb5bd;
        }
        [data-bs-theme="dark"] .table {
            color: #e9ecef;
            border-color: #373b3e;
        }
        [data-bs-theme="dark"] .accordion-button {
            background-color: transparent;
            color: #e9ecef;
        }
        [data-bs-theme="dark"] .accordion-button:not(.collapsed) {
            color: var(--primary-gold);
        }

        /* --- CUSTOM ACCORDION THEME --- */
        .accordion-button:not(.collapsed) {
            color: var(--primary-gold) !important; 
            background-color: rgba(195, 142, 68, 0.15) !important; 
            box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
            border-left: 3px solid var(--primary-gold) !important;
        }
        .accordion-button:focus {
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 0.25rem rgba(195, 142, 68, 0.25);
        }
        .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23c38e44'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e") !important;
        }

        /* ========================================================== */
        /* --- [PERBAIKAN] FIX DARK MODE: AVATAR & BADGE ANGKA --- */
        /* ========================================================== */

        /* 1. Fix Lingkaran Huruf di Dark Mode */
        [data-bs-theme="dark"] .avatar-preview.bg-light {
            background-color: #2b3035 !important; /* Ganti putih jadi abu gelap */
            color: var(--primary-gold) !important; /* Text tetap emas */
            border-color: var(--primary-gold);
        }

        /* 2. Fix Badge Angka (Jumlah Data) di Accordion */
        [data-bs-theme="dark"] .accordion-button .badge {
            background-color: #343a40 !important; /* Background badge jadi gelap */
            color: #e9ecef !important; /* Angka jadi putih/terang */
            border: 1px solid #495057 !important; /* Border biar kelihatan */
        }
    </style>
    <div class="container-fluid px-4 py-4">
        {{-- HEADER --}}
        <div class="mb-4 d-flex align-items-center">
            <a href="{{ route('admin.progress-penilaian', $siklus->id) }}" class="text-decoration-none text-muted fw-bold" style="font-size: 0.9rem;">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar Progress
            </a>
        </div>

        <div class="row g-4">
            {{-- KOLOM KIRI: PROFILE --}}
            <div class="col-lg-4">
                <div class="card card-minimal h-100">
                    <div class="card-body text-center pt-5 pb-4">
                        <div class="avatar-display-container mb-3">
                            @if($user->profile_photo_path)
                                <img src="{{ asset('storage/'.$user->profile_photo_path) }}" class="avatar-preview">
                            @else
                                <div class="avatar-preview d-flex align-items-center justify-content-center bg-light text-gold fw-bold" style="font-size: 3rem;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <h4 class="fw-bold mb-1 text-dark">{{ $user->name }}</h4>
                        <div class="mb-4 mt-2">
                            @if($user->pegawai->jabatans->isNotEmpty())
                                @foreach($user->pegawai->jabatans as $jabatan)
                                    <span class="badge bg-opacity-10 text-gold border border-warning rounded-pill px-3 py-2 mx-1 mb-1" style="background-color: rgba(195, 142, 68, 0.1);">
                                    {{ $jabatan->nama_jabatan }}
                                    </span>
                                @endforeach
                            @else
                                <span class="badge bg-secondary opacity-50 rounded-pill px-3 py-2">Non-Jabatan</span>
                            @endif
                        </div>
                        <div class="text-start mt-3 px-2">
                            <div class="info-box">
                                <small class="info-label">NIP / Identitas</small>
                                <span class="fw-bold text-dark">{{ $user->pegawai->nip ?? '-' }}</span>
                            </div>
                            <div class="info-box">
                                <small class="info-label">Email</small>
                                <span class="fw-bold text-dark text-break">{{ $user->email }}</span>
                            </div>
                            @php
                                $noHp = $user->pegawai->no_hp ?? '-';
                                $waLink = '#';
                                if($noHp != '-' && $noHp != null) {
                                    $cleanHp = preg_replace('/^0/', '62', $noHp);
                                    $waLink = "https://wa.me/" . $cleanHp;
                                }
                            @endphp
                            <div class="info-box d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="info-label">WhatsApp</small>
                                    <span class="fw-bold text-dark">{{ $noHp }}</span>
                                </div>
                                @if($waLink != '#')
                                    <a href="{{ $waLink }}" target="_blank" class="btn btn-sm btn-outline-success rounded-circle border-0" title="Chat WhatsApp"><i class="bi bi-whatsapp fs-5"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="col-lg-8">
                <div class="card card-minimal mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-4" style="width: 60px; height: 60px; color: var(--primary-gold); background-color: rgba(195, 142, 68, 0.1);">
                                <i class="bi bi-pie-chart-fill fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold text-dark mb-1">Total Progress Penilaian</h5>
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <span class="display-6 fw-bold text-gold">{{ $stats['persen'] }}%</span>
                                    <span class="text-muted fw-medium">{{ $stats['sudah'] }} dari {{ $stats['total'] }} Selesai</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-gold" role="progressbar" style="width: {{ $stats['persen'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-minimal">
                    <div class="card-header bg-transparent border-bottom py-3 px-4">
                        <h6 class="mb-0 fw-bold text-dark">Rincian Target Penilaian</h6>
                    </div>
                    <div class="card-body p-0">
                        @if(empty($groupedTargets))
                            <div class="p-5 text-center text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada data penilaian.
                            </div>
                        @else
                            <div class="accordion accordion-flush" id="accordionTargets">
                                {{-- 
                                    KARENA LOGIKA SUDAH DIPERBAIKI DI CONTROLLER, 
                                    KITA BISA LOOPING LANGSUNG TANPA MAPPING 
                                --}}
                                @foreach($groupedTargets as $kategori => $listTarget)
                                    <div class="accordion-item bg-transparent">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ Str::slug($kategori) }}">
                                                {{ $kategori }} 
                                                <span class="badge bg-light text-dark border ms-2 rounded-pill">
                                                    {{ count($listTarget) }}
                                                </span>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ Str::slug($kategori) }}" class="accordion-collapse collapse show" data-bs-parent="#accordionTargets">
                                            <div class="accordion-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table align-middle mb-0">
                                                        <thead class="bg-light opacity-75">
                                                            <tr style="font-size: 0.8rem;">
                                                                <th class="ps-4 py-3 border-bottom-0 text-muted text-uppercase">Nama (Disamarkan)</th>
                                                                <th class="py-3 border-bottom-0 text-muted text-uppercase">Jabatan</th>
                                                                <th class="text-center py-3 border-bottom-0 text-muted text-uppercase">Status</th>
                                                                <th class="text-end pe-4 py-3 border-bottom-0 text-muted text-uppercase">Waktu</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($listTarget as $target)
                                                            <tr>
                                                                <td class="ps-4 py-3">
                                                                    {{-- TAMPILKAN NAMA SENSOR SESUAI PERMINTAAN --}}
                                                                    <span class="fw-medium text-dark">{{ $target['nama_sensor'] }}</span>
                                                                </td>
                                                                <td class="py-3">
                                                                    <span class="text-muted small">{{ $target['jabatan'] }}</span>
                                                                </td>
                                                                <td class="text-center py-3">
                                                                    @if($target['status'] == 'Sudah')
                                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Selesai</span>
                                                                    @else
                                                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">Belum</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-end pe-4 py-3 small text-muted font-monospace">
                                                                    {{ $target['tanggal'] }}
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>