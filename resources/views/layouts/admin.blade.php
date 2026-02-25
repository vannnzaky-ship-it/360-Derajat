<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} - Penilaian 360</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-polkam.png') }}">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @livewireStyles

    <!-- ==== STYLE KUSTOM ==== -->
    <style>
        /* Variabel Warna Tema */
        :root {
            --color-primary: #C38E44;
            --color-primary-dark: #a8793a;
            --color-primary-light: #d6a96f;
            --color-dark: #212D46;
            
            /* Variabel Warna Sidebar (Light Mode) */
            --sidebar-bg: #C38E44; 
            --sidebar-text-color: #f8f9fa;
            --sidebar-text-hover: #ffffff;
            --sidebar-active-bg: #a8793a;
            --sidebar-border: #d6a96f;

            --bs-body-bg: #f8f9fa;
            --bs-body-color: #212529;
            --bs-border-color: #dee2e6;
            --bs-emphasis-color: #000;
        }

        /* Tema Dark Mode */
        [data-bs-theme="dark"] {
            --sidebar-bg: #212D46;
            --sidebar-text-color: #adb5bd;
            --sidebar-text-hover: #ffffff;
            --sidebar-active-bg: #2a3a5a;
            --sidebar-border: #3a4b6b;

            --bs-body-bg: #1a1a1a;
            --bs-body-color: #dee2e6;
            --bs-border-color: #3a3a3a;
            --bs-emphasis-color: #fff;
            --bs-tertiary-bg: #2a2a2a;
            --bs-secondary-bg: #222;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bs-body-bg);
            color: var(--bs-body-color);
            transition: margin-left 0.3s ease;
        }

        /* --- Warna Kustom Bootstrap --- */
        .text-custom-brown { color: var(--color-primary) !important; }
        .bg-custom-brown { background-color: var(--color-primary) !important; }
        .btn-custom-brown {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
        }
        .btn-custom-brown:hover {
            background-color: var(--color-primary-dark);
            border-color: var(--color-primary-dark);
            color: white;
        }
        .progress-bar.bg-custom-brown {
            background-color: var(--color-primary) !important;
        }

        /* --- Struktur Layout Utama --- */
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0; width: 260px;
            background-color: var(--sidebar-bg); color: var(--sidebar-text-color);
            overflow-y: auto; transition: width 0.3s ease; z-index: 1050;
        }
        .sidebar-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.25rem; border-bottom: 1px solid var(--sidebar-border); min-height: 73px;
        }
        .sidebar-logo {
            display: flex; align-items: center; gap: 10px; color: var(--sidebar-text-hover);
            text-decoration: none; font-weight: 600; transition: opacity 0.3s ease; white-space: nowrap;
        }
        .sidebar-logo img { height: 40px; width: 40px; }
        #sidebar-toggle {
            background: none; border: none; color: var(--sidebar-text-hover);
            font-size: 1.25rem; cursor: pointer;
        }
        .sidebar-nav { padding: 1rem 0; }
        .sidebar-item { list-style: none; }
        .sidebar-link {
            display: flex; align-items: center; gap: 1rem; padding: 0.75rem 1.25rem;
            color: var(--sidebar-text-color); text-decoration: none;
            transition: all 0.2s ease; white-space: nowrap;
        }
        .sidebar-link i { font-size: 1.1rem; width: 20px; text-align: center; }
        .sidebar-link .sidebar-text { transition: opacity 0.3s ease; }
        .sidebar-link:hover, .sidebar-item.active .sidebar-link {
            color: var(--sidebar-text-hover); background-color: var(--sidebar-active-bg);
            border-left: 3px solid var(--color-primary); padding-left: calc(1.25rem - 3px);
        }
        .main-content {
            flex-grow: 1; margin-left: 260px; transition: margin-left 0.3s ease;
            display: flex; flex-direction: column; min-height: 100vh;
        }
        .top-navbar {
            position: sticky; top: 0; background-color: var(--bs-body-bg);
            border-bottom: 1px solid var(--bs-border-color); padding: 0 1.5rem;
            display: flex; justify-content: flex-end; align-items: center;
            z-index: 1040; box-shadow: 0 1px 3px rgba(0,0,0,0.05); min-height: 73px;
        }
        .navbar-right-menu { display: flex; align-items: center; gap: 1rem; }
        .theme-toggle {
            background: none; border: none; font-size: 1.2rem;
            color: var(--bs-emphasis-color); cursor: pointer;
        }
        .dropdown-menu {
            --bs-dropdown-bg: var(--bs-tertiary-bg, white);
            --bs-dropdown-border-color: var(--bs-border-color);
            --bs-dropdown-link-color: var(--bs-emphasis-color);
            --bs-dropdown-link-hover-bg: var(--color-primary-light);
            --bs-dropdown-link-hover-color: var(--color-dark);
        }
        .profile-img-xs { width: 32px; height: 32px; border-radius: 50%; }

        /* --- ISI HALAMAN & LOGO BACKGROUND --- */
        .page-content {
            flex-grow: 1;
            position: relative; 
            z-index: 1; 
        }
        .page-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('/images/logo-polkam.png');
            background-repeat: no-repeat;
            background-position: center;
            background-size: 55%; 
            opacity: 0.05; 
            z-index: -1; 
        }

        /* --- EFEK SIDEBAR MENGECIL --- */
        body.sidebar-mini .sidebar { width: 70px; }
        body.sidebar-mini .main-content { margin-left: 70px; }
        body.sidebar-mini .sidebar-logo span,
        body.sidebar-mini .sidebar-link .sidebar-text { opacity: 0; visibility: hidden; }
        body.sidebar-mini .sidebar-header { justify-content: center; }
        body.sidebar-mini .sidebar-logo { opacity: 0; visibility: hidden; width: 0; }
        body.sidebar-mini #sidebar-toggle { margin-left: -8px; }
        
        @media (max-width: 768px) {
            .sidebar { width: 70px; }
            .main-content { margin-left: 70px; }
            .sidebar-logo span,
            .sidebar-link .sidebar-text { opacity: 0; visibility: hidden; }
            .sidebar-header { justify-content: center; }
            .sidebar-logo { opacity: 0; visibility: hidden; width: 0; }
            #sidebar-toggle { margin-left: -8px; display: none; }
            body .sidebar-header { justify-content: center; }
            body .sidebar-logo { display: none; }
            .sidebar-logo-mobile { display: block !important; padding-top: 0; text-align: center; }
            .sidebar-logo-mobile img { height: 40px; width: 40px; }
        }

        .modal-backdrop { display: none !important; }

        /* Style Container Kapsul (Pill) */
        .nav-profile-pill {
            display: flex; align-items: center; gap: 10px;
            padding: 5px 12px 5px 5px; border-radius: 50px;
            transition: all 0.3s ease; text-decoration: none;
            color: #333; border: 1px solid transparent;
        }
        .nav-profile-pill:hover, .nav-profile-pill[aria-expanded="true"] {
            background-color: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-color: #e0e0e0;
        }
        
        .profile-img-nav {
            width: 40px; height: 40px; object-fit: cover; 
            border-radius: 50%; border: 2px solid #c38e44; padding: 2px; background: #fff;
        }

        .profile-info { display: flex; flex-direction: column; line-height: 1.2; }
        .profile-name { font-weight: 600; font-size: 0.9rem; color: #444; }
        .profile-role { font-size: 0.7rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }

        .profile-arrow { font-size: 0.8rem; color: #aaa; transition: transform 0.3s; }
        .nav-profile-pill[aria-expanded="true"] .profile-arrow { transform: rotate(180deg); color: #c38e44; }

        @media (max-width: 768px) {
            .profile-info, .profile-arrow { display: none; }
            .nav-profile-pill { padding: 0; border: none; }
            .nav-profile-pill:hover { background: none; box-shadow: none; }
        }

        [data-bs-theme="dark"] .card,
        [data-bs-theme="dark"] .list-group-item,
        [data-bs-theme="dark"] .modal-content {
            background-color: #212529 !important;
            color: #ffffff !important;
            border-color: #495057 !important;
        }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #212529 !important;
            color: #ffffff !important;
            border-color: #495057 !important;
        }

        [data-bs-theme="dark"] .form-control::placeholder {
            color: #adb5bd !important;
        }

        [data-bs-theme="dark"] .table {
            --bs-table-bg: #212529 !important;
            --bs-table-color: #ffffff !important;
            color: #ffffff !important;
        }

        /* Tambahan khusus untuk tombol menu dashboard (Gambar 4) */
        [data-bs-theme="dark"] .btn-light,
        [data-bs-theme="dark"] .bg-white {
            background-color: #212529 !important;
            color: #fff !important;
            border: 1px solid #495057 !important;
        }

        /* 1. Memaksa semua teks di dalam Card, List, dan Modal menjadi putih */
        [data-bs-theme="dark"] .card h1, 
        [data-bs-theme="dark"] .card h2, 
        [data-bs-theme="dark"] .card h3, 
        [data-bs-theme="dark"] .card h4, 
        [data-bs-theme="dark"] .card h5, 
        [data-bs-theme="dark"] .card h6, 
        [data-bs-theme="dark"] .card p, 
        [data-bs-theme="dark"] .card span, 
        [data-bs-theme="dark"] .card div,
        [data-bs-theme="dark"] .card small,
        [data-bs-theme="dark"] .card strong,
        [data-bs-theme="dark"] .card b {
            color: #ffffff !important;
        }

        /* 2. Khusus untuk Ikon (<i> atau <svg>) agar putih */
        [data-bs-theme="dark"] .card i,
        [data-bs-theme="dark"] .card svg {
            color: #ffffff !important;
            fill: #ffffff !important;
        }

        /* 3. Memperbaiki Judul Halaman (seperti "Dashboard", "Selamat Datang...") */
        [data-bs-theme="dark"] h1, 
        [data-bs-theme="dark"] h2, 
        [data-bs-theme="dark"] h3, 
        [data-bs-theme="dark"] h4, 
        [data-bs-theme="dark"] h5, 
        [data-bs-theme="dark"] h6 {
            color: #f8f9fa !important; /* Putih agak soft */
        }

        /* 4. Jika ada teks biasa di body yang masih gelap */
        [data-bs-theme="dark"] body {
            color: #e9ecef !important;
        }
        
        /* Ini kuncinya: Memaksa class 'text-dark' berubah jadi putih saat mode gelap */
        [data-bs-theme="dark"] .text-dark {
            color: #f8f9fa !important; /* Warna putih terang */
        }

        /* Efek Hover Matahari: Muter sedikit & warna jadi Emas */
        #theme-icon-sun:hover {
            transform: rotate(90deg);
            color: #C38E44; /* Warna Emas Tema Kamu */
            fill: rgba(195, 142, 68, 0.2);
        }
        
        /* Efek Hover Bulan: Goyang sedikit & warna jadi Biru/Abu */
        #theme-icon-moon:hover {
            transform: rotate(-15deg);
            color: #adb5bd;
            fill: rgba(255, 255, 255, 0.2);
        }

        /* --- STYLE PROFIL NAVBAR --- */

        /* 1. Container Utama (Bentuk Kapsul) */
        .nav-profile-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 12px 4px 4px; /* Padding kanan lebih lebar untuk panah */
            border-radius: 50px; /* Bentuk bulat kapsul */
            transition: all 0.3s ease;
            text-decoration: none;
            
            /* Warna Default (Light Mode) */
            background-color: #f8f9fa; /* Abu sangat terang */
            border: 1px solid #dee2e6; /* Border halus */
        }

        /* Hover Effect (Light Mode) */
        .nav-profile-pill:hover, 
        .nav-profile-pill[aria-expanded="true"] {
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border-color: #C38E44; /* Border jadi emas saat hover */
        }

        /* 2. Foto Profil */
        .profile-img-nav {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff; /* Ring putih di foto */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* 3. Teks Info */
        .profile-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            text-align: left;
        }

        .profile-name {
            font-weight: 700;
            font-size: 0.85rem;
            color: #343a40; /* Teks gelap */
        }

        .profile-role {
            font-size: 0.7rem;
            font-weight: 500;
            color: #C38E44; /* Warna Emas Tema Kamu */
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* 4. Ikon Panah */
        .profile-arrow {
            font-size: 0.8rem;
            color: #adb5bd;
            transition: transform 0.3s;
        }

        /* Putar panah saat dropdown aktif */
        .nav-profile-pill[aria-expanded="true"] .profile-arrow {
            transform: rotate(180deg);
            color: #C38E44;
        }

        /* --- KHUSUS DARK MODE (Automatic Switch) --- */
        [data-bs-theme="dark"] .nav-profile-pill {
            background-color: #2b3035; /* Background gelap */
            border-color: #495057;
        }

        [data-bs-theme="dark"] .nav-profile-pill:hover,
        [data-bs-theme="dark"] .nav-profile-pill[aria-expanded="true"] {
            background-color: #343a40; /* Lebih terang dikit saat hover */
            border-color: #C38E44;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        [data-bs-theme="dark"] .profile-img-nav {
            border-color: #495057; /* Ring foto jadi abu gelap */
        }

        [data-bs-theme="dark"] .profile-name {
            color: #e9ecef; /* Nama jadi putih */
        }
/* Role tetap emas (#C38E44) agar konsisten */
    </style>
</head>
<body>

    <div class="wrapper">
        <!-- ==== SIDEBAR NAVIGASI ==== -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="#" class="sidebar-logo">
                    <img src="/images/logo-polkam.png" alt="Logo Polkam">
                    <span>Penilaian 360</span>
                </a>
                <button id="sidebar-toggle" class="d-none d-md-block">
                    <i class="bi bi-list"></i>
                </button>
                <div class="sidebar-logo-mobile d-block d-md-none">
                     <img src="/images/logo-polkam.png" alt="Logo Polkam">
                </div>
            </div>
            
            <nav class="sidebar-nav">
                {{-- LOGIKA SIDEBAR OTOMATIS BERDASARKAN URL & SESSION --}}
                @php
                    $segment = request()->segment(1);
                    $sessionRole = session('selected_role');
                    
                    // Prioritas: URL Segment > Session > Auth Role Default
                    if (in_array($segment, ['superadmin', 'admin', 'peninjau', 'karyawan'])) {
                        $currentContext = $segment;
                    } else {
                        $currentContext = $sessionRole ?? auth()->user()->roles->first()->name;
                    }
                @endphp

                @if($currentContext == 'superadmin')
                    @include('layouts.partials.sidebar-superadmin')

                @elseif($currentContext == 'admin')
                    @include('layouts.partials.sidebar-admin')

                @elseif($currentContext == 'peninjau')
                    @include('layouts.partials.sidebar-peninjau')

                @elseif($currentContext == 'karyawan')
                    @include('layouts.partials.sidebar-karyawan')

                @else
                    {{-- Fallback jika tidak terdeteksi --}}
                    @if(auth()->user()->hasRole('superadmin'))
                        @include('layouts.partials.sidebar-superadmin')
                    @elseif(auth()->user()->hasRole('admin'))
                        @include('layouts.partials.sidebar-admin')
                    @elseif(auth()->user()->hasRole('karyawan'))
                        @include('layouts.partials.sidebar-karyawan')
                    @endif
                @endif
            </nav>
        </aside>

        <!-- ==== KONTEN UTAMA ==== -->
        <div class="main-content" id="main-content">
            <header class="top-navbar">
                <div class="navbar-right-menu">
                    <button class="theme-toggle" id="theme-toggle">
                        <svg id="theme-icon-moon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon" style="cursor: pointer; transition: transform 0.3s;">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>

                        <svg id="theme-icon-sun" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun" style="display: none; cursor: pointer; transition: transform 0.3s;">
                            <circle cx="12" cy="12" r="5"></circle>
                            <line x1="12" y1="1" x2="12" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="23"></line>
                            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                            <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                            <line x1="1" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="12" x2="23" y2="12"></line>
                            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                            <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                        </svg>
                    </button>
                    
                    <div class="dropdown">
                        <a href="#" class="nav-profile-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                            
                            {{-- Foto Profil --}}
                            @if(auth()->user()->profile_photo_path)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" 
                                    alt="Profil" class="profile-img-nav">
                            @else
                                <img src="/images/avatar.jpg" alt="Profil" class="profile-img-nav">
                            @endif

                            {{-- Info Teks --}}
                            <div class="profile-info d-none d-sm-flex"> {{-- d-none d-sm-flex: Sembunyikan teks di HP biar rapi --}}
                                <span class="profile-name">{{ Str::limit(auth()->user()->name, 15) }}</span>
                                
                                @php
                                    $urlSegment = request()->segment(1);
                                    if ($urlSegment == 'profil') {
                                        if (auth()->user()->hasRole('superadmin')) $displayRole = 'Super Admin';
                                        elseif (auth()->user()->hasRole('admin')) $displayRole = 'Administrator';
                                        elseif (auth()->user()->hasRole('peninjau')) $displayRole = 'Peninjau';
                                        else $displayRole = 'Karyawan';
                                    } else {
                                        $displayRole = match($urlSegment) {
                                            'superadmin' => 'Super Admin',
                                            'admin'      => 'Administrator',
                                            'peninjau'   => 'Peninjau',
                                            'karyawan'   => 'Karyawan',
                                            default      => auth()->user()->roles->first()->label ?? 'Pengguna'
                                        };
                                    }
                                @endphp

                                <span class="profile-role">{{ $displayRole }}</span>
                            </div>
                            
                            {{-- Ikon Panah (Ganti class bi profile-arrow jadi bi-chevron-down) --}}
                            <i class="bi profile-arrow ms-2"></i>
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2" style="min-width: 220px;">
                            <li>
                                <h6 class="dropdown-header fw-bold text-uppercase small text-muted">
                                    {{ auth()->user()->name ?? 'Pengguna' }}
                                </h6>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            
                            @if(!auth()->user()->hasRole('superadmin'))
                            <li>
                                {{-- LINK PROFIL DINAMIS --}}
                                @php
                                    $currentPrefix = request()->segment(1);
                                    if (!in_array($currentPrefix, ['admin', 'karyawan', 'peninjau', 'superadmin'])) {
                                        $currentPrefix = auth()->user()->roles->first()->name;
                                    }
                                @endphp

                                <a class="dropdown-item py-2" href="{{ route($currentPrefix . '.profil') }}">
                                    <i class="bi bi-person-gear me-2 text-warning"></i> Ganti Password & Info Akun
                                </a>
                            </li>
                            @if(auth()->user()->roles->count() > 1)
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('pilih-role') }}">
                                    <i class="bi bi-arrow-left-right me-2 text-info"></i> Ganti Hak Akses / Peran
                                </a>
                            </li>
                            @endif
                            {{-- AKHIR TOMBOL GANTI PERAN --}}
                            @endif

                            <li>
                                <a class="dropdown-item py-2 text-danger fw-bold" href="#" 
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            <main class="page-content">
                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Bootstrap & SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @livewireScripts
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebar-toggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.body.classList.toggle('sidebar-mini');
                });
            }

            const themeToggle = document.getElementById('theme-toggle');
            const moonIcon = document.getElementById('theme-icon-moon');
            const sunIcon = document.getElementById('theme-icon-sun');
            const htmlEl = document.documentElement;
            function setTheme(theme) {
                if (theme === 'dark') {
                    htmlEl.setAttribute('data-bs-theme', 'dark');
                    if(moonIcon) moonIcon.style.display = 'none';
                    if(sunIcon) sunIcon.style.display = 'inline';
                    localStorage.setItem('theme', 'dark');
                } else {
                    htmlEl.setAttribute('data-bs-theme', 'light');
                    if(moonIcon) moonIcon.style.display = 'inline';
                    if(sunIcon) sunIcon.style.display = 'none';
                    localStorage.setItem('theme', 'light');
                }
            }
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
            if(themeToggle) {
                themeToggle.addEventListener('click', function() {
                    const currentTheme = htmlEl.getAttribute('data-bs-theme');
                    setTheme(currentTheme === 'light' ? 'dark' : 'light');
                });
            }

            const currentURL = window.location.href;
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            let bestMatch = null;
            let longestMatch = 0;
            sidebarLinks.forEach(l => l.closest('.sidebar-item').classList.remove('active'));
            sidebarLinks.forEach(link => {
                if (link.href && currentURL.startsWith(link.href)) {
                    if (link.href.length > longestMatch) {
                        longestMatch = link.href.length;
                        bestMatch = link;
                    }
                }
            });
            if (bestMatch) {
                bestMatch.closest('.sidebar-item').classList.add('active');
            }
        });
    </script>
</body>
</html>