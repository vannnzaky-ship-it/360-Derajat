<?php

namespace App\Livewire\Peninjau;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Siklus;
use App\Models\User;
use App\Models\PenilaianAlokasi; // Import wajib
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

        foreach ($users as $user) {
            // Filter Search
            if ($this->search && stripos($user->name, $this->search) === false) {
                continue;
            }

            // Hitung Skor (Multi Jabatan)
            $totalSkor = 0;
            $jumlahJabatan = 0;
            $skorAkhir = 0;
            $predikat = 'Belum Dinilai';

            if ($user->pegawai && $user->pegawai->jabatans->isNotEmpty()) {
                foreach ($user->pegawai->jabatans as $jabatan) {
                    $hasil = $service->hitungNilaiAkhir($user->id, $sessionId, $jabatan->id);
                    if (isset($hasil['skor_akhir']) && $hasil['skor_akhir'] > 0) {
                        $totalSkor += floatval($hasil['skor_akhir']);
                        $jumlahJabatan++;
                    }
                }

                if ($jumlahJabatan > 0) {
                    $skorAkhir = round($totalSkor / $jumlahJabatan, 2);
                    $predikat = $this->getPredikat($skorAkhir);
                }
            }

            // [BARU] Hitung Jumlah Suara (Validitas)
            $jumlahSuara = PenilaianAlokasi::where('target_user_id', $user->id)
                            ->where('penilaian_session_id', $sessionId)
                            ->where('status_nilai', 'Sudah')
                            ->count();

            $tempData[] = [
                'user_id' => $user->id,
                'nama' => $user->name,
                'nip' => $user->pegawai->nip ?? '-',
                'jabatan' => $user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-',
                'skor_akhir' => $skorAkhir,
                'skor_raw' => (float) $skorAkhir, 
                'predikat' => $predikat,
                'total_penilai' => $jumlahSuara, // Data Penting Baru
                'foto' => $user->profile_photo_path ?? null 
            ];
        }

        // [LOGIKA RANKING] Sortir Skor Tinggi -> Suara Terbanyak
        usort($tempData, function ($a, $b) {
            if ($a['skor_raw'] == $b['skor_raw']) {
                return $b['total_penilai'] <=> $a['total_penilai'];
            }
            return $b['skor_raw'] <=> $a['skor_raw'];
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
            'dataPegawai' => $this->dataPegawai, 
            'tanggal' => now()->format('d F Y')
        ];
        // Pastikan view PDF disesuaikan jika ingin menampilkan jumlah penilai
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
            // Header Excel
            fputcsv($file, ['Rank', 'Nama', 'NIP', 'Jabatan', 'Skor Akhir', 'Jml Penilai', 'Predikat']);

            foreach ($this->dataPegawai as $index => $row) {
                fputcsv($file, [
                    $index + 1, $row['nama'], $row['nip'], $row['jabatan'], 
                    $row['skor_akhir'], 
                    $row['total_penilai'], // Data Baru
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