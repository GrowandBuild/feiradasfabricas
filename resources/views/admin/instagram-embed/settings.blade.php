@use Illuminate\Support\Str
@extends('admin.layouts.app')

@section('title', 'Configurações do Instagram Embed')
@section('page-title', 'Integração com Instagram')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-instagram me-2"></i>
                        Instagram Embed - Caminhos que Funcionam
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>Como funciona:</h6>
                        <ol class="mb-0">
                            <li>Copie os links diretos dos posts/stories do Instagram</li>
                            <li>Cole um link por linha no campo abaixo</li>
                            <li>O sistema usa oEmbed oficial do Instagram (seguro e confiável)</li>
                            <li>Funciona com posts, reels, stories highlights e perfis públicos</li>
                        </ol>
                    </div>

                    <div class="alert alert-success">
                        <h6><i class="bi bi-check-circle me-2"></i>URLs Suportadas:</h6>
                        <ul class="mb-0 small">
                            <li><strong>Posts:</strong> https://instagram.com/p/CODE123/</li>
                            <li><strong>Reels:</strong> https://instagram.com/reel/CODE123/</li>
                            <li><strong>Stories Highlights:</strong> https://instagram.com/stories/highlights/1234567890/</li>
                            <li><strong>IGTV:</strong> https://instagram.com/tv/CODE123/</li>
                            <li><strong>Perfis:</strong> https://instagram.com/nomeperfil</li>
                        </ul>
                    </div>

                    <form id="instagramEmbedForm">
                        @csrf
                        <div class="mb-4">
                            <label for="instagram_urls" class="form-label">
                                <i class="bi bi-link-45deg me-2"></i>URLs do Instagram
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="instagram_urls" name="instagram_urls" 
                                      rows="8" placeholder="Cole uma URL por linha:&#10;https://instagram.com/p/CODE123/&#10;https://instagram.com/reel/CODE456/&#10;https://instagram.com/stories/highlights/1234567890/" required>{{ setting('instagram_embed_urls') }}</textarea>
                            <div class="form-text">
                                Cole os links diretos dos posts/stories do Instagram, um por linha
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="auto_sync" name="auto_sync" 
                                       {{ setting('instagram_embed_auto_sync', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="auto_sync">
                                    <i class="bi bi-arrow-repeat me-2"></i>Sincronização Automática
                                </label>
                                <div class="form-text">
                                    Atualiza automaticamente os embeds a cada 30 minutos
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Salvar URLs
                            </button>
                            <button type="button" id="testUrlBtn" class="btn btn-outline-info">
                                <i class="bi bi-plug me-2"></i>Testar URL
                            </button>
                            <button type="button" id="clearEmbedsBtn" class="btn btn-outline-danger">
                                <i class="bi bi-trash me-2"></i>Limpar Tudo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Status da Conexão
                    </h6>
                </div>
                <div class="card-body">
                    @if(setting('instagram_embed_urls'))
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-success me-2">
                                    Configurado
                                </span>
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            <small class="text-muted d-block">
                                <i class="bi bi-link-45deg me-1"></i>
                                {{ count(explode("\n", trim(setting('instagram_embed_urls')))) }} URLs configuradas
                            </small>
                        </div>

                        @if(setting('instagram_embed_last_sync'))
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                Última sincronização: {{ \Carbon\Carbon::parse(setting('instagram_embed_last_sync'))->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        @endif
                    @else
                        <div class="text-center text-muted">
                            <i class="bi bi-instagram" style="font-size: 3rem;"></i>
                            <p class="mt-2">Nenhuma URL configurada</p>
                            <small>Adicione URLs do Instagram para começar</small>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-question-circle me-2"></i>Ajuda
                    </h6>
                </div>
                <div class="card-body">
                    <h6>Como obter as URLs:</h6>
                    <ol class="small">
                        <li>Abra o post/story no Instagram</li>
                        <li>Clique nos três pontos (⋮)</li>
                        <li>Selecione "Copiar link"</li>
                        <li>Cole no campo acima</li>
                    </ol>
                    
                    <h6 class="mt-3">Requisitos:</h6>
                    <ul class="small">
                        <li>Posts/Stories devem ser públicos</li>
                        <li>Contas privadas não funcionam</li>
                        <li>oEmbed é oficial do Instagram</li>
                        <li>Sem necessidade de API tokens</li>
                    </ul>

                    <h6 class="mt-3">Limitações:</h6>
                    <ul class="small">
                        <li>Máximo 10 URLs recomendadas</li>
                        <li>Não lista highlights automaticamente</li>
                        <li>Links manuais necessários</li>
                        <li>Cache de 30 minutos</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Previews Section -->
        @if(!empty($savedUrls))
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-eye me-2"></i>
                        Previews dos Embeds ({{ count($savedUrls) }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($savedUrls as $index => $embed)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge bg-primary me-2">{{ $embed['type'] ?? 'post' }}</span>
                                            @if($embed['is_video'] ?? false)
                                                <i class="bi bi-play-circle text-danger"></i>
                                            @else
                                                <i class="bi bi-image text-info"></i>
                                            @endif
                                        </div>
                                        
                                        @if($embed['thumbnail_url'])
                                            <img src="{{ $embed['thumbnail_url'] }}" 
                                                 class="img-fluid rounded mb-3" 
                                                 alt="Instagram preview"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                            <div class="text-center text-muted py-3" style="display: none;">
                                                <i class="bi bi-image" style="font-size: 2rem;"></i>
                                                <p class="mb-0">Imagem não disponível</p>
                                            </div>
                                        @else
                                            <div class="text-center text-muted py-3">
                                                <i class="bi bi-image" style="font-size: 2rem;"></i>
                                                <p class="mb-0">Preview não disponível</p>
                                            </div>
                                        @endif
                                        
                                        <h6 class="card-title">{{ $embed['title'] ?? 'Instagram Post' }}</h6>
                                        
                                        @if($embed['caption'])
                                            <p class="card-text small text-muted">{{ Str::limit($embed['caption'], 100) }}</p>
                                        @endif
                                        
                                        <div class="mt-auto">
                                            <a href="{{ $embed['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>
                                                Ver no Instagram
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('instagramEmbedForm');
    const testBtn = document.getElementById('testUrlBtn');
    const clearBtn = document.getElementById('clearEmbedsBtn');

    // Salvar URLs
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Converter checkbox para boolean
        data.auto_sync = formData.has('auto_sync');

        fetch('{{ route("admin.instagram-embed.save-urls") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            showAlert('Erro ao salvar URLs', 'danger');
        });
    });

    // Testar URL
    testBtn.addEventListener('click', function() {
        const url = prompt('Cole a URL do Instagram para testar:');
        if (!url) return;

        this.disabled = true;
        this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Testando...';

        fetch('{{ route("admin.instagram-embed.test-url") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ url: url })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(`${data.message}<br><small>Tipo: ${data.data.type}</small>`, 'success');
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            showAlert('Erro ao testar URL', 'danger');
        })
        .finally(() => {
            testBtn.disabled = false;
            testBtn.innerHTML = '<i class="bi bi-plug me-2"></i>Testar URL';
        });
    });

    // Limpar embeds
    clearBtn.addEventListener('click', function() {
        if (!confirm('Tem certeza que deseja remover todas as URLs do Instagram?')) {
            return;
        }

        this.disabled = true;
        this.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Limpando...';

        fetch('{{ route("admin.instagram-embed.clear-embeds") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            showAlert('Erro ao limpar embeds', 'danger');
        })
        .finally(() => {
            clearBtn.disabled = false;
            clearBtn.innerHTML = '<i class="bi bi-trash me-2"></i>Limpar Tudo';
        });
    });

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        form.parentElement.insertBefore(alertDiv, form);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});
</script>
@endsection
