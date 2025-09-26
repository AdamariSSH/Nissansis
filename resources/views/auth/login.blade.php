{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}


@extends('layouts.app')

@section('content')
<div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100 h-100">
        <!-- Sección del Logo -->
        <div class="col-md-6 d-flex align-items-center justify-content-center bg-white">
            <img src="{{ asset('images/Nissan_2020_logo.svg') }}" alt="Nissan Logo" class="logo-nissan">
        </div>

        <!-- Sección del Formulario -->
        <div class="col-md-6 d-flex align-items-center justify-content-center bg-white">
            <div class="w-75">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required autofocus placeholder="Escriba su email">
                        @error('email')
                            <span class="text-danger"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">Contraseña</label>
                        <div class="input-group">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" required placeholder="Escriba su contraseña">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="text-danger"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="mb-3 text-end">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-decoration-none text-muted">
                                ¿Has olvidado tu contraseña?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-danger w-100 fw-bold">Login</button>
                    <div class="mt-3 text-center">
                        <a href="{{ route('login.google') }}" class="btn btn-outline-danger w-100 fw-bold">
                            <i class="fab fa-google me-2"></i> Iniciar sesión con Google
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Footer Rojo -->
<div class="bg-danger text-white text-center py-2">
    <small>Control de Almacén Grupo GranAuto.</small>
</div>

<style>
/* Estilos para el logo */
.logo-nissan {
    max-width: 80%;  /* Para que el logo ocupe el 80% del contenedor */
    height: auto;    /* Mantiene la proporción */
}

/* Hace que la sección del logo tenga la misma altura que la pantalla */
.col-md-6 {
    height: 100vh;
}
</style>

<script>
function togglePassword() {
    var passwordField = document.getElementById("password");
    passwordField.type = (passwordField.type === "password") ? "text" : "password";
}
</script>
@endsection

