<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Siklus;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\PenilaianSession;
use App\Models\PenilaianAlokasi;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class RandomPenilai extends Component
{
    use WithPagination;

    public $siklus_id;
    
    // Form Inputs
    public $batas_waktu;
    public $limit_rekan = 5;
    public $pilihan_kategori = ['Atasan', 'Bawahan', 'Rekan', 'Diri Sendiri']; 

    // Status State
    public $isSessionExists = false; // Apakah sesi sudah dibuat?
    public $existingSession = null;  // Data sesi jika ada
    public $isExpired = false;       // Apakah sudah lewat waktu?
    public $isProcessing = false;

    protected $rules = [
        'siklus_id' => 'required|exists:siklus,id',
        'batas_waktu' => 'required|date|after:today',
        'limit_rekan' => 'required|integer|min:1|max:50',
        'pilihan_kategori' => 'required|array|min:1',
    ];

    public function mount()
    {
        // 1. Cari Siklus yang 'Aktif' tapi BELUM punya sesi (Prioritas untuk Generate Baru)
        $freshSiklus = Siklus::where('status', 'Aktif')
            ->whereDoesntHave('penilaianSession')
            ->first();

        if ($freshSiklus) {
            $this->siklus_id = $freshSiklus->id;
        } else {
            // Kalau semua sudah digenerate, ambil sembarang siklus aktif
            $anySiklus = Siklus::where('status', 'Aktif')->first();
            if($anySiklus) $this->siklus_id = $anySiklus->id;
        }

        $this->checkSessionStatus();
    }

    public function updatedSiklusId()
    {
        // Setiap kali dropdown berubah, cek statusnya
        $this->checkSessionStatus();
    }

    public function checkSessionStatus()
    {
        $this->existingSession = PenilaianSession::where('siklus_id', $this->siklus_id)->first();
        
        if ($this->existingSession) {
            // KONDISI: SUDAH PERNAH DIGENERATE (Data Ada)
            $this->isSessionExists = true;
            
            // Cek apakah expired
            $this->isExpired = now() > $this->existingSession->batas_waktu;
            
            // Isi variabel tampilan sekedar untuk info (Read Only)
            $this->batas_waktu = \Carbon\Carbon::parse($this->existingSession->batas_waktu)->format('Y-m-d\TH:i');
        } else {
            // KONDISI: BELUM PERNAH DIGENERATE (Siap Input Baru)
            $this->isSessionExists = false;
            $this->isExpired = false;
            
            // Reset form agar bersih untuk input baru
            $this->batas_waktu = null;
            $this->limit_rekan = 5;
            $this->pilihan_kategori = ['Atasan', 'Bawahan', 'Rekan', 'Diri Sendiri'];
        }
    }

    public function generate()
    {
        $this->validate();
        
        // Proteksi Ganda: Jangan generate kalau sesi sudah ada
        if ($this->isSessionExists) {
             session()->flash('error', 'Siklus ini sudah memiliki data penilaian. Silakan pilih siklus lain.'); 
             return;
        }

        $this->isProcessing = true;
        DB::beginTransaction();
        try {
            // 1. Buat Sesi Baru
            $session = PenilaianSession::create([
                'siklus_id' => $this->siklus_id,
                'tanggal_mulai' => now(),
                'batas_waktu' => $this->batas_waktu,
                'limit_rekan' => $this->limit_rekan,
                'status' => 'Open'
            ]);

            // 2. LOGIKA GENERATE
            $allTargets = Pegawai::with(['user', 'jabatans'])->get();
            $dataAlokasi = [];

            foreach ($allTargets as $target) {
                if (!$target->user) continue;

                foreach ($target->jabatans as $jabatanTarget) {
                    
                    // A. Diri Sendiri
                    if (in_array('Diri Sendiri', $this->pilihan_kategori)) {
                        $this->tambahAlokasi($dataAlokasi, $session->id, $target->user_id, $jabatanTarget->id, $target->user_id, $jabatanTarget->id, 'Diri Sendiri');
                    }

                    // B. ATASAN
                    // Logika: Cari Parent dari Jabatan Target. Orang yang memegang Parent Jabatan itu adalah ATASAN si Target.
                    if (in_array('Atasan', $this->pilihan_kategori) && $jabatanTarget->parent_id) {
                        $atasanList = Pegawai::whereHas('jabatans', function($q) use ($jabatanTarget) {
                            $q->where('jabatan.id', $jabatanTarget->parent_id);
                        })->with('user', 'jabatans')->get();

                        foreach ($atasanList as $atasan) {
                            if(!$atasan->user_id) continue;
                            foreach($atasan->jabatans as $jabatanAtasan) {
                                if($jabatanAtasan->id == $jabatanTarget->parent_id) {
                                    // [PERBAIKAN LOGIKA DISINI]
                                    // Si Penilai ($atasan) bertindak SEBAGAI ATASAN bagi si Target
                                    $this->tambahAlokasi($dataAlokasi, $session->id, $atasan->user_id, $jabatanAtasan->id, $target->user_id, $jabatanTarget->id, 'Atasan');
                                }
                            }
                        }
                    }

                    // C. BAWAHAN
                    // Logika: Cari Jabatan yang Parent-nya adalah Jabatan Target. Orang itu adalah BAWAHAN si Target.
                    if (in_array('Bawahan', $this->pilihan_kategori)) {
                        $bawahanList = Pegawai::whereHas('jabatans', function($q) use ($jabatanTarget) {
                            $q->where('parent_id', $jabatanTarget->id);
                        })->with('user', 'jabatans')->get();

                        foreach ($bawahanList as $bawahan) {
                            if(!$bawahan->user_id) continue;
                            foreach($bawahan->jabatans as $jabatanBawahan) {
                                if($jabatanBawahan->parent_id == $jabatanTarget->id) {
                                    // [PERBAIKAN LOGIKA DISINI]
                                    // Si Penilai ($bawahan) bertindak SEBAGAI BAWAHAN bagi si Target
                                    $this->tambahAlokasi($dataAlokasi, $session->id, $bawahan->user_id, $jabatanBawahan->id, $target->user_id, $jabatanTarget->id, 'Bawahan');
                                }
                            }
                        }
                    }

                    // D. REKAN SEJAWAT
                    if (in_array('Rekan', $this->pilihan_kategori) && $jabatanTarget->parent_id) {
                        
                        // Tambahkan where('level', ...) agar Ka Lab tidak menilai Dosen sebagai Rekan (Beda Level)
                        $rekanCandidates = Pegawai::whereHas('jabatans', function($q) use ($jabatanTarget) {
                            $q->where('parent_id', $jabatanTarget->parent_id)
                              ->where('level', $jabatanTarget->level); 
                        })->where('id', '!=', $target->id)->with('user', 'jabatans')->get();

                        $rekanList = $rekanCandidates->shuffle()->take($this->limit_rekan);

                        foreach ($rekanList as $rekan) {
                            if(!$rekan->user_id) continue;
                            
                            foreach($rekan->jabatans as $jabatanRekan) {
                                if(
                                    $jabatanRekan->parent_id == $jabatanTarget->parent_id && 
                                    $jabatanRekan->level == $jabatanTarget->level 
                                ) {
                                     $this->tambahAlokasi($dataAlokasi, $session->id, $rekan->user_id, $jabatanRekan->id, $target->user_id, $jabatanTarget->id, 'Rekan');
                                     break; 
                                }
                            }
                        }
                    }
                }
            }

            foreach (array_chunk($dataAlokasi, 100) as $chunk) {
                PenilaianAlokasi::insertOrIgnore($chunk);
            }

            DB::commit();
            $this->checkSessionStatus(); // Refresh status jadi 'Exists'
            session()->flash('message', "Berhasil! Penilaian untuk siklus ini telah digenerate.");

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
        $this->isProcessing = false;
    }

    private function tambahAlokasi(&$array, $sessionId, $penilaiId, $penilaiJabatanId, $targetId, $targetJabatanId, $sebagai)
    {
        if ($sebagai != 'Diri Sendiri' && $penilaiId == $targetId && $penilaiJabatanId == $targetJabatanId) return;

        $key = $sessionId . '-' . $penilaiId . '-' . $penilaiJabatanId . '-' . $targetId . '-' . $targetJabatanId;
        
        if (!isset($array[$key])) {
             $array[$key] = [
                'penilaian_session_id' => $sessionId,
                'user_id' => $penilaiId,
                'penilai_jabatan_id' => $penilaiJabatanId,
                'target_user_id' => $targetId,
                'jabatan_id' => $targetJabatanId, 
                'sebagai' => $sebagai,
                'status_nilai' => 'Belum',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    }

    public function render()
    {
        return view('livewire.admin.random-penilai', [
            'sikluses' => Siklus::where('status', 'Aktif')->with('penilaianSession')->get(),
            'histories' => PenilaianSession::with('siklus')->latest()->paginate(5)
        ])->layout('layouts.admin');
    }
}