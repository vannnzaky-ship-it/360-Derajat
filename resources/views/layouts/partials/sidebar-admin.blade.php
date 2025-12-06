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
    
    {{-- 1. DASHBOARD (Tetap Sendiri) --}}
    <li class="sidebar-item">
        <a class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"> 
            <i class="bi bi-grid-fill" style="width: 20px;"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
    </li>

    {{-- 2. GROUP MASTER DATA (Struktur & Pegawai) --}}
    <li class="sidebar-item">
        <a class="sidebar-link collapsed" data-bs-target="#masterData" data-bs-toggle="collapse" href="#">
            <i class="bi bi-database-fill" style="width: 20px;"></i>
            <span class="sidebar-text">Master Data</span>
        </a>
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

    {{-- 3. GROUP KONFIGURASI PENILAIAN (Bobot, Siklus, Skema, Pertanyaan) --}}
    <li class="sidebar-item">
        <a class="sidebar-link collapsed" data-bs-target="#configPenilaian" data-bs-toggle="collapse" href="#">
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

    {{-- 4. GROUP PELAKSANAAN (Proses & Random) --}}
    <li class="sidebar-item">
        <a class="sidebar-link collapsed" data-bs-target="#pelaksanaan" data-bs-toggle="collapse" href="#">
            <i class="bi bi-clipboard-check-fill" style="width: 20px;"></i>
            <span class="sidebar-text">Pelaksanaan</span>
        </a>
        <ul id="pelaksanaan" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebarNavAccordion">
            
            <li class="sidebar-item">
                <a class="sidebar-link" href="#">
                    <i class="bi bi-person-check-fill" style="width: 20px;"></i>
                    <span class="sidebar-text">Proses Penilai</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="#">
                    <i class="bi bi-shuffle" style="width: 20px;"></i>
                    <span class="sidebar-text">Random Penilai</span>
                </a>
            </li>

        </ul>
    </li>

</ul>