<div class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
    <div class="card shadow-lg border-0 rounded-4" style="width: 25rem;">
        <div class="card-body p-5">
            <h1 class="h5 text-center fw-bold text-custom-dark mb-4">
                Pilih Peran
            </h1>
            <p class="text-center text-muted mb-4">
                Anda memiliki lebih dari satu peran. Silakan pilih peran untuk melanjutkan:
            </p>

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="d-grid gap-3">
                @foreach ($roles as $role)
                    <button 
                        wire:click="selectRole('{{ $role->name }}')" 
                        class="btn btn-custom-brown btn-lg fw-bold">
                        Masuk sebagai {{ $role->label }}
                    </button>
                @endforeach
            </div>
            
            <div class="text-center mt-4">
                <a href="/logout" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="text-muted text-decoration-none small">
                   Bukan Anda? Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>