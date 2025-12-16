<style>
    /* CSS Sederhana untuk Rotasi Panah & Indentasi */
    .sidebar-link[data-bs-toggle="collapse"] {
        position: relative;
    }
    .sidebar-link[data-bs-toggle="collapse"]::after {
        content: "\F282"; /* Icon Chevron Down Bootstrap */
        font-family: bootstrap-icons;
        position: absolute;
        right: 1rem;
        transition: transform 0.3s ease;
        font-size: 0.8rem;
    }
    /* Putar panah saat menu terbuka */
    .sidebar-link[data-bs-toggle="collapse"][aria-expanded="true"]::after {
        transform: rotate(-180deg);
    }
    /* Geser sub-menu sedikit ke kanan agar terlihat sebagai anak */
    .sidebar-dropdown .sidebar-item {
        padding-left: 1rem; 
        background: rgba(0, 0, 0, 0.03); /* Sedikit gelap untuk pembeda */
    }
</style>

<ul class="sidebar-nav" id="sidebarNavAccordion">
    
    {{-- 1. DASHBOARD --}}
    <li class="sidebar-item">
        <a class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"> 
            <i class="bi bi-grid-fill" style="width: 20px;"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
    </li>

    {{-- 2. GROUP MASTER DATA --}}
    <li class="sidebar-item">
        {{-- Logika agar panah tidak menutup jika anak menu aktif --}}
        <a class="sidebar-link {{ request()->routeIs('admin.jabatan', 'admin.data-pegawai') ? '' : 'collapsed' }}" 
           data-bs-target="#masterData" 
           data-bs-toggle="collapse" 
           href="#"
           aria-expanded="{{ request()->routeIs('admin.jabatan', 'admin.data-pegawai') ? 'true' : 'false' }}">
            <i class="bi bi-database-fill" style="width: 20px;"></i>
            <span class="sidebar-text">Master Data</span>
        </a>
        
        {{-- Logika agar dropdown tetap terbuka (show) jika anak menu aktif --}}
        <ul id="masterData" class="sidebar-dropdown list-unstyled collapse {{ request()->routeIs('admin.jabatan', 'admin.data-pegawai') ? 'show' : '' }}" data-bs-parent="#sidebarNavAccordion">
            
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('admin.jabatan') ? 'active' : '' }}" href="{{ route('admin.jabatan') }}">
                    <i class="bi bi-people-fill" style="width: 20px;"></i> 
                    <span class="sidebar-text">Manajemen Struktur</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('admin.data-pegawai') ? 'active' : '' }}" href="{{ route('admin.data-pegawai') }}">
                    <i class="bi bi-people-fill" style="width: 20px;"></i>
                    <span class="sidebar-text">Data Pegawai</span>
                </a>
            </li>

        </ul>
    </li>

    {{-- 3. GROUP KONFIGURASI PENILAIAN --}}
    <li class="sidebar-item">
        <a class="sidebar-link {{ request()->routeIs('admin.kompetensi', 'admin.siklus-semester', 'admin.skema-penilaian', 'admin.pertanyaan') ? '' : 'collapsed' }}" 
           data-bs-target="#configPenilaian" 
           data-bs-toggle="collapse" 
           href="#"
           aria-expanded="{{ request()->routeIs('admin.kompetensi', 'admin.siklus-semester', 'admin.skema-penilaian', 'admin.pertanyaan') ? 'true' : 'false' }}">
            <i class="bi bi-gear-wide-connected" style="width: 20px;"></i>
            <span class="sidebar-text">Konfigurasi Penilaian</span>
        </a>

        <ul id="configPenilaian" class="sidebar-dropdown list-unstyled collapse {{ request()->routeIs('admin.kompetensi', 'admin.siklus-semester', 'admin.skema-penilaian', 'admin.pertanyaan') ? 'show' : '' }}" data-bs-parent="#sidebarNavAccordion">
            
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('admin.kompetensi') ? 'active' : '' }}" href="{{ route('admin.kompetensi') }}"> 
                    <i class="bi bi-bar-chart-fill" style="width: 20px;"></i>
                    <span class="sidebar-text">Bobot kompetensi</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('admin.siklus-semester') ? 'active' : '' }}" href="{{ route('admin.siklus-semester') }}">
                    <i class="bi bi-arrow-repeat" style="width: 20px;"></i>
                    <span class="sidebar-text">Siklus Semester</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('admin.skema-penilaian') ? 'active' : '' }}" href="{{ route('admin.skema-penilaian') }}">
                    <i class="bi bi-diagram-3" style="width: 20px;"></i>
                    <span class="sidebar-text">Skema Penilaian</span>
                </a>
            </li>
            
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('admin.pertanyaan') ? 'active' : '' }}" href="{{ route('admin.pertanyaan') }}">
                    <i class="bi bi-question-circle-fill" style="width: 20px;"></i>
                    <span class="sidebar-text">Pertanyaan</span>
                </a>
            </li>

        </ul>
    </li>

