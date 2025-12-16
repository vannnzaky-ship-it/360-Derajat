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
    public $hasilPenilaian = null;
    public $tableData = [];

    public function mount($siklusId, $userId)
    {
        $this->user = User::with('pegawai.jabatans')->findOrFail($userId);
        $this->siklus = Siklus::with('penilaianSession')->findOrFail($siklusId);

        // Ambil Data Raport (Reuse Logic Service)
        $sessionId = $this->siklus->penilaianSession->id;
        $jabatan = $this->user->pegawai->jabatans->first();

        if ($jabatan) {
            $service = new HitungSkorService();
            // 1. Data Umum
            $this->hasilPenilaian = $service->hitungNilaiAkhir($userId, $sessionId, $jabatan->id);
            // 2. Data Rincian Kompetensi
            $this->tableData = $service->getRekapKompetensi($userId, $sessionId, $jabatan->id);
        }
    }

    // --- PERBAIKAN DI SINI (EXPORT PDF) ---
    public function exportPdf()
    {
        // LOGIC LAMA (Hanya 1 Jabatan):
        // $jabatan = $this->user->pegawai->jabatans->first();
        // $jabatanUser = $jabatan->nama_jabatan ?? '-';

        // LOGIC BARU (Semua Jabatan):
        $jabatanUser = $this->user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-';

        $data = [
            'namaUser' => $this->user->name,
            'nipUser' => $this->user->pegawai->nip,
            'jabatanUser' => $jabatanUser, // Pakai variabel yang sudah diperbaiki
            'tableData' => $this->tableData,
            'finalScore' => $this->hasilPenilaian['skor_akhir'],
            'predikat' => $this->hasilPenilaian['mutu'],
            'siklus' => $this->siklus->tahun_ajaran . ' ' . $this->siklus->semester
        ];

        // Gunakan view yang sama dengan karyawan
        $pdf = Pdf::loadView('livewire.karyawan.cetak-raport-pdf', $data);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Raport-'.$this->user->name.'.pdf');
    }

    // --- PERBAIKAN DI SINI (EXPORT EXCEL) ---
    public function exportExcel()
    {
        // LOGIC BARU (Semua Jabatan):
        $jabatanUser = $this->user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-';
        
        $namaFile = 'Raport-' . str_replace(' ', '-', $this->user->name) . '.csv';

        $data = [
            'Nama' => $this->user->name,
            'NIP' => $this->user->pegawai->nip,
            'Jabatan' => $jabatanUser, // Pakai variabel yang sudah diperbaiki
            'Siklus' => $this->siklus->tahun_ajaran . ' ' . $this->siklus->semester,
            'Skor' => $this->hasilPenilaian['skor_akhir'],
            'Predikat' => $this->hasilPenilaian['mutu'],
            'Kompetensi' => $this->tableData
        ];

        return response()->streamDownload(function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header Info
            fputcsv($file, ['RAPORT KINERJA INDIVIDU']);
            fputcsv($file, []);
            fputcsv($file, ['Nama Pegawai', $data['Nama']]);
            fputcsv($file, ['NIP', $data['NIP']]);
            fputcsv($file, ['Jabatan', $data['Jabatan']]);
            fputcsv($file, ['Siklus', $data['Siklus']]);
            fputcsv($file, []);
            
            // Skor Utama
            fputcsv($file, ['SKOR AKHIR', $data['Skor']]);
            fputcsv($file, ['PREDIKAT', $data['Predikat']]);
            fputcsv($file, []);

            // Rincian
            fputcsv($file, ['RINCIAN KOMPETENSI', 'NILAI (0-100)']);
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