@extends('admin.layouts.app')

@section('page-title', 'Editar Badge Promocional')
@section('page-icon', 'bi bi-tag-fill')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Editar Badge Promocional</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.promotional-badges.update', $promotionalBadge) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="text" class="form-label">Texto do Badge *</label>
                        <textarea name="text" id="text" class="form-control" rows="3" required>{{ old('text', $promotionalBadge->text) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Imagem (opcional)</label>
                        @if($promotionalBadge->image_url)
                            <div class="mb-2">
                                <img src="{{ $promotionalBadge->image_url }}" alt="Imagem atual" style="max-width: 200px; max-height: 100px;" class="img-thumbnail">
                            </div>
                        @endif
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label for="link" class="form-label">Link (opcional)</label>
                        <input type="url" name="link" id="link" class="form-control" value="{{ old('link', $promotionalBadge->link) }}">
                    </div>

                    <div class="mb-3">
                        <label for="position" class="form-label">Posição *</label>
                        <select name="position" id="position" class="form-select" required>
                            <optgroup label="Inferior">
                                <option value="center-bottom" {{ old('position', $promotionalBadge->position) === 'center-bottom' ? 'selected' : '' }}>Centro Inferior</option>
                                <option value="bottom-right" {{ old('position', $promotionalBadge->position) === 'bottom-right' ? 'selected' : '' }}>Canto Inferior Direito</option>
                                <option value="bottom-left" {{ old('position', $promotionalBadge->position) === 'bottom-left' ? 'selected' : '' }}>Canto Inferior Esquerdo</option>
                            </optgroup>
                            <optgroup label="Superior">
                                <option value="center-top" {{ old('position', $promotionalBadge->position) === 'center-top' ? 'selected' : '' }}>Centro Superior</option>
                                <option value="top-right" {{ old('position', $promotionalBadge->position) === 'top-right' ? 'selected' : '' }}>Canto Superior Direito</option>
                                <option value="top-left" {{ old('position', $promotionalBadge->position) === 'top-left' ? 'selected' : '' }}>Canto Superior Esquerdo</option>
                            </optgroup>
                            <optgroup label="Centro">
                                <option value="center" {{ old('position', $promotionalBadge->position) === 'center' ? 'selected' : '' }}>Centro da Tela</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="auto_close_seconds" class="form-label">Fechar Automaticamente (segundos)</label>
                        <input type="number" name="auto_close_seconds" id="auto_close_seconds" class="form-control" value="{{ old('auto_close_seconds', $promotionalBadge->auto_close_seconds) }}" min="0" max="300">
                        <small class="text-muted">0 = não fecha automaticamente</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="show_close_button" id="show_close_button" class="form-check-input" value="1" {{ old('show_close_button', $promotionalBadge->show_close_button) ? 'checked' : '' }}>
                            <label for="show_close_button" class="form-check-label">Mostrar botão de fechar (X)</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $promotionalBadge->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Ativo</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <a href="{{ route('admin.promotional-badges.index') }}" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

