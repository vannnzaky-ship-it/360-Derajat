<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Siklus;
use App\Models\PenilaianSession;
use App\Models\Pegawai;
use App\Services\HitungSkorService;
use Barryvdh\DomPDF\Facade\Pdf; 

#[Layout('layouts.admin')] 
class Raport extends Component
{
    // Data Pengguna
    public $namaUser, $nipUser, $jabatanUser;
    public $pegawaiId, $jabatanId; 

    // Pilihan Semester
    public $listSemester = [];
    public $selectedSemester; 

    // Data Raport
    public $chartData = [];
    public $tableData = [];
    public $ranking = '-';
    public $finalScore = 0; 
    
    // [BARU] Status Kunci Raport
    public $isLocked = false;
    public $lockMessage = '';

    public function mount()
    {
        $user = Auth::user();

        if ($user) {
            $user->load('pegawai.jabatans'); 
            
            $this->namaUser = $user->name;
            $this->nipUser = $user->pegawai?->nip ?? 'N/A';
            $this->pegawaiId = $user->pegawai?->id;

            $firstJabatan = $user->pegawai?->jabatans->first();
            $this->jabatanId = $firstJabatan?->id;

            if ($user->pegawai && $user->pegawai->jabatans->isNotEmpty()) {
                $this->jabatanUser = $user->pegawai->jabatans->pluck('nama_jabatan')->implode(', ');
            } else {
                $this->jabatanUser = 'N/A';
            }

        } else {
            return redirect('/login'); 
        }

        // 1. Ambil List Semester
        $sikluses = Siklus::orderBy('tahun_ajaran', 'desc')->orderBy('semester', 'desc')->get();
        
        foreach($sikluses as $siklus) {
            $this->listSemester[$siklus->id] = $siklus->tahun_ajaran . ' ' . $siklus->semester;
        }

        // Set default ke semester pertama
        if (count($this->listSemester) > 0) {
            $this->selectedSemester = array_key_first($this->listSemester);
        }
        
        $this->loadRaportData();
    }

    public function updatedSelectedSemester()
    {
        $this->loadRaportData();
    }

    public function loadRaportData()
    {
        // Reset Data
        $this->chartData = ['labels' => [], 'scores' => []];
        $this->tableData = [];
        $this->ranking = '-';
        $this->finalScore = 0;
        $this->isLocked = false; // Reset lock status

        if (!$this->selectedSemester || !$this->jabatanId) return;

        // 1. Cari Sesi Penilaian
        $session = PenilaianSession::where('siklus_id', $this->selectedSemester)->latest()->first();

        if ($session) {
            // [LOGIKA PENGUNCIAN HASIL]
            // Cek apakah sekarang masih sebelum batas waktu?
            if ($session->batas_waktu && now() < $session->batas_waktu) {
                $this->isLocked = true;
                $this->lockMessage = 'Hasil penilaian untuk periode ini belum dibuka. Harap tunggu hingga tanggal ' . \Carbon\Carbon::parse($session->batas_waktu)->translatedFormat('d F Y H:i');
                return; // Stop proses, jangan hitung nilai
            }

            // Jika lolos (sudah lewat batas waktu), lanjut hitung nilai
            $service = new HitungSkorService();

            // 2. Hitung Nilai Per Kompetensi
            $rekapKompetensi = $service->getRekapKompetensi(Auth::id(), $session->id, $this->jabatanId);

            if (!empty($rekapKompetensi)) {
                $this->tableData = $rekapKompetensi;
                
                $this->chartData = [
                    'labels' => array_keys($rekapKompetensi),
                    'scores' => array_values($rekapKompetensi)
                ];

                // 3. Hitung Nilai Akhir Total
                $totalSkor = array_sum($rekapKompetensi);
                $jumlahItem = count($rekapKompetensi);
                $this->finalScore = $jumlahItem > 0 ? round($totalSkor / $jumlahItem) : 0;

                // 4. Hitung Ranking
                $this->hitungRanking($session->id, $this->finalScore);
            }
        } else {
            // Jika sesi tidak ditemukan (belum dibuat admin)
            $this->isLocked = true;
            $this->lockMessage = 'Sesi penilaian untuk periode ini belum tersedia.';
        }
    }

    private function hitungRanking($sessionId, $myScore)
    {
        $this->ranking = $this->getPredikat($myScore);
    }

    public function export($type)
    {
        // [TAMBAHAN KEAMANAN] Cegah export jika terkunci
        if ($this->isLocked) {
            session()->flash('error', 'Maaf, hasil penilaian belum dapat diunduh.');
            return;
        }

        $data = [
            'namaUser' => $this->namaUser,
            'nipUser' => $this->nipUser,
            'jabatanUser' => $this->jabatanUser,
            'tableData' => $this->tableData,
            'finalScore' => $this->finalScore,
            'predikat' => $this->getPredikat($this->finalScore),
            'siklus' => $this->listSemester[$this->selectedSemester] ?? '-'
        ];

        if ($type == 'pdf') {
            $pdf = Pdf::loadView('livewire.karyawan.cetak-raport-pdf', $data);
            $filename = 'Raport-' . str_replace(' ', '-', $this->namaUser) . '.pdf';
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename);
        }

        if ($type == 'excel') {
            $filename = 'Raport-' . str_replace(' ', '-', $this->namaUser) . '.csv';
            return response()->streamDownload(function () use ($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['RAPORT KINERJA 360']);
                fputcsv($file, ['Siklus', $data['siklus']]);
                fputcsv($file, []); 
                fputcsv($file, ['Nama', $data['namaUser']]);
                fputcsv($file, ['NIP', $data['nipUser']]);
                fputcsv($file, ['Jabatan', $data['jabatanUser']]);
                fputcsv($file, []); 
                fputcsv($file, ['SKOR AKHIR', $data['finalScore']]);
                fputcsv($file, ['PREDIKAT', $data['predikat']]);
                fputcsv($file, []); 
                fputcsv($file, ['Kompetensi', 'Nilai (0-100)']);
                foreach ($data['tableData'] as $kategori => $nilai) {
                    fputcsv($file, [$kategori, $nilai]);
                }
                fclose($file);
            }, $filename);
        }
    }

    private function getPredikat($score) {
        if($score >= 90) return 'Sangat Baik';
        if($score >= 76) return 'Baik';
        if($score >= 60) return 'Cukup';
        return 'Kurang';
    }

    public function render()
    {
        return view('livewire.karyawan.raport');
    }
}