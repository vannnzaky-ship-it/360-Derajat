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
    public $jawaban = []; // Array [pertanyaan_id => nilai]

    public function mount($id)
    {
        $this->alokasi_id = $id;
        
        $this->alokasi = PenilaianAlokasi::with(['target', 'penilaianSession'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Cek apakah sudah menilai
        if ($this->alokasi->status_nilai == 'Sudah') {
            return redirect()->route('karyawan.penilaian');
        }

        // Cek batas waktu
        if (now() > $this->alokasi->penilaianSession->batas_waktu) {
            session()->flash('error', 'Maaf, batas waktu penilaian untuk sesi ini sudah habis.');
            return redirect()->route('karyawan.penilaian');
        }
    }

    public function simpan()
    {
        // Validasi
        $this->validate([
            'jawaban.*' => 'required|integer|min:1|max:5',
        ], [
            'jawaban.*.required' => 'Pertanyaan ini wajib diisi.',
            'jawaban.*.min' => 'Nilai minimal adalah 1.',
            'jawaban.*.max' => 'Nilai maksimal adalah 5.',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan Jawaban ke tabel skor
            foreach ($this->jawaban as $pertanyaanId => $nilai) {
                DB::table('penilaian_skor')->updateOrInsert(
                    [
                        'penilaian_alokasi_id' => $this->alokasi_id,
                        'pertanyaan_id' => $pertanyaanId
                    ],
                    [
                        'nilai' => $nilai,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            // 2. Ubah status alokasi menjadi 'Sudah'
            $this->alokasi->update(['status_nilai' => 'Sudah']);

            DB::commit();
            
            session()->flash('message', 'Penilaian berhasil dikirim!');
            return redirect()->route('karyawan.penilaian');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error untuk developer jika perlu: \Log::error($e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function render()
    {
        $role = $this->alokasi->sebagai; // Atasan, Bawahan, Rekan, Diri Sendiri

        // Ambil kompetensi beserta pertanyaan yang relevan dengan peran user
        $kompetensis = Kompetensi::where('status', 'Aktif')
            ->with(['pertanyaans' => function($q) use ($role) {
                $q->where('status', 'Aktif');
                
                // Filter pertanyaan berdasarkan peran
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