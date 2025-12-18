<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Siklus;
use App\Models\Pegawai;
use App\Models\PenilaianAlokasi; 
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
        $service = new HitungSkorService();
        $tempData = []; 

        // --- [CONFIG] BAYESIAN AVERAGE SETTINGS ---
        // C = Baseline/Nilai Standar (Misal KKM = 70). 
        // Nilai pegawai yang suaranya sedikit akan ditarik mendekati angka ini.
        $C = 70; 
        
        // m = Minimum Vote (Threshold). 
        // Berapa suara minimal biar nilainya dianggap murni? (Misal 10 orang).
        $m = 10; 

        foreach ($pegawais as $peg) {
            if(!$peg->user) continue;
            
            $namaJabatanFull = $peg->jabatans->pluck('nama_jabatan')->implode(', ');
            
            // --- HITUNG SKOR MURNI (RATA-RATA) ---
            $totalSkor = 0;
            $jumlahJabatan = 0;

            foreach ($peg->jabatans as $jabatan) {
                $hasil = $service->hitungNilaiAkhir($peg->user->id, $sessionId, $jabatan->id);
                if (isset($hasil['skor_akhir']) && $hasil['skor_akhir'] > 0) {
                    $totalSkor += floatval($hasil['skor_akhir']);
                    $jumlahJabatan++;
                }
            }

            if ($jumlahJabatan > 0) {
                $skorMurni = round($totalSkor / $jumlahJabatan, 2);
                $predikat = $this->getPredikat($skorMurni);
            } else {
                $skorMurni = 0;
                $predikat = 'Belum Dinilai';
            }

            // --- AMBIL JUMLAH SUARA (v) ---
            $v = PenilaianAlokasi::where('target_user_id', $peg->user->id)
                            ->where('penilaian_session_id', $sessionId)
                            ->where('status_nilai', 'Sudah')
                            ->count();

            // --- HITUNG SKOR RANKING (RUMUS BAYESIAN) ---
            // Rumus: (v / (v+m)) * R + (m / (v+m)) * C
            if ($v > 0) {
                $skorRanking = ( ($v / ($v + $m)) * $skorMurni ) + ( ($m / ($v + $m)) * $C );
            } else {
                $skorRanking = 0;
            }

            $tempData[] = [
                'user_id' => $peg->user->id,
                'nip' => $peg->nip,
                'nama' => $peg->user->name,
                'jabatan' => $namaJabatanFull,
                
                // TAMPILAN DI LAYAR TETAP SKOR MURNI (ASLI)
                'skor_akhir' => (float) $skorMurni, 
                
                // TAPI URUTAN BERDASARKAN SKOR TERUJI (BAYESIAN)
                'skor_ranking' => (float) $skorRanking, 
                
                'predikat' => $predikat,
                'total_penilai' => (int) $v,
                'foto' => $peg->user->profile_photo_path ?? null 
            ];
        }

        // --- SORTING BERDASARKAN SKOR RANKING (BAYESIAN) ---
        usort($tempData, function ($a, $b) {
            // 1. Bandingkan Skor Bayesian (Kualitas Teruji)
            if (abs($b['skor_ranking'] - $a['skor_ranking']) > 0.001) {
                return $b['skor_ranking'] <=> $a['skor_ranking'];
            }
            // 2. Jika Skor Ranking sama, cek Skor Murni
            if ($b['skor_akhir'] != $a['skor_akhir']) {
                return $b['skor_akhir'] <=> $a['skor_akhir'];
            }
            // 3. Terakhir nama
            return strcmp($a['nama'], $b['nama']);
        });

        $this->dataPegawai = $tempData;
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

            fputcsv($file, ['Peringkat', 'NIP', 'Nama Pegawai', 'Jabatan', 'Skor Akhir', 'Jml Penilai', 'Predikat']);

            foreach ($data as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row['nip'],
                    $row['nama'],
                    $row['jabatan'], 
                    $row['skor_akhir'],
                    $row['total_penilai'],
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