<ul class="sidebar-nav">

    <li class="sidebar-item">
        <a class="sidebar-link {{ request()->routeIs('peninjau.laporan*') ? 'active' : '' }}" href="{{ route('peninjau.laporan') }}">
            <i class="bi bi-file-earmark-text-fill"></i>
            <span class="sidebar-text">Laporan Hasil</span>
        </a>
    </li>
</ul>