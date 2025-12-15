<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use App\Models\PenilaianAlokasi;
use App\Models\Kompetensi;
use Illuminate\Support\Facades\DB;

class IsiPenilaian extends Component
{
    public $alokasi_id;
    public $alokasi;
    public $jawaban = []; // Array untuk menampung jawaban [pertanyaan_id => nilai]

    public function mount($id)
{
    $this->alokasi_id = $id;
    
    $this->alokasi = PenilaianAlokasi::with(['target', 'penilaianSession'])
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    // PROTEKSI 1: Sudah menilai?
    if ($this->alokasi->status_nilai == 'Sudah') {
        return redirect()->route('karyawan.penilaian');
    }

    // PROTEKSI 2: SUDAH EXPIRED?
    if (now() > $this->alokasi->penilaianSession->batas_waktu) {
        session()->flash('error', 'Maaf, batas waktu penilaian untuk sesi ini sudah habis.');
        return redirect()->route('karyawan.penilaian');
    }
}

    public function simpan()
    {
        // Validasi: Pastikan semua pertanyaan dijawab
        $this->validate([
            'jawaban.*' => 'required|integer|min:1|max:5',
        ], [
            'jawaban.*.required' => 'Pertanyaan ini wajib diisi.',
        ]);

        DB::beginTransaction();
        try {
            // Simpan Skor
            foreach ($this->jawaban as $pertanyaanId => $nilai) {
                DB::table('penilaian_skor')->insert([
                    'penilaian_alokasi_id' => $this->alokasi_id,
                    'pertanyaan_id' => $pertanyaanId,
                    'nilai' => $nilai,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Update Status Alokasi jadi 'Sudah'
            $this->alokasi->update(['status_nilai' => 'Sudah']);

            DB::commit();
            session()->flash('message', 'Penilaian berhasil dikirim!');
            return redirect()->route('karyawan.penilaian');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function render()
    {
        // LOGIKA FILTER PERTANYAAN SESUAI PERAN
        $role = $this->alokasi->sebagai; // Atasan, Bawahan, Rekan, Diri Sendiri

        // Ambil kompetensi beserta pertanyaannya yang Aktif & Sesuai Peran
        $kompetensis = Kompetensi::where('status', 'Aktif')
            ->with(['pertanyaans' => function($q) use ($role) {
                $q->where('status', 'Aktif');
                
                // Filter kolom database 'pertanyaan'
                if ($role == 'Atasan') $q->where('untuk_atasan', 1);
                elseif ($role == 'Bawahan') $q->where('untuk_bawahan', 1);
                elseif ($role == 'Rekan') $q->where('untuk_rekan', 1);
                elseif ($role == 'Diri Sendiri') $q->where('untuk_diri', 1);
            }])
            ->get();

        return view('livewire.karyawan.isi-penilaian', [
            'kompetensis' => $kompetensis
        ])->layout('layouts.admin');
    }
}