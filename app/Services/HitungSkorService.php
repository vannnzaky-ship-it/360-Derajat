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
                // Pastikan tipe data array agar tidak error
                $levels = is_array($s->level_target) ? $s->level_target : json_decode($s->level_target, true);
                $levels = $levels ?? [];
                return in_array((string)$jabatan->level, $levels);
            })->first();

        if (!$skema) return ['skor_akhir' => 0, 'mutu' => 'No Schema', 'jabatan' => $jabatan->nama_jabatan, 'skema' => '-', 'detail' => [], 'bobot_used' => []];

        // 1. Hitung Rata-rata per Peran (Hasilnya masih Skala 1-5)
        $rataAtasan  = $this->getAverageByRole($targetUserId, $sessionId, $jabatanId, 'Bawahan'); 
        $rataBawahan = $this->getAverageByRole($targetUserId, $sessionId, $jabatanId, 'Atasan'); 
        $rataRekan   = $this->getAverageByRole($targetUserId, $sessionId, $jabatanId, 'Rekan');
        $rataDiri    = $this->getAverageByRole($targetUserId, $sessionId, $jabatanId, 'Diri Sendiri');

        // 2. Kalkulasi Bobot (Hasilnya masih Skala 1-5, misal: 1.25)
        $skorAkhir = 
            ($rataDiri    * ($skema->persen_diri / 100)) +
            ($rataAtasan  * ($skema->persen_atasan / 100)) +
            ($rataRekan   * ($skema->persen_rekan / 100)) +
            ($rataBawahan * ($skema->persen_bawahan / 100));

        // 3. Tentukan Mutu (Logic getMutu menggunakan skala 1-5)
        $mutu = $this->getMutu($skorAkhir);

        // 4. [PERBAIKAN UTAMA] KONVERSI KE SKALA 100 (Dikali 20)
        // Contoh: 1.25 * 20 = 25
        $skorAkhir100 = $skorAkhir * 20;

        return [
            'jabatan' => $jabatan->nama_jabatan,
            'skema' => $skema->nama_skema,
            'detail' => [
                // Detail juga dikonversi ke skala 100 agar konsisten di tampilan
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
            // Mengembalikan nilai yang SUDAH DIKALI 20 (Skala 100)
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
            $rataDiri    = $this->getAveragePerKompetensi($targetUserId, $sessionId, $jabatanId, 'Diri Sendiri', $kompetensi->id);
            $rataAtasan  = $this->getAveragePerKompetensi($targetUserId, $sessionId, $jabatanId, 'Bawahan', $kompetensi->id);
            $rataRekan   = $this->getAveragePerKompetensi($targetUserId, $sessionId, $jabatanId, 'Rekan', $kompetensi->id);
            $rataBawahan = $this->getAveragePerKompetensi($targetUserId, $sessionId, $jabatanId, 'Atasan', $kompetensi->id);

            $skorAkhirKompetensi = 
                ($rataDiri    * ($skema->persen_diri / 100)) +
                ($rataAtasan  * ($skema->persen_atasan / 100)) +
                ($rataRekan   * ($skema->persen_rekan / 100)) +
                ($rataBawahan * ($skema->persen_bawahan / 100));
            
            // Bagian ini sudah benar (Scale 100)
            $hasil[$kompetensi->nama_kompetensi] = round($skorAkhirKompetensi * 20, 0);
        }

        return $hasil;
    }

    private function getAverageByRole($targetUserId, $sessionId, $jabatanId, $sebagai)
    {
        $alokasis = PenilaianAlokasi::where('penilaian_session_id', $sessionId)
            ->where('target_user_id', $targetUserId)
            ->where('jabatan_id', $jabatanId)
            ->where('sebagai', $sebagai)
            ->where('status_nilai', 'Sudah')
            ->with('skors')
            ->get();

        if ($alokasis->isEmpty()) return 0;

        $grandTotalNilai = 0;
        $grandTotalItem = 0;

        foreach ($alokasis as $alokasi) {
            $sum = $alokasi->skors->sum('nilai');
            $count = $alokasi->skors->count();

            if ($count > 0) {
                $grandTotalNilai += $sum;
                $grandTotalItem += $count;
            }
        }

        return $grandTotalItem > 0 ? ($grandTotalNilai / $grandTotalItem) : 0;
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
        // Mutu dihitung berdasarkan skala 1-5
        if ($nilai >= 4.51) return 'Sangat Baik';
        if ($nilai >= 3.51) return 'Baik';
        if ($nilai >= 2.51) return 'Cukup';
        if ($nilai >= 1.51) return 'Kurang';
        return 'Sangat Kurang';
    }
}