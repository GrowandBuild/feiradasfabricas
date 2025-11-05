@extends('auth.layout')

@section('title', 'Login - Feira das Fábricas')

@section('header')
<h1>Entrar na sua conta</h1>
<p>Acesse sua conta para continuar comprando</p>
@endsection

@section('content')
<form method="POST" action="{{ route('customer.login') }}">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label">
            E-mail <span class="required">*</span>
        </label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">
            Senha <span class="required">*</span>
        </label>
        <div class="position-relative">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="current-password">
            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3" 
                    onclick="togglePassword()" style="border: none; background: none;">
                <i id="password-toggle" class="fas fa-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="mb-3 form-check">
        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="remember">
            Lembrar de mim
        </label>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-sign-in-alt me-2"></i>
            Entrar
        </button>
    </div>
</form>

<div class="auth-links">
    <p class="mb-2">
        <a href="{{ route('customer.register') }}">Não tem uma conta? Criar conta</a>
    </p>
    <p class="mb-2">
        <a href="{{ route('customer.register.b2b') }}">Cadastrar empresa (B2B)</a>
    </p>
    <p class="mb-0">
        <a href="{{ route('home') }}">Voltar ao início</a>
    </p>
</div>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('password-toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
@endsection
