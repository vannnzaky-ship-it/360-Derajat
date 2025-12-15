<?php

namespace App\Livewire\Karyawan;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Siklus;
use App\Models\PenilaianSession;
use App\Models\Pegawai;
use App\Services\HitungSkorService;
use Barryvdh\DomPDF\Facade\Pdf; // <--- PENTING: Import Library PDF

#[Layout('layouts.admin')] 
class Raport extends Component
{
    // Data Pengguna
    public $namaUser, $nipUser, $jabatanUser;
    public $pegawaiId, $jabatanId; // Simpan ID untuk query

    // Pilihan Semester
    public $listSemester = [];
    public $selectedSemester; // Ini akan menyimpan ID Siklus

    // Data Raport
    public $chartData = [];
    public $tableData = [];
    public $ranking = '-';
    public $finalScore = 0; // Tambahan untuk nilai akhir total

    public function mount()
    {
        $user = Auth::user();

        if ($user) {
            // Load relasi pegawai dan jabatan
            $user->load('pegawai.jabatans'); 
            
            $this->namaUser = $user->name;
            $this->nipUser = $user->pegawai?->nip ?? 'N/A';
            $this->pegawaiId = $user->pegawai?->id;

            // Ambil jabatan pertama (Primary) untuk penilaian
            // Jika user punya banyak jabatan, idealnya ada dropdown pilih jabatan
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

        // 1. Ambil List Semester (Siklus) dari Database
        $sikluses = Siklus::orderBy('tahun_ajaran', 'desc')->orderBy('semester', 'desc')->get();
        
        foreach($sikluses as $siklus) {
            // Format: "2024/2025 Ganjil" -> Key-nya pakai ID Siklus biar mudah
            $this->listSemester[$siklus->id] = $siklus->tahun_ajaran . ' ' . $siklus->semester;
        }

        // Set default ke semester pertama (paling baru)
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
        // Reset dulu
        $this->chartData = ['labels' => [], 'scores' => []];
        $this->tableData = [];
        $this->ranking = '-';
        $this->finalScore = 0;

        if (!$this->selectedSemester || !$this->jabatanId) return;

        // 1. Cari Sesi Penilaian berdasarkan Siklus yang dipilih
        $session = PenilaianSession::where('siklus_id', $this->selectedSemester)->latest()->first();

        if ($session) {
            $service = new HitungSkorService();

            // 2. Hitung Nilai Per Kompetensi (Untuk Chart & Tabel)
            // Hasilnya array ['Kedisiplinan' => 90, 'Etika' => 85, ...]
            $rekapKompetensi = $service->getRekapKompetensi(Auth::id(), $session->id, $this->jabatanId);

            if (!empty($rekapKompetensi)) {
                $this->tableData = $rekapKompetensi;
                
                // Siapkan data Chart
                $this->chartData = [
                    'labels' => array_keys($rekapKompetensi),
                    'scores' => array_values($rekapKompetensi)
                ];

                // 3. Hitung Nilai Akhir Total (Skala 100)
                $totalSkor = array_sum($rekapKompetensi);
                $jumlahItem = count($rekapKompetensi);
                $this->finalScore = $jumlahItem > 0 ? round($totalSkor / $jumlahItem) : 0;

                // 4. Hitung Ranking (Opsional - Sederhana)
                $this->hitungRanking($session->id, $this->finalScore);
            }
        }
    }

    private function hitungRanking($sessionId, $myScore)
    {
        // Placeholder Ranking
        // Sementara kita gunakan Predikat sebagai status ranking agar tidak berat query-nya
        $this->ranking = $this->getPredikat($myScore);
    }

    // --- FUNGSI EXPORT YANG SUDAH JADI ---
    public function export($type)
    {
        // Siapkan Data untuk dikirim ke PDF/Excel
        $data = [
            'namaUser' => $this->namaUser,
            'nipUser' => $this->nipUser,
            'jabatanUser' => $this->jabatanUser,
            'tableData' => $this->tableData,
            'finalScore' => $this->finalScore,
            'predikat' => $this->getPredikat($this->finalScore),
            'siklus' => $this->listSemester[$this->selectedSemester] ?? '-'
        ];

        // 1. Export PDF
        if ($type == 'pdf') {
            // Pastikan file view 'livewire.karyawan.cetak-raport-pdf' SUDAH DIBUAT
            $pdf = Pdf::loadView('livewire.karyawan.cetak-raport-pdf', $data);
            $filename = 'Raport-' . str_replace(' ', '-', $this->namaUser) . '.pdf';
            
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename);
        }

        // 2. Export Excel (CSV Native)
        if ($type == 'excel') {
            $filename = 'Raport-' . str_replace(' ', '-', $this->namaUser) . '.csv';
            
            return response()->streamDownload(function () use ($data) {
                $file = fopen('php://output', 'w');
                
                // Header CSV
                fputcsv($file, ['RAPORT KINERJA 360']);
                fputcsv($file, ['Siklus', $data['siklus']]);
                fputcsv($file, []); // Baris kosong
                fputcsv($file, ['Nama', $data['namaUser']]);
                fputcsv($file, ['NIP', $data['nipUser']]);
                fputcsv($file, ['Jabatan', $data['jabatanUser']]);
                fputcsv($file, []); 
                fputcsv($file, ['SKOR AKHIR', $data['finalScore']]);
                fputcsv($file, ['PREDIKAT', $data['predikat']]);
                fputcsv($file, []); 

                // Tabel Data
                fputcsv($file, ['Kompetensi', 'Nilai (0-100)']);
                foreach ($data['tableData'] as $kategori => $nilai) {
                    fputcsv($file, [$kategori, $nilai]);
                }

                fclose($file);
            }, $filename);
        }
    }

    // Helper Predikat (Digunakan untuk menentukan Baik/Buruk)
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