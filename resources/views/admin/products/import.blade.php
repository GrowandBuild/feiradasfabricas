@extends('admin.layouts.app')

@section('title', 'Importar Produtos')
@section('page-title', 'Importar Produtos em Massa')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-upload"></i> Importar Produtos via Excel/CSV
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="file" class="form-label">Arquivo Excel ou CSV *</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" 
                               id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">Formatos aceitos: .xlsx, .xls, .csv (máx. 10MB)</div>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Importar Produtos
                        </button>
                        <a href="{{ route('admin.products.import.template') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-download"></i> Baixar Template
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success') || session('errors'))
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Resultado da Importação</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('errors'))
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-exclamation-triangle"></i> Erros encontrados:</h6>
                            <ul class="mb-0">
                                @foreach(session('errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Como usar</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="bi bi-info-circle"></i> Instruções:</h6>
                    <ol class="mb-0">
                        <li>Baixe o template CSV</li>
                        <li>Preencha com seus produtos</li>
                        <li>Faça upload do arquivo</li>
                        <li>Produtos serão criados automaticamente</li>
                    </ol>
                </div>

                <div class="alert alert-warning">
                    <h6><i class="bi bi-exclamation-triangle"></i> Campos obrigatórios:</h6>
                    <ul class="mb-0 small">
                        <li><strong>nome:</strong> Nome do produto</li>
                        <li><strong>marca:</strong> Marca do produto</li>
                        <li><strong>preco:</strong> Preço de venda</li>
                        <li><strong>estoque:</strong> Quantidade em estoque</li>
                    </ul>
                </div>

                <div class="alert alert-success">
                    <h6><i class="bi bi-check-circle"></i> Campos opcionais:</h6>
                    <ul class="mb-0 small">
                        <li><strong>sku:</strong> Código único</li>
                        <li><strong>modelo:</strong> Modelo do produto</li>
                        <li><strong>descricao:</strong> Descrição detalhada</li>
                        <li><strong>preco_b2b:</strong> Preço para B2B</li>
                        <li><strong>preco_custo:</strong> Preço de custo</li>
                        <li><strong>estoque_minimo:</strong> Estoque mínimo</li>
                        <li><strong>peso:</strong> Peso do produto</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Dicas</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li><i class="bi bi-lightbulb text-warning"></i> Use o template para evitar erros</li>
                    <li><i class="bi bi-lightbulb text-warning"></i> SKUs duplicados serão rejeitados</li>
                    <li><i class="bi bi-lightbulb text-warning"></i> Preços devem usar ponto decimal</li>
                    <li><i class="bi bi-lightbulb text-warning"></i> Estoque deve ser número inteiro</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
