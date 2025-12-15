<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-md-5">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            
            <h5 class="fw-bold text-dark mb-4">
                <i class="bi bi-shuffle text-custom-brown me-2"></i> Random Penilai
            </h5>

            <form wire:submit.prevent="generate">
                
                {{-- 1. PILIH SIKLUS (DROPDOWN) --}}
                <div class="mb-4">
                    <label class="form-label small text-muted fw-bold">PILIH SIKLUS SEMESTER</label>
                    <select wire:model.live="siklus_id" class="form-select py-2">
                        @foreach($sikluses as $siklus)
                            <option value="{{ $siklus->id }}">
                                {{ $siklus->tahun_ajaran }} - {{ $siklus->semester }}
                                {{-- Kasih tanda kalau sudah dipakai --}}
                                @if($siklus->penilaianSession) (Data Ada) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- LOGIKA TAMPILAN BERUBAH DISINI --}}
                @if($isSessionExists)
                    
                    {{-- TAMPILAN JIKA DATA SUDAH ADA (READ ONLY / INFO) --}}
                    <div class="alert {{ $isExpired ? 'alert-danger' : 'alert-success' }} text-center rounded-3 p-4">
                        @if($isExpired)
                            <i class="bi bi-x-circle-fill fs-1 d-block mb-2"></i>
                            <h5 class="fw-bold">Masa Penilaian Berakhir</h5>
                            <p class="small mb-0">Siklus ini sudah selesai dan expired.</p>
                        @else
                            <i class="bi bi-check-circle-fill fs-1 d-block mb-2"></i>
                            <h5 class="fw-bold">Penilaian Sedang Berjalan</h5>
                            <p class="small mb-0">Data untuk siklus ini sudah digenerate dan aktif.</p>
                        @endif
                        
                        <hr>
                        <div class="text-start small">
                            <strong>Batas Waktu:</strong><br> 
                            {{ \Carbon\Carbon::parse($batas_waktu)->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                    
                    <div class="d-grid mt-3">
                        <button type="button" class="btn btn-secondary py-2" disabled>
                             Tidak Bisa Generate Ulang
                        </button>
                    </div>

                @else

                    {{-- TAMPILAN JIKA DATA BELUM ADA (FORM MUNCUL) --}}
                    
                    {{-- Batas Waktu --}}
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">BATAS WAKTU (SAMPAI)</label>
                        <input type="datetime-local" wire:model="batas_waktu" class="form-select @error('batas_waktu') is-invalid @enderror">
                        @error('batas_waktu') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    {{-- Jumlah Sampel --}}
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">JUMLAH SAMPEL REKAN</label>
                        <input type="number" wire:model="limit_rekan" class="form-control">
                        <div class="form-text">Jumlah rekan sejawat yang dipilih acak per pegawai.</div>
                    </div>

                    {{-- Filter --}}
                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold d-block">FILTER GENERATE</label>
                        <div class="d-flex gap-3 flex-wrap">
                            @foreach(['Atasan', 'Bawahan', 'Rekan', 'Diri Sendiri'] as $kategori)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $kategori }}" wire:model="pilihan_kategori">
                                    <label class="form-check-label">{{ $kategori }}</label>
                                </div>
                            @endforeach
                        </div>
                        @error('pilihan_kategori') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    {{-- Tombol Generate --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-custom-brown py-2 fw-bold" 
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="generate">Mulai Random Penilai</span>
                            <span wire:loading wire:target="generate">
                                <span class="spinner-border spinner-border-sm me-2"></span> Proses...
                            </span>
                        </button>
                    </div>

                @endif

            </form>
        </div>
    </div>
</div>
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">History Generate</h5>
                </div>
                <div class="card-body px-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Siklus</th>
                                    <th>Tgl Generate</th>
                                    <th>Batas Waktu</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($histories as $history)
                                <tr>
                                    <td>
                                        <span class="fw-bold d-block">{{ $history->siklus->tahun_ajaran }}</span>
                                        <span class="small text-muted">{{ $history->siklus->semester }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($history->tanggal_mulai)->format('d M Y') }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($history->batas_waktu)->format('d M Y H:i') }}
                                        @if(\Carbon\Carbon::now() > $history->batas_waktu)
                                            <br><span class="badge bg-danger">Expired</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($history->status == 'Open')
                                            <span class="badge bg-success">Berlangsung</span>
                                        @else
                                            <span class="badge bg-secondary">Selesai</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada history.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $histories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>