<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - Feira das Fábricas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h2 {
            color: #333;
            font-weight: 600;
        }
        .login-header p {
            color: #666;
            margin-bottom: 0;
        }
        .form-control {
            border-radius: 0.5rem;
            border: 1px solid #ddd;
            padding: 0.75rem 1rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-weight: 500;
            width: 100%;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h2><i class="bi bi-shield-lock"></i> Admin Login</h2>
            <p>Faça login para acessar o painel administrativo</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Entrar
            </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">
                <a href="{{ route('home') }}" class="text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Voltar ao site
                </a>
            </small>
        </div>

        <!-- Credenciais de Acesso - TEMPORÁRIO -->
        <div class="alert alert-info mt-4" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border: 1px solid #2196f3;">
            <h6 class="alert-heading mb-2">
                <i class="bi bi-info-circle"></i> Acesso Temporário - Desenvolvimento
            </h6>
            <div class="row">
                <div class="col-12 mb-2">
                    <strong>🔑 Administrador:</strong><br>
                    <code>admin@feiradasfabricas.com</code><br>
                    <code>admin123</code>
                </div>
                <div class="col-12">
                    <strong>👨‍💼 Gerente:</strong><br>
                    <code>gerente@feiradasfabricas.com</code><br>
                    <code>gerente123</code>
                </div>
            </div>
            <hr class="my-2">
            <small class="text-muted">
                <i class="bi bi-exclamation-triangle"></i> 
                Estas credenciais são apenas para desenvolvimento. Remover em produção.
            </small>
        </div>

        <!-- Botões de Acesso Rápido -->
        <div class="row mt-3">
            <div class="col-6">
                <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="fillLogin('admin@feiradasfabricas.com', 'admin123')">
                    <i class="bi bi-person-check"></i> Admin
                </button>
            </div>
            <div class="col-6">
                <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="fillLogin('gerente@feiradasfabricas.com', 'gerente123')">
                    <i class="bi bi-person"></i> Gerente
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Função para preencher automaticamente os campos de login
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            
            // Destacar os campos preenchidos
            document.getElementById('email').style.backgroundColor = '#e8f5e8';
            document.getElementById('password').style.backgroundColor = '#e8f5e8';
            
            // Remover o destaque após 2 segundos
            setTimeout(() => {
                document.getElementById('email').style.backgroundColor = '';
                document.getElementById('password').style.backgroundColor = '';
            }, 2000);
            
            // Focar no botão de login
            document.querySelector('.btn-login').focus();
        }
        
        // Auto-focus no primeiro campo
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });
        
        // Enter para submeter o formulário
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });
    </script>
</body>
</html>
