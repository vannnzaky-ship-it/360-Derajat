<?php

namespace App\Livewire\Peninjau;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Siklus;
use App\Models\User;
use App\Models\PenilaianAlokasi; 
use App\Services\HitungSkorService;
use Barryvdh\DomPDF\Facade\Pdf;

// Library Excel (Wajib Import)
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

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
        
        $targetUsers = PenilaianAlokasi::where('penilaian_session_id', $sessionId)
            ->distinct()
            ->pluck('target_user_id');

        $users = User::with(['pegawai.jabatans'])->whereIn('id', $targetUsers)->get();
        $service = new HitungSkorService();
        $tempData = [];

        // --- KONFIGURASI BAYESIAN ---
        $C = 70; // Baseline
        $m = 10; // Threshold Vote

        foreach ($users as $user) {
            // Filter Search
            if ($this->search && stripos($user->name, $this->search) === false) {
                continue;
            }

            $totalSkor = 0;
            $jumlahJabatan = 0;
            
            // Hitung Skor Murni
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

            // Hitung Jumlah Penilai (v)
            $v = PenilaianAlokasi::where('target_user_id', $user->id)
                            ->where('penilaian_session_id', $sessionId)
                            ->where('status_nilai', 'Sudah')
                            ->count();

            // Hitung Skor Ranking (Bayesian)
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
                'skor_akhir' => (float) $skorMurni,
                'skor_ranking' => (float) $skorRanking, 
                'predikat' => $predikat,
                'total_penilai' => (int) $v,
                'foto' => $user->profile_photo_path ?? null 
            ];
        }

        // Sorting Bayesian
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

    public function updatedSearch() { $this->hitungRanking(); }

    // --- FUNGSI HELPER NAMA FILE ---
    private function getSafeFilename($ext) {
        $tahunBersih = str_replace(['/', '\\'], '-', $this->siklus->tahun_ajaran);
        $semester = $this->siklus->semester;
        return "Laporan-Ranking-{$tahunBersih}-{$semester}.{$ext}";
    }

    // --- EXPORT PDF (SAMA SEPERTI REKAP SIKLUS) ---
    public function exportPdf()
    {
        // Logo Base64 Anti Error
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
            'pegawais' => $this->dataPegawai, // Perhatikan nama variabel di view PDF harus 'pegawais'
            'tanggal' => now()->translatedFormat('d F Y'),
            'logoBase64' => $logoBase64
        ];
        
        // Gunakan view yang sama dengan Rekap Siklus agar konsisten
        $pdf = Pdf::loadView('livewire.peninjau.cetak-ranking-pdf', $data)->setPaper('a4', 'landscape');
        
        return response()->streamDownload(function() use ($pdf) { 
            echo $pdf->output(); 
        }, $this->getSafeFilename('pdf'));
    }
    
    // --- EXPORT EXCEL (SAMA SEPERTI REKAP SIKLUS) ---
    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ranking');

        // 1. LOGO
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

        // 2. KOP SURAT
        $sheet->mergeCells('B1:G1'); $sheet->setCellValue('B1', 'POLITEKNIK KAMPAR');
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('B2:G2'); $sheet->setCellValue('B2', 'PERINGKAT KINERJA PEGAWAI (360 DERAJAT)');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('B3:G3'); $sheet->setCellValue('B3', 'Periode: ' . $this->siklus->tahun_ajaran . ' ' . $this->siklus->semester);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 3. HEADER TABEL
        $row = 5;
        $headers = ['RANK', 'NAMA PEGAWAI', 'NRP', 'JABATAN', 'TOTAL PENILAI', 'SKOR AKHIR', 'PREDIKAT'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.$row, $h);
            $col++;
        }
        
        // Style Header
        $sheet->getStyle("A$row:G$row")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle("A$row:G$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFC38E44'); 
        $sheet->getStyle("A$row:G$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 4. ISI DATA
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

        // Border Tabel
        $sheet->getStyle("A".($startData-1).":G$endData")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Auto Width
        foreach(range('A','G') as $colID) {
            $sheet->getColumnDimension($colID)->setAutoSize(true);
        }

        // 5. TANDA TANGAN
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

        // Gunakan nama file aman
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->save('php://output');
        }, $this->getSafeFilename('xlsx'));
    }

    public function render()
    {
        return view('livewire.peninjau.ranking-peninjau');
    }
}