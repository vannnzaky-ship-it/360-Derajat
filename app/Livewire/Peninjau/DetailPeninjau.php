<?php

namespace App\Livewire\Peninjau;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Siklus;
use App\Models\PenilaianAlokasi;
use App\Services\HitungSkorService;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.admin')]
class DetailPeninjau extends Component
{
    public $user, $siklus;
    
    // Properties Filter & Data
    public $selectedJabatanId = 'all';
    public $listJabatanFull = [];
    public $listJabatanIds = [];
    
    // Output Properties
    public $chartData = [];
    public $tableData = [];
    public $finalScore = 0;
    public $mutu = '-';
    public $ranking = '-';
    public $totalPegawai = 0;

    public function mount($siklusId, $userId)
    {
        $this->user = User::with('pegawai.jabatans')->findOrFail($userId);
        $this->siklus = Siklus::with('penilaianSession')->findOrFail($siklusId);

        // Load List Jabatan User untuk Filter
        if ($this->user->pegawai && $this->user->pegawai->jabatans->isNotEmpty()) {
            $this->listJabatanFull = $this->user->pegawai->jabatans;
            $this->listJabatanIds = $this->user->pegawai->jabatans->pluck('id')->toArray();
        }

        $this->loadRaportData();
    }

    public function updatedSelectedJabatanId() 
    { 
        $this->loadRaportData(); 
    }

    public function loadRaportData()
    {
        $this->resetData();
        $session = $this->siklus->penilaianSession;

        if ($session) {
            $service = new HitungSkorService();
            $totalNilai = 0; 
            $countJabatan = 0; 
            $tempKomp = []; 

            // Tentukan jabatan mana yang dihitung (Filter Logic)
            $jabatanToProcess = ($this->selectedJabatanId === 'all') 
                ? $this->listJabatanIds 
                : [$this->selectedJabatanId];

            foreach ($jabatanToProcess as $jId) {
                // Hitung skor akhir per jabatan
                $hasil = $service->hitungNilaiAkhir($this->user->id, $session->id, $jId);
                // Hitung rekap kompetensi per jabatan
                $rekap = $service->getRekapKompetensi($this->user->id, $session->id, $jId);

                if (isset($hasil['skor_akhir'])) {
                    $totalNilai += floatval($hasil['skor_akhir']);
                    $countJabatan++;
                    
                    if (!empty($rekap)) {
                        foreach ($rekap as $nama => $nilai) { 
                            $tempKomp[$nama][] = $nilai; 
                        }
                    }
                }
            }

            if ($countJabatan > 0) {
                $this->finalScore = round($totalNilai / $countJabatan, 2);
                $this->mutu = $this->getPredikat($this->finalScore);
                
                // Rata-rata kompetensi gabungan
                foreach ($tempKomp as $nama => $vals) { 
                    $this->tableData[$nama] = round(array_sum($vals) / count($vals), 2); 
                }
                
                $this->chartData = [
                    'labels' => array_keys($this->tableData), 
                    'scores' => array_values($this->tableData)
                ];
                
                // Hitung Ranking (Bayesian Logic)
                $rankingInfo = $this->calculateRank($this->user->id, $session->id, $this->selectedJabatanId);
                $this->ranking = $rankingInfo['rank'];
                $this->totalPegawai = $rankingInfo['total'];

                // Update Chart di Frontend
                $this->dispatch('refreshChart', data: $this->chartData);
            }
        }
    }

