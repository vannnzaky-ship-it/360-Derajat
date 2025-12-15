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
                // PERBAIKAN: Cek apakah sudah array atau masih string
                $levels = is_array($s->level_target) ? $s->level_target : json_decode($s->level_target, true);
                $levels = $levels ?? [];
                return in_array((string)$jabatan->level, $levels);
            })->first();

        if (!$skema) return ['skor_akhir' => 0, 'mutu' => 'No Schema', 'jabatan' => $jabatan->nama_jabatan, 'skema' => '-', 'detail' => [], 'bobot_used' => []];

        // ... (sisanya sama, tidak perlu diubah) ...
        
        $rataAtasan  = $this->getAverageByRole($targetUserId, $sessionId, $jabatanId, 'Bawahan'); 
        $rataBawahan = $this->getAverageByRole($targetUserId, $sessionId, $jabatanId, 'Atasan'); 
        $rataRekan   = $this->getAverageByRole($targetUserId, $sessionId, $jabatanId, 'Rekan');
        $rataDiri    = $this->getAverageByRole($targetUserId, $sessionId, $jabatanId, 'Diri Sendiri');

        $skorAkhir = 
            ($rataDiri    * ($skema->persen_diri / 100)) +
            ($rataAtasan  * ($skema->persen_atasan / 100)) +
            ($rataRekan   * ($skema->persen_rekan / 100)) +
            ($rataBawahan * ($skema->persen_bawahan / 100));

        $mutu = $this->getMutu($skorAkhir);

        return [
            'jabatan' => $jabatan->nama_jabatan,
            'skema' => $skema->nama_skema,
            'detail' => [
                'diri' => number_format($rataDiri, 2),
                'atasan' => number_format($rataAtasan, 2),
                'rekan' => number_format($rataRekan, 2),
                'bawahan' => number_format($rataBawahan, 2),
            ],
            'bobot_used' => [
                'diri' => $skema->persen_diri . '%',
                'atasan' => $skema->persen_atasan . '%',
                'rekan' => $skema->persen_rekan . '%',
                'bawahan' => $skema->persen_bawahan . '%',
            ],
            'skor_akhir' => number_format($skorAkhir, 2),
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
                // PERBAIKAN DI SINI JUGA (PENTING!)
                // Penyebab Error: Argument #1 ($json) must be of type string, array given
                // Solusi: Cek tipe data dulu
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
            
            $hasil[$kompetensi->nama_kompetensi] = round($skorAkhirKompetensi * 20, 0);
        }

        return $hasil;
    }

    // ... (Method private di bawahnya biarkan saja, sudah aman) ...
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
        if ($nilai >= 4.51) return 'Sangat Baik';
        if ($nilai >= 3.51) return 'Baik';
        if ($nilai >= 2.51) return 'Cukup';
        if ($nilai >= 1.51) return 'Kurang';
        return 'Sangat Kurang';
    }
}