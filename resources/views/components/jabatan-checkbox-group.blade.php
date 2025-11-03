@props([
    'groupedJabatans',
    'parent_id' => null,
    'level' => 0,
    'takenSingletons' => [],
    'selectedJabatans' => []
])

{{-- Hitung padding berdasarkan level --}}
@php $padding = $level * 20; @endphp

{{-- Loop hanya untuk jabatan yang parent_id-nya sesuai --}}
@if(isset($groupedJabatans[$parent_id]))
    @foreach ($groupedJabatans[$parent_id] as $jabatan)
        @php
            $isSingleton = $jabatan->is_singleton;
            // Cek apakah jabatan ini sudah diambil DAN BUKAN oleh user yang sedang diedit
            // (Kita asumsikan $selectedJabatans sudah benar dari komponen Livewire)
            $isTaken = $isSingleton && in_array($jabatan->id, $takenSingletons);
            $isChecked = in_array($jabatan->id, $selectedJabatans);
        @endphp

        <div class="form-check" style="padding-left: {{ $padding + 24 }}px;">
            <input 
                class="form-check-input" 
                type="checkbox" 
                wire:model.live="selectedJabatans" {{-- Gunakan .live agar update --}}
                value="{{ $jabatan->id }}" 
                id="jabatan-{{ $jabatan->id }}"
                
                {{-- Nonaktifkan jika singleton dan sudah diambil --}}
                @if($isTaken && !$isChecked) disabled @endif
            >
            
            <label class="form-check-label" for="jabatan-{{ $jabatan->id }}">
                {{ $jabatan->nama_jabatan }}
                
                {{-- Beri tanda jika singleton --}}
                @if($isSingleton)
                    <i class="bi bi-person-fill" title="Jabatan Tunggal (Singleton)"></i>
                @endif
                
                {{-- Beri tanda jika terisi --}}
                @if($isTaken && !$isChecked)
                    <span class="text-muted small">(Sudah terisi)</span>
                @endif
            </label>
        </div>

        {{-- Panggil komponen ini lagi (rekursif) untuk anak-anaknya --}}
        <x-jabatan-checkbox-group 
            :groupedJabatans="$groupedJabatans" 
            :parent_id="$jabatan->id" 
            :level="$level + 1"
            :takenSingletons="$takenSingletons"
            :selectedJabatans="$selectedJabatans"
        />
    @endforeach
@endif