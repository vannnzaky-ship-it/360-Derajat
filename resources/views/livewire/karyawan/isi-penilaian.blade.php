<div class="container p-4" style="max-width: 900px;">
    
    {{-- Header Kartu --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 bg-white rounded-4">
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('karyawan.penilaian') }}" class="btn btn-light rounded-circle me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="fw-bold mb-1">Evaluasi Kinerja</h5>
                    <div class="text-muted small">
                        Menilai: <strong class="text-custom-brown">{{ $alokasi->target->name }}</strong> 
                        sebagai <span class="badge bg-secondary">{{ $alokasi->sebagai }}</span>
                    </div>
                </div>
            </div>
            <div class="alert alert-warning border-0 d-flex align-items-center small mb-0">
                <i class="bi bi-info-circle me-2 fs-5"></i>
                <div>
                    Berikan penilaian secara objektif dengan skala <strong>1 (Sangat Buruk)</strong> sampai <strong>5 (Sangat Baik)</strong>.
                </div>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="simpan">
        @foreach($kompetensis as $kompetensi)
            @if($kompetensi->pertanyaans->count() > 0)
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-custom-brown text-white py-3 rounded-top-4">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-star me-2"></i> {{ $kompetensi->nama_kompetensi }}</h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach($kompetensi->pertanyaans as $index => $tanya)
                            <div class="p-4 border-bottom {{ $loop->last ? 'border-0' : '' }}">
                                <p class="fw-medium mb-3">{{ $tanya->teks_pertanyaan }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="btn-group w-100" role="group">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <input type="radio" class="btn-check" 
                                                name="q_{{ $tanya->id }}" 
                                                id="q_{{ $tanya->id }}_{{ $i }}" 
                                                value="{{ $i }}" 
                                                wire:model="jawaban.{{ $tanya->id }}">
                                            
                                            <label class="btn btn-outline-secondary py-2" for="q_{{ $tanya->id }}_{{ $i }}">
                                                {{ $i }}
                                            </label>
                                        @endfor
                                    </div>
                                    <div class="d-flex justify-content-between w-100 px-1 mt-1 small text-muted">
                                        <span>Sangat Buruk</span>
                                        <span>Sangat Baik</span>
                                    </div>
                                </div>
                                @error('jawaban.'.$tanya->id) 
                                    <span class="text-danger small mt-2 d-block"><i class="bi bi-exclamation-circle"></i> Wajib diisi</span> 
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        <div class="d-grid pb-5">
            <button type="submit" class="btn btn-custom-brown btn-lg fw-bold rounded-pill py-3">
                <i class="bi bi-send me-2"></i> Kirim Penilaian
            </button>
        </div>
    </form>
</div>