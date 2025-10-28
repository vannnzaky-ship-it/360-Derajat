<ul class="sidebar-nav">
    
    <li class="sidebar-item">
        <a class="sidebar-link" href="{{ route('admin.dashboard') }}"> 
            <i class="bi bi-grid-fill" style="width: 20px;"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link" href="{{ route('admin.data-pegawai') }}">
            <i class="bi bi-people-fill" style="width: 20px;"></i>
            <span class="sidebar-text">Data Pegawai</span>
        </a>
    </li>
    
    <li class="sidebar-item">
        {{-- Ganti nama dan href --}}
        <a class="sidebar-link" href="{{ route('admin.kompetensi') }}"> 
            <i class="bi bi-bar-chart-fill" style="width: 20px;"></i>
            <span class="sidebar-text">Bobot</span> {{-- Ganti nama menu --}}
        </a>
    </li>

    <li class="sidebar-item">
        <a class="sidebar-link" href="{{ route('admin.siklus-semester') }}">
            <i class="bi bi-arrow-repeat" style="width: 20px;"></i>
            <span class="sidebar-text">Siklus Semester</span>
        </a>
    </li>
    
    <li class="sidebar-item">
        <a class="sidebar-link" href="{{ route('admin.pertanyaan') }}">
            <i class="bi bi-question-circle-fill" style="width: 20px;"></i>
            <span class="sidebar-text">Pertanyaan</span>
        </a>
    </li>

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