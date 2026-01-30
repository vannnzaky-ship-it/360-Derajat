<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use App\Models\PenilaianAlokasi;
use App\Models\Kompetensi;
use App\Models\Pertanyaan;
use App\Models\PenilaianSkor; // Pastikan Model ini diimport
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IsiPenilaian extends Component
{
    public $alokasi_id;
    public $alokasi;
    public $jawaban = []; 
    public $deadline;

    public function mount($id)
    {
        $this->alokasi_id = $id;
        
        $this->alokasi = PenilaianAlokasi::with(['target', 'penilaianSession'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($this->alokasi->status_nilai == 'Sudah') {
            return redirect()->route('karyawan.penilaian');
        }

        $this->deadline = $this->alokasi->penilaianSession->batas_waktu;

        if (now() > $this->deadline) {
            session()->flash('error', 'Maaf, batas waktu penilaian untuk sesi ini sudah habis.');
            return redirect()->route('karyawan.penilaian');
        }

        // [BARU] Load jawaban sementara (draft) jika ada
        // Supaya kalau user refresh, jawaban yg belum dikirim tidak hilang (jika sudah tersimpan di DB sebagai 0 atau draft)
        // Kita ambil data dari snapshot skor yang sudah dibuat.
        $existingScores = PenilaianSkor::where('penilaian_alokasi_id', $id)->get();
        foreach($existingScores as $skor) {
            // Hanya isi jika nilainya > 0 (artinya sudah pernah dijawab/disimpan)
            if($skor->nilai > 0) {
                $this->jawaban[$skor->pertanyaan_id] = $skor->nilai;
            }
        }
    }

    public function simpan()
    {
        // [PERBAIKAN UTAMA DI SINI]
        // Jangan ambil dari tabel Pertanyaan based on status/tanggal.
        // Tapi ambil ID Pertanyaan yang SUDAH ADA di tabel skor untuk alokasi ini.
        // Ini menjamin hanya pertanyaan yang 'terkunci' saat generate yang wajib diisi.
        
        $requiredIds = PenilaianSkor::where('penilaian_alokasi_id', $this->alokasi_id)
                        ->pluck('pertanyaan_id')
                        ->toArray();

        // 2. Susun Rules
        $dynamicRules = [];
        foreach ($requiredIds as $id) {
            $dynamicRules["jawaban.$id"] = 'required|integer|min:1|max:5';
        }

        // 3. Validasi
        $this->validate($dynamicRules, [
            'jawaban.*.required' => 'Pertanyaan ini wajib diberi nilai.',
            'jawaban.*.min' => 'Nilai minimal 1.',
            'jawaban.*.max' => 'Nilai maksimal 5.',
        ]);

        DB::beginTransaction();
        try {
            // Update nilai di tabel skor yang sudah ada
            foreach ($this->jawaban as $pertanyaanId => $nilai) {
                // Pastikan kita hanya mengupdate pertanyaan yang memang jatahnya (snapshot)
                if (in_array($pertanyaanId, $requiredIds)) {
                    PenilaianSkor::where('penilaian_alokasi_id', $this->alokasi_id)
                        ->where('pertanyaan_id', $pertanyaanId)
                        ->update([
                            'nilai' => $nilai,
                            'updated_at' => now()
                        ]);
                }
            }

            // Ubah status alokasi
            $this->alokasi->update(['status_nilai' => 'Sudah']);

            DB::commit();
            
            session()->flash('message', 'Penilaian berhasil dikirim!');
            return redirect()->route('karyawan.penilaian');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // [PERBAIKAN UTAMA DI RENDER]
        // Ambil ID pertanyaan yang HANYA ada di tabel skor alokasi ini.
        // Abaikan status 'Aktif/Tidak Aktif' di tabel master Pertanyaan sekarang.
        // Kita patuh pada Snapshot.
        
        $lockedPertanyaanIds = PenilaianSkor::where('penilaian_alokasi_id', $this->alokasi_id)
                                ->pluck('pertanyaan_id')
                                ->toArray();

        // Ambil Kompetensi beserta Pertanyaan-nya, TAPI filter pertanyaannya
        // hanya yang ada di daftar $lockedPertanyaanIds
        $kompetensis = Kompetensi::whereHas('pertanyaans', function($q) use ($lockedPertanyaanIds) {
                $q->whereIn('id', $lockedPertanyaanIds);
            })
            ->with(['pertanyaans' => function($q) use ($lockedPertanyaanIds) {
                $q->whereIn('id', $lockedPertanyaanIds);
            }])
            ->get();

        return view('livewire.karyawan.isi-penilaian', [
            'kompetensis' => $kompetensis
        ])->layout('layouts.admin');
    }
}