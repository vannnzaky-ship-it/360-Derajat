<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Siklus;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\PenilaianSession;
use App\Models\PenilaianAlokasi;
use App\Services\HitungSkorService;
use Barryvdh\DomPDF\Facade\Pdf;

// Library Excel & Chart (Wajib ada)
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\XAxis;
use PhpOffice\PhpSpreadsheet\Chart\YAxis;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing; 

#[Layout('layouts.admin')]
class Raport extends Component
{
    public $user;
    public $namaUser, $nipUser, $jabatanUser; 
    public $listJabatanIds = [];
    public $listJabatanFull = [];

    public $listSemester = [];
    public $selectedSemester; 
    public $selectedJabatanId = 'all';
    public $siklus; 

    // Output Properties
    public $chartData = [];
    public $tableData = []; 
    public $ranking = '-';
    public $totalPegawai = 0;
    public $finalScore = 0; 
    public $mutu = '-';
    
    // Fitur Karyawan (Lock System)
    public $isLocked = false;
    public $lockMessage = '';
    public $deadline;

    public function mount()
    {
        $this->user = Auth::user();
        if (!$this->user) return redirect('/login'); 

        $this->user->load('pegawai.jabatans'); 
        $this->namaUser = $this->user->name;
        $this->nipUser = $this->user->pegawai?->nip ?? 'N/A';

        if ($this->user->pegawai && $this->user->pegawai->jabatans->isNotEmpty()) {
            $this->listJabatanFull = $this->user->pegawai->jabatans;
            $this->listJabatanIds = $this->user->pegawai->jabatans->pluck('id')->toArray();
            $this->jabatanUser = $this->user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ');
        } else {
            $this->jabatanUser = 'N/A';
        }

        $sikluses = Siklus::orderBy('tahun_ajaran', 'desc')->orderBy('semester', 'desc')->get();
        foreach($sikluses as $s) {
            $this->listSemester[$s->id] = $s->tahun_ajaran . ' ' . $s->semester;
        }

        $siklusAktif = Siklus::where('status', 'Aktif')->first();
        $this->selectedSemester = $siklusAktif ? $siklusAktif->id : (count($this->listSemester) > 0 ? array_key_first($this->listSemester) : null);
        
        $this->loadRaportData();
    }

    public function updatedSelectedSemester() { $this->loadRaportData(); }
    public function updatedSelectedJabatanId() { $this->loadRaportData(); }

    public function loadRaportData()
    {
        $this->resetData();
        if (!$this->selectedSemester) return;

        $this->siklus = Siklus::find($this->selectedSemester);
        $session = PenilaianSession::where('siklus_id', $this->selectedSemester)->latest()->first();

        if ($session) {
            $this->deadline = $session->batas_waktu;
            
            // CEK LOCK: Karyawan tidak boleh lihat nilai sebelum batas waktu habis
            if ($this->siklus->status == 'Aktif' && $session->batas_waktu && now() < $session->batas_waktu) {
                $this->isLocked = true;
                $this->lockMessage = 'Hasil penilaian periode ini masih berjalan. Raport akan terbuka otomatis setelah batas waktu berakhir.';
                return; 
            }

            $service = new HitungSkorService();
            $totalNilai = 0; $countJabatan = 0; $tempKomp = []; 

            $jabatanToProcess = ($this->selectedJabatanId === 'all') ? $this->listJabatanIds : [$this->selectedJabatanId];

            foreach ($jabatanToProcess as $jId) {
                $hasil = $service->hitungNilaiAkhir($this->user->id, $session->id, $jId);
                $rekap = $service->getRekapKompetensi($this->user->id, $session->id, $jId);
                
                if (isset($hasil['skor_akhir'])) {
                    $totalNilai += floatval($hasil['skor_akhir']);
                    $countJabatan++;
                    
                    if (!empty($rekap)) {
                        foreach ($rekap as $nama => $nilai) { $tempKomp[$nama][] = $nilai; }
                    }
                }
            }

            if ($countJabatan > 0) {
                $this->finalScore = round($totalNilai / $countJabatan, 2);
                
                // Gunakan Standar Polkam
                $this->mutu = $this->getPredikatPolkam($this->finalScore);
                
                foreach ($tempKomp as $nama => $vals) { 
                    $this->tableData[$nama] = round(array_sum($vals) / count($vals), 2); 
                }
                
                $this->chartData = ['labels' => array_keys($this->tableData), 'scores' => array_values($this->tableData)];
                
                // Ranking Advanced (Level & Bayesian)
                $rankingInfo = $this->calculateRank($this->user->id, $session->id, $this->selectedJabatanId);
                
                $this->ranking = $rankingInfo['rank'];
                $this->totalPegawai = $rankingInfo['total'];

                $this->dispatch('refreshChart', data: $this->chartData);
            }
        }
    }

