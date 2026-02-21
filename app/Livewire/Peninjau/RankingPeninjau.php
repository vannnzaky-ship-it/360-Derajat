<?php

namespace App\Livewire\Peninjau;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Siklus;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\PenilaianAlokasi; 
use App\Services\HitungSkorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

// Library Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

#[Layout('layouts.admin', ['title' => 'Ranking Peninjau'])]
class RankingPeninjau extends Component
{
    public $siklus;
    public $search = '';
    
    // --- FILTER PROPERTIES (Sama seperti Admin) ---
    public $filterKategori = ''; 
    public $filterBidang = ''; 

    public $listBidang = []; 
    public $dataPegawai = [];

    public function mount($siklusId)
    {
        $this->siklus = Siklus::with('penilaianSession')->findOrFail($siklusId);
        
        if (!$this->siklus->penilaianSession) {
            return redirect()->route('peninjau.laporan')->with('error', 'Sesi penilaian belum ada.');
        }

        // Ambil daftar bidang unik untuk dropdown
        $this->listBidang = Jabatan::select('bidang')
                            ->whereNotNull('bidang')
                            ->where('bidang', '!=', '')
                            ->distinct()
                            ->orderBy('bidang')
                            ->pluck('bidang')
                            ->toArray();

        $this->loadData();
    }

    // --- LOGIKA FILTER KATEGORI (Sama seperti Admin) ---
    private function cekKategori($namaJabatan)
    {
        $kategori = $this->filterKategori;
        if (!$kategori) return true;

        $jabatan = strtolower($namaJabatan);

        switch ($kategori) {
            case 'direktur':
                return $jabatan === 'direktur';
            case 'wadir':
                return str_contains($jabatan, 'wadir') || str_contains($jabatan, 'wakil direktur');
            case 'kaprodi':
                return str_contains($jabatan, 'prodi');
            case 'kalab':
                return str_contains($jabatan, 'lab');
            case 'kaunit':
                return str_starts_with($jabatan, 'ka ') && 
                       !str_contains($jabatan, 'sub') && 
                       !str_contains($jabatan, 'prodi') && 
                       !str_contains($jabatan, 'lab');
            case 'kasi':
                return str_contains($jabatan, 'sub bag') || str_contains($jabatan, 'kasi') || str_contains($jabatan, 'kepala seksi');
            case 'dosen':
                return str_contains($jabatan, 'dosen') || str_contains($jabatan, 'lektor') || str_contains($jabatan, 'asisten ahli');
            case 'karyawan':
                return str_starts_with($jabatan, 'staff') || str_starts_with($jabatan, 'staf') || str_contains($jabatan, 'cleaning') || str_contains($jabatan, 'satpam') || str_contains($jabatan, 'administrasi');
            default:
                return true;
        }
    }

    public function loadData()
    {
        // 1. Query Data Pegawai
        $query = Pegawai::with(['user', 'jabatans'])
            ->whereHas('user', function($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            });

        // Filter Bidang di level database
        if ($this->filterBidang) {
            $query->whereHas('jabatans', function($q) {
                $q->where('bidang', $this->filterBidang);
            });
        }

        $pegawais = $query->get();

        $sessionId = $this->siklus->penilaianSession->id;
        $service = new HitungSkorService(); 
        $tempData = []; 

        // Konfigurasi Bayesian
        $C = 70; 
        $m = 10; 

        foreach ($pegawais as $peg) {
            if(!$peg->user) continue;

            // 2. Filter Jabatan Spesifik (Logic Admin)
            $filteredJabatans = $peg->jabatans->filter(function ($jabatan) {
                if ($this->filterBidang && $jabatan->bidang != $this->filterBidang) {
                    return false;
                }
                return $this->cekKategori($jabatan->nama_jabatan);
            });

            if ($filteredJabatans->isEmpty()) continue;

            $namaJabatanDisplay = $filteredJabatans->pluck('nama_jabatan')->implode(', ');
            $bidangDisplay = $filteredJabatans->first()->bidang ?? '-';
            
            // Hitung Skor
            $totalSkor = 0;
            $jumlahJabatanHitung = 0;

            foreach ($filteredJabatans as $jabatan) {
                $hasil = $service->hitungNilaiAkhir($peg->user->id, $sessionId, $jabatan->id);
                
                if (isset($hasil['skor_akhir']) && $hasil['skor_akhir'] > 0) {
                    $totalSkor += floatval($hasil['skor_akhir']);
                    $jumlahJabatanHitung++;
                }
            }

            // Rata-rata Skor
            if ($jumlahJabatanHitung > 0) {
                $skorMurni = round($totalSkor / $jumlahJabatanHitung, 2);
                $predikat = $this->getPredikat($skorMurni);
            } else {
                $skorMurni = 0;
                $predikat = 'Belum Dinilai';
            }

            // Hitung Validitas (Jumlah Penilai pada jabatan terpilih)
            $jabatanIds = $filteredJabatans->pluck('id')->toArray();
            
            $v = PenilaianAlokasi::where('target_user_id', $peg->user->id)
                            ->where('penilaian_session_id', $sessionId)
                            ->whereIn('jabatan_id', $jabatanIds)
                            ->where('status_nilai', 'Sudah')
                            ->count();

            // Hitung Skor Ranking (Bayesian)
            if ($v > 0) {
                $skorRanking = ( ($v / ($v + $m)) * $skorMurni ) + ( ($m / ($v + $m)) * $C );
            } else {
                $skorRanking = 0;
            }

            $tempData[] = [
                'user_id' => $peg->user->id,
                'nip' => $peg->nip,
                'nama' => $peg->user->name,
                'jabatan' => $namaJabatanDisplay,
                'bidang' => $bidangDisplay,
                'skor_akhir' => (float) $skorMurni, 
                'skor_ranking' => (float) $skorRanking, 
                'predikat' => $predikat,
                'total_penilai' => (int) $v,
                'foto' => $peg->user->profile_photo_path ?? null 
            ];
        }

        // Sorting (Peringkat berdasarkan Skor Bayesian)
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

    // Listener Livewire
    public function updatedFilterKategori() { $this->loadData(); }
    public function updatedFilterBidang() { $this->loadData(); }
    public function updatedSearch() { $this->loadData(); }

    // --- FUNGSI EXPORT (Sama seperti Admin) ---
    private function getSafeFilename($ext) {
        $tahun = str_replace(['/', '\\'], '-', $this->siklus->tahun_ajaran);
        $smt = $this->siklus->semester;
        $filter = ($this->filterKategori ?: 'All') . ($this->filterBidang ? '-'.$this->filterBidang : '');
        return "Ranking-Peninjau-{$tahun}-{$smt}-{$filter}.{$ext}";
    }

    private function getJudulFilter() {
        $kat = match($this->filterKategori) {
            'direktur' => 'Direktur',
            'wadir' => 'Wakil Direktur',
            'kaprodi' => 'Kaprodi',
            'kalab' => 'Ka. Lab',
            'kaunit' => 'Ka. Unit',
            'kasi' => 'Kasi / Kasubbag',
            'dosen' => 'Dosen',
            'karyawan' => 'Staff/Karyawan',
            default => 'Seluruh Jabatan'
        };
        $bid = $this->filterBidang ? " (Bidang: {$this->filterBidang})" : "";
        return $kat . $bid;
    }

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
            'logoBase64' => $logoBase64,
            'kategori' => $this->getJudulFilter()
        ];
        
