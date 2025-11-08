@extends('admin.layouts.app')

@section('title', 'Criar Banner')
@section('page-title', 'Criar Novo Banner')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Voltar para Banners
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><i class="bi bi-exclamation-triangle"></i> Erro ao salvar banner:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">Título <small class="text-muted">(obrigatório apenas quando exibido)</small></label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Imagem Desktop *</label>
                        <div id="image-preview-container" class="mb-2" style="display: none;">
                            <img id="image-preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                            <div class="small text-success mt-1">
                                <i class="bi bi-check-circle"></i> <span id="image-filename"></span>
                            </div>
                        </div>
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image" 
                               accept="image/*"
                               required>
                        <small class="text-muted">
                            <strong>📁 Escolha um arquivo da sua área de trabalho</strong><br>
                            Formatos aceitos: JPG, PNG, GIF, WEBP. Tamanho máximo: 10MB<br>
                            Recomendado: 1920x600px para banners principais
                        </small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="mobile_image" class="form-label">Imagem Mobile (Opcional)</label>
                        <div id="mobile-image-preview-container" class="mb-2" style="display: none;">
                            <img id="mobile-image-preview" src="" alt="Preview Mobile" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                            <div class="small text-success mt-1">
                                <i class="bi bi-check-circle"></i> <span id="mobile-image-filename"></span>
                            </div>
                        </div>
                        <input type="file" 
                               class="form-control @error('mobile_image') is-invalid @enderror" 
                               id="mobile_image" 
                               name="mobile_image" 
                               accept="image/*">
                        <small class="text-muted">
                            <strong>📱 Imagem otimizada para celulares</strong><br>
                            Recomendado: 768x600px. Se não escolher, usará a imagem desktop
                        </small>
                        @error('mobile_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="link" class="form-label">Link (URL) <span class="text-muted">(Opcional)</span></label>
                        <input type="text" 
                               class="form-control @error('link') is-invalid @enderror" 
                               id="link" 
                               name="link" 
                               value="{{ old('link') }}"
                               placeholder="Ex: /produtos ou https://exemplo.com">
                        <small class="text-muted">
                            Adicione um link para onde o banner irá redirecionar ao ser clicado (opcional)
                        </small>
                        @error('link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="department_id" class="form-label">Departamento</label>
                            <select class="form-select @error('department_id') is-invalid @enderror" 
                                    id="department_id" 
                                    name="department_id">
                                <option value="">Banner Global (Todos os departamentos)</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ (old('department_id') == $department->id || request('department_id') == $department->id) ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> Deixe em branco para banner global ou selecione um departamento específico
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">Posição *</label>
                            <select class="form-select @error('position') is-invalid @enderror" 
                                    id="position" 
                                    name="position" 
                                    required>
                                <option value="">Selecione...</option>
                                <option value="hero" {{ (old('position') == 'hero' || request('position') == 'hero') ? 'selected' : '' }}>Topo (Hero)</option>
                                <option value="category" {{ old('position') == 'category' ? 'selected' : '' }}>Categorias</option>
                                <option value="product" {{ old('position') == 'product' ? 'selected' : '' }}>Produtos</option>
                                <option value="footer" {{ old('position') == 'footer' ? 'selected' : '' }}>Rodapé</option>
                            </select>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="sort_order" class="form-label">Ordem de Exibição</label>
                            <input type="number" 
                                   class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" 
                                   name="sort_order" 
                                   value="{{ old('sort_order', 0) }}"
                                   min="0">
                            <small class="text-muted">Menor número = maior prioridade</small>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="target_audience" class="form-label">Público-Alvo *</label>
                        <select class="form-select @error('target_audience') is-invalid @enderror" 
                                id="target_audience" 
                                name="target_audience" 
                                required>
                            <option value="">Selecione...</option>
                            <option value="all" {{ (old('target_audience') == 'all' || request('target_audience') == 'all') ? 'selected' : '' }}>Todos</option>
                            <option value="b2c" {{ old('target_audience') == 'b2c' ? 'selected' : '' }}>B2C (Consumidor Final)</option>
                            <option value="b2b" {{ old('target_audience') == 'b2b' ? 'selected' : '' }}>B2B (Empresas)</option>
                        </select>
                        @error('target_audience')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="starts_at" class="form-label">Data de Início</label>
                            <input type="datetime-local" 
                                   class="form-control @error('starts_at') is-invalid @enderror" 
                                   id="starts_at" 
                                   name="starts_at" 
                                   value="{{ old('starts_at') }}">
                            @error('starts_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="expires_at" class="form-label">Data de Término</label>
                            <input type="datetime-local" 
                                   class="form-control @error('expires_at') is-invalid @enderror" 
                                   id="expires_at" 
                                   name="expires_at" 
                                   value="{{ old('expires_at') }}">
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Banner Ativo
                            </label>
                        </div>
                        <small class="text-muted">Quando desativado, o banner não será exibido no site</small>
                    </div>

                    <!-- Seção de Customização Avançada -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-palette"></i> Customização Avançada
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Abas para organizar as opções -->
                            <ul class="nav nav-tabs" id="customizationTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="text-tab" data-bs-toggle="tab" data-bs-target="#text-pane" type="button" role="tab">
                                        <i class="bi bi-type"></i> Texto
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="layout-tab" data-bs-toggle="tab" data-bs-target="#layout-pane" type="button" role="tab">
                                        <i class="bi bi-layout-text-window"></i> Layout
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="display-tab" data-bs-toggle="tab" data-bs-target="#display-pane" type="button" role="tab">
                                        <i class="bi bi-eye"></i> Exibição
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content mt-3" id="customizationTabsContent">
                                <!-- Aba Texto -->
                                <div class="tab-pane fade show active" id="text-pane" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Estilo do Título</h6>
                                            <div class="mb-3">
                                                <label for="text_color" class="form-label">Cor do Título</label>
                                                <input type="color" 
                                                       class="form-control form-control-color" 
                                                       id="text_color" 
                                                       name="text_color" 
                                                       value="{{ old('text_color', '#ffffff') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="text_size" class="form-label">Tamanho da Fonte</label>
                                                <select class="form-select" id="text_size" name="text_size">
                                                    <option value="1rem" {{ old('text_size', '3rem') == '1rem' ? 'selected' : '' }}>Pequeno (1rem)</option>
                                                    <option value="1.5rem" {{ old('text_size', '3rem') == '1.5rem' ? 'selected' : '' }}>Médio (1.5rem)</option>
                                                    <option value="2rem" {{ old('text_size', '3rem') == '2rem' ? 'selected' : '' }}>Grande (2rem)</option>
                                                    <option value="3rem" {{ old('text_size', '3rem') == '3rem' ? 'selected' : '' }}>Extra Grande (3rem)</option>
                                                    <option value="4rem" {{ old('text_size', '3rem') == '4rem' ? 'selected' : '' }}>Enorme (4rem)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="text_align" class="form-label">Alinhamento</label>
                                                <select class="form-select" id="text_align" name="text_align">
                                                    <option value="left" {{ old('text_align', 'center') == 'left' ? 'selected' : '' }}>Esquerda</option>
                                                    <option value="center" {{ old('text_align', 'center') == 'center' ? 'selected' : '' }}>Centro</option>
                                                    <option value="right" {{ old('text_align', 'center') == 'right' ? 'selected' : '' }}>Direita</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="text_font_weight" class="form-label">Peso da Fonte</label>
                                                <select class="form-select" id="text_font_weight" name="text_font_weight">
                                                    <option value="300" {{ old('text_font_weight', '700') == '300' ? 'selected' : '' }}>Leve (300)</option>
                                                    <option value="400" {{ old('text_font_weight', '700') == '400' ? 'selected' : '' }}>Normal (400)</option>
                                                    <option value="600" {{ old('text_font_weight', '700') == '600' ? 'selected' : '' }}>Semi-negrito (600)</option>
                                                    <option value="700" {{ old('text_font_weight', '700') == '700' ? 'selected' : '' }}>Negrito (700)</option>
                                                    <option value="800" {{ old('text_font_weight', '700') == '800' ? 'selected' : '' }}>Extra-negrito (800)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="text_shadow_color" class="form-label">Cor da Sombra do Texto</label>
                                                <input type="color" 
                                                       class="form-control form-control-color" 
                                                       id="text_shadow_color" 
                                                       name="text_shadow_color" 
                                                       value="{{ old('text_shadow_color', '#000000') }}">
                                                <small class="text-muted">Deixe vazio para não usar sombra</small>
                                            </div>
                                            <div class="mb-3">
                                                <label for="text_shadow_blur" class="form-label">Intensidade da Sombra (Blur)</label>
                                                <input type="range" 
                                                       class="form-range" 
                                                       id="text_shadow_blur" 
                                                       name="text_shadow_blur" 
                                                       min="0" 
                                                       max="20" 
                                                       value="{{ old('text_shadow_blur', 4) }}">
                                                <div class="d-flex justify-content-between">
                                                    <small>0 (sem blur)</small>
                                                    <small id="text_shadow_blur_value">{{ old('text_shadow_blur', 4) }}</small>
                                                    <small>20 (máximo)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Estilo da Descrição</h6>
                                            <div class="mb-3">
                                                <label for="description_color" class="form-label">Cor da Descrição</label>
                                                <input type="color" 
                                                       class="form-control form-control-color" 
                                                       id="description_color" 
                                                       name="description_color" 
                                                       value="{{ old('description_color', '#ffffff') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="description_size" class="form-label">Tamanho da Descrição</label>
                                                <select class="form-select" id="description_size" name="description_size">
                                                    <option value="0.8rem" {{ old('description_size', '1.2rem') == '0.8rem' ? 'selected' : '' }}>Pequeno (0.8rem)</option>
                                                    <option value="1rem" {{ old('description_size', '1.2rem') == '1rem' ? 'selected' : '' }}>Médio (1rem)</option>
                                                    <option value="1.2rem" {{ old('description_size', '1.2rem') == '1.2rem' ? 'selected' : '' }}>Grande (1.2rem)</option>
                                                    <option value="1.5rem" {{ old('description_size', '1.2rem') == '1.5rem' ? 'selected' : '' }}>Extra Grande (1.5rem)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description_align" class="form-label">Alinhamento da Descrição</label>
                                                <select class="form-select" id="description_align" name="description_align">
                                                    <option value="left" {{ old('description_align', 'center') == 'left' ? 'selected' : '' }}>Esquerda</option>
                                                    <option value="center" {{ old('description_align', 'center') == 'center' ? 'selected' : '' }}>Centro</option>
                                                    <option value="right" {{ old('description_align', 'center') == 'right' ? 'selected' : '' }}>Direita</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Aba Layout -->
                                <div class="tab-pane fade" id="layout-pane" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Espaçamentos do Título</h6>
                                            <div class="row">
                                                <div class="col-6 mb-3">
                                                    <label for="text_padding_top" class="form-label">Padding Superior (rem)</label>
                                                    <input type="number" class="form-control" id="text_padding_top" name="text_padding_top" 
                                                           value="{{ old('text_padding_top', 0) }}" min="0" max="100">
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label for="text_padding_bottom" class="form-label">Padding Inferior (rem)</label>
                                                    <input type="number" class="form-control" id="text_padding_bottom" name="text_padding_bottom" 
                                                           value="{{ old('text_padding_bottom', 0) }}" min="0" max="100">
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label for="text_padding_left" class="form-label">Padding Esquerdo (rem)</label>
                                                    <input type="number" class="form-control" id="text_padding_left" name="text_padding_left" 
                                                           value="{{ old('text_padding_left', 0) }}" min="0" max="100">
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label for="text_padding_right" class="form-label">Padding Direito (rem)</label>
                                                    <input type="number" class="form-control" id="text_padding_right" name="text_padding_right" 
                                                           value="{{ old('text_padding_right', 0) }}" min="0" max="100">
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label for="text_margin_top" class="form-label">Margem Superior (rem)</label>
                                                    <input type="number" class="form-control" id="text_margin_top" name="text_margin_top" 
                                                           value="{{ old('text_margin_top', 0) }}" min="0" max="100">
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label for="text_margin_bottom" class="form-label">Margem Inferior (rem)</label>
                                                    <input type="number" class="form-control" id="text_margin_bottom" name="text_margin_bottom" 
                                                           value="{{ old('text_margin_bottom', 0) }}" min="0" max="100">
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label for="text_margin_left" class="form-label">Margem Esquerda (rem)</label>
                                                    <input type="number" class="form-control" id="text_margin_left" name="text_margin_left" 
                                                           value="{{ old('text_margin_left', 0) }}" min="0" max="100">
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label for="text_margin_right" class="form-label">Margem Direita (rem)</label>
                                                    <input type="number" class="form-control" id="text_margin_right" name="text_margin_right" 
                                                           value="{{ old('text_margin_right', 0) }}" min="0" max="100">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Espaçamentos da Descrição</h6>
                                            <div class="mb-3">
                                                <label for="description_margin_top" class="form-label">Margem Superior da Descrição (rem)</label>
                                                <input type="number" class="form-control" id="description_margin_top" name="description_margin_top" 
                                                       value="{{ old('description_margin_top', 2) }}" min="0" max="100">
                                                <small class="text-muted">Espaço entre o título e a descrição</small>
                                            </div>
                                            
                                            <h6 class="mt-4">Banner Geral</h6>
                                            <div class="mb-3">
                                                <label for="banner_height" class="form-label">Altura do Banner</label>
                                                <select class="form-select" id="banner_height" name="banner_height">
                                                    <option value="200px" {{ old('banner_height', '400px') == '200px' ? 'selected' : '' }}>Baixo (200px)</option>
                                                    <option value="300px" {{ old('banner_height', '400px') == '300px' ? 'selected' : '' }}>Médio (300px)</option>
                                                    <option value="400px" {{ old('banner_height', '400px') == '400px' ? 'selected' : '' }}>Alto (400px)</option>
                                                    <option value="500px" {{ old('banner_height', '400px') == '500px' ? 'selected' : '' }}>Extra Alto (500px)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="banner_background_color" class="form-label">Cor de Fundo do Banner</label>
                                                <input type="color" 
                                                       class="form-control form-control-color" 
                                                       id="banner_background_color" 
                                                       name="banner_background_color" 
                                                       value="{{ old('banner_background_color', '#000000') }}">
                                                <small class="text-muted">Deixe vazio para transparente</small>
                                            </div>
                                            <div class="mb-3">
                                                <label for="banner_padding_top" class="form-label">Padding Superior do Banner (rem)</label>
                                                <input type="number" class="form-control" id="banner_padding_top" name="banner_padding_top" 
                                                       value="{{ old('banner_padding_top', 0) }}" min="0" max="100">
                                            </div>
                                            <div class="mb-3">
                                                <label for="banner_padding_bottom" class="form-label">Padding Inferior do Banner (rem)</label>
                                                <input type="number" class="form-control" id="banner_padding_bottom" name="banner_padding_bottom" 
                                                       value="{{ old('banner_padding_bottom', 0) }}" min="0" max="100">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Aba Exibição -->
                                <div class="tab-pane fade" id="display-pane" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Elementos Visíveis</h6>
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="show_title" name="show_title" 
                                                           {{ old('show_title', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_title">
                                                        Mostrar Título
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="show_description" name="show_description" 
                                                           {{ old('show_description', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_description">
                                                        Mostrar Descrição
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="show_overlay" name="show_overlay" 
                                                           {{ old('show_overlay', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_overlay">
                                                        Mostrar Overlay
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Configurações do Overlay</h6>
                                            <div class="mb-3">
                                                <label for="overlay_color" class="form-label">Cor do Overlay</label>
                                                <input type="color" 
                                                       class="form-control form-control-color" 
                                                       id="overlay_color" 
                                                       name="overlay_color" 
                                                       value="{{ old('overlay_color', '#000000') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="overlay_opacity" class="form-label">Opacidade do Overlay (%)</label>
                                                <input type="range" 
                                                       class="form-range" 
                                                       id="overlay_opacity" 
                                                       name="overlay_opacity" 
                                                       min="0" 
                                                       max="100" 
                                                       value="{{ old('overlay_opacity', 70) }}">
                                                <div class="d-flex justify-content-between">
                                                    <small>0%</small>
                                                    <small id="overlay_opacity_value">{{ old('overlay_opacity', 70) }}%</small>
                                                    <small>100%</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Botões - Desktop</h6>
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="show_primary_button_desktop" name="show_primary_button_desktop" 
                                                           {{ old('show_primary_button_desktop', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_primary_button_desktop">
                                                        Mostrar botão primário
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="show_secondary_button_desktop" name="show_secondary_button_desktop" 
                                                           {{ old('show_secondary_button_desktop', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_secondary_button_desktop">
                                                        Mostrar botão secundário
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Botões - Mobile</h6>
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="show_primary_button_mobile" name="show_primary_button_mobile" 
                                                           {{ old('show_primary_button_mobile', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_primary_button_mobile">
                                                        Mostrar botão primário
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="show_secondary_button_mobile" name="show_secondary_button_mobile" 
                                                           {{ old('show_secondary_button_mobile', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_secondary_button_mobile">
                                                        Mostrar botão secundário
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Criar Banner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-info-circle"></i> Dicas
                </h5>
                <ul class="small">
                    <li class="mb-2">Use imagens de alta qualidade e otimizadas para web</li>
                    <li class="mb-2">Posição "Topo" é ideal para destaques principais</li>
                    <li class="mb-2">Configure público-alvo para segmentar melhor suas promoções</li>
                    <li class="mb-2">Use ordem de exibição para controlar sequência de banners</li>
                    <li class="mb-2">Configure datas para campanhas temporárias</li>
                </ul>
            </div>
        </div>
        
        @if(request('position') == 'hero')
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title text-primary">
                    <i class="bi bi-star"></i> Banner Principal
                </h5>
                <div class="alert alert-info border-0">
                    <small>
                        <strong>Este banner aparecerá na página inicial!</strong><br>
                        📁 <strong>Escolha arquivos da sua área de trabalho</strong><br>
                        • Desktop: 1920x600px<br>
                        • Mobile: 768x600px<br>
                        • Máximo: 10MB por imagem<br>
                        • Formatos: JPG, PNG, GIF, WEBP
                    </small>
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
        // Preview de imagem desktop
        const imageInput = document.getElementById('image');
        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('image-preview');
                        const container = document.getElementById('image-preview-container');
                        const filename = document.getElementById('image-filename');
                        
                        if (preview && container && filename) {
                            preview.src = e.target.result;
                            filename.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                            container.style.display = 'block';
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    const container = document.getElementById('image-preview-container');
                    if (container) container.style.display = 'none';
                }
            });
        }

        // Preview de imagem mobile
        const mobileImageInput = document.getElementById('mobile_image');
        if (mobileImageInput) {
            mobileImageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('mobile-image-preview');
                        const container = document.getElementById('mobile-image-preview-container');
                        const filename = document.getElementById('mobile-image-filename');
                        
                        if (preview && container && filename) {
                            preview.src = e.target.result;
                            filename.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                            container.style.display = 'block';
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    const container = document.getElementById('mobile-image-preview-container');
                    if (container) container.style.display = 'none';
                }
            });
        }

        // Atualizar valor do slider de opacidade
        const overlayOpacity = document.getElementById('overlay_opacity');
        if (overlayOpacity) {
            overlayOpacity.addEventListener('input', function() {
                document.getElementById('overlay_opacity_value').textContent = this.value + '%';
            });
        }
        
        // Atualizar valor do slider de blur da sombra
        const textShadowBlur = document.getElementById('text_shadow_blur');
        if (textShadowBlur) {
            textShadowBlur.addEventListener('input', function() {
                document.getElementById('text_shadow_blur_value').textContent = this.value;
            });
        }

        // Garantir obrigatoriedade condicional do título
        const showTitleSwitch = document.getElementById('show_title');
        const titleInput = document.getElementById('title');
        const handleTitleRequirement = () => {
            if (!titleInput) {
                return;
            }
            if (showTitleSwitch && showTitleSwitch.checked) {
                titleInput.setAttribute('required', 'required');
            } else {
                titleInput.removeAttribute('required');
            }
        };
        handleTitleRequirement();
        if (showTitleSwitch) {
            showTitleSwitch.addEventListener('change', handleTitleRequirement);
        }
    });
</script>
@endsection

