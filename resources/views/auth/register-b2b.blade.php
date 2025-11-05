@extends('auth.layout')

@section('title', 'Cadastro B2B - Feira das Fábricas')

@section('header')
<h1>Cadastro Empresarial</h1>
<p>Tenha acesso a preços especiais e condições diferenciadas</p>
@endsection

@section('content')
<form method="POST" action="{{ route('customer.register.b2b') }}">
    @csrf

    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Importante:</strong> Após o cadastro, sua conta será analisada e você receberá um e-mail de confirmação em até 24 horas.
    </div>

    <h6 class="text-primary mb-3">Dados Pessoais</h6>

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
            E-mail Corporativo <span class="required">*</span>
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
            Telefone <span class="required">*</span>
        </label>
        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" 
               name="phone" value="{{ old('phone') }}" required placeholder="(11) 99999-9999">
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
    <h6 class="text-primary mb-3">Dados da Empresa</h6>

    <div class="mb-3">
        <label for="company_name" class="form-label">
            Nome da Empresa <span class="required">*</span>
        </label>
        <input id="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror" 
               name="company_name" value="{{ old('company_name') }}" required>
        @error('company_name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-8 mb-3">
            <label for="cnpj" class="form-label">
                CNPJ <span class="required">*</span>
            </label>
            <input id="cnpj" type="text" class="form-control @error('cnpj') is-invalid @enderror" 
                   name="cnpj" value="{{ old('cnpj') }}" required placeholder="00.000.000/0000-00">
            @error('cnpj')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-4 mb-3">
            <label for="ie" class="form-label">
                Inscrição Estadual
            </label>
            <input id="ie" type="text" class="form-control @error('ie') is-invalid @enderror" 
                   name="ie" value="{{ old('ie') }}">
            @error('ie')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="contact_person" class="form-label">
                Pessoa de Contato <span class="required">*</span>
            </label>
            <input id="contact_person" type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                   name="contact_person" value="{{ old('contact_person') }}" required>
            @error('contact_person')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="department" class="form-label">
                Departamento
            </label>
            <input id="department" type="text" class="form-control @error('department') is-invalid @enderror" 
                   name="department" value="{{ old('department') }}">
            @error('department')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <hr class="my-4">
    <h6 class="text-primary mb-3">Endereço da Empresa</h6>

    <div class="mb-3">
        <label for="address" class="form-label">
            Endereço <span class="required">*</span>
        </label>
        <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" 
               name="address" value="{{ old('address') }}" required>
        @error('address')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="neighborhood" class="form-label">
                Bairro <span class="required">*</span>
            </label>
            <input id="neighborhood" type="text" class="form-control @error('neighborhood') is-invalid @enderror" 
                   name="neighborhood" value="{{ old('neighborhood') }}" required>
            @error('neighborhood')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label for="city" class="form-label">
                Cidade <span class="required">*</span>
            </label>
            <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" 
                   name="city" value="{{ old('city') }}" required>
            @error('city')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="state" class="form-label">
                Estado <span class="required">*</span>
            </label>
            <select id="state" class="form-select @error('state') is-invalid @enderror" name="state" required>
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
            <label for="zip_code" class="form-label">
                CEP <span class="required">*</span>
            </label>
            <input id="zip_code" type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                   name="zip_code" value="{{ old('zip_code') }}" required placeholder="00000-000">
            @error('zip_code')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-building me-2"></i>
            Cadastrar Empresa
        </button>
    </div>
</form>

<div class="auth-links">
    <p class="mb-2">
        <a href="{{ route('customer.login') }}">Já tem uma conta? Entrar</a>
    </p>
    <p class="mb-2">
        <a href="{{ route('customer.register') }}">Cadastro pessoal (B2C)</a>
    </p>
    <p class="mb-0">
        <a href="{{ route('home') }}">Voltar ao início</a>
    </p>
</div>

<script>
// Máscara para CNPJ
document.getElementById('cnpj').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/^(\d{2})(\d)/, '$1.$2');
    value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
    value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
    value = value.replace(/(\d{4})(\d)/, '$1-$2');
    e.target.value = value;
});

// Máscara para telefone
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
        value = value.replace(/^(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{4,5})(\d{4})$/, '$1-$2');
    }
    e.target.value = value;
});

// Máscara para CEP
document.getElementById('zip_code').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/^(\d{5})(\d)/, '$1-$2');
    e.target.value = value;
});
</script>
@endsection
