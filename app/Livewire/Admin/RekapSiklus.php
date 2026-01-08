<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Siklus;
use App\Models\Pegawai;
use App\Models\PenilaianAlokasi; 
use App\Services\HitungSkorService;
use Barryvdh\DomPDF\Facade\Pdf;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

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
        $tempData = []; 

        $C = 70; // Baseline
        $m = 10; // Threshold

        foreach ($pegawais as $peg) {
            if(!$peg->user) continue;
            
            $namaJabatanFull = $peg->jabatans->pluck('nama_jabatan')->implode(', ');
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

            $v = PenilaianAlokasi::where('target_user_id', $peg->user->id)
                            ->where('penilaian_session_id', $sessionId)
                            ->where('status_nilai', 'Sudah')
                            ->count();

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
                'skor_akhir' => (float) $skorMurni, 
                'skor_ranking' => (float) $skorRanking, 
                'predikat' => $predikat,
                'total_penilai' => (int) $v,
                'foto' => $peg->user->profile_photo_path ?? null 
            ];
        }

        usort($tempData, function ($a, $b) {
            if (abs($b['skor_ranking'] - $a['skor_ranking']) > 0.001) {
                return $b['skor_ranking'] <=> $a['skor_ranking'];
            }
            if ($b['skor_akhir'] != $a['skor_akhir']) {
                return $b['skor_akhir'] <=> $a['skor_akhir'];
            }
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

    public function updatedSearch() { $this->loadData(); }

    // --- FUNGSI HELPER NAMA FILE (PENTING AGAR TIDAK ERROR) ---
    private function getSafeFilename($ext) {
        // Ganti '/' dengan '-' agar tidak dianggap folder
        $tahunBersih = str_replace(['/', '\\'], '-', $this->siklus->tahun_ajaran);
        $semester = $this->siklus->semester;
        return "Rekap-Siklus-{$tahunBersih}-{$semester}.{$ext}";
    }

    // --- EXPORT PDF ---
    public function exportPdf()
    {
        $pathLogo = public_path('images/logo-polkam.png');
        $logoBase64 = null;
        if (file_exists($pathLogo)) {
            try {
                $type = pathinfo($pathLogo, PATHINFO_EXTENSION);
                $data = file_get_contents($pathLogo);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } catch (\Exception $e) {}
        }

        $data = [
            'siklus' => $this->siklus,
            'pegawais' => $this->dataPegawai,
            'tanggal' => now()->translatedFormat('d F Y'),
            'logoBase64' => $logoBase64
        ];
        
        $pdf = Pdf::loadView('livewire.admin.cetak-rekap-siklus', $data)->setPaper('a4', 'landscape');
        
        // GUNAKAN NAMA FILE AMAN
        return response()->streamDownload(function () use ($pdf) { 
            echo $pdf->output(); 
        }, $this->getSafeFilename('pdf'));
    }

    // --- EXPORT EXCEL ---
    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Siklus');

        // LOGO
        $pathLogo = public_path('images/logo-polkam.png');
        if (file_exists($pathLogo)) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setPath($pathLogo);
            $drawing->setHeight(60);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }

        // HEADER
        $sheet->mergeCells('B1:G1'); $sheet->setCellValue('B1', 'POLITEKNIK KAMPAR');
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('B2:G2'); $sheet->setCellValue('B2', 'REKAPITULASI HASIL EVALUASI 360 DERAJAT');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('B3:G3'); $sheet->setCellValue('B3', 'Periode: ' . $this->siklus->tahun_ajaran . ' ' . $this->siklus->semester);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // HEADER TABEL
        $row = 5;
        $headers = ['RANK', 'NAMA PEGAWAI', 'NIP / NIK', 'JABATAN', 'TOTAL PENILAI', 'SKOR AKHIR', 'PREDIKAT'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.$row, $h);
            $col++;
        }
        
        // Style Header
        $sheet->getStyle("A$row:G$row")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle("A$row:G$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFC38E44');
        $sheet->getStyle("A$row:G$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ISI DATA
        $row++;
        $startData = $row;
        foreach ($this->dataPegawai as $index => $d) {
            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, $d['nama']);
            $sheet->setCellValue('C'.$row, $d['nip']);
            $sheet->setCellValue('D'.$row, $d['jabatan']);
            $sheet->setCellValue('E'.$row, $d['total_penilai']);
            $sheet->setCellValue('F'.$row, $d['skor_akhir']);
            $sheet->setCellValue('G'.$row, $d['predikat']);
            
            $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E$row:G$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $row++;
        }
        $endData = $row - 1;

        $sheet->getStyle("A".($startData-1).":G$endData")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach(range('A','G') as $colID) {
            $sheet->getColumnDimension($colID)->setAutoSize(true);
        }

        // TANDA TANGAN
        $ttdRow = $row + 3;
        $sheet->setCellValue('B'.$ttdRow, 'Mengetahui,');
        $sheet->setCellValue('F'.$ttdRow, 'Bangkinang, ' . now()->translatedFormat('d F Y'));
        $sheet->getStyle('B'.$ttdRow.':F'.$ttdRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $ttdRow++;
        $sheet->setCellValue('B'.$ttdRow, 'Wakil Direktur I');
        $sheet->setCellValue('F'.$ttdRow, 'Ka. BPM');
        $sheet->getStyle('B'.$ttdRow.':F'.$ttdRow)->getFont()->setBold(true);
        $sheet->getStyle('B'.$ttdRow.':F'.$ttdRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $ttdRow += 5;
        $sheet->setCellValue('B'.$ttdRow, '(....................................)');
        $sheet->setCellValue('F'.$ttdRow, '(....................................)');
        $ttdRow++;
        $sheet->setCellValue('B'.$ttdRow, 'NRP: .......................');
        $sheet->setCellValue('F'.$ttdRow, 'NRP: .......................');
        $sheet->getStyle('B'.($ttdRow-1).':F'.$ttdRow)->getFont()->setBold(true);
        $sheet->getStyle('B'.($ttdRow-1).':F'.$ttdRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // GUNAKAN NAMA FILE AMAN
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->save('php://output');
        }, $this->getSafeFilename('xlsx'));
    }

    public function render() { return view('livewire.admin.rekap-siklus'); }
}