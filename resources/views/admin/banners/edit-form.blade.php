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

@php
    $deptColor = '#0d6efd';
    if (isset($banner) && $banner->department && $banner->department->color) {
        $deptColor = $banner->department->color;
    } elseif (isset($departments) && $departments->first() && $departments->first()->color) {
        $deptColor = $departments->first()->color;
    }
@endphp

<form id="banner-edit-form" action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" style="--dept-color: {{ $deptColor }};">
    @csrf
    @method('PUT')

    <!-- Top-level tabs to reduce vertical scroll and group related settings -->
    <ul class="nav nav-tabs mb-3 compact-tabs nav-fill" id="bannerEditTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-general-btn" data-bs-toggle="tab" data-bs-target="#tab-general" type="button" role="tab">
                <i class="bi bi-gear"></i> Geral
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-media-btn" data-bs-toggle="tab" data-bs-target="#tab-media" type="button" role="tab">
                <i class="bi bi-image"></i> Imagens
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-custom-btn" data-bs-toggle="tab" data-bs-target="#tab-custom" type="button" role="tab">
                <i class="bi bi-palette"></i> Customização
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-visibility-btn" data-bs-toggle="tab" data-bs-target="#tab-visibility" type="button" role="tab">
                <i class="bi bi-eye"></i> Visibilidade
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-buttons-btn" data-bs-toggle="tab" data-bs-target="#tab-buttons" type="button" role="tab">
                <i class="bi bi-ui-checks-grid"></i> Botões
            </button>
        </li>
    </ul>

    <div class="tab-content" id="bannerEditTabsContent">
        <!-- Geral -->
        <div class="tab-pane fade show active" id="tab-general" role="tabpanel">
            <div class="mb-3">
                <div class="row g-2">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Título <small class="text-muted">(obrigatório apenas quando exibido)</small></label>
                        <input type="text"
                               class="form-control form-control-sm @error('title') is-invalid @enderror"
                               id="title"
                               name="title"
                               value="{{ old('title', $banner->title) }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 d-none d-md-block">
                        <label class="form-label">&nbsp;</label>
                        <div class="form-control-plaintext small text-muted">Posição: <strong>{{ ucfirst(old('position', $banner->position ?? '')) }}</strong></div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descrição</label>
                <textarea class="form-control form-control-sm @error('description') is-invalid @enderror"
                          id="description"
                          name="description"
                          rows="2">{{ old('description', $banner->description) }}</textarea>
                @error('description')
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
                            <option value="{{ $department->id }}" data-color="{{ $department->color ?? '' }}" {{ old('department_id', $banner->department_id) == $department->id ? 'selected' : '' }}>
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
        </div>

        <!-- Imagens -->
        <div class="tab-pane fade" id="tab-media" role="tabpanel">
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
        </div>

        <!-- Customização (mantém abas internas existentes) -->
        <div class="tab-pane fade" id="tab-custom" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <!-- Reuse existing customization block (text/position/layout tabs) -->
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
        </div>

        <!-- Visibilidade -->
        <div class="tab-pane fade" id="tab-visibility" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6>Elementos Visíveis</h6>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="modal_show_title" name="show_title" 
                                   {{ old('show_title', $banner->show_title) ? 'checked' : '' }}>
                            <label class="form-check-label" for="modal_show_title">
                                Mostrar Título
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="modal_show_description" name="show_description" 
                                   {{ old('show_description', $banner->show_description) ? 'checked' : '' }}>
                            <label class="form-check-label" for="modal_show_description">
                                Mostrar Descrição
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="modal_show_overlay" name="show_overlay" 
                                   {{ old('show_overlay', $banner->show_overlay) ? 'checked' : '' }}>
                            <label class="form-check-label" for="modal_show_overlay">
                                Mostrar Overlay
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Configurações do Overlay</h6>
                    <div class="mb-3">
                        <label for="modal_overlay_color" class="form-label">Cor do Overlay</label>
                        <input type="color" 
                               class="form-control form-control-color" 
                               id="modal_overlay_color" 
                               name="overlay_color" 
                               value="{{ old('overlay_color', $banner->overlay_color ?? '#000000') }}">
                    </div>
                    <div class="mb-3">
                        <label for="modal_overlay_opacity" class="form-label">Opacidade do Overlay (%)</label>
                        <input type="range" 
                               class="form-range" 
                               id="modal_overlay_opacity" 
                               name="overlay_opacity" 
                               min="0" 
                               max="100" 
                               value="{{ old('overlay_opacity', $banner->overlay_opacity ?? 70) }}">
                        <div class="d-flex justify-content-between">
                            <small>0%</small>
                            <small id="modal_overlay_opacity_value">{{ old('overlay_opacity', $banner->overlay_opacity ?? 70) }}%</small>
                            <small>100%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões -->
        <div class="tab-pane fade" id="tab-buttons" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6>Botões - Desktop</h6>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="modal_show_primary_button_desktop" name="show_primary_button_desktop" 
                                   {{ old('show_primary_button_desktop', $banner->show_primary_button_desktop ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="modal_show_primary_button_desktop">
                                Mostrar botão primário
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="primary_button_text" class="form-label">Texto do botão primário</label>
                        <input type="text" class="form-control form-control-sm" id="primary_button_text" name="primary_button_text" value="{{ old('primary_button_text', $banner->primary_button_text) }}" placeholder="Ex: Ver detalhes">
                    </div>
                    <div class="mb-3">
                        <label for="primary_button_link" class="form-label">Link do botão primário</label>
                        <input type="text" class="form-control form-control-sm" id="primary_button_link" name="primary_button_link" value="{{ old('primary_button_link', $banner->primary_button_link) }}" placeholder="Ex: /produtos/123 ou https://exemplo.com">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="modal_show_secondary_button_desktop" name="show_secondary_button_desktop" 
                                   {{ old('show_secondary_button_desktop', $banner->show_secondary_button_desktop ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="modal_show_secondary_button_desktop">
                                Mostrar botão secundário
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="secondary_button_text" class="form-label">Texto do botão secundário</label>
                        <input type="text" class="form-control form-control-sm" id="secondary_button_text" name="secondary_button_text" value="{{ old('secondary_button_text', $banner->secondary_button_text) }}" placeholder="Ex: Saiba mais">
                    </div>
                    <div class="mb-3">
                        <label for="secondary_button_link" class="form-label">Link do botão secundário</label>
                        <input type="text" class="form-control form-control-sm" id="secondary_button_link" name="secondary_button_link" value="{{ old('secondary_button_link', $banner->secondary_button_link) }}" placeholder="Ex: /contato">
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Botões - Mobile</h6>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="modal_show_primary_button_mobile" name="show_primary_button_mobile" 
                                   {{ old('show_primary_button_mobile', $banner->show_primary_button_mobile ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="modal_show_primary_button_mobile">
                                Mostrar botão primário
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="primary_button_text_mobile" class="form-label">Texto do botão primário (mobile) — opcional</label>
                        <input type="text" class="form-control form-control-sm" id="primary_button_text_mobile" name="primary_button_text_mobile" value="{{ old('primary_button_text_mobile', $banner->primary_button_text) }}" placeholder="Se vazio, usa o texto desktop">
                    </div>
                    <div class="mb-3">
                        <label for="primary_button_link_mobile" class="form-label">Link do botão primário (mobile) — opcional</label>
                        <input type="text" class="form-control form-control-sm" id="primary_button_link_mobile" name="primary_button_link_mobile" value="{{ old('primary_button_link_mobile', $banner->primary_button_link) }}" placeholder="Se vazio, usa o link desktop">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="modal_show_secondary_button_mobile" name="show_secondary_button_mobile" 
                                   {{ old('show_secondary_button_mobile', $banner->show_secondary_button_mobile ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="modal_show_secondary_button_mobile">
                                Mostrar botão secundário
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="secondary_button_text_mobile" class="form-label">Texto do botão secundário (mobile) — opcional</label>
                        <input type="text" class="form-control form-control-sm" id="secondary_button_text_mobile" name="secondary_button_text_mobile" value="{{ old('secondary_button_text_mobile', $banner->secondary_button_text) }}" placeholder="Se vazio, usa o texto desktop">
                    </div>
                    <div class="mb-3">
                        <label for="secondary_button_link_mobile" class="form-label">Link do botão secundário (mobile) — opcional</label>
                        <input type="text" class="form-control form-control-sm" id="secondary_button_link_mobile" name="secondary_button_link_mobile" value="{{ old('secondary_button_link_mobile', $banner->secondary_button_link) }}" placeholder="Se vazio, usa o link desktop">
                    </div>
                </div>
            </div>
            <hr />
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="cta_position" class="form-label">Posição vertical dos botões</label>
                    <select id="cta_position" name="cta_position" class="form-select form-select-sm">
                        <option value="top" {{ old('cta_position', $banner->cta_position ?? 'bottom') == 'top' ? 'selected' : '' }}>Topo</option>
                        <option value="center" {{ old('cta_position', $banner->cta_position ?? 'bottom') == 'center' ? 'selected' : '' }}>Centro</option>
                        <option value="bottom" {{ old('cta_position', $banner->cta_position ?? 'bottom') == 'bottom' ? 'selected' : '' }}>Baixo (padrão)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="cta_align" class="form-label">Alinhamento lateral</label>
                    <select id="cta_align" name="cta_align" class="form-select form-select-sm">
                        <option value="left" {{ old('cta_align', $banner->cta_align ?? 'center') == 'left' ? 'selected' : '' }}>Esquerda</option>
                        <option value="center" {{ old('cta_align', $banner->cta_align ?? 'center') == 'center' ? 'selected' : '' }}>Centro</option>
                        <option value="right" {{ old('cta_align', $banner->cta_align ?? 'center') == 'right' ? 'selected' : '' }}>Direita</option>
                    </select>
                    <small class="text-muted">Posicionamento lateral dos botões sobre o banner.</small>
                </div>
                <div class="col-md-4">
                    <label for="cta_size" class="form-label">Tamanho dos botões</label>
                    <select id="cta_size" name="cta_size" class="form-select form-select-sm">
                        <option value="small" {{ old('cta_size', $banner->cta_size ?? 'medium') == 'small' ? 'selected' : '' }}>Pequeno</option>
                        <option value="medium" {{ old('cta_size', $banner->cta_size ?? 'medium') == 'medium' ? 'selected' : '' }}>Médio (padrão)</option>
                        <option value="large" {{ old('cta_size', $banner->cta_size ?? 'medium') == 'large' ? 'selected' : '' }}>Grande</option>
                        <option value="xlarge" {{ old('cta_size', $banner->cta_size ?? 'medium') == 'xlarge' ? 'selected' : '' }}>Muito grande</option>
                        <option value="xxlarge" {{ old('cta_size', $banner->cta_size ?? 'medium') == 'xxlarge' ? 'selected' : '' }}>Extra grande</option>
                        <option value="xxxlarge" {{ old('cta_size', $banner->cta_size ?? 'medium') == 'xxxlarge' ? 'selected' : '' }}>Super grande</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Layout dos botões</label>
                    <div class="btn-group d-flex" role="group" aria-label="Layout dos botões">
                        <input type="radio" class="btn-check" name="cta_layout" id="cta_layout_horizontal" autocomplete="off" value="horizontal" {{ old('cta_layout', $banner->cta_layout ?? 'horizontal') == 'horizontal' ? 'checked' : '' }}>
                        <label class="btn btn-outline-secondary" for="cta_layout_horizontal">Horizontal</label>

                        <input type="radio" class="btn-check" name="cta_layout" id="cta_layout_vertical" autocomplete="off" value="vertical" {{ old('cta_layout', $banner->cta_layout ?? 'horizontal') == 'vertical' ? 'checked' : '' }}>
                        <label class="btn btn-outline-secondary" for="cta_layout_vertical">Vertical</label>
                    </div>
                    <small class="text-muted d-block mt-1">Escolha se os botões ficam lado a lado (horizontal) ou empilhados (vertical).</small>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancelar
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Salvar Alterações
        </button>
    </div>
</form>

<style>
    /* compact tab styles */
    .compact-tabs .nav-link { padding: .35rem .5rem; font-size: .9rem; border-radius: .4rem; color: rgba(0,0,0,.78); }
    .compact-tabs .nav-link .bi { font-size: .95rem; margin-right: .3rem; vertical-align: -0.125rem; }
    .compact-tabs .nav-link.active { background: var(--dept-color) !important; color: #fff !important; border-color: var(--dept-color) !important; box-shadow: 0 6px 14px rgba(0,0,0,.06); }
    .compact-tabs .nav-item { margin: .06rem; }
    @media (max-width: 576px) {
        .compact-tabs .nav-link { font-size: .82rem; padding: .25rem .35rem; }
        .compact-tabs .nav-link .bi { font-size: .9rem; }
    }
    /* compact form fields inside modal */
    #editBannerModal .form-label { margin-bottom: .2rem; font-size: .9rem; }
    #editBannerModal .mb-3 { margin-bottom: .45rem !important; }
    #editBannerModal .form-control, #editBannerModal .form-select { padding: .28rem .42rem; font-size: .9rem; }
    #editBannerModal textarea.form-control { padding: .32rem .42rem; }
    #editBannerModal .form-check { margin-bottom: .3rem; }
    #editBannerModal .card-body { padding: .45rem; }
    #editBannerModal .modal-body { padding: .6rem 0.8rem; }
    #editBannerModal .nav-tabs .nav-link { padding: .3rem .45rem; }
    #editBannerModal .img-thumbnail { max-height: 120px; }
    #editBannerModal .form-text { font-size: .82rem; }
    /* style modal header with dept color when set */
    #editBannerModal .modal-header.dept-colored { background: var(--dept-color) !important; }
    #editBannerModal .modal-header.dept-colored .modal-title, #editBannerModal .modal-header.dept-colored .btn-close { color: #fff !important; }
</style>

<script>
    // Quando o formulário é injetado via AJAX, ativar a aba que contém o primeiro erro (se houver)
    (function(){
        try {
            const firstInvalid = document.querySelector('.is-invalid, .alert-danger');
            if (firstInvalid) {
                const pane = firstInvalid.closest('.tab-pane');
                if (pane && pane.id) {
                    const trigger = document.querySelector(`#bannerEditTabs button[data-bs-target="#${pane.id}"]`);
                    if (trigger) {
                        const tab = new bootstrap.Tab(trigger);
                        tab.show();
                    }
                }
                try { firstInvalid.focus(); } catch(e){}
            }

            const overlayRange = document.getElementById('modal_overlay_opacity');
            const overlayVal = document.getElementById('modal_overlay_opacity_value');
            if (overlayRange && overlayVal) {
                overlayRange.addEventListener('input', function(){ overlayVal.textContent = this.value + '%'; });
            }

            // Apply department color to the tabs and update when department select changes
            try {
                const formEl = document.getElementById('banner-edit-form');
                const deptSelect = document.getElementById('department_id');
                function normalizeHex(input){
                    if (!input) return null;
                    let s = String(input).trim();
                    // accept values with or without '#', with 3 or 6 hex chars
                    if (s.startsWith('#')) s = s.slice(1);
                    if (!/^[0-9a-fA-F]{3}$/.test(s) && !/^[0-9a-fA-F]{6}$/.test(s)) return null;
                    return '#' + s;
                }

                function luminance(hex){
                    // compute relative luminance to decide text color
                    if (!hex) return 0;
                    hex = hex.replace('#','');
                    if (hex.length === 3) hex = hex.split('').map(c=>c+c).join('');
                    const r = parseInt(hex.substr(0,2),16)/255;
                    const g = parseInt(hex.substr(2,2),16)/255;
                    const b = parseInt(hex.substr(4,2),16)/255;
                    const a = [r,g,b].map(v => (v <= 0.03928) ? v/12.92 : Math.pow((v+0.055)/1.055,2.4));
                    return 0.2126*a[0] + 0.7152*a[1] + 0.0722*a[2];
                }

                function applyDeptColor(){
                    if (!formEl || !deptSelect) return;
                    const opt = deptSelect.selectedOptions && deptSelect.selectedOptions[0];
                    let raw = (opt && opt.dataset && opt.dataset.color) ? opt.dataset.color : null;
                    if (!raw) raw = getComputedStyle(formEl).getPropertyValue('--dept-color') || '#0d6efd';
                    const hex = normalizeHex(raw) || '#0d6efd';
                    formEl.style.setProperty('--dept-color', hex);
                    // color modal header
                    const modal = document.getElementById('editBannerModal');
                    if (modal){
                        const header = modal.querySelector('.modal-header');
                        if (header){
                            header.classList.add('dept-colored');
                            header.style.background = hex;
                            // pick text color based on luminance
                            const lum = luminance(hex);
                            const textColor = (lum > 0.55) ? '#111' : '#fff';
                            header.style.color = textColor;
                            const title = header.querySelector('.modal-title'); if (title) title.style.color = textColor;
                            const close = header.querySelector('.btn-close'); if (close) close.style.filter = (textColor === '#fff') ? 'invert(1) grayscale(1) brightness(2)' : 'none';
                        }
                    }
                }
                if (deptSelect){ deptSelect.addEventListener('change', applyDeptColor); applyDeptColor(); }
            } catch(err){ console.error('dept color apply error', err); }

            // AJAX submit: prevent full navigation. Submit via fetch and handle success/errors inline.
            const form = document.getElementById('banner-edit-form');
            if (form) {
                form.addEventListener('submit', async function(e){
                    e.preventDefault();
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalBtnHtml = submitBtn ? submitBtn.innerHTML : null;
                    // clear previous alerts
                    const prevAlert = form.querySelector('.ajax-errors'); if (prevAlert) prevAlert.remove();
                    try {
                        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...'; }
                        const fd = new FormData(form);
                        const resp = await fetch(form.action, {
                            method: 'POST',
                            body: fd,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json, text/html'
                            },
                            credentials: 'same-origin'
                        });

                        const contentType = (resp.headers.get('content-type') || '');

                        // Helper to show toast
                        function showToast(msg){
                            const toastEl = document.getElementById('toast-notification');
                            const toastMessage = document.getElementById('toast-message');
                            if (toastMessage) toastMessage.textContent = msg;
                            if (toastEl) { const t = new bootstrap.Toast(toastEl); t.show(); }
                            else { console.log(msg); }
                        }

                        // Helper to render validation errors object
                        function renderErrors(errors){
                            const container = document.createElement('div');
                            container.className = 'alert alert-danger ajax-errors';
                            let html = '<strong><i class="bi bi-exclamation-triangle"></i> Erros:</strong><ul class="mb-0 mt-2">';
                            for (const key in errors){
                                if (!errors.hasOwnProperty(key)) continue;
                                const arr = errors[key];
                                for (const m of arr){ html += `<li>${m}</li>`; }
                            }
                            html += '</ul>';
                            container.innerHTML = html;
                            form.insertBefore(container, form.firstChild);
                            // focus first invalid field
                            const firstKey = Object.keys(errors)[0];
                            if (firstKey){
                                const field = form.querySelector(`[name="${firstKey}"]`);
                                if (field) try{ field.focus(); }catch(e){}
                            }
                        }

                        if (contentType.includes('application/json')){
                            const json = await resp.json();
                            if (json && json.success) {
                                showToast(json.message || 'Banner atualizado com sucesso!');
                                // close modal
                                const modalEl = document.getElementById('editBannerModal');
                                if (modalEl){ const bs = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl); bs.hide(); }
                                // dispatch event for other parts of the UI to refresh
                                try { window.dispatchEvent(new CustomEvent('banner:updated', { detail: { id: {{ $banner->id ?? 'null' }} } } )); } catch(e){}
                            } else if (json && json.errors) {
                                renderErrors(json.errors || {});
                            } else {
                                // unknown json shape: show generic
                                showToast('Resposta inesperada do servidor.');
                            }
                        } else if (contentType.includes('text/html')){
                            const text = await resp.text();
                            // if server redirected to listing page with flash message, the HTML may contain success text
                            if (/banner atualizado/i.test(text) || /Banner atualizado/i.test(text)){
                                showToast('Banner atualizado com sucesso!');
                                const modalEl = document.getElementById('editBannerModal');
                                if (modalEl){ const bs = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl); bs.hide(); }
                                try { window.dispatchEvent(new CustomEvent('banner:updated', { detail: { id: {{ $banner->id ?? 'null' }} } } )); } catch(e){}
                            } else {
                                // replace modal body with returned HTML (likely contains validation errors)
                                const modalBody = document.getElementById('editBannerModalBody');
                                if (modalBody) modalBody.innerHTML = text;
                            }
                        } else if (resp.status === 422){
                            const json = await resp.json();
                            renderErrors(json.errors || {});
                        } else {
                            const text = await resp.text();
                            if (/banner atualizado/i.test(text)){
                                showToast('Banner atualizado com sucesso!');
                                const modalEl = document.getElementById('editBannerModal');
                                if (modalEl){ const bs = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl); bs.hide(); }
                                try { window.dispatchEvent(new CustomEvent('banner:updated', { detail: { id: {{ $banner->id ?? 'null' }} } } )); } catch(e){}
                            } else {
                                alert('Erro ao salvar banner. Veja o console para mais detalhes.');
                                console.error('Resposta inesperada ao salvar banner:', resp, text);
                            }
                        }
                    } catch(err){
                        console.error('Erro AJAX ao salvar banner', err);
                        alert('Erro ao salvar banner: ' + (err.message || err));
                    } finally {
                        if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = originalBtnHtml; }
                    }
                });
            }
        } catch (err) { console.error('banner form init error', err); }
    })();
</script>

