@extends('admin.layouts.app')

@section('page-title', 'Badges Promocionais')
@section('page-icon', 'bi bi-tag-fill')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Badges Promocionais</h5>
                <a href="{{ route('admin.promotional-badges.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Novo Badge
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($badges->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Texto</th>
                                    <th>Posição</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($badges as $badge)
                                    <tr>
                                        <td>{{ Str::limit($badge->text, 50) }}</td>
                                        <td>
                                            @if($badge->position === 'bottom-right')
                                                Canto Inferior Direito
                                            @elseif($badge->position === 'bottom-left')
                                                Canto Inferior Esquerdo
                                            @elseif($badge->position === 'center-bottom')
                                                Centro Inferior
                                            @elseif($badge->position === 'top-right')
                                                Canto Superior Direito
                                            @elseif($badge->position === 'top-left')
                                                Canto Superior Esquerdo
                                            @elseif($badge->position === 'center-top')
                                                Centro Superior
                                            @elseif($badge->position === 'center')
                                                Centro da Tela
                                            @else
                                                {{ $badge->position }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($badge->is_active)
                                                <span class="badge bg-success">Ativo</span>
                                            @else
                                                <span class="badge bg-secondary">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.promotional-badges.edit', $badge) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.promotional-badges.destroy', $badge) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja remover este badge?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Nenhum badge cadastrado ainda.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

