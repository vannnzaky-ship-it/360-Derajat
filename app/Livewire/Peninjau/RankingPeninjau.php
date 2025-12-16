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
            // Filter Search (Manual Array Filter)
            if ($this->search && stripos($user->name, $this->search) === false) {
                continue;
            }

            $jabatan = $user->pegawai->jabatans->first();
            if (!$jabatan) continue;

            // Hitung Nilai Pakai Service
            $hasil = $service->hitungNilaiAkhir($user->id, $sessionId, $jabatan->id);

            $tempData[] = [
                'user_id' => $user->id,
                'nama' => $user->name,
                'nip' => $user->pegawai->nip ?? '-',
                'jabatan' => $user->pegawai->jabatans->pluck('nama_jabatan')->implode(', '),
                'skor_akhir' => $hasil['skor_akhir'],
                'skor_raw' => (float) $hasil['skor_akhir'], // Untuk sorting
                'predikat' => $hasil['mutu']
            ];
        }

        // Sorting dari nilai tertinggi (Ranking)
        usort($tempData, fn($a, $b) => $b['skor_raw'] <=> $a['skor_raw']);
        $this->dataPegawai = $tempData;
    }

    // Supaya search jalan real-time
    public function updatedSearch()
    {
        $this->hitungRanking();
    }

    public function exportPdf()
    {
        $data = [
            'siklus' => $this->siklus,
            'dataPegawai' => $this->dataPegawai, // Pastikan ini sama dengan di view
            'tanggal' => now()->format('d F Y')
        ];
        
        // GUNAKAN VIEW BARU YANG KITA BUAT DI ATAS
        $pdf = Pdf::loadView('livewire.peninjau.cetak-ranking-pdf', $data)
                  ->setPaper('a4', 'landscape');
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'Laporan-Ranking-'.$this->siklus->tahun_ajaran.'.pdf');
    }
    
    public function exportExcel()
    {
        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['RANKING KINERJA PEGAWAI']);
            fputcsv($file, ['Siklus', $this->siklus->tahun_ajaran . ' ' . $this->siklus->semester]);
            fputcsv($file, []);
            fputcsv($file, ['Rank', 'Nama', 'NIP', 'Jabatan', 'Skor Akhir', 'Predikat']);

            foreach ($this->dataPegawai as $index => $row) {
                fputcsv($file, [
                    $index + 1, $row['nama'], $row['nip'], $row['jabatan'], $row['skor_akhir'], $row['predikat']
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