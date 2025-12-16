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
        $jabatan = $this->user->pegawai->jabatans->first();

        if ($jabatan) {
            $service = new HitungSkorService();
            // Reuse service hitung skor
            $this->hasilPenilaian = $service->hitungNilaiAkhir($userId, $sessionId, $jabatan->id);
            $this->tableData = $service->getRekapKompetensi($userId, $sessionId, $jabatan->id);
        }
    }

    public function exportPdf()
    {
        $jabatanUser = $this->user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-';
        
        $data = [
            'namaUser' => $this->user->name,
            'nipUser' => $this->user->pegawai->nip,
            'jabatanUser' => $jabatanUser,
            'tableData' => $this->tableData,
            'finalScore' => $this->hasilPenilaian['skor_akhir'],
            'predikat' => $this->hasilPenilaian['mutu'],
            'siklus' => $this->siklus->tahun_ajaran . ' ' . $this->siklus->semester
        ];

        $pdf = Pdf::loadView('livewire.karyawan.cetak-raport-pdf', $data);
        
        // PERBAIKAN: Gunakan function() use ($pdf)
        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, 'Raport-'.$this->user->name.'.pdf');
    }

    public function exportExcel()
    {
        $jabatanUser = $this->user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-';
        return response()->streamDownload(function () use ($jabatanUser) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['RAPORT KINERJA INDIVIDU']);
            fputcsv($file, []);
            fputcsv($file, ['Nama', $this->user->name]);
            fputcsv($file, ['NIP', $this->user->pegawai->nip]);
            fputcsv($file, ['Jabatan', $jabatanUser]);
            fputcsv($file, ['Skor Akhir', $this->hasilPenilaian['skor_akhir']]);
            fputcsv($file, ['Predikat', $this->hasilPenilaian['mutu']]);
            fputcsv($file, []);
            fputcsv($file, ['Kompetensi', 'Nilai']);
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