    // --- LOGIKA RANKING (Cross-Department Level & Bayesian) ---
    private function calculateRank($userId, $sessionId, $jabatanIdFilter = 'all') {
        $service = new HitungSkorService();
        $C = 70; $m = 10;
        
        $targetIds = PenilaianAlokasi::where('penilaian_session_id', $sessionId)->distinct()->pluck('target_user_id');
        
        $targetLevel = null;
        if ($jabatanIdFilter !== 'all') {
             $jabatanDipilih = Jabatan::find($jabatanIdFilter);
             if ($jabatanDipilih) $targetLevel = $jabatanDipilih->level;
        }

        $rankList = [];

        foreach ($targetIds as $id) {
            $u = User::find($id);
            if (!$u || !$u->pegawai) continue;
            $totalSkor = 0; $jumlahJabatan = 0;
            
            if ($jabatanIdFilter !== 'all' && $targetLevel) {
                $jabatanSetara = $u->pegawai->jabatans->where('level', $targetLevel)->first();
                if (!$jabatanSetara) continue; 
                $h = $service->hitungNilaiAkhir($id, $sessionId, $jabatanSetara->id);
                if (isset($h['skor_akhir']) && $h['skor_akhir'] > 0) {
                    $totalSkor = floatval($h['skor_akhir']); $jumlahJabatan = 1;
                }
            } else {
                foreach ($u->pegawai->jabatans as $j) {
                    $h = $service->hitungNilaiAkhir($id, $sessionId, $j->id);
                    if (isset($h['skor_akhir']) && $h['skor_akhir'] > 0) { 
                        $totalSkor += floatval($h['skor_akhir']); $jumlahJabatan++; 
                    }
                }
            }

            $skorMurni = ($jumlahJabatan > 0) ? round($totalSkor / $jumlahJabatan, 2) : 0;
            if ($skorMurni <= 0) continue;

            $v = PenilaianAlokasi::where('target_user_id', $id)->where('penilaian_session_id', $sessionId)->where('status_nilai', 'Sudah')->count();
            if ($v > 0) {
                $skorRanking = ( ($v / ($v + $m)) * $skorMurni ) + ( ($m / ($v + $m)) * $C );
            } else { $skorRanking = 0; }

            $rankList[] = ['id' => $id, 'nama' => $u->name, 'skor_murni' => $skorMurni, 'skor_ranking' => $skorRanking];
        }

        usort($rankList, function ($a, $b) {
            if (abs($b['skor_ranking'] - $a['skor_ranking']) > 0.001) return $b['skor_ranking'] <=> $a['skor_ranking'];
            if (abs($b['skor_murni'] - $a['skor_murni']) > 0.001) return $b['skor_murni'] <=> $a['skor_murni'];
            return strcmp($a['nama'], $b['nama']);
        });

        foreach ($rankList as $index => $data) {
            if ($data['id'] == $userId) return ['rank' => $index + 1, 'total' => count($rankList)];
        }
        return ['rank' => '-', 'total' => count($rankList)];
    }

    private function resetData() {
        $this->chartData = []; $this->tableData = []; $this->ranking = '-'; $this->finalScore = 0; $this->isLocked = false; $this->mutu = '-';
    }

    public function getLabelJabatanProperty()
    {
        if ($this->selectedJabatanId === 'all') {
            return $this->user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-';
        }
        $jbt = $this->listJabatanFull->firstWhere('id', $this->selectedJabatanId);
        return $jbt ? $jbt->nama_jabatan : 'N/A';
    }

