<ul class="sidebar-nav">
    {{-- MENU DASHBOARD --}}
    <li class="sidebar-item {{ Request::routeIs('karyawan.dashboard') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('karyawan.dashboard') }}">
            <i class="bi bi-grid-fill"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
    </li>

    {{-- MENU PENILAIAN (Pakai tanda * agar saat isi penilaian, menu tetap aktif) --}}
    <li class="sidebar-item {{ Request::routeIs('karyawan.penilaian*') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('karyawan.penilaian') }}">
            <i class="bi bi-ui-checks"></i>
            <span class="sidebar-text">Penilaian</span>
        </a>
    </li>

    {{-- MENU RAPORT --}}
    <li class="sidebar-item {{ Request::routeIs('karyawan.raport') ? 'active' : '' }}">
        <a class="sidebar-link" href="{{ route('karyawan.raport') }}">
            <i class="bi bi-clipboard-data-fill"></i>
            <span class="sidebar-text">Raport</span>
        </a>
    </li>
</ul>