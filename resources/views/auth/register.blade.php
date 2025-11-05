@extends('auth.layout')

@section('title', 'Criar Conta - Feira das Fábricas')

@section('header')
<h1>Criar conta</h1>
<p>Cadastre-se para começar a comprar</p>
@endsection

@section('content')
<form method="POST" action="{{ route('customer.register') }}">
    @csrf

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="first_name" class="form-label">
                Nome <span class="required">*</span>
            </label>
            <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" 
                   name="first_name" value="{{ old('first_name') }}" required autofocus>
            @error('first_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="last_name" class="form-label">
                Sobrenome <span class="required">*</span>
            </label>
            <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" 
                   name="last_name" value="{{ old('last_name') }}" required>
            @error('last_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">
            E-mail <span class="required">*</span>
        </label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
               name="email" value="{{ old('email') }}" required>
        @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="phone" class="form-label">
            Telefone
        </label>
        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" 
               name="phone" value="{{ old('phone') }}" placeholder="(11) 99999-9999">
        @error('phone')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label">
                Senha <span class="required">*</span>
            </label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required>
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="password_confirmation" class="form-label">
                Confirmar Senha <span class="required">*</span>
            </label>
            <input id="password_confirmation" type="password" class="form-control" 
                   name="password_confirmation" required>
        </div>
    </div>

    <hr class="my-4">
    <h6 class="text-muted mb-3">Endereço (opcional)</h6>

    <div class="mb-3">
        <label for="address" class="form-label">Endereço</label>
        <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" 
               name="address" value="{{ old('address') }}">
        @error('address')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="neighborhood" class="form-label">Bairro</label>
            <input id="neighborhood" type="text" class="form-control @error('neighborhood') is-invalid @enderror" 
                   name="neighborhood" value="{{ old('neighborhood') }}">
            @error('neighborhood')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="city" class="form-label">Cidade</label>
            <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" 
                   name="city" value="{{ old('city') }}">
            @error('city')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="state" class="form-label">Estado</label>
            <select id="state" class="form-select @error('state') is-invalid @enderror" name="state">
                <option value="">Selecione</option>
                <option value="AC" {{ old('state') == 'AC' ? 'selected' : '' }}>Acre</option>
                <option value="AL" {{ old('state') == 'AL' ? 'selected' : '' }}>Alagoas</option>
                <option value="AP" {{ old('state') == 'AP' ? 'selected' : '' }}>Amapá</option>
                <option value="AM" {{ old('state') == 'AM' ? 'selected' : '' }}>Amazonas</option>
                <option value="BA" {{ old('state') == 'BA' ? 'selected' : '' }}>Bahia</option>
                <option value="CE" {{ old('state') == 'CE' ? 'selected' : '' }}>Ceará</option>
                <option value="DF" {{ old('state') == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                <option value="ES" {{ old('state') == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                <option value="GO" {{ old('state') == 'GO' ? 'selected' : '' }}>Goiás</option>
                <option value="MA" {{ old('state') == 'MA' ? 'selected' : '' }}>Maranhão</option>
                <option value="MT" {{ old('state') == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                <option value="MS" {{ old('state') == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                <option value="MG" {{ old('state') == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                <option value="PA" {{ old('state') == 'PA' ? 'selected' : '' }}>Pará</option>
                <option value="PB" {{ old('state') == 'PB' ? 'selected' : '' }}>Paraíba</option>
                <option value="PR" {{ old('state') == 'PR' ? 'selected' : '' }}>Paraná</option>
                <option value="PE" {{ old('state') == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                <option value="PI" {{ old('state') == 'PI' ? 'selected' : '' }}>Piauí</option>
                <option value="RJ" {{ old('state') == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                <option value="RN" {{ old('state') == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                <option value="RS" {{ old('state') == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                <option value="RO" {{ old('state') == 'RO' ? 'selected' : '' }}>Rondônia</option>
                <option value="RR" {{ old('state') == 'RR' ? 'selected' : '' }}>Roraima</option>
                <option value="SC" {{ old('state') == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                <option value="SP" {{ old('state') == 'SP' ? 'selected' : '' }}>São Paulo</option>
                <option value="SE" {{ old('state') == 'SE' ? 'selected' : '' }}>Sergipe</option>
                <option value="TO" {{ old('state') == 'TO' ? 'selected' : '' }}>Tocantins</option>
            </select>
            @error('state')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="zip_code" class="form-label">CEP</label>
            <input id="zip_code" type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                   name="zip_code" value="{{ old('zip_code') }}" placeholder="00000-000">
            @error('zip_code')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>
            Criar Conta
        </button>
    </div>
</form>

<div class="auth-links">
    <p class="mb-2">
        <a href="{{ route('customer.login') }}">Já tem uma conta? Entrar</a>
    </p>
    <p class="mb-2">
        <a href="{{ route('customer.register.b2b') }}">Cadastrar empresa (B2B)</a>
    </p>
    <p class="mb-0">
        <a href="{{ route('home') }}">Voltar ao início</a>
    </p>
</div>
@endsection
