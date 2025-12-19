<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Siklus;
use App\Models\User;
use App\Models\Jabatan; // PENTING: Tambahkan Model Jabatan
use App\Models\PenilaianSession;
use App\Models\PenilaianAlokasi;
use App\Services\HitungSkorService;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public $chartData = [];
    public $tableData = []; 
    public $ranking = '-';
    public $totalPegawai = 0;
    public $finalScore = 0; 
    public $mutu = '-';
    
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
            
            // Cek Lock System
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
                $this->mutu = $this->getPredikat($this->finalScore);
                
                foreach ($tempKomp as $nama => $vals) { 
                    $this->tableData[$nama] = round(array_sum($vals) / count($vals), 2); 
                }
                
                $this->chartData = ['labels' => array_keys($this->tableData), 'scores' => array_values($this->tableData)];
                
                // UPDATE: Kirim parameter filter jabatan ke fungsi ranking
                $rankingInfo = $this->calculateRank($this->user->id, $session->id, $this->selectedJabatanId);
                
                $this->ranking = $rankingInfo['rank'];
                $this->totalPegawai = $rankingInfo['total'];

                $this->dispatch('refreshChart', data: $this->chartData);
            }
        }
    }

    // --- LOGIKA RANKING BARU (Cross-Department Level & Bayesian) ---
    private function calculateRank($userId, $sessionId, $jabatanIdFilter = 'all') {
        $service = new HitungSkorService();
        $C = 70; // Baseline
        $m = 10; // Threshold
        
        $targetIds = PenilaianAlokasi::where('penilaian_session_id', $sessionId)
                        ->distinct()
                        ->pluck('target_user_id');
        
        // Cek Level Target jika filter aktif
        $targetLevel = null;
        if ($jabatanIdFilter !== 'all') {
             $jabatanDipilih = Jabatan::find($jabatanIdFilter);
             if ($jabatanDipilih) {
                 $targetLevel = $jabatanDipilih->level;
             }
        }

        $rankList = [];

        foreach ($targetIds as $id) {
            $u = User::find($id);
            if (!$u || !$u->pegawai) continue;

            $totalSkor = 0; $jumlahJabatan = 0;
            
            if ($jabatanIdFilter !== 'all' && $targetLevel) {
                // LOGIKA: Cari jabatan setara (Level sama) pada user target
                $jabatanSetara = $u->pegawai->jabatans->where('level', $targetLevel)->first();

                if (!$jabatanSetara) continue; // Skip jika tidak punya jabatan selevel

                $h = $service->hitungNilaiAkhir($id, $sessionId, $jabatanSetara->id);
                if (isset($h['skor_akhir']) && $h['skor_akhir'] > 0) {
                    $totalSkor = floatval($h['skor_akhir']);
                    $jumlahJabatan = 1;
                }
            } else {
                // LOGIKA: Gabungan (Rata-rata semua jabatan)
                foreach ($u->pegawai->jabatans as $j) {
                    $h = $service->hitungNilaiAkhir($id, $sessionId, $j->id);
                    if (isset($h['skor_akhir']) && $h['skor_akhir'] > 0) { 
                        $totalSkor += floatval($h['skor_akhir']); 
                        $jumlahJabatan++; 
                    }
                }
            }

            $skorMurni = ($jumlahJabatan > 0) ? round($totalSkor / $jumlahJabatan, 2) : 0;
            if ($skorMurni <= 0) continue;

            // Hitung Bayesian Score
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

        // Sorting (Bayesian -> Murni -> Nama)
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

    public function exportPdf()
    {
        if ($this->isLocked || empty($this->tableData)) return;

        $data = [
            'namaUser' => $this->namaUser,
            'nipUser' => $this->nipUser,
            'jabatanUser' => $this->jabatanUser,
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
        $filename = 'Raport-' . str_replace(' ', '-', $this->label_jabatan) . '-' . $this->namaUser . '.pdf';
        
        return response()->streamDownload(fn() => print($pdf->output()), $filename);
    }

    public function exportExcel()
    {
        if ($this->isLocked || empty($this->tableData)) return;
        $filename = 'Raport-' . str_replace(' ', '-', $this->label_jabatan) . '-' . $this->namaUser . '.csv';

        return response()->streamDownload(function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['RAPORT KINERJA PEGAWAI']);
            fputcsv($file, ['Nama', $this->namaUser]);
            fputcsv($file, ['Status Raport', $this->label_jabatan]);
            fputcsv($file, ['Skor Akhir', $this->finalScore]);
            fputcsv($file, ['Predikat', $this->mutu]);
            fputcsv($file, []);
            foreach ($this->tableData as $k => $v) { fputcsv($file, [$k, $v]); }
            fclose($file);
        }, $filename);
    }

    private function resetData() {
        $this->chartData = []; $this->tableData = []; $this->ranking = '-'; $this->finalScore = 0; $this->isLocked = false;
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

    public function render() { return view('livewire.karyawan.raport'); }
}