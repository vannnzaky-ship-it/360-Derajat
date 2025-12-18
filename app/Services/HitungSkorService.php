<?php

namespace App\Services;

use App\Models\PenilaianAlokasi;
use App\Models\SkemaPenilaian;
use App\Models\Jabatan;
use App\Models\Kompetensi;

class HitungSkorService
{
    public function hitungNilaiAkhir($targetUserId, $sessionId, $jabatanId)
    {
        $jabatan = Jabatan::find($jabatanId);
        if (!$jabatan) return ['skor_akhir' => 0, 'mutu' => '-', 'jabatan' => '-', 'skema' => '-', 'detail' => [], 'bobot_used' => []];

        $siklusId = \App\Models\PenilaianSession::find($sessionId)->siklus_id ?? 0;
        
        $skema = SkemaPenilaian::where('siklus_id', $siklusId)
            ->get()
            ->filter(function ($s) use ($jabatan) {
                $levels = is_array($s->level_target) ? $s->level_target : json_decode($s->level_target, true);
                $levels = $levels ?? [];
                return in_array((string)$jabatan->level, $levels);
            })->first();

        if (!$skema) return ['skor_akhir' => 0, 'mutu' => 'No Schema', 'jabatan' => $jabatan->nama_jabatan, 'skema' => '-', 'detail' => [], 'bobot_used' => []];

        // 1. Hitung Nilai Rata-rata per Peran (SUDAH MENGGUNAKAN BOBOT KOMPETENSI)
        // Note: Saya sudah memperbaiki parameter 'sebagai' agar sesuai variabel
        $rataAtasan  = $this->getAverageByRole($targetUserId, $sessionId, $jabatanId, 'Atasan'); 
        $rataBawahan = $this->getAverageByRole($targetUserId, $sessionId, $jabatanId, 'Bawahan'); 
        $rataRekan   = $this->getAverageByRole($targetUserId, $sessionId, $jabatanId, 'Rekan');
        $rataDiri    = $this->getAverageByRole($targetUserId, $sessionId, $jabatanId, 'Diri Sendiri');

        // 2. Kalkulasi Bobot Peran (Skema Penilaian)
        // Contoh: (Nilai Diri * 25%) + (Nilai Atasan * 25%) ...
        $skorAkhir = 
            ($rataDiri    * ($skema->persen_diri / 100)) +
            ($rataAtasan  * ($skema->persen_atasan / 100)) +
            ($rataRekan   * ($skema->persen_rekan / 100)) +
            ($rataBawahan * ($skema->persen_bawahan / 100));

        // 3. Tentukan Mutu
        $mutu = $this->getMutu($skorAkhir);

        // 4. Konversi ke Skala 100
        $skorAkhir100 = $skorAkhir * 20;

        return [
            'jabatan' => $jabatan->nama_jabatan,
            'skema' => $skema->nama_skema,
            'detail' => [
                'diri' => number_format($rataDiri * 20, 2),
                'atasan' => number_format($rataAtasan * 20, 2),
                'rekan' => number_format($rataRekan * 20, 2),
                'bawahan' => number_format($rataBawahan * 20, 2),
            ],
            'bobot_used' => [
                'diri' => $skema->persen_diri . '%',
                'atasan' => $skema->persen_atasan . '%',
                'rekan' => $skema->persen_rekan . '%',
                'bawahan' => $skema->persen_bawahan . '%',
            ],
            'skor_akhir' => number_format($skorAkhir100, 2), 
            'mutu' => $mutu
        ];
    }

