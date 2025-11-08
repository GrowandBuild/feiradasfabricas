@extends('admin.layouts.app')

@section('title', 'Editar Banner')
@section('page-title', 'Editar Banner')

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
                        <strong><i class="bi bi-exclamation-triangle"></i> Erro ao atualizar banner:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="title" class="form-label">Título <small class="text-muted">(obrigatório apenas quando exibido)</small></label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $banner->title) }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3">{{ old('description', $banner->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Imagem Desktop</label>
                        @if($banner->image && !empty($banner->image))
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $banner->image) }}?v={{ time() }}" 
                                     alt="{{ $banner->title }}" 
                                     class="img-thumbnail" 
                                     id="current-image-preview"
                                     style="max-width: 300px;"
                                     onerror="this.style.display='none';">
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-check-circle text-success"></i> Imagem atual: {{ basename($banner->image) }}
                                </div>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="remove_image" 
                                       name="remove_image" 
                                       value="1">
                                <label class="form-check-label text-danger" for="remove_image">
                                    <i class="bi bi-trash"></i> Remover imagem desktop
                                </label>
                            </div>
                        @else
                            <div class="alert alert-warning mb-2">
                                <i class="bi bi-exclamation-triangle"></i> Nenhuma imagem cadastrada
                            </div>
                        @endif
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image" 
                               accept="image/*">
                        <small class="text-muted">Deixe em branco para manter a imagem atual. Formatos: JPG, PNG, GIF, WEBP. Máximo: 10MB</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="mobile_image" class="form-label">Imagem Mobile (Opcional)</label>
                        @if($banner->mobile_image && !empty($banner->mobile_image))
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $banner->mobile_image) }}?v={{ time() }}" 
                                     alt="{{ $banner->title }} - Mobile" 
                                     class="img-thumbnail" 
                                     id="current-mobile-image-preview"
                                     style="max-width: 200px;"
                                     onerror="this.style.display='none';">
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-check-circle text-success"></i> Imagem mobile atual: {{ basename($banner->mobile_image) }}
                                </div>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="remove_mobile_image" 
                                       name="remove_mobile_image" 
                                       value="1">
                                <label class="form-check-label text-danger" for="remove_mobile_image">
                                    <i class="bi bi-trash"></i> Remover imagem mobile
                                </label>
                            </div>
                        @else
                            <div class="small text-muted mb-2">
                                <i class="bi bi-info-circle"></i> Nenhuma imagem mobile cadastrada. Será usada a imagem desktop.
                            </div>
                        @endif
                        <input type="file" 
                               class="form-control @error('mobile_image') is-invalid @enderror" 
                               id="mobile_image" 
                               name="mobile_image" 
                               accept="image/*">
                        <small class="text-muted">Deixe em branco para manter a imagem atual</small>
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
                               value="{{ old('link', $banner->link) }}"
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
                                    <option value="{{ $department->id }}" {{ old('department_id', $banner->department_id) == $department->id ? 'selected' : '' }}>
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

                        <hr class="my-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6>Elementos Visíveis</h6>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_title" name="show_title" 
                                               {{ old('show_title', $banner->show_title) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_title">
                                            Mostrar Título
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_description" name="show_description" 
                                               {{ old('show_description', $banner->show_description) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_description">
                                            Mostrar Descrição
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_overlay" name="show_overlay" 
                                               {{ old('show_overlay', $banner->show_overlay) ? 'checked' : '' }}>
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
                                           value="{{ old('overlay_color', $banner->overlay_color ?? '#000000') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="overlay_opacity" class="form-label">Opacidade do Overlay (%)</label>
                                    <input type="range" 
                                           class="form-range" 
                                           id="overlay_opacity" 
                                           name="overlay_opacity" 
                                           min="0" 
                                           max="100" 
                                           value="{{ old('overlay_opacity', $banner->overlay_opacity ?? 70) }}">
                                    <div class="d-flex justify-content-between">
                                        <small>0%</small>
                                        <small id="overlay_opacity_value">{{ old('overlay_opacity', $banner->overlay_opacity ?? 70) }}%</small>
                                        <small>100%</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6>Botões - Desktop</h6>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_primary_button_desktop" name="show_primary_button_desktop" 
                                               {{ old('show_primary_button_desktop', $banner->show_primary_button_desktop ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_primary_button_desktop">
                                            Mostrar botão primário
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_secondary_button_desktop" name="show_secondary_button_desktop" 
                                               {{ old('show_secondary_button_desktop', $banner->show_secondary_button_desktop ?? true) ? 'checked' : '' }}>
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
                                               {{ old('show_primary_button_mobile', $banner->show_primary_button_mobile ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_primary_button_mobile">
                                            Mostrar botão primário
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="show_secondary_button_mobile" name="show_secondary_button_mobile" 
                                               {{ old('show_secondary_button_mobile', $banner->show_secondary_button_mobile ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_secondary_button_mobile">
                                            Mostrar botão secundário
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">Posição *</label>
                            <select class="form-select @error('position') is-invalid @enderror" 
                                    id="position" 
                                    name="position" 
                                    required>
                                <option value="">Selecione...</option>
                                <option value="hero" {{ old('position', $banner->position) == 'hero' ? 'selected' : '' }}>Topo (Hero)</option>
                                <option value="category" {{ old('position', $banner->position) == 'category' ? 'selected' : '' }}>Categorias</option>
                                <option value="product" {{ old('position', $banner->position) == 'product' ? 'selected' : '' }}>Produtos</option>
                                <option value="footer" {{ old('position', $banner->position) == 'footer' ? 'selected' : '' }}>Rodapé</option>
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
                                   value="{{ old('sort_order', $banner->sort_order ?? 0) }}"
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
                            <option value="all" {{ old('target_audience', $banner->target_audience) == 'all' ? 'selected' : '' }}>Todos</option>
                            <option value="b2c" {{ old('target_audience', $banner->target_audience) == 'b2c' ? 'selected' : '' }}>B2C (Consumidor Final)</option>
                            <option value="b2b" {{ old('target_audience', $banner->target_audience) == 'b2b' ? 'selected' : '' }}>B2B (Empresas)</option>
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
                                   value="{{ old('starts_at', $banner->starts_at ? $banner->starts_at->format('Y-m-d\TH:i') : '') }}">
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
                                   value="{{ old('expires_at', $banner->expires_at ? $banner->expires_at->format('Y-m-d\TH:i') : '') }}">
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
                                   {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Banner Ativo
                            </label>
                        </div>
                        <small class="text-muted">Quando desativado, o banner não será exibido no site</small>
                    </div>

                    <!-- Seção de Customização Avançada -->
                    <div class="card mt-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-palette"></i> Customização Avançada do Banner
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Abas para organizar as opções -->
                            <ul class="nav nav-tabs mb-3" id="customizationTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="text-tab" data-bs-toggle="tab" data-bs-target="#text-pane" type="button" role="tab">
                                        <i class="bi bi-type"></i> Estilo do Texto
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="position-tab" data-bs-toggle="tab" data-bs-target="#position-pane" type="button" role="tab">
                                        <i class="bi bi-arrows-move"></i> Posicionamento
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="layout-tab" data-bs-toggle="tab" data-bs-target="#layout-pane" type="button" role="tab">
                                        <i class="bi bi-layout-text-window"></i> Layout
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="customizationTabsContent">
                                <!-- Aba Texto -->
                                <div class="tab-pane fade show active" id="text-pane" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="border-bottom pb-2 mb-3">
                                                <i class="bi bi-textarea-t"></i> Título
                                            </h6>
                                            <div class="mb-3">
                                                <label for="text_color" class="form-label">Cor do Título</label>
                                                <input type="color" 
                                                       class="form-control form-control-color" 
                                                       id="text_color" 
                                                       name="text_color" 
                                                       value="{{ old('text_color', $banner->text_color ?? '#ffffff') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="text_size" class="form-label">Tamanho da Fonte</label>
                                                <select class="form-select" id="text_size" name="text_size">
                                                    <option value="1.5rem" {{ old('text_size', $banner->text_size ?? '2.5rem') == '1.5rem' ? 'selected' : '' }}>Pequeno (1.5rem)</option>
                                                    <option value="2rem" {{ old('text_size', $banner->text_size ?? '2.5rem') == '2rem' ? 'selected' : '' }}>Médio (2rem)</option>
                                                    <option value="2.5rem" {{ old('text_size', $banner->text_size ?? '2.5rem') == '2.5rem' ? 'selected' : '' }}>Grande (2.5rem)</option>
                                                    <option value="3rem" {{ old('text_size', $banner->text_size ?? '2.5rem') == '3rem' ? 'selected' : '' }}>Extra Grande (3rem)</option>
                                                    <option value="3.5rem" {{ old('text_size', $banner->text_size ?? '2.5rem') == '3.5rem' ? 'selected' : '' }}>Enorme (3.5rem)</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="text_font_weight" class="form-label">Peso da Fonte</label>
                                                <select class="form-select" id="text_font_weight" name="text_font_weight">
                                                    <option value="300" {{ old('text_font_weight', $banner->text_font_weight ?? '700') == '300' ? 'selected' : '' }}>Leve (300)</option>
                                                    <option value="400" {{ old('text_font_weight', $banner->text_font_weight ?? '700') == '400' ? 'selected' : '' }}>Normal (400)</option>
                                                    <option value="600" {{ old('text_font_weight', $banner->text_font_weight ?? '700') == '600' ? 'selected' : '' }}>Semi-negrito (600)</option>
                                                    <option value="700" {{ old('text_font_weight', $banner->text_font_weight ?? '700') == '700' ? 'selected' : '' }}>Negrito (700)</option>
                                                    <option value="800" {{ old('text_font_weight', $banner->text_font_weight ?? '700') == '800' ? 'selected' : '' }}>Extra-negrito (800)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="border-bottom pb-2 mb-3">
                                                <i class="bi bi-file-text"></i> Descrição
                                            </h6>
                                            <div class="mb-3">
                                                <label for="description_color" class="form-label">Cor da Descrição</label>
                                                <input type="color" 
                                                       class="form-control form-control-color" 
                                                       id="description_color" 
                                                       name="description_color" 
                                                       value="{{ old('description_color', $banner->description_color ?? '#ffffff') }}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="description_size" class="form-label">Tamanho da Fonte</label>
                                                <select class="form-select" id="description_size" name="description_size">
                                                    <option value="0.9rem" {{ old('description_size', $banner->description_size ?? '1.2rem') == '0.9rem' ? 'selected' : '' }}>Pequeno (0.9rem)</option>
                                                    <option value="1rem" {{ old('description_size', $banner->description_size ?? '1.2rem') == '1rem' ? 'selected' : '' }}>Médio (1rem)</option>
                                                    <option value="1.2rem" {{ old('description_size', $banner->description_size ?? '1.2rem') == '1.2rem' ? 'selected' : '' }}>Grande (1.2rem)</option>
                                                    <option value="1.4rem" {{ old('description_size', $banner->description_size ?? '1.2rem') == '1.4rem' ? 'selected' : '' }}>Extra Grande (1.4rem)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Aba Posicionamento -->
                                <div class="tab-pane fade" id="position-pane" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="border-bottom pb-2 mb-3">
                                                <i class="bi bi-arrows-move"></i> Alinhamento do Título
                                            </h6>
                                            <div class="mb-3">
                                                <label for="text_align" class="form-label">Alinhamento Horizontal</label>
                                                <select class="form-select" id="text_align" name="text_align">
                                                    <option value="left" {{ old('text_align', $banner->text_align ?? 'center') == 'left' ? 'selected' : '' }}>⬅️ Esquerda</option>
                                                    <option value="center" {{ old('text_align', $banner->text_align ?? 'center') == 'center' ? 'selected' : '' }}>⬆️ Centro</option>
                                                    <option value="right" {{ old('text_align', $banner->text_align ?? 'center') == 'right' ? 'selected' : '' }}>➡️ Direita</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="text_vertical_align" class="form-label">Alinhamento Vertical</label>
                                                <select class="form-select" id="text_vertical_align" name="text_vertical_align">
                                                    <option value="top" {{ old('text_vertical_align', $banner->text_vertical_align ?? 'bottom') == 'top' ? 'selected' : '' }}>⬆️ Topo</option>
                                                    <option value="center" {{ old('text_vertical_align', $banner->text_vertical_align ?? 'bottom') == 'center' ? 'selected' : '' }}>⬌ Centro</option>
                                                    <option value="bottom" {{ old('text_vertical_align', $banner->text_vertical_align ?? 'bottom') == 'bottom' ? 'selected' : '' }}>⬇️ Baixo</option>
                                                </select>
                                                <small class="text-muted">Posição do texto no banner (topo, centro ou baixo)</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="border-bottom pb-2 mb-3">
                                                <i class="bi bi-arrows-move"></i> Alinhamento da Descrição
                                            </h6>
                                            <div class="mb-3">
                                                <label for="description_align" class="form-label">Alinhamento Horizontal</label>
                                                <select class="form-select" id="description_align" name="description_align">
                                                    <option value="left" {{ old('description_align', $banner->description_align ?? 'center') == 'left' ? 'selected' : '' }}>⬅️ Esquerda</option>
                                                    <option value="center" {{ old('description_align', $banner->description_align ?? 'center') == 'center' ? 'selected' : '' }}>⬆️ Centro</option>
                                                    <option value="right" {{ old('description_align', $banner->description_align ?? 'center') == 'right' ? 'selected' : '' }}>➡️ Direita</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description_vertical_align" class="form-label">Alinhamento Vertical</label>
                                                <select class="form-select" id="description_vertical_align" name="description_vertical_align">
                                                    <option value="top" {{ old('description_vertical_align', $banner->description_vertical_align ?? 'bottom') == 'top' ? 'selected' : '' }}>⬆️ Topo</option>
                                                    <option value="center" {{ old('description_vertical_align', $banner->description_vertical_align ?? 'bottom') == 'center' ? 'selected' : '' }}>⬌ Centro</option>
                                                    <option value="bottom" {{ old('description_vertical_align', $banner->description_vertical_align ?? 'bottom') == 'bottom' ? 'selected' : '' }}>⬇️ Baixo</option>
                                                </select>
                                                <small class="text-muted">Posição da descrição no banner (topo, centro ou baixo)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Aba Layout -->
                                <div class="tab-pane fade" id="layout-pane" role="tabpanel">
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> <strong>Dica:</strong> Configure espaçamentos e layout avançado aqui. Para a maioria dos casos, os valores padrão são suficientes.
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h6 class="border-bottom pb-2 mb-3">Espaçamentos e Layout Avançado</h6>
                                            <p class="text-muted small">As configurações de layout avançado estão disponíveis, mas geralmente não são necessárias para a maioria dos banners.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-info-circle"></i> Informações
                </h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2">
                        <strong>Criado em:</strong><br>
                        {{ $banner->created_at->format('d/m/Y H:i') }}
                    </li>
                    <li>
                        <strong>Última atualização:</strong><br>
                        {{ $banner->updated_at->format('d/m/Y H:i') }}
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-info-circle"></i> Dicas
                </h5>
                <ul class="small">
                    <li class="mb-2">Mantenha as imagens otimizadas para melhor performance</li>
                    <li class="mb-2">Teste o banner em diferentes dispositivos</li>
                    <li class="mb-2">Configure datas para campanhas sazonais</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview de imagem desktop antes do upload
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Criar ou atualizar preview
                    let preview = document.getElementById('new-image-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'new-image-preview';
                        preview.className = 'img-thumbnail mt-2';
                        preview.style.maxWidth = '300px';
                        imageInput.parentElement.appendChild(preview);
                    }
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    
                    // Mostrar mensagem de sucesso
                    const successMsg = document.createElement('div');
                    successMsg.className = 'alert alert-success mt-2';
                    successMsg.innerHTML = '<i class="bi bi-check-circle"></i> Nova imagem selecionada: ' + file.name;
                    const existingMsg = imageInput.parentElement.querySelector('.alert-success');
                    if (existingMsg) existingMsg.remove();
                    imageInput.parentElement.appendChild(successMsg);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Preview de imagem mobile antes do upload
    const mobileImageInput = document.getElementById('mobile_image');
    if (mobileImageInput) {
        mobileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Criar ou atualizar preview
                    let preview = document.getElementById('new-mobile-image-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'new-mobile-image-preview';
                        preview.className = 'img-thumbnail mt-2';
                        preview.style.maxWidth = '200px';
                        mobileImageInput.parentElement.appendChild(preview);
                    }
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    
                    // Mostrar mensagem de sucesso
                    const successMsg = document.createElement('div');
                    successMsg.className = 'alert alert-success mt-2';
                    successMsg.innerHTML = '<i class="bi bi-check-circle"></i> Nova imagem mobile selecionada: ' + file.name;
                    const existingMsg = mobileImageInput.parentElement.querySelector('.alert-success');
                    if (existingMsg) existingMsg.remove();
                    mobileImageInput.parentElement.appendChild(successMsg);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Forçar recarregamento da imagem após atualização bem-sucedida
    @if(session('success'))
        // Recarregar a página após 500ms para garantir que a imagem atualizada seja exibida
        setTimeout(function() {
            const currentImage = document.getElementById('current-image-preview');
            const currentMobileImage = document.getElementById('current-mobile-image-preview');
            if (currentImage) {
                const src = currentImage.src.split('?')[0];
                currentImage.src = src + '?v=' + Date.now();
            }
            if (currentMobileImage) {
                const src = currentMobileImage.src.split('?')[0];
                currentMobileImage.src = src + '?v=' + Date.now();
            }
        }, 500);
    @endif

    // Atualizar valor do slider de opacidade
    const overlayOpacityInput = document.getElementById('overlay_opacity');
    const overlayOpacityValue = document.getElementById('overlay_opacity_value');
    if (overlayOpacityInput && overlayOpacityValue) {
        overlayOpacityInput.addEventListener('input', function() {
            overlayOpacityValue.textContent = `${this.value}%`;
        });
    }

    // Garantir obrigatoriedade condicional do título
    const showTitleSwitch = document.getElementById('show_title');
    const titleInput = document.getElementById('title');
    const syncTitleRequirement = () => {
        if (!titleInput) {
            return;
        }
        if (showTitleSwitch && showTitleSwitch.checked) {
            titleInput.setAttribute('required', 'required');
        } else {
            titleInput.removeAttribute('required');
        }
    };
    syncTitleRequirement();
    if (showTitleSwitch) {
        showTitleSwitch.addEventListener('change', syncTitleRequirement);
    }
});
</script>
@endsection
