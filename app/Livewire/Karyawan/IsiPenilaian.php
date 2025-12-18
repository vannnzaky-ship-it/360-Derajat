<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use App\Models\PenilaianAlokasi;
use App\Models\Kompetensi;
use App\Models\Pertanyaan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IsiPenilaian extends Component
{
    public $alokasi_id;
    public $alokasi;
    public $jawaban = []; // Array [pertanyaan_id => nilai]
    public $deadline;

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

        // Simpan deadline untuk tampilan timer
        $this->deadline = $this->alokasi->penilaianSession->batas_waktu;

        // Cek batas waktu
        if (now() > $this->deadline) {
            session()->flash('error', 'Maaf, batas waktu penilaian untuk sesi ini sudah habis.');
            return redirect()->route('karyawan.penilaian');
        }
    }

    public function simpan()
    {
        $role = $this->alokasi->sebagai;

        // 1. Ambil semua ID pertanyaan yang wajib diisi berdasarkan role
        $requiredIds = Pertanyaan::where('status', 'Aktif')
            ->where(function($q) use ($role) {
                if ($role == 'Atasan') $q->where('untuk_atasan', 1);
                elseif ($role == 'Bawahan') $q->where('untuk_bawahan', 1);
                elseif ($role == 'Rekan') $q->where('untuk_rekan', 1);
                elseif ($role == 'Diri Sendiri') $q->where('untuk_diri', 1);
            })->pluck('id')->toArray();

        // 2. Susun Rules secara dinamis untuk setiap ID pertanyaan agar WAJIB diisi
        $dynamicRules = [];
        foreach ($requiredIds as $id) {
            $dynamicRules["jawaban.$id"] = 'required|integer|min:1|max:5';
        }

        // 3. Jalankan Validasi
        $this->validate($dynamicRules, [
            'jawaban.*.required' => 'Pertanyaan ini belum diisi.',
            'jawaban.*.min' => 'Nilai minimal 1.',
            'jawaban.*.max' => 'Nilai maksimal 5.',
        ]);

        DB::beginTransaction();
        try {
            // Simpan Jawaban ke tabel skor
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

            // Ubah status alokasi menjadi 'Sudah'
            $this->alokasi->update(['status_nilai' => 'Sudah']);

            DB::commit();
            
            session()->flash('message', 'Penilaian berhasil dikirim!');
            return redirect()->route('karyawan.penilaian');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function render()
    {
        $role = $this->alokasi->sebagai;

        $kompetensis = Kompetensi::where('status', 'Aktif')
            ->with(['pertanyaans' => function($q) use ($role) {
                $q->where('status', 'Aktif');
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