    public function getRekapKompetensi($targetUserId, $sessionId, $jabatanId)
    {
        $jabatan = Jabatan::find($jabatanId);
        if (!$jabatan) return [];

        $siklusId = \App\Models\PenilaianSession::find($sessionId)->siklus_id ?? 0;
        
        $skema = SkemaPenilaian::where('siklus_id', $siklusId)
            ->get()
            ->filter(function ($s) use ($jabatan) {
                $levels = is_array($s->level_target) ? $s->level_target : json_decode($s->level_target, true);
                $levels = $levels ?? [];
                return in_array((string)$jabatan->level, $levels);
            })->first();

        if (!$skema) return [];

        $kompetensis = Kompetensi::where('status', 'Aktif')->get();
        $hasil = [];

        foreach ($kompetensis as $kompetensi) {
            // Mengambil rata-rata murni per kompetensi per peran
            $rataDiri    = $this->getAveragePerKompetensi($targetUserId, $sessionId, $jabatanId, 'Diri Sendiri', $kompetensi->id);
            $rataAtasan  = $this->getAveragePerKompetensi($targetUserId, $sessionId, $jabatanId, 'Atasan', $kompetensi->id);
            $rataRekan   = $this->getAveragePerKompetensi($targetUserId, $sessionId, $jabatanId, 'Rekan', $kompetensi->id);
            $rataBawahan = $this->getAveragePerKompetensi($targetUserId, $sessionId, $jabatanId, 'Bawahan', $kompetensi->id);

            // Hitung skor akhir kompetensi berdasarkan bobot peran (bukan bobot kompetensi, karena ini detail per kompetensi)
            $skorAkhirKompetensi = 
                ($rataDiri    * ($skema->persen_diri / 100)) +
                ($rataAtasan  * ($skema->persen_atasan / 100)) +
                ($rataRekan   * ($skema->persen_rekan / 100)) +
                ($rataBawahan * ($skema->persen_bawahan / 100));
            
            $hasil[$kompetensi->nama_kompetensi] = round($skorAkhirKompetensi * 20, 0);
        }

        return $hasil;
    }

    /**
     * FUNGSI INI TELAH DIUBAH UNTUK MENGHITUNG BERDASARKAN BOBOT KOMPETENSI
     * Rumus: (Rata2_Komp_A * Bobot_A%) + (Rata2_Komp_B * Bobot_B%) + ...
     */
    private function getAverageByRole($targetUserId, $sessionId, $jabatanId, $sebagai)
    {
        // 1. Ambil semua kompetensi aktif
        $kompetensis = Kompetensi::where('status', 'Aktif')->get();

        if ($kompetensis->isEmpty()) return 0;

        $totalWeightedScore = 0;
        $totalWeightCheck = 0; // Hanya untuk pengecekan jika diperlukan

        foreach ($kompetensis as $kompetensi) {
            // 2. Hitung rata-rata murni untuk kompetensi ini (Scale 1-5)
            $avgKompetensi = $this->getAveragePerKompetensi($targetUserId, $sessionId, $jabatanId, $sebagai, $kompetensi->id);

            // 3. Ambil bobot dari database (misal: 80 atau 20)
            $bobotPersen = $kompetensi->bobot / 100; // 80 jadi 0.8

            // 4. Akumulasi Hasil
            $totalWeightedScore += ($avgKompetensi * $bobotPersen);
            
            $totalWeightCheck += $kompetensi->bobot;
        }

        // Opsional: Jika Anda ingin menormalisasi jika total bobot tidak 100%
        // Tapi jika asumsi input bobot di DB selalu total 100, langsung return saja.
        return $totalWeightedScore;
    }

    private function getAveragePerKompetensi($targetUserId, $sessionId, $jabatanId, $sebagai, $kompetensiId)
    {
        $alokasis = PenilaianAlokasi::where('penilaian_session_id', $sessionId)
            ->where('target_user_id', $targetUserId)
            ->where('jabatan_id', $jabatanId)
            ->where('sebagai', $sebagai)
            ->where('status_nilai', 'Sudah')
            ->with(['skors' => function($q) use ($kompetensiId) {
                $q->whereHas('pertanyaan', function($sq) use ($kompetensiId) {
                    $sq->where('kompetensi_id', $kompetensiId);
                });
            }])
            ->get();

        if ($alokasis->isEmpty()) return 0;

        $totalNilai = 0;
        $totalItem = 0;

        foreach ($alokasis as $alokasi) {
            $sum = $alokasi->skors->sum('nilai');
            $count = $alokasi->skors->count(); 

            if ($count > 0) {
                $totalNilai += $sum;
                $totalItem += $count;
            }
        }

        return $totalItem > 0 ? ($totalNilai / $totalItem) : 0;
    }

    private function getMutu($nilai)
    {
        if ($nilai >= 4.51) return 'Sangat Baik';
        if ($nilai >= 3.51) return 'Baik';
        if ($nilai >= 2.51) return 'Cukup';
        if ($nilai >= 1.51) return 'Kurang';
        return 'Sangat Kurang';
    }
}