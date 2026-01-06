<div class="container-fluid p-4">
    
    {{-- CSS CUSTOM: DARK MODE & RESPONSIVE TWEAKS --}}
    <style>
        :root { --primary-gold: #c38e44; }
        .text-custom { color: var(--primary-gold) !important; }
        .bg-custom { background-color: var(--primary-gold) !important; color: white; }
        
        /* Card & Layout */
        .card-minimal { border: 1px solid rgba(0,0,0,0.05); border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); background: white; }
        .info-box { background-color: #f8f9fa; border-radius: 8px; padding: 12px; margin-bottom: 8px; }
        .info-label { font-size: 0.7rem; text-transform: uppercase; color: #6c757d; font-weight: 700; letter-spacing: 0.5px; display: block; margin-bottom: 2px; }
        
        /* Avatar */
        .avatar-display-container { width: 100px; height: 100px; margin: 0 auto; position: relative; }
        .avatar-preview { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 3px solid #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .avatar-initial { width: 100%; height: 100%; border-radius: 50%; background-color: rgba(195, 142, 68, 0.1); color: var(--primary-gold); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; border: 3px solid #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }

        /* Accordion Custom */
        .accordion-button:not(.collapsed) { background-color: rgba(195, 142, 68, 0.08); color: var(--primary-gold); box-shadow: none; }
        .accordion-button:focus { box-shadow: none; border-color: rgba(0,0,0,0.1); }
        
        /* --- DARK MODE --- */
        [data-bs-theme="dark"] .card-minimal { background-color: #212529; border-color: #373b3e; }
        [data-bs-theme="dark"] .text-dark { color: #fff !important; }
        [data-bs-theme="dark"] .info-box { background-color: #2b3035; }
        [data-bs-theme="dark"] .bg-light { background-color: #2c3034 !important; }
        [data-bs-theme="dark"] .accordion-button { color: #e9ecef; }
        [data-bs-theme="dark"] .accordion-button:not(.collapsed) { background-color: rgba(195, 142, 68, 0.2); color: #e0b675; }

        /* --- RESPONSIVE TABLE FIX --- */
        @media (max-width: 767px) {
            /* Sembunyikan Header Tabel */
            .table thead { display: none; }
            
            /* Ubah Baris jadi Block */
            .table tbody tr {
                display: block;
                border-bottom: 1px solid rgba(0,0,0,0.05);
                padding: 10px 15px;
            }
            .table tbody tr:last-child { border-bottom: none; }

            /* Atur Sel */
            .table tbody td {
                display: block;
                border: none !important;
                padding: 2px 0;
                width: 100%;
            }

            /* Kolom 1: Nama (Judul) */
            .table tbody td:nth-child(1) {
                font-weight: bold; font-size: 0.95rem; margin-bottom: 2px;
            }
            
            /* Kolom 2: Jabatan */
            .table tbody td:nth-child(2) {
                font-size: 0.85rem; color: #6c757d; margin-bottom: 5px;
            }
            
            /* Kolom 3 & 4: Status & Waktu (Sebelah-sebelahan) */
            .table tbody td:nth-child(3),
            .table tbody td:nth-child(4) {
                display: inline-block; width: auto; font-size: 0.8rem;
            }
            .table tbody td:nth-child(4) { float: right; }
        }
    </style>

    {{-- HEADER --}}
    <div class="mb-4">
        <a href="{{ route('admin.progress-penilaian', $siklus->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm border-0">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        
        {{-- KOLOM KIRI: PROFIL PEGAWAI --}}
        <div class="col-lg-4">
            <div class="card card-minimal h-100">
                <div class="card-body text-center pt-5 pb-4">
                    {{-- Avatar --}}
                    <div class="avatar-display-container mb-3">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/'.$user->profile_photo_path) }}" class="avatar-preview">
                        @else
                            <div class="avatar-initial">{{ substr($user->name, 0, 1) }}</div>
                        @endif
                    </div>
                    
                    {{-- Nama & Jabatan --}}
                    <h5 class="fw-bold mb-1 text-dark">{{ $user->name }}</h5>
                    <div class="mb-4">
                        @if($user->pegawai->jabatans->isNotEmpty())
                            @foreach($user->pegawai->jabatans as $jabatan)
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3 py-1 mb-1">
                                    {{ $jabatan->nama_jabatan }}
                                </span>
                            @endforeach
                        @else
                            <span class="badge bg-secondary opacity-50 rounded-pill px-3 py-1">Non-Jabatan</span>
                        @endif
                    </div>

                    {{-- Detail Info --}}
                    <div class="text-start px-2">
                        <div class="info-box">
                            <span class="info-label">NIP / Identitas</span>
                            <div class="fw-bold text-dark">{{ $user->pegawai->nip ?? '-' }}</div>
                        </div>
                        <div class="info-box">
                            <span class="info-label">Email</span>
                            <div class="fw-bold text-dark text-break">{{ $user->email }}</div>
                        </div>
                        @php
                            $noHp = $user->pegawai->no_hp ?? '-';
                            $waLink = ($noHp != '-' && $noHp != null) ? "https://wa.me/" . preg_replace('/^0/', '62', $noHp) : '#';
                        @endphp
                        <div class="info-box d-flex justify-content-between align-items-center">
                            <div>
                                <span class="info-label">WhatsApp</span>
                                <div class="fw-bold text-dark">{{ $noHp }}</div>
                            </div>
                            @if($waLink != '#')
                                <a href="{{ $waLink }}" target="_blank" class="btn btn-sm btn-success rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: STATISTIK & RINCIAN --}}
        <div class="col-lg-8">
            
            {{-- KARTU STATISTIK --}}
            <div class="card card-minimal mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px; color: var(--primary-gold); background-color: rgba(195, 142, 68, 0.1);">
                            <i class="bi bi-pie-chart-fill fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold text-dark mb-1">Total Progress Penilaian</h6>
                            <div class="d-flex justify-content-between align-items-end mb-1">
                                <span class="display-6 fw-bold text-custom" style="font-size: 1.5rem;">{{ $stats['persen'] }}%</span>
                                <span class="text-muted small fw-medium">{{ $stats['sudah'] }} dari {{ $stats['total'] }} Selesai</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-custom" role="progressbar" style="width: {{ $stats['persen'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DAFTAR TARGET PENILAIAN --}}
            <div class="card card-minimal">
                <div class="card-header bg-transparent border-bottom py-3 px-4">
                    <h6 class="mb-0 fw-bold text-dark">Rincian Target Penilaian</h6>
                </div>
                <div class="card-body p-0">
                    @if(empty($groupedTargets))
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-clipboard-x fs-1 d-block mb-2 opacity-50"></i>
                            Belum ada data penilaian.
                        </div>
                    @else
                        <div class="accordion accordion-flush" id="accordionTargets">
                            @foreach($groupedTargets as $kategori => $listTarget)
                                <div class="accordion-item bg-transparent">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ Str::slug($kategori) }}">
                                            {{ $kategori }} 
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary ms-2 rounded-pill">{{ count($listTarget) }}</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ Str::slug($kategori) }}" class="accordion-collapse collapse show" data-bs-parent="#accordionTargets">
                                        <div class="accordion-body p-0">
                                            <div class=""> {{-- Wrapper tabel tanpa responsive class agar card view css jalan --}}
                                                <table class="table mb-0">
                                                    <thead class="bg-light opacity-75">
                                                        <tr style="font-size: 0.8rem;">
                                                            <th class="ps-4 py-3 text-muted text-uppercase border-bottom-0">Nama</th>
                                                            <th class="py-3 text-muted text-uppercase border-bottom-0">Jabatan</th>
                                                            <th class="text-center py-3 text-muted text-uppercase border-bottom-0">Status</th>
                                                            <th class="text-end pe-4 py-3 text-muted text-uppercase border-bottom-0">Waktu</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($listTarget as $target)
                                                            <tr>
                                                                <td class="ps-4 py-3">
                                                                    {{ $target['nama_sensor'] }}
                                                                </td>
                                                                <td class="py-3">
                                                                    {{ $target['jabatan'] }}
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