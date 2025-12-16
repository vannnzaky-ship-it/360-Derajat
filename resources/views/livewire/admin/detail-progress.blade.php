<div class="container-fluid p-4">

    {{-- HEADER KEMBALI --}}
    <div class="mb-4">
        <a href="{{ route('admin.progress-penilaian', $siklus->id) }}" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Progress
        </a>
    </div>

    <div class="row g-4">
        
        {{-- KOLOM KIRI: PROFILE PEGAWAI --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body p-4 text-center">
                    {{-- Foto / Avatar --}}
                    @if($user->profile_photo_path)
                        <img src="{{ asset('storage/'.$user->profile_photo_path) }}" class="rounded-circle mb-3 shadow-sm" width="120" height="120" style="object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 120px; height: 120px; font-size: 3rem; color: #c38e44; font-weight: bold;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif

                    <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
                    <p class="text-muted small mb-3">{{ $user->pegawai->nip ?? '-' }}</p>
                    
                    {{-- Badge Jabatan --}}
                    <div class="mb-4">
                        <span class="badge bg-custom-brown rounded-pill px-3 py-2 fw-normal">
                            {{ $user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-' }}
                        </span>
                    </div>

                    <hr>

                    {{-- Kontak Info --}}
                    <div class="text-start px-2">
                        <p class="mb-2 small text-muted"><i class="bi bi-envelope me-2"></i> {{ $user->email }}</p>
                        
                        @php
                            $noHp = $user->pegawai->no_hp ?? '-';
                            // Format nomor WA: Ganti 08 jadi 628
                            $waLink = '#';
                            if($noHp != '-' && $noHp != null) {
                                if(substr($noHp, 0, 1) == '0') {
                                    $waNumber = '62' . substr($noHp, 1);
                                } else {
                                    $waNumber = $noHp;
                                }
                                $waLink = "https://wa.me/" . $waNumber;
                            }
                        @endphp

                        <p class="mb-3 small text-muted">
                            <i class="bi bi-telephone me-2"></i> {{ $noHp }}
                        </p>

                        @if($waLink != '#')
                            <a href="{{ $waLink }}" target="_blank" class="btn btn-success w-100 rounded-pill btn-sm">
                                <i class="bi bi-whatsapp me-2"></i> Hubungi WhatsApp
                            </a>
                        @else
                            <button class="btn btn-secondary w-100 rounded-pill btn-sm" disabled>
                                <i class="bi bi-whatsapp me-2"></i> No HP Tidak Ada
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: STATISTIK & DAFTAR TARGET --}}
        <div class="col-lg-8">
            
            {{-- KARTU STATISTIK PROGRESS --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4 bg-white">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">Total Progress Menilai</h6>
                    <div class="d-flex align-items-center mb-2">
                        <h2 class="display-4 fw-bold text-custom-brown mb-0 me-3">{{ $stats['persen'] }}%</h2>
                        <div class="flex-grow-1">
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-custom-brown" role="progressbar" style="width: {{ $stats['persen'] }}%"></div>
                            </div>
                            <small class="text-muted">Sudah menilai {{ $stats['sudah'] }} dari {{ $stats['total'] }} target.</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LIST TARGET PENILAIAN --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="mb-0 fw-bold">Rincian Target Penilaian</h6>
                </div>
                <div class="card-body p-0">
                    
                    {{-- Accordion untuk Grouping --}}
                    <div class="accordion accordion-flush" id="accordionTargets">
                        @foreach(['Atasan', 'Bawahan', 'Rekan', 'Diri Sendiri'] as $kategori)
                            @if(isset($groupedTargets[$kategori]))
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-bold text-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ Str::slug($kategori) }}">
                                            {{ $kategori }} 
                                            <span class="badge bg-light text-dark border ms-2">
                                                {{ count($groupedTargets[$kategori]) }} Orang
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ Str::slug($kategori) }}" class="accordion-collapse collapse" data-bs-parent="#accordionTargets">
                                        <div class="accordion-body p-0">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="bg-light small">
                                                    <tr>
                                                        <th class="ps-4">Nama (Disamarkan)</th>
                                                        <th>Jabatan Target</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-end pe-4">Waktu Menilai</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($groupedTargets[$kategori] as $target)
                                                    <tr>
                                                        <td class="ps-4 text-muted fst-italic">
                                                            <i class="bi bi-shield-lock-fill me-1"></i> {{ $target['nama_sensor'] }}
                                                        </td>
                                                        <td class="fw-medium text-dark">
                                                            {{ $target['jabatan'] }}
                                                        </td>
                                                        <td class="text-center">
                                                            @if($target['status'] == 'Sudah')
                                                                <span class="badge bg-success rounded-pill px-3">Selesai</span>
                                                            @else
                                                                <span class="badge bg-secondary rounded-pill px-3">Belum</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end pe-4 small text-muted">
                                                            {{ $target['tanggal'] }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    @if(empty($groupedTargets))
                        <div class="p-5 text-center text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                            Belum ada alokasi penilaian untuk pegawai ini.
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>