<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Siklus;
use App\Services\HitungSkorService;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.admin')]
class DetailNilai extends Component
{
    public $user, $siklus;
    public $hasilPenilaian = null; // Akan berisi skor akhir gabungan & mutu
    public $tableData = []; // Akan berisi rekap kompetensi gabungan

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
                // 1. Hitung Skor Akhir per Jabatan
                $hasil = $service->hitungNilaiAkhir($userId, $sessionId, $jabatan->id);
                if (isset($hasil['skor_akhir'])) {
                    $totalSkor += floatval($hasil['skor_akhir']);
                    $jumlahJabatan++;
                }

                // 2. Ambil Rincian Kompetensi per Jabatan
                $rekapKomp = $service->getRekapKompetensi($userId, $sessionId, $jabatan->id);
                foreach ($rekapKomp as $namaKomp => $nilaiKomp) {
                    $tempKompetensi[$namaKomp][] = $nilaiKomp;
                }
            }

            // [HITUNG RATA-RATA FINAL]
            $skorAkhir = $jumlahJabatan > 0 ? round($totalSkor / $jumlahJabatan, 2) : 0;
            
            // Hitung rata-rata per kompetensi
            $finalKompetensi = [];
            foreach ($tempKompetensi as $nama => $nilaiArray) {
                $avg = array_sum($nilaiArray) / count($nilaiArray);
                $finalKompetensi[$nama] = round($avg, 2);
            }

            // Simpan ke properti public
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

        // Gunakan view raport yang sama
        $pdf = Pdf::loadView('livewire.karyawan.cetak-raport-pdf', $data);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Raport-'.$this->user->name.'.pdf');
    }

    public function exportExcel()
    {
        $jabatanUser = $this->user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-';
        $namaFile = 'Raport-' . str_replace(' ', '-', $this->user->name) . '.csv';

        $data = [
            'Nama' => $this->user->name,
            'NIP' => $this->user->pegawai->nip,
            'Jabatan' => $jabatanUser,
            'Siklus' => $this->siklus->tahun_ajaran . ' ' . $this->siklus->semester,
            'Skor' => $this->hasilPenilaian['skor_akhir'] ?? 0,
            'Predikat' => $this->hasilPenilaian['mutu'] ?? '-',
            'Kompetensi' => $this->tableData
        ];

        return response()->streamDownload(function () use ($data) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['RAPORT KINERJA INDIVIDU (GABUNGAN JABATAN)']);
            fputcsv($file, []);
            fputcsv($file, ['Nama Pegawai', $data['Nama']]);
            fputcsv($file, ['NIP', $data['NIP']]);
            fputcsv($file, ['Jabatan', $data['Jabatan']]);
            fputcsv($file, ['Siklus', $data['Siklus']]);
            fputcsv($file, []);
            
            fputcsv($file, ['SKOR AKHIR', $data['Skor']]);
            fputcsv($file, ['PREDIKAT', $data['Predikat']]);
            fputcsv($file, []);

            fputcsv($file, ['RINCIAN KOMPETENSI', 'RATA-RATA NILAI']);
            foreach ($data['Kompetensi'] as $kompetensi => $nilai) {
                fputcsv($file, [$kompetensi, $nilai]);
            }

            fclose($file);
        }, $namaFile);
    }

    public function render()
    {
        return view('livewire.admin.detail-nilai');
    }
}