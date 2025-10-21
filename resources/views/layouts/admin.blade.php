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
            /* Variabel Warna Sidebar (Dark Mode) */
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

        /* --- (Sisa CSS Sidebar & Navbar biarkan seperti sebelumnya) --- */
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

        /* --- ISI HALAMAN & LOGO BACKGROUND (PERBAIKAN DI SINI) --- */
        .page-content {
            flex-grow: 1;
            position: relative; /* Membuatnya menjadi 'parent' untuk posisi logo */
            z-index: 1; /* Memastikan konten tetap di atas logo */
        }
        /* Pseudo-element untuk logo background */
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
            background-size: 55%; /* Atur ukuran logo, misal 50% dari area konten */
            opacity: 0.05; /* Opacity 10% */
            z-index: -1; /* Meletakkan logo di belakang konten */
        }


        /* --- EFEK SIDEBAR MENGECIL --- */
        body.sidebar-mini .sidebar { width: 70px; }
        body.sidebar-mini .main-content { margin-left: 70px; }
        body.sidebar-mini .sidebar-logo span,
        body.sidebar-mini .sidebar-link .sidebar-text { opacity: 0; visibility: hidden; }
        body.sidebar-mini .sidebar-header { justify-content: center; }
        body.sidebar-mini .sidebar-logo { opacity: 0; visibility: hidden; width: 0; }
        body.sidebar-mini #sidebar-toggle { margin-left: -8px; }
        
        /* --- (Sisa CSS Media Query biarkan seperti sebelumnya) --- */
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
            @if(session('selected_role') == 'karyawan')
                @include('layouts.partials.sidebar-karyawan')

            @elseif(session('selected_role') == 'superadmin')
                @include('layouts.partials.sidebar-superadmin')

            @elseif(session('selected_role') == 'admin')
                @include('layouts.partials.sidebar-admin')

            @elseif(session('selected_role') == 'peninjau')
                @include('layouts.partials.sidebar-peninjau')

            @else
                <p class="text-center text-muted p-3">Tidak ada menu</p>
            @endif
        </nav>
        </aside>

        <!-- ==== KONTEN UTAMA (HEADER + HALAMAN) ==== -->
        <div class="main-content" id="main-content">
            <header class="top-navbar">
                <div class="navbar-right-menu">
                    <button class="theme-toggle" id="theme-toggle">
                        <i class="bi bi-moon-stars-fill" id="theme-icon-moon"></i>
                        <i class="bi bi-sun-fill" id="theme-icon-sun" style="display: none;"></i>
                    </button>
                    <div class="dropdown">
                        <button class="btn border-0 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear-fill fs-5 text-secondary"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Ganti Password</a></li>
                            <li><a class="dropdown-item" href="#">Informasi Akun</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                         <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="/avatar.png" alt="Profil" class="profile-img-xs">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">{{ auth()->user()->name ?? 'Pengguna' }}</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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

    <!-- ==== JAVASCRIPT KUSTOM ==== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script Sidebar Toggle
            const sidebarToggle = document.getElementById('sidebar-toggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.body.classList.toggle('sidebar-mini');
                });
            }

            // Script Dark Mode
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

            // Script Sidebar Active Link
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