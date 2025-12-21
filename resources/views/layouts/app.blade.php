<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>{{ $title ?? 'Login' }}</title>

    {{-- === FAVICON (LOGO DI TAB BROWSER) === --}}
    <link rel="icon" href="{{ asset('images/logo-polkam.png') }}" type="image/png">
    {{-- ===================================== --}}

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    @livewireStyles

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            /* Kita pindahkan background ke body, agar kolom kanan otomatis abu-abu */
            background-color: #f8f9fa; /* Warna default 'bg-light' Bootstrap */
        }

        .bg-custom-brown {
            background-color: #C38E44 !important;
        }
        
        /* WARNA BARU UNTUK KOTAK FORM */
        .bg-custom-form-bg {
            background-color: #EFEAEA !important;
        }

        .text-custom-dark {
            color: #212D46 !important;
        }

        .btn-custom-brown {
            background-color: #C38E44;
            border-color: #C38E44;
            color: white;
        }
        .btn-custom-brown:hover {
            background-color: #a8793a;
            border-color: #a8793a;
            color: white;
        }
        .form-control:focus {
            border-color: #C38E44;
            box-shadow: 0 0 0 0.25rem rgba(195, 142, 68, 0.25);
        }
    </style>
</head>
<body class="bg-light">

    {{ $slot }}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    @livewireScripts

    <script>
        // Pastikan script dijalankan setelah halaman dimuat
        document.addEventListener("DOMContentLoaded", function() {
            
            const togglePasswordBtn = document.getElementById('togglePasswordBtn');
            const passwordField = document.getElementById('password-field');
            const passwordIcon = document.getElementById('togglePasswordIcon');

            // Cek jika elemen-elemennya ada di halaman ini
            if (togglePasswordBtn && passwordField && passwordIcon) {
                
                togglePasswordBtn.addEventListener('click', function() {
                    // Cek tipe input saat ini
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);
                    
                    // Ganti ikon berdasarkan tipe
                    if (type === 'password') {
                        // Jika tipe adalah password (tersembunyi)
                        passwordIcon.classList.remove('bi-eye-slash');
                        passwordIcon.classList.add('bi-eye');
                    } else {
                        // Jika tipe adalah text (terlihat)
                        passwordIcon.classList.remove('bi-eye');
                        passwordIcon.classList.add('bi-eye-slash');
                    }
                });
            }
        });
    </script>
</body>
</html>