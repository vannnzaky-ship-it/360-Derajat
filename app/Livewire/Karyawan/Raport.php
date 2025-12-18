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
    public $pegawaiId; 
    public $listJabatanIds = []; // [BARU] Simpan semua ID jabatan user

    // Pilihan Semester
    public $listSemester = [];
    public $selectedSemester; 

    // Data Raport
    public $chartData = [];
    public $tableData = []; // Akan berisi rekap gabungan
    public $ranking = '-';
    public $finalScore = 0; 
    
    // Status Kunci Raport
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

            // [PERBAIKAN] Ambil SEMUA ID Jabatan
            if ($user->pegawai && $user->pegawai->jabatans->isNotEmpty()) {
                $this->listJabatanIds = $user->pegawai->jabatans->pluck('id')->toArray();
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
        $this->isLocked = false; 

        if (!$this->selectedSemester || empty($this->listJabatanIds)) return;

        // 1. Cari Sesi Penilaian
        $session = PenilaianSession::where('siklus_id', $this->selectedSemester)->latest()->first();

        if ($session) {
            // Cek Batas Waktu
            if ($session->batas_waktu && now() < $session->batas_waktu) {
                $this->isLocked = true;
                $this->lockMessage = 'Hasil penilaian untuk periode ini belum dibuka. Harap tunggu hingga tanggal ' . \Carbon\Carbon::parse($session->batas_waktu)->translatedFormat('d F Y H:i');
                return; 
            }

            $service = new HitungSkorService();
            
            // [LOGIKA BARU] Loop semua jabatan, hitung rata-rata gabungan
            $totalNilaiSemuaJabatan = 0;
            $jumlahJabatanDinilai = 0;
            
            // Array sementara untuk menampung nilai per kompetensi dari semua jabatan
            // Format: ['Kehadiran' => [nilai1, nilai2, ...], 'Disiplin' => [...]]
            $tempKompetensiScores = []; 

            foreach ($this->listJabatanIds as $jabatanId) {
                // Hitung skor untuk jabatan ini
                $hasilHitung = $service->hitungNilaiAkhir(Auth::id(), $session->id, $jabatanId);
                
                // Ambil rekap kompetensi untuk jabatan ini
                $rekapKomp = $service->getRekapKompetensi(Auth::id(), $session->id, $jabatanId);

                // Jika jabatan ini ada nilainya (tidak 0), masukkan ke perhitungan
                // Note: Kita cek apakah user memang dinilai di jabatan ini
                if (!empty($rekapKomp)) {
                    $nilaiAkhirJabatan = floatval($hasilHitung['skor_akhir']); // Ambil skor akhir (skala 100)
                    $totalNilaiSemuaJabatan += $nilaiAkhirJabatan;
                    $jumlahJabatanDinilai++;

                    // Gabungkan nilai kompetensi
                    foreach ($rekapKomp as $namaKomp => $nilaiKomp) {
                        $tempKompetensiScores[$namaKomp][] = $nilaiKomp;
                    }
                }
            }

            // [HITUNG RATA-RATA FINAL]
            if ($jumlahJabatanDinilai > 0) {
                // 1. Final Score User (Rata-rata dari seluruh jabatan)
                $this->finalScore = round($totalNilaiSemuaJabatan / $jumlahJabatanDinilai, 2);
                
                // 2. Final Score Per Kompetensi (Rata-rata kompetensi antar jabatan)
                foreach ($tempKompetensiScores as $nama => $kumpulanNilai) {
                    $avgKomp = array_sum($kumpulanNilai) / count($kumpulanNilai);
                    $this->tableData[$nama] = round($avgKomp, 2);
                }

                // Siapkan Chart
                $this->chartData = [
                    'labels' => array_keys($this->tableData),
                    'scores' => array_values($this->tableData)
                ];

                $this->hitungRanking($session->id, $this->finalScore);
            }
            
        } else {
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
                fputcsv($file, ['RAPORT KINERJA 360 (GABUNGAN JABATAN)']);
                fputcsv($file, ['Siklus', $data['siklus']]);
                fputcsv($file, []); 
                fputcsv($file, ['Nama', $data['namaUser']]);
                fputcsv($file, ['NIP', $data['nipUser']]);
                fputcsv($file, ['Jabatan', $data['jabatanUser']]);
                fputcsv($file, []); 
                fputcsv($file, ['TOTAL SKOR AKHIR', $data['finalScore']]);
                fputcsv($file, ['PREDIKAT', $data['predikat']]);
                fputcsv($file, []); 
                fputcsv($file, ['Kompetensi', 'Rata-rata Nilai']);
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