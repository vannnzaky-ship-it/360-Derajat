<tr>
    <td class="p-3">
        <div class="d-flex align-items-center">
            {{-- Avatar Inisial Nama --}}
            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 0.9rem;">
                {{ substr($alokasi->target->name, 0, 2) }}
            </div>
            
            {{-- Nama & NIP / Konteks --}}
            <div>
                <div class="fw-bold text-truncate">{{ $alokasi->target->name }}</div>
                
                {{-- PERBAIKAN DI SINI: --}}
                {{-- Jika menilai ATASAN, kasih tahu user dia menilai sebagai jabatan apa --}}
                @if($alokasi->sebagai == 'Bawahan' && $alokasi->penilaiJabatan)
                    <div class="small text-danger fst-italic" style="font-size: 0.75rem;">
                        <i class="bi bi-arrow-return-right"></i> 
                        Atasan dari: <strong>{{ $alokasi->penilaiJabatan->nama_jabatan }}</strong>
                    </div>
                @else
                    {{-- Jika bukan atasan, tampilkan NIP biasa --}}
                    <div class="small text-muted">{{ $alokasi->target->pegawai->nip ?? '-' }}</div>
                @endif
            </div>
        </div>
    </td>

    {{-- KOLOM JABATAN TARGET --}}
    <td class="p-3 small text-truncate">
        @if($alokasi->jabatan)
            <span class="badge bg-light text-dark border fw-bold text-uppercase">
                {{ $alokasi->jabatan->nama_jabatan }}
            </span>
        @else
            <span class="text-muted fst-italic">
                {{ $alokasi->target->pegawai->jabatans->first()->nama_jabatan ?? '-' }}
            </span>
        @endif
    </td>

    {{-- Status Penilaian --}}
    <td class="p-3 text-center">
        @if($alokasi->status_nilai == 'Sudah')
            <span class="badge bg-success">Selesai</span>
        @else
            <span class="badge bg-warning text-dark">Belum</span>
        @endif
    </td>

    {{-- Tombol Aksi --}}
    <td class="p-3 text-end">
        @if($alokasi->status_nilai == 'Belum')
            <a href="{{ route('karyawan.isi-penilaian', $alokasi->id) }}" class="btn btn-custom-brown btn-sm px-3 rounded-pill">
                <i class="bi bi-pencil-square me-1"></i> Nilai
            </a>
        @else
            <button class="btn btn-secondary btn-sm px-3 rounded-pill" disabled>
                <i class="bi bi-check-circle me-1"></i> Terkirim
            </button>
        @endif
    </td>
</tr>