<?php

namespace App\Livewire\Peninjau;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Siklus;
use App\Services\HitungSkorService;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.admin')]
class DetailPeninjau extends Component
{
    public $user, $siklus;
    public $hasilPenilaian = null;
    public $tableData = [];

    public function mount($siklusId, $userId)
    {
        $this->user = User::with('pegawai.jabatans')->findOrFail($userId);
        $this->siklus = Siklus::with('penilaianSession')->findOrFail($siklusId);

        $sessionId = $this->siklus->penilaianSession->id;
        $jabatans = $this->user->pegawai->jabatans;

        if ($jabatans->isNotEmpty()) {
            $service = new HitungSkorService();
            
            // [LOGIKA MULTI-JABATAN]
            $totalSkor = 0;
            $jumlahJabatan = 0;
            $tempKompetensi = [];

            foreach ($jabatans as $jabatan) {
                // 1. Hitung Skor per Jabatan
                $hasil = $service->hitungNilaiAkhir($userId, $sessionId, $jabatan->id);
                
                if (isset($hasil['skor_akhir'])) {
                    $totalSkor += floatval($hasil['skor_akhir']);
                    $jumlahJabatan++;
                }

                // 2. Ambil Rincian Kompetensi
                $rekapKomp = $service->getRekapKompetensi($userId, $sessionId, $jabatan->id);
                foreach ($rekapKomp as $namaKomp => $nilaiKomp) {
                    $tempKompetensi[$namaKomp][] = $nilaiKomp;
                }
            }

            // Hitung Rata-rata Akhir
            $skorAkhir = $jumlahJabatan > 0 ? round($totalSkor / $jumlahJabatan, 2) : 0;
            
            // Hitung Rata-rata Kompetensi
            $finalKompetensi = [];
            foreach ($tempKompetensi as $nama => $nilaiArray) {
                $avg = array_sum($nilaiArray) / count($nilaiArray);
                $finalKompetensi[$nama] = round($avg, 2);
            }

            // Simpan ke Property
            $this->hasilPenilaian = [
                'skor_akhir' => $skorAkhir,
                'mutu' => $this->getPredikat($skorAkhir)
            ];
            $this->tableData = $finalKompetensi;
        }
    }

    private function getPredikat($score) {
        if($score >= 90) return 'Sangat Baik';
        if($score >= 76) return 'Baik';
        if($score >= 60) return 'Cukup';
        return 'Kurang';
    }

    public function exportPdf()
    {
        $jabatanUser = $this->user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-';
        
        $data = [
            'namaUser' => $this->user->name,
            'nipUser' => $this->user->pegawai->nip,
            'jabatanUser' => $jabatanUser,
            'tableData' => $this->tableData,
            'finalScore' => $this->hasilPenilaian['skor_akhir'] ?? 0,
            'predikat' => $this->hasilPenilaian['mutu'] ?? '-',
            'siklus' => $this->siklus->tahun_ajaran . ' ' . $this->siklus->semester
        ];

        $pdf = Pdf::loadView('livewire.karyawan.cetak-raport-pdf', $data);
        
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'Raport-'.$this->user->name.'.pdf');
    }

    public function exportExcel()
    {
        $jabatanUser = $this->user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-';
        
        return response()->streamDownload(function () use ($jabatanUser) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['RAPORT KINERJA INDIVIDU (GABUNGAN JABATAN)']);
            fputcsv($file, []);
            fputcsv($file, ['Nama', $this->user->name]);
            fputcsv($file, ['NIP', $this->user->pegawai->nip]);
            fputcsv($file, ['Jabatan', $jabatanUser]);
            
            fputcsv($file, ['Skor Akhir', $this->hasilPenilaian['skor_akhir'] ?? 0]);
            fputcsv($file, ['Predikat', $this->hasilPenilaian['mutu'] ?? '-']);
            fputcsv($file, []);
            
            fputcsv($file, ['Kompetensi', 'Rata-rata Nilai']);
            foreach ($this->tableData as $k => $v) {
                fputcsv($file, [$k, $v]);
            }
            fclose($file);
        }, 'Raport-'.$this->user->name.'.csv');
    }

    public function render()
    {
        return view('livewire.peninjau.detail-peninjau');
    }
}