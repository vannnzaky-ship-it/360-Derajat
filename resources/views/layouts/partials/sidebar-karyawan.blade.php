<ul class="sidebar-nav">
    <li class="sidebar-item">
        <!-- Link ini sudah benar menunjuk ke rute dashboard -->
        <a class="sidebar-link" href="{{ route('karyawan.dashboard') }}">
            <i class="bi bi-grid-fill"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
    </li>
    <li class="sidebar-item">
        <!-- Link yang belum jadi, kita beri javascript:void(0) agar tidak bisa diklik -->
        <a class="sidebar-link" href="{{ route('karyawan.penilaian') }}">
            <i class="bi bi-ui-checks"></i>
            <span class="sidebar-text">Penilaian</span>
        </a>
    </li>
    <li class="sidebar-item">
    <a class="sidebar-link" href="{{ route('karyawan.raport') }}">
        <i class="bi bi-clipboard-data-fill"></i>
        <span class="sidebar-text">Raport</span>
    </a>
</li>
    <li class="sidebar-item">
        <a class="sidebar-link" href="javascript:void(0);">
            <i class="bi bi-trophy-fill"></i>
            <span class="sidebar-text">Ranking</span>
        </a>
    </li>
</ul>