<<<<<<< Updated upstream
    {{-- 4. GROUP PELAKSANAAN (Proses & Random) --}}
    {{-- Cek apakah salah satu menu anak sedang aktif --}}
    @php
        $isPelaksanaanActive = request()->routeIs('admin.progress-penilaian', 'admin.random-penilai');
    @endphp

    <li class="sidebar-item">
        <a class="sidebar-link {{ $isPelaksanaanActive ? '' : 'collapsed' }}" 
           data-bs-target="#pelaksanaan" 
           data-bs-toggle="collapse" 
           href="#"
           aria-expanded="{{ $isPelaksanaanActive ? 'true' : 'false' }}">
            <i class="bi bi-clipboard-check-fill" style="width: 20px;"></i>
            <span class="sidebar-text">Pelaksanaan</span>
        </a>
        
        {{-- Tambahkan class 'show' jika salah satu menu anak aktif --}}
        <ul id="pelaksanaan" 
            class="sidebar-dropdown list-unstyled collapse {{ $isPelaksanaanActive ? 'show' : '' }}" 
            data-bs-parent="#sidebarNavAccordion">
            
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('admin.progress-penilaian') ? 'active' : '' }}" 
                   href="{{ route('admin.progress-penilaian') }}">
=======
    {{-- 4. GROUP PELAKSANAAN (YANG DIPERBAIKI) --}}
    <li class="sidebar-item">
        {{-- Tambahkan logika active/collapsed di parent menu ini --}}
        <a class="sidebar-link {{ request()->routeIs('admin.progress-penilaian', 'admin.random-penilai') ? '' : 'collapsed' }}" 
           data-bs-target="#pelaksanaan" 
           data-bs-toggle="collapse" 
           href="#"
           aria-expanded="{{ request()->routeIs('admin.progress-penilaian', 'admin.random-penilai') ? 'true' : 'false' }}">
            <i class="bi bi-clipboard-check-fill" style="width: 20px;"></i>
            <span class="sidebar-text">Pelaksanaan</span>
        </a>

        {{-- Tambahkan class 'show' jika route sesuai --}}
        <ul id="pelaksanaan" class="sidebar-dropdown list-unstyled collapse {{ request()->routeIs('admin.progress-penilaian', 'admin.random-penilai') ? 'show' : '' }}" data-bs-parent="#sidebarNavAccordion">
            
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('admin.progress-penilaian') ? 'active' : '' }}" href="{{ route('admin.progress-penilaian') }}">
>>>>>>> Stashed changes
                    <i class="bi bi-person-check-fill" style="width: 20px;"></i>
                    <span class="sidebar-text">Proses Penilai</span>
                </a>
            </li>

            <li class="sidebar-item">
<<<<<<< Updated upstream
                <a class="sidebar-link {{ request()->routeIs('admin.random-penilai') ? 'active' : '' }}" 
                   href="{{ route('admin.random-penilai') }}">
                    <i class="bi bi-shuffle"></i>
=======
                <a class="sidebar-link {{ request()->routeIs('admin.random-penilai') ? 'active' : '' }}" href="{{ route('admin.random-penilai') }}">
                    <i class="bi bi-shuffle" style="width: 20px;"></i>
>>>>>>> Stashed changes
                    <span class="sidebar-text">Random Penilai</span>
                </a>
            </li>

        </ul>
    </li>

</ul>