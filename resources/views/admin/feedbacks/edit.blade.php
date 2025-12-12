@extends('admin.layouts.app')

@section('title', 'Editar Feedback')
@section('page-title', 'Editar Feedback')
@section('page-icon', 'bi bi-chat-heart')
@section('page-description', 'Edite as informações do feedback.')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.feedbacks.update', $feedback) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="product_id" class="form-label">Produto <span class="text-danger">*</span></label>
                <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                    <option value="">Selecione um produto</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id', $feedback->product_id) == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="text" class="form-label">Texto do Feedback</label>
                <textarea name="text" id="text" class="form-control @error('text') is-invalid @enderror" rows="4" placeholder="Digite o feedback textual (opcional)">{{ old('text', $feedback->text) }}</textarea>
                <small class="form-text text-muted">Opcional. Você pode adicionar apenas texto, apenas imagem, ou ambos.</small>
                @error('text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Imagem</label>
                @if($feedback->image)
                    <div class="mb-2">
                        <img src="{{ $feedback->image_url }}" alt="Imagem atual" class="img-thumbnail" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                        <p class="text-muted small mt-1">Imagem atual</p>
                    </div>
                @endif
                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                <small class="form-text text-muted">Opcional. Deixe em branco para manter a imagem atual. Formatos aceitos: JPEG, PNG, JPG, GIF, WEBP. Máximo 5MB.</small>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="image-preview" class="mt-2"></div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_approved" id="is_approved" value="1" {{ old('is_approved', $feedback->is_approved) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_approved">
                        Aprovado
                    </label>
                </div>
                <small class="form-text text-muted">Feedbacks aprovados são exibidos no site.</small>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> <strong>Importante:</strong> O feedback deve ter pelo menos um texto ou uma imagem.
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.feedbacks.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Atualizar Feedback
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('image-preview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px; max-height: 200px; object-fit: cover;"><p class="text-muted small mt-1">Nova imagem (será substituída)</p>';
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
        }
    });
</script>
@endpush
@endsection

