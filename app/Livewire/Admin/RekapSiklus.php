<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Siklus;
use App\Models\Pegawai;
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
        $pegawais = Pegawai::with(['user', 'jabatans'])
            ->whereHas('user', function($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            })
            ->whereHas('jabatans') 
            ->get();

        $sessionId = $this->siklus->penilaianSession->id;
        $service = new HitungSkorService();
        $tempData = []; // Tampung dulu di array sementara

        foreach ($pegawais as $peg) {
            if(!$peg->user) continue;
            
            $namaJabatanFull = $peg->jabatans->pluck('nama_jabatan')->implode(', ');
            $jabatanUtama = $peg->jabatans->first(); 
            
            $skor = 0;
            $predikat = 'Belum Dinilai';

            if ($jabatanUtama) {
                $hasil = $service->hitungNilaiAkhir($peg->user->id, $sessionId, $jabatanUtama->id);
                $skor = $hasil['skor_akhir'];
                $predikat = $hasil['mutu'];
            }

            $tempData[] = [
                'user_id' => $peg->user->id,
                'nip' => $peg->nip,
                'nama' => $peg->user->name,
                'jabatan' => $namaJabatanFull,
                'skor_akhir' => $skor,
                'predikat' => $predikat,
            ];
        }

        // --- SORTING OTOMATIS BERDASARKAN SKOR TERTINGGI (RANKING) ---
        // Kita gunakan collection Laravel untuk sorting array multi-dimensi dengan mudah
        $this->dataPegawai = collect($tempData)->sortByDesc('skor_akhir')->values()->all();
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

            fputcsv($file, ['Peringkat', 'NIP', 'Nama Pegawai', 'Jabatan', 'Skor Akhir', 'Predikat']);

            foreach ($data as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row['nip'],
                    $row['nama'],
                    $row['jabatan'], 
                    $row['skor_akhir'],
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