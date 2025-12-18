<?php

namespace App\Livewire\Peninjau;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Siklus;
use App\Models\User;
use App\Models\PenilaianAlokasi; 
use App\Services\HitungSkorService;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.admin')]
class RankingPeninjau extends Component
{
    public $siklus;
    public $search = '';
    public $dataPegawai = [];

    public function mount($siklusId)
    {
        $this->siklus = Siklus::with('penilaianSession')->findOrFail($siklusId);
        
        if (!$this->siklus->penilaianSession) {
            return redirect()->route('peninjau.dashboard')->with('error', 'Sesi penilaian belum ada.');
        }

        $this->hitungRanking();
    }

    public function hitungRanking()
    {
        $sessionId = $this->siklus->penilaianSession->id;
        
        // Ambil User yang dinilai pada sesi ini
        $targetUsers = PenilaianAlokasi::where('penilaian_session_id', $sessionId)
            ->distinct()
            ->pluck('target_user_id');

        $users = User::with(['pegawai.jabatans'])->whereIn('id', $targetUsers)->get();
        $service = new HitungSkorService();
        $tempData = [];

        // --- [CONFIG] BAYESIAN AVERAGE SETTINGS ---
        // C = Baseline/Nilai Standar (Misal KKM = 70). 
        // Nilai pegawai yang suaranya sedikit akan ditarik mendekati angka ini.
        $C = 70; 
        
        // m = Minimum Vote (Threshold). 
        // Berapa suara minimal biar nilainya dianggap murni? (Misal 10 orang).
        $m = 10; 

        foreach ($users as $user) {
            // Filter Search
            if ($this->search && stripos($user->name, $this->search) === false) {
                continue;
            }

            $totalSkor = 0;
            $jumlahJabatan = 0;
            
            // --- HITUNG SKOR MURNI ---
            if ($user->pegawai && $user->pegawai->jabatans->isNotEmpty()) {
                foreach ($user->pegawai->jabatans as $jabatan) {
                    $hasil = $service->hitungNilaiAkhir($user->id, $sessionId, $jabatan->id);
                    if (isset($hasil['skor_akhir']) && $hasil['skor_akhir'] > 0) {
                        $totalSkor += floatval($hasil['skor_akhir']);
                        $jumlahJabatan++;
                    }
                }
            }

            if ($jumlahJabatan > 0) {
                $skorMurni = round($totalSkor / $jumlahJabatan, 2);
                $predikat = $this->getPredikat($skorMurni);
            } else {
                $skorMurni = 0;
                $predikat = 'Belum Dinilai';
            }

            // --- HITUNG JUMLAH PENILAI (v) ---
            $v = PenilaianAlokasi::where('target_user_id', $user->id)
                            ->where('penilaian_session_id', $sessionId)
                            ->where('status_nilai', 'Sudah')
                            ->count();

            // --- HITUNG SKOR RANKING (BAYESIAN) ---
            // Rumus: (v / (v+m)) * R + (m / (v+m)) * C
            if ($v > 0) {
                $skorRanking = ( ($v / ($v + $m)) * $skorMurni ) + ( ($m / ($v + $m)) * $C );
            } else {
                $skorRanking = 0;
            }

            $tempData[] = [
                'user_id' => $user->id,
                'nama' => $user->name,
                'nip' => $user->pegawai->nip ?? '-',
                'jabatan' => $user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-',
                
                // TAMPILAN DI LAYAR TETAP SKOR MURNI (ASLI)
                'skor_akhir' => (float) $skorMurni,
                'skor_raw' => (float) $skorMurni, // Alias untuk kompatibilitas view lama
                
                // TAPI URUTAN BERDASARKAN SKOR TERUJI (BAYESIAN)
                'skor_ranking' => (float) $skorRanking, 
                
                'predikat' => $predikat,
                'total_penilai' => (int) $v,
                'foto' => $user->profile_photo_path ?? null 
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

    public function updatedSearch() { $this->hitungRanking(); }

   public function exportPdf()
    {
        $data = [
            
            'siklus' => $this->siklus, 
            
            'pegawais' => $this->dataPegawai, 
            'tanggal' => now()->format('d F Y')
        ];
        
        $pdf = Pdf::loadView('livewire.peninjau.cetak-ranking-pdf', $data)->setPaper('a4', 'landscape');
        return response()->streamDownload(function() use ($pdf) { echo $pdf->output(); }, 'Laporan-Ranking.pdf');
    }
    
    public function exportExcel()
    {
        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['RANKING KINERJA PEGAWAI']);
            fputcsv($file, ['Siklus', $this->siklus->tahun_ajaran . ' ' . $this->siklus->semester]);
            fputcsv($file, []);
            fputcsv($file, ['Rank', 'Nama', 'NIP', 'Jabatan', 'Skor Akhir', 'Jml Penilai', 'Predikat']);

            foreach ($this->dataPegawai as $index => $row) {
                fputcsv($file, [
                    $index + 1, $row['nama'], $row['nip'], $row['jabatan'], 
                    $row['skor_akhir'], 
                    $row['total_penilai'],
                    $row['predikat']
                ]);
            }
            fclose($file);
        }, 'Laporan-Ranking.csv');
    }

    public function render()
    {
        return view('livewire.peninjau.ranking-peninjau');
    }
}