    private function getPredikatPolkam($nilai) {
        if ($nilai > 87.5) return 'Baik Sekali';
        if ($nilai > 75) return 'Baik';
        if ($nilai > 62.5) return 'Cukup';
        if ($nilai > 50) return 'Kurang';
        return 'Buruk';
    }

    // --- Helper Nama File ---
    private function getGeneratedFilename($ext) {
        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $this->namaUser));
        if ($this->selectedJabatanId === 'all') {
            $jabatanLabel = 'Seluruh-Jabatan';
        } else {
            $jbt = $this->listJabatanFull->firstWhere('id', $this->selectedJabatanId);
            $rawName = $jbt ? $jbt->nama_jabatan : 'Jabatan';
            $jabatanLabel = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $rawName));
        }
        return "Raport-{$jabatanLabel}-{$cleanName}.{$ext}";
    }

    // --- EXPORT PDF (FITUR LENGKAP + ANTI ERROR GD) ---
    public function exportPdf()
    {
        if ($this->isLocked || empty($this->tableData)) return;

        // 1. Setup Logo
        $pathLogo = public_path('images/logo-polkam.png');
        $pathLogo = str_replace('\\', '/', $pathLogo);
        $logoBase64 = null;
        if (file_exists($pathLogo)) {
            try {
                $type = pathinfo($pathLogo, PATHINFO_EXTENSION);
                $data = file_get_contents($pathLogo);
                $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } catch (\Exception $e) {}
        }

        // 2. Data Nilai (Hanya 360)
        $rataRata = $this->finalScore;
        $predikatAkhir = $this->getPredikatPolkam($rataRata);

        // 3. Data View
        $data = [
            'namaUser' => $this->namaUser,
            'unitKerja' => $this->user->pegawai->unit_kerja ?? 'Politeknik Kampar',
            'jabatan' => $this->label_jabatan,
            'tableData' => $this->tableData,
            
            'finalScore' => $this->finalScore,
            'nilai360' => $this->finalScore, // Disisakan
            'rataRata' => $rataRata,
            'mutu' => $predikatAkhir,
            
            'tanggal_cetak' => now()->translatedFormat('d F Y'),
            'logoBase64' => $logoBase64
        ];

        $pdf = Pdf::loadView('livewire.karyawan.cetak-raport-pdf', $data)->setPaper('a4', 'portrait');
        return response()->streamDownload(fn() => print($pdf->output()), $this->getGeneratedFilename('pdf'));
    }

    public function exportExcel()
    {
        // 1. Cek Data
        if (empty($this->tableData)) return;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // --- PERBAIKAN 1: NAMA SHEET JANGAN PAKAI SPASI ---
        $sheet->setTitle('Raport'); 

        // --- 1. LOGO ASLI ---
        $pathLogo = public_path('images/logo-polkam.png');
        if (file_exists($pathLogo)) {
            $drawing = new Drawing();
            $drawing->setName('Logo Polkam');
            $drawing->setPath($pathLogo);
            $drawing->setHeight(60);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }

        // --- 2. HEADER ---
        $sheet->mergeCells('B1:F1'); 
        $sheet->setCellValue('B1', 'POLITEKNIK KAMPAR');
        $sheet->getStyle('B1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('B2:F2'); 
        $sheet->setCellValue('B2', 'RAPORT HASIL EVALUASI 360 DERAJAT');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- 3. BIODATA ---
        $nama = $this->namaUser ?? $this->user->name; 
        
        $row = 5;
        $sheet->setCellValue('A'.$row, 'Nama');       $sheet->setCellValue('C'.$row, ': ' . $nama); $row++;
        $sheet->setCellValue('A'.$row, 'Jabatan');    $sheet->setCellValue('C'.$row, ': ' . $this->label_jabatan); $row++;
        $sheet->setCellValue('A'.$row, 'Periode');    $sheet->setCellValue('C'.$row, ': ' . $this->siklus->tahun_ajaran . ' ' . $this->siklus->semester); $row+=2;

        // --- 4. TABEL NILAI ---
        $sheet->setCellValue('A'.$row, 'KOMPETENSI / ASPEK PENILAIAN');
        $sheet->setCellValue('D'.$row, 'NILAI');
        
        $sheet->mergeCells("A$row:C$row");
        $sheet->getStyle("A$row:D$row")->getFont()->setBold(true);
        $sheet->getStyle("A$row:D$row")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
        $sheet->getStyle("A$row:D$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $row++;
        $startData = $row;
        foreach ($this->tableData as $kategori => $nilai) {
            $sheet->mergeCells("A$row:C$row");
            $sheet->setCellValue('A' . $row, $kategori);
            $sheet->setCellValue('D' . $row, (float)$nilai);
            $row++;
        }
        $endData = $row - 1;

        // TOTAL & MUTU
        $sheet->mergeCells("A$row:C$row"); $sheet->setCellValue('A' . $row, 'RATA-RATA NILAI AKHIR');
        $sheet->setCellValue('D' . $row, (float)$this->finalScore);
        $sheet->getStyle("A$row:D$row")->getFont()->setBold(true);
        $row++;

        $sheet->mergeCells("A$row:C$row"); $sheet->setCellValue('A' . $row, 'MUTU / PREDIKAT');
        $sheet->setCellValue('D' . $row, $this->mutu);
        $sheet->getStyle("A$row:D$row")->getFont()->setBold(true);

        $sheet->getStyle("A".($startData-1).":D$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // --- 5. TANDA TANGAN ---
        $ttdRow = $row + 3;
        $sheet->setCellValue('B'.$ttdRow, 'Mengetahui,');
        $sheet->setCellValue('F'.$ttdRow, 'Bangkinang, ' . date('d F Y'));
        $sheet->getStyle('B'.$ttdRow.':F'.$ttdRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $ttdRow++;
        $sheet->setCellValue('B'.$ttdRow, 'Wakil Direktur I');
        $sheet->setCellValue('F'.$ttdRow, 'Ka. BPM');
        $sheet->getStyle('B'.$ttdRow.':F'.$ttdRow)->getFont()->setBold(true);
        $sheet->getStyle('B'.$ttdRow.':F'.$ttdRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $ttdRow += 5;
        $sheet->setCellValue('B'.$ttdRow, '(....................................)');
        $sheet->setCellValue('F'.$ttdRow, 'Sri Wahyuni, SP, M.Si');
        $ttdRow++;
        $sheet->setCellValue('B'.$ttdRow, 'NIP/NRP: .......................');
        $sheet->setCellValue('F'.$ttdRow, 'NRP: 110907028');
        $sheet->getStyle('B'.($ttdRow-1).':F'.$ttdRow)->getFont()->setBold(true);
        $sheet->getStyle('B'.($ttdRow-1).':F'.$ttdRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- 6. GRAFIK CHART (DIPINDAHKAN KE BAWAH TTD & DIUBAH JADI HORIZONTAL) ---
        $chartRowStart = $ttdRow + 3; // Mulai 3 baris di bawah tanda tangan terakhir

        // Sumbu X (Nilai) & Sumbu Y (Label Kompetensi) - DIBALIK AGAR HORIZONTAL
        $xAxis = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Raport!$A$'.$startData.':$A$'.$endData, null, count($this->tableData))];
        $values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Raport!$D$'.$startData.':$D$'.$endData, null, count($this->tableData))];
        
        // Gunakan TYPE_BARCHART dengan arah DIRECTION_BAR untuk horizontal
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,       
            DataSeries::GROUPING_STANDARD,
            range(0, count($values)-1),
            [],
            $xAxis, // Label di sumbu Y
            $values // Nilai di sumbu X
        );
        // Ubah arah plot menjadi BAR (Horizontal)
        $series->setPlotDirection(DataSeries::DIRECTION_BAR);

        $plotArea = new PlotArea(null, [$series]);
        $title = new Title('Statistik Kompetensi');
        $chart = new Chart('chart1', $title, null, $plotArea);
        
        // Posisi Grafik: Di bawah Tanda Tangan, melebar dari A sampai F
        $chart->setTopLeftPosition('A'.$chartRowStart); 
        $chart->setBottomRightPosition('F'.($chartRowStart + 15)); // Tinggi grafik sekitar 15 baris
        
        $sheet->addChart($chart);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->setIncludeCharts(true);
            $writer->setPreCalculateFormulas(false);
            $writer->save('php://output');
        }, $this->getGeneratedFilename('xlsx'));
    }

    public function render() { return view('livewire.karyawan.raport'); }
}