        // Menggunakan view cetak PDF yang sudah ada (sesuaikan path jika perlu)
        // Disarankan membuat: livewire.peninjau.cetak-ranking-pdf yang isinya mirip admin
        $pdf = Pdf::loadView('livewire.peninjau.cetak-ranking-pdf', $data)->setPaper('a4', 'landscape');
        return response()->streamDownload(function () use ($pdf) { echo $pdf->output(); }, $this->getSafeFilename('pdf'));
    }

    public function exportExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ranking Peninjau');

        // Logo
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

        // Header Text
        $sheet->mergeCells('B1:H1'); $sheet->setCellValue('B1', 'POLITEKNIK KAMPAR');
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $judul = 'PERINGKAT KINERJA PEGAWAI (360 DERAJAT) - ' . strtoupper($this->getJudulFilter());
        $sheet->mergeCells('B2:H2'); $sheet->setCellValue('B2', $judul);
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('B3:H3'); $sheet->setCellValue('B3', 'Periode: ' . $this->siklus->tahun_ajaran . ' ' . $this->siklus->semester);
        $sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Tabel
        $row = 5;
        $headers = ['RANK', 'NAMA PEGAWAI', 'NIP', 'BIDANG', 'JABATAN', 'PENILAI', 'SKOR AKHIR', 'PREDIKAT'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.$row, $h);
            $col++;
        }
        
        $sheet->getStyle("A$row:H$row")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle("A$row:H$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFC38E44');
        $sheet->getStyle("A$row:H$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row++;
        $startData = $row;
        foreach ($this->dataPegawai as $index => $d) {
            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValue('B'.$row, $d['nama']);
            $sheet->setCellValue('C'.$row, $d['nip']);
            $sheet->setCellValue('D'.$row, $d['bidang']);
            $sheet->setCellValue('E'.$row, $d['jabatan']);
            $sheet->setCellValue('F'.$row, $d['total_penilai']);
            $sheet->setCellValue('G'.$row, $d['skor_akhir']);
            $sheet->setCellValue('H'.$row, $d['predikat']);
            
            $sheet->getStyle("A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F$row:H$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }
        $endData = $row - 1;

        // Border
        $sheet->getStyle("A".($startData-1).":H$endData")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        foreach(range('A','H') as $colID) {
            $sheet->getColumnDimension($colID)->setAutoSize(true);
        }

        // Tanda Tangan
        $ttdRow = $row + 3;
        $sheet->setCellValue('B'.$ttdRow, 'Mengetahui,');
        $sheet->setCellValue('G'.$ttdRow, 'Bangkinang, ' . now()->translatedFormat('d F Y'));
        $sheet->getStyle('B'.$ttdRow.':G'.$ttdRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $ttdRow++;
        $sheet->setCellValue('B'.$ttdRow, 'Wakil Direktur I');
        $sheet->setCellValue('G'.$ttdRow, 'Ka. BPM');
        $sheet->getStyle('B'.$ttdRow.':G'.$ttdRow)->getFont()->setBold(true);
        $sheet->getStyle('B'.$ttdRow.':G'.$ttdRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $ttdRow += 5;
        $sheet->setCellValue('B'.$ttdRow, '(....................................)');
        $sheet->setCellValue('G'.$ttdRow, '(....................................)');
        $ttdRow++;
        $sheet->setCellValue('B'.$ttdRow, 'NRP: .......................');
        $sheet->setCellValue('G'.$ttdRow, 'NRP: .......................');
        $sheet->getStyle('B'.($ttdRow-1).':G'.$ttdRow)->getFont()->setBold(true);
        $sheet->getStyle('B'.($ttdRow-1).':G'.$ttdRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

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