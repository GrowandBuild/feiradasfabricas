@extends('admin.layouts.app')

@section('title', 'Detalhes do Atributo')
@section('page-title', 'Detalhes do Atributo')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $attribute->name }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Tipo:</strong>
                        @php
                            $typeLabels = [
                                'color' => 'Cor',
                                'size' => 'Tamanho',
                                'text' => 'Texto',
                                'number' => 'Número',
                                'image' => 'Imagem'
                            ];
                        @endphp
                        <span class="badge bg-primary">{{ $typeLabels[$attribute->type] ?? ucfirst($attribute->type) }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $attribute->is_active ? 'success' : 'danger' }}">
                            {{ $attribute->is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>Slug:</strong> <code>{{ $attribute->slug }}</code>
                </div>

                <div class="mb-3">
                    <strong>Ordem:</strong> {{ $attribute->sort_order ?? 0 }}
                </div>

                <hr>

                <h6 class="mb-3">Valores do Atributo ({{ $attribute->allValues->count() }})</h6>
                
                @if($attribute->allValues->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Valor</th>
                                    <th>Exibição</th>
                                    @if($attribute->type === 'color')
                                        <th>Cor</th>
                                    @endif
                                    @if($attribute->type === 'image')
                                        <th>Imagem</th>
                                    @endif
                                    <th>Ordem</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attribute->allValues->sortBy('sort_order') as $value)
                                    <tr>
                                        <td><strong>{{ $value->value }}</strong></td>
                                        <td>{{ $value->display_value ?: $value->value }}</td>
                                        @if($attribute->type === 'color')
                                            <td>
                                                @if($value->color_hex)
                                                    <span class="color-preview" style="background-color: {{ $value->color_hex }};"></span>
                                                    <code>{{ $value->color_hex }}</code>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        @endif
                                        @if($attribute->type === 'image')
                                            <td>
                                                @if($value->image_url)
                                                    <img src="{{ $value->image_url }}" alt="{{ $value->value }}" 
                                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>{{ $value->sort_order }}</td>
                                        <td>
                                            <span class="badge bg-{{ $value->is_active ? 'success' : 'danger' }}">
                                                {{ $value->is_active ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Nenhum valor cadastrado.</p>
                @endif

                <div class="mt-4">
                    <a href="{{ route('admin.attributes.edit', $attribute) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar Atributo
                    </a>
                    <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .color-preview {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        display: inline-block;
        vertical-align: middle;
        margin-right: 0.5rem;
    }
</style>
@endsection