   private function calculateRank($userId, $sessionId, $jabatanIdFilter = 'all') {
        $service = new HitungSkorService();
        $C = 70; 
        $m = 10; 

        // 1. Ambil target user
        $targetIds = PenilaianAlokasi::where('penilaian_session_id', $sessionId)
                        ->distinct()
                        ->pluck('target_user_id');
        
        // 2. Cek Jabatan Filter untuk menentukan STRATEGI RANKING
        $targetLevel = null;
        if ($jabatanIdFilter !== 'all') {
             // Ambil Level dari jabatan yang sedang dipilih
             $jabatanDipilih = \App\Models\Jabatan::find($jabatanIdFilter);
             if ($jabatanDipilih) {
                 $targetLevel = $jabatanDipilih->level; // Misal: Level 3 (Kaprodi)
             }
        }

        $rankList = [];

        foreach ($targetIds as $id) {
            $u = User::find($id);
            if (!$u || !$u->pegawai) continue;

            $totalSkor = 0; 
            $jumlahJabatan = 0;
            
            // --- LOGIKA BARU: BANDINGKAN BY LEVEL (BUKAN BY ID) ---
            if ($jabatanIdFilter !== 'all' && $targetLevel) {
                // Cari apakah user ini punya jabatan DENGAN LEVEL YANG SAMA?
                // Contoh: Apakah user ini punya jabatan Level 3? (Entah itu Kaprodi TIF atau TPS)
                $jabatanSetara = $u->pegawai->jabatans->where('level', $targetLevel)->first();

                if (!$jabatanSetara) continue; // Skip jika tidak punya jabatan selevel

                // Hitung nilai pada jabatan yang setara tersebut
                $h = $service->hitungNilaiAkhir($id, $sessionId, $jabatanSetara->id);
                if (isset($h['skor_akhir']) && $h['skor_akhir'] > 0) {
                    $totalSkor = floatval($h['skor_akhir']);
                    $jumlahJabatan = 1;
                }
            } else {
                // LOGIKA GABUNGAN (ALL) - Tetap sama
                foreach ($u->pegawai->jabatans as $j) {
                    $h = $service->hitungNilaiAkhir($id, $sessionId, $j->id);
                    if (isset($h['skor_akhir']) && $h['skor_akhir'] > 0) { 
                        $totalSkor += floatval($h['skor_akhir']); 
                        $jumlahJabatan++; 
                    }
                }
            }

            // ... (Sisa kode perhitungan Bayesian sama persis) ...
            $skorMurni = ($jumlahJabatan > 0) ? round($totalSkor / $jumlahJabatan, 2) : 0;
            if ($skorMurni <= 0) continue;

            $v = PenilaianAlokasi::where('target_user_id', $id)
                    ->where('penilaian_session_id', $sessionId)
                    ->where('status_nilai', 'Sudah')
                    ->count();

            if ($v > 0) {
                $skorRanking = ( ($v / ($v + $m)) * $skorMurni ) + ( ($m / ($v + $m)) * $C );
            } else {
                $skorRanking = 0;
            }

            $rankList[] = [
                'id' => $id, 
                'nama' => $u->name,
                'skor_murni' => $skorMurni,
                'skor_ranking' => $skorRanking
            ];
        }

        // ... (Sorting & Return sama persis) ...
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
        $this->chartData = []; 
        $this->tableData = []; 
        $this->ranking = '-'; 
        $this->finalScore = 0; 
        $this->mutu = '-';
    }

    public function getLabelJabatanProperty()
    {
        if ($this->selectedJabatanId === 'all') {
            return $this->user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ') ?: '-';
        }
        $jbt = $this->listJabatanFull->firstWhere('id', $this->selectedJabatanId);
        return $jbt ? $jbt->nama_jabatan : 'N/A';
    }

    private function getPredikat($score) {
        if($score >= 90) return 'Sangat Baik';
        if($score >= 76) return 'Baik';
        if($score >= 60) return 'Cukup';
        return 'Kurang';
    }

    public function exportPdf()
    {
        if (empty($this->tableData)) return;

        $data = [
            'namaUser' => $this->user->name,
            'nipUser' => $this->user->pegawai->nip ?? '-',
            'jabatanUser' => $this->user->pegawai->jabatans->pluck('nama_jabatan')->implode(', '),
            'labelJabatan' => $this->label_jabatan,
            'tableData' => $this->tableData,
            'finalScore' => $this->finalScore,
            'mutu' => $this->mutu,
            'predikat' => $this->mutu,
            'siklus' => $this->siklus->tahun_ajaran . ' ' . $this->siklus->semester,
            'ranking' => $this->ranking,
            'totalPegawai' => $this->totalPegawai
        ];

        $pdf = Pdf::loadView('livewire.karyawan.cetak-raport-pdf', $data);
        $filename = 'Raport-PeninjauView-' . str_replace(' ', '-', $this->label_jabatan) . '-' . $this->user->name . '.pdf';
        
        return response()->streamDownload(fn() => print($pdf->output()), $filename);
    }

    public function exportExcel()
    {
        if (empty($this->tableData)) return;
        $filename = 'Raport-PeninjauView-' . str_replace(' ', '-', $this->label_jabatan) . '-' . $this->user->name . '.csv';

        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['RAPORT KINERJA PEGAWAI (PENINJAU VIEW)']);
            fputcsv($file, ['Nama', $this->user->name]);
            fputcsv($file, ['Filter Jabatan', $this->label_jabatan]);
            fputcsv($file, ['Skor Akhir', $this->finalScore]);
            fputcsv($file, ['Predikat', $this->mutu]);
            fputcsv($file, ['Peringkat', $this->ranking . ' dari ' . $this->totalPegawai]);
            fputcsv($file, []);
            foreach ($this->tableData as $k => $v) { fputcsv($file, [$k, $v]); }
            fclose($file);
        }, $filename);
    }

    public function render()
    {
        return view('livewire.peninjau.detail-peninjau');
    }
}