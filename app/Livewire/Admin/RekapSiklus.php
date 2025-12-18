<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Siklus;
use App\Models\Pegawai;
use App\Models\PenilaianAlokasi; // Wajib Import ini
use App\Services\HitungSkorService;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.admin')]
class RekapSiklus extends Component
{
    public $siklus;
    public $search = '';
    public $dataPegawai = [];

    public function mount($siklusId)
    {
        $this->siklus = Siklus::with('penilaianSession')->findOrFail($siklusId);
        
        if (!$this->siklus->penilaianSession) {
            return redirect()->route('admin.siklus-semester')->with('error', 'Sesi penilaian belum ada.');
        }

        $this->loadData();
    }

    public function loadData()
    {
        // 1. Ambil Pegawai
        $pegawais = Pegawai::with(['user', 'jabatans'])
            ->whereHas('user', function($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            })
            ->whereHas('jabatans') 
            ->get();

        $sessionId = $this->siklus->penilaianSession->id;
        $service = new HitungSkorService(); // Pastikan Service ini ada
        $tempData = []; 

        foreach ($pegawais as $peg) {
            if(!$peg->user) continue;
            
            // Gabungkan nama jabatan jika rangkap
            $namaJabatanFull = $peg->jabatans->pluck('nama_jabatan')->implode(', ');
            
            // --- A. HITUNG SKOR RATA-RATA DARI SEMUA JABATAN ---
            $totalSkorSemuaJabatan = 0;
            $jumlahJabatanDinilai = 0;

            foreach ($peg->jabatans as $jabatan) {
                $hasil = $service->hitungNilaiAkhir($peg->user->id, $sessionId, $jabatan->id);
                
                if (isset($hasil['skor_akhir']) && $hasil['skor_akhir'] > 0) {
                    $totalSkorSemuaJabatan += floatval($hasil['skor_akhir']);
                    $jumlahJabatanDinilai++;
                }
            }

            // Hitung Rata-rata Akhir
            if ($jumlahJabatanDinilai > 0) {
                $skorAkhir = round($totalSkorSemuaJabatan / $jumlahJabatanDinilai, 2);
                $predikat = $this->getPredikat($skorAkhir);
            } else {
                $skorAkhir = 0;
                $predikat = 'Belum Dinilai';
            }

            // --- B. HITUNG JUMLAH PENILAI (VALIDITAS) ---
            // Menghitung berapa orang yang status nilainya 'Sudah' untuk pegawai ini
            $jumlahSuara = PenilaianAlokasi::where('target_user_id', $peg->user->id)
                            ->where('penilaian_session_id', $sessionId)
                            ->where('status_nilai', 'Sudah')
                            ->count();

            $tempData[] = [
                'user_id' => $peg->user->id,
                'nip' => $peg->nip,
                'nama' => $peg->user->name,
                'jabatan' => $namaJabatanFull,
                'skor_akhir' => $skorAkhir,
                'predikat' => $predikat,
                'total_penilai' => $jumlahSuara, // Data Penting Baru
                'foto' => $peg->user->profile_photo_path ?? null 
            ];
        }

        // --- C. SORTING (LOGIKA PERINGKAT) ---
        // Prioritas 1: Skor Akhir Tertinggi
        // Prioritas 2: Jika Skor Sama, Jumlah Penilai Terbanyak Menang
        $this->dataPegawai = collect($tempData)->sort(function ($a, $b) {
            if ($a['skor_akhir'] == $b['skor_akhir']) {
                return $b['total_penilai'] <=> $a['total_penilai'];
            }
            return $b['skor_akhir'] <=> $a['skor_akhir'];
        })->values()->all();
    }

    private function getPredikat($score) {
        if($score >= 90) return 'Sangat Baik';
        if($score >= 76) return 'Baik';
        if($score >= 60) return 'Cukup';
        if($score > 0) return 'Kurang';
        return 'Belum Dinilai';
    }

    public function updatedSearch()
    {
        $this->loadData();
    }

    // EXPORT PDF
    public function exportPdf()
    {
        $data = [
            'siklus' => $this->siklus->tahun_ajaran . ' ' . $this->siklus->semester,
            'pegawais' => $this->dataPegawai
        ];
        // Pastikan view 'livewire.admin.cetak-rekap-siklus' Anda sesuaikan jika ingin menampilkan kolom penilai juga
        $pdf = Pdf::loadView('livewire.admin.cetak-rekap-siklus', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Ranking-Nilai-'.$this->siklus->tahun_ajaran.'.pdf');
    }

    // EXPORT EXCEL
    public function exportExcel()
    {
        $data = $this->dataPegawai;
        $siklusName = $this->siklus->tahun_ajaran . '-' . $this->siklus->semester;
        $fileName = 'Ranking-Nilai-' . $siklusName . '.csv';

        return response()->streamDownload(function () use ($data, $siklusName) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['PERINGKAT KINERJA PEGAWAI']);
            fputcsv($file, ['Siklus', $siklusName]);
            fputcsv($file, []); 

            // Header Excel Update
            fputcsv($file, ['Peringkat', 'NIP', 'Nama Pegawai', 'Jabatan', 'Skor Akhir', 'Jml Penilai', 'Predikat']);

            foreach ($data as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row['nip'],
                    $row['nama'],
                    $row['jabatan'], 
                    $row['skor_akhir'],
                    $row['total_penilai'], // Kolom Baru
                    $row['predikat']
                ]);
            }
            fclose($file);
        }, $fileName);
    }

    public function render()
    {
        return view('livewire.admin.rekap-siklus');
    }
}