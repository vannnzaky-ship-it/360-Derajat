<ul class="sidebar-nav">
    
    {{-- MENU 1: DASHBOARD (BARU) --}}
    <li class="sidebar-item">
        <a class="sidebar-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}" 
           href="{{ route('superadmin.dashboard') }}">
            {{-- Ikon Grid/Dashboard --}}
            <i class="bi bi-grid-1x2-fill"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
    </li>

    {{-- MENU 2: MANAJEMEN ADMIN --}}
    <li class="sidebar-item">
        <a class="sidebar-link {{ request()->routeIs('superadmin.manajemen-admin') ? 'active' : '' }}" 
           href="{{ route('superadmin.manajemen-admin') }}">
            {{-- Saya ubah ikonnya jadi Shield-Lock agar senada dengan halaman Manajemen Admin tadi --}}
            <i class="bi bi-shield-lock-fill"></i>
            <span class="sidebar-text">Manajemen Admin</span>
        </a>
    </li>

    {{-- MENU 3: DATA PEGAWAI --}}
    <li class="sidebar-item">
        <a class="sidebar-link {{ request()->routeIs('superadmin.data-pegawai') ? 'active' : '' }}" 
           href="{{ route('superadmin.data-pegawai') }}">
            <i class="bi bi-people-fill"></i>
            <span class="sidebar-text">Data Pegawai</span>
        </a>
    </li>

</ul>