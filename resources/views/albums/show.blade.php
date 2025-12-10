@extends('layouts.app')

@section('title', $album->title)

@section('content')
<div class="container py-4 album-show-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('albums.index') }}" class="btn-back-album btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Voltar
        </a>
        @if(auth()->guard('admin')->check())
            <a href="{{ route('admin.albums.edit', $album->id) }}" 
               class="btn-edit-album btn btn-sm" 
               title="Editar álbum"
               style="background: var(--secondary-color, #ff6b35); border-color: var(--secondary-color, #ff6b35); color: #ffffff;">
                <i class="bi bi-pencil-square me-1"></i>Editar
            </a>
        @endif
    </div>

    <div class="album-header d-flex align-items-center gap-3 mb-4 p-3 rounded position-relative" style="background: linear-gradient(135deg, rgba(var(--secondary-color-rgb, 255, 107, 53), 0.05) 0%, #ffffff 100%); border: 2px solid rgba(var(--secondary-color-rgb, 255, 107, 53), 0.1);">
        <div class="album-cover-thumb ratio ratio-1x1" style="width:100px; min-width: 100px;">
            <img src="{{ $album->cover_url ?? asset('images/no-image.svg') }}" 
                 class="w-100 h-100 rounded shadow-sm" 
                 style="object-fit:cover; border: 3px solid rgba(var(--secondary-color-rgb, 255, 107, 53), 0.2);" 
                 loading="lazy">
        </div>
        <div class="flex-grow-1">
            <h1 class="album-title-display h3 m-0 mb-2 fw-bold" style="color: var(--text-dark);">{{ $album->title }}</h1>
            @if($album->description)
                <div class="album-description text-muted">{{ $album->description }}</div>
            @endif
            @if($album->images->count() > 0)
                <div class="album-image-count mt-2">
                    <small class="text-muted">
                        <i class="bi bi-images me-1"></i>{{ $album->images->count() }} {{ $album->images->count() === 1 ? 'imagem' : 'imagens' }}
                    </small>
                </div>
            @endif
        </div>
    </div>

    @if($album->images->count() === 0)
        <div class="alert alert-secondary text-center py-4">
            <i class="bi bi-images fs-3 d-block mb-2"></i>
            Nenhuma imagem neste álbum.
        </div>
    @else
        <div class="album-images-grid row g-3 g-md-4">
            @foreach($album->images as $image)
            <div class="col-6 col-md-4 col-lg-3 position-relative album-image-item">
                @if(auth()->guard('admin')->check())
                    <button type="button" class="btn btn-sm btn-danger position-absolute m-2 btn-delete-image" 
                            style="z-index:6; right:0; border-radius: 50%; width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;" 
                            data-image-id="{{ $image->id }}"
                            title="Remover imagem">
                        <i class="bi bi-x-lg"></i>
                    </button>
                @endif

                <div class="select-target position-relative album-image-wrapper" data-image-id="{{ $image->id }}" style="z-index:1">
                    <a href="{{ Storage::url($image->path) }}" 
                       target="_blank" 
                       rel="noopener" 
                       class="album-image-link d-block border rounded overflow-hidden shadow-sm"
                       data-lightbox="album"
                       data-title="{{ $image->alt ?? $album->title }}">
                        <div class="album-image-container position-relative" style="aspect-ratio:1/1; overflow: hidden;">
                            <img src="{{ Storage::url($image->path) }}" 
                                 class="w-100 h-100" 
                                 style="object-fit:cover; transition: transform 0.3s ease;" 
                                 alt="{{ $image->alt ?? $album->title }}" 
                                 loading="lazy">
                            <div class="album-image-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-zoom-in text-white fs-2 opacity-0" style="transition: opacity 0.3s ease;"></i>
                            </div>
                        </div>
                    </a>

                    @if(auth()->guard('admin')->check())
                        <button type="button" 
                                class="select-toggle btn btn-sm btn-light position-absolute" 
                                style="top:8px; left:8px; z-index:5; width:36px; height:36px; padding:0; border-radius:50%; display:flex; align-items:center; justify-content:center; border:2px solid rgba(255,255,255,0.9); background: rgba(255,255,255,0.95); box-shadow: 0 2px 8px rgba(0,0,0,0.15);"
                                title="Selecionar">
                            <i class="bi bi-square" aria-hidden="true"></i>
                        </button>
                        <div class="selected-check position-absolute" style="top:8px; right:8px; z-index:5; display:none;">
                            <span class="badge bg-success rounded-circle p-2" style="box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                                <i class="bi bi-check-lg"></i>
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
    @if(auth()->guard('admin')->check())
        <!-- Selection toolbar for admins -->
        <div id="album-selection-toolbar" class="position-fixed shadow-lg d-none" style="right:16px; bottom:90px; z-index:2000; background:#fff; border-radius:12px; padding:14px 18px; min-width:250px; border: 2px solid var(--secondary-color, #ff6b35);">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div>
                    <strong id="selected-count" style="color: var(--secondary-color, #ff6b35); font-size: 1.25rem;">0</strong>
                    <div class="small text-muted">imagens selecionadas</div>
                </div>
                <div>
                    <button id="create-product-from-selected" class="btn btn-primary btn-sm" style="background: var(--secondary-color); border-color: var(--secondary-color);">
                        <i class="bi bi-plus-lg me-1"></i>Criar produto
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(auth()->guard('admin')->check())
        <hr class="my-4" style="border-color: rgba(var(--secondary-color-rgb, 255, 107, 53), 0.2);">
        <div class="card p-4 shadow-sm border-0 admin-upload-section" style="background: linear-gradient(135deg, rgba(var(--secondary-color-rgb, 255, 107, 53), 0.05) 0%, #ffffff 100%); border: 2px solid rgba(var(--secondary-color-rgb, 255, 107, 53), 0.1) !important; border-radius: 12px;">
            <h5 class="mb-3 fw-bold">
                <i class="bi bi-cloud-upload me-2" style="color: var(--secondary-color, #ff6b35);"></i>Adicionar imagens (admin)
            </h5>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <form action="{{ route('albums.images.store', $album->slug) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input type="file" name="images[]" multiple accept="image/*" class="form-control" />
                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle me-1"></i>Selecione uma ou mais imagens para adicionar ao álbum
                    </small>
                </div>
                <div>
                    <button class="btn btn-primary btn-sm" style="background: var(--secondary-color); border-color: var(--secondary-color);">
                        <i class="bi bi-upload me-1"></i>Enviar imagens
                    </button>
                </div>
            </form>
        </div>
    @endif
    <script>
    (function(){
        const form = document.querySelector('form[action$="/images"]');
        if (!form) return;

        const fileInput = form.querySelector('input[type="file"][name="images[]"]');
        const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('button');

        // Preview container (insert before form)
        const previewWrap = document.createElement('div');
        previewWrap.className = 'mb-3 d-flex flex-wrap gap-2';
        form.parentNode.insertBefore(previewWrap, form);

        fileInput.addEventListener('change', () => {
            previewWrap.innerHTML = '';
            Array.from(fileInput.files).forEach(file => {
                const reader = new FileReader();
                const box = document.createElement('div');
                box.style.width = '80px';
                box.style.height = '80px';
                box.className = 'border rounded overflow-hidden d-inline-block';
                box.style.objectFit = 'cover';
                reader.onload = e => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    box.appendChild(img);
                };
                reader.readAsDataURL(file);
                previewWrap.appendChild(box);
            });
        });

        form.addEventListener('submit', function(ev){
            ev.preventDefault();
            if (!fileInput.files || fileInput.files.length === 0) return alert('Selecione ao menos uma imagem.');

            submitBtn.setAttribute('disabled', 'disabled');

            // create UI for overall progress
            const progressContainer = document.createElement('div');
            progressContainer.className = 'mt-2';
            form.appendChild(progressContainer);

            const fd = new FormData(form);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener('progress', function(e){
                let percent = 0;
                if (e.lengthComputable) percent = Math.round((e.loaded / e.total) * 100);
                progressContainer.innerHTML = `
                    <div class="progress" style="height:8px">
                        <div class="progress-bar" role="progressbar" style="width: ${percent}%" aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="small text-muted mt-1">Enviando... ${percent}%</div>
                `;
            });

            xhr.onreadystatechange = function(){
                if (xhr.readyState !== 4) return;
                submitBtn.removeAttribute('disabled');
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (res.success && Array.isArray(res.images)) {
                            // remove 'Nenhuma imagem' alert if present
                            const noneAlert = document.querySelector('.alert.alert-secondary');
                            if (noneAlert) noneAlert.remove();

                            // find gallery row or create it
                            let gallery = document.querySelector('.row.g-3');
                            if (!gallery) {
                                gallery = document.createElement('div');
                                gallery.className = 'row g-3';
                                // insert before the upload card
                                form.parentNode.parentNode.insertBefore(gallery, form.parentNode);
                            }

                            res.images.forEach(img => {
                                const col = document.createElement('div');
                                col.className = 'col-6 col-md-4 col-lg-3';
                                col.innerHTML = `
                                    <a href="${img.url}" target="_blank" rel="noopener" class="d-block border rounded overflow-hidden">
                                        <img src="${img.url}" class="w-100" style="aspect-ratio:1/1; object-fit:cover;" alt="${img.alt}" loading="lazy">
                                    </a>
                                `;
                                gallery.appendChild(col);
                            });

                            // clear file input and previews
                            fileInput.value = '';
                            previewWrap.innerHTML = '';
                            progressContainer.innerHTML = '<div class="small text-success">Upload finalizado.</div>';
                        } else {
                            progressContainer.innerHTML = '<div class="text-danger small">Erro no upload.</div>';
                        }
                    } catch (err) {
                        progressContainer.innerHTML = '<div class="text-danger small">Resposta inválida do servidor.</div>';
                    }
                } else if (xhr.status === 422) {
                    // validation error
                    let json = {};
                    try { json = JSON.parse(xhr.responseText); } catch(e){}
                    const msgs = [];
                    if (json.errors) {
                        Object.values(json.errors).forEach(v => { msgs.push(...v); });
                    }
                    progressContainer.innerHTML = `<div class="text-danger small">${msgs.join('<br>') || 'Erro de validação.'}</div>`;
                } else {
                    progressContainer.innerHTML = '<div class="text-danger small">Erro no envio. Código: ' + xhr.status + '</div>';
                }
            };

            xhr.send(fd);
        });
        // Delete image handlers (delegated)
        // Selection handling (admin): toggle images and update toolbar
        const selectedIds = new Set();
        const toolbar = document.getElementById('album-selection-toolbar');
        const selectedCountEl = document.getElementById('selected-count');
        const createBtn = document.getElementById('create-product-from-selected');

        function updateToolbar() {
            const count = selectedIds.size;
            selectedCountEl.textContent = count;
            if (count > 0) {
                toolbar.classList.remove('d-none');
            } else {
                toolbar.classList.add('d-none');
            }
        }

        // Delegate clicks on the select-toggle buttons
        document.addEventListener('click', function(e){
            const toggle = e.target.closest('.select-toggle');
            if (toggle) {
                e.preventDefault();
                e.stopPropagation();
                const target = toggle.closest('.select-target');
                if (!target) return;
                const id = target.getAttribute('data-image-id');
                if (!id) return;
                if (selectedIds.has(id)) {
                    selectedIds.delete(id);
                    toggle.querySelector('i').className = 'bi bi-square';
                    const check = target.querySelector('.selected-check'); if (check) check.style.display = 'none';
                } else {
                    selectedIds.add(id);
                    toggle.querySelector('i').className = 'bi bi-check-square-fill';
                    const check = target.querySelector('.selected-check'); if (check) check.style.display = 'block';
                }
                updateToolbar();
                return;
            }

            const btn = e.target.closest('.btn-delete-image');
            if (!btn) return;
            if (!confirm('Remover esta imagem?')) return;
            const imageId = btn.getAttribute('data-image-id');
            const albumSlug = '{{ $album->slug }}';
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const token = tokenMeta ? tokenMeta.getAttribute('content') : '';

            btn.setAttribute('disabled', 'disabled');

            fetch(`/albuns/${albumSlug}/images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(json => {
                if (json.success) {
                    // remove the parent column
                    const col = btn.closest('.col-6, .col-md-4, .col-lg-3') || btn.closest('.position-relative');
                    if (col) col.remove();
                    // ensure removed id is cleared from selection
                    if (selectedIds.has(String(imageId))) {
                        selectedIds.delete(String(imageId));
                        updateToolbar();
                    }
                } else {
                    alert('Erro ao remover.');
                    btn.removeAttribute('disabled');
                }
            }).catch(err => {
                alert('Erro na requisição.');
                btn.removeAttribute('disabled');
            });
        });

        // Handle create product action
        if (createBtn) {
            createBtn.addEventListener('click', function(){
                if (selectedIds.size === 0) return alert('Selecione ao menos uma imagem.');
                const ids = Array.from(selectedIds).join(',');
                // Open admin product create route with image_ids as query string
                const base = '{{ route("admin.products.create") }}';
                const url = base + (base.indexOf('?') === -1 ? '?' : '&') + 'image_ids=' + encodeURIComponent(ids);
                window.open(url, '_blank');
            });
        }
    })();
    </script>
</div>

@push('styles')
<style>
    /* Página de Visualização de Álbum - Design Moderno */
    .album-show-page {
        max-width: var(--site-container-max-width, 1320px);
        margin: 0 auto;
    }

    .btn-back-album {
        transition: all 0.3s ease;
        border-color: var(--secondary-color, #ff6b35);
        color: var(--secondary-color, #ff6b35);
    }

    .btn-back-album:hover {
        background: var(--secondary-color, #ff6b35);
        color: #ffffff;
        transform: translateX(-3px);
    }

    .btn-edit-album {
        transition: all 0.3s ease;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.3);
        position: relative;
        overflow: hidden;
    }

    .btn-edit-album::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }

    .btn-edit-album:hover::before {
        left: 100%;
    }

    .btn-edit-album:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 4px 16px rgba(var(--secondary-color-rgb, 255, 107, 53), 0.4);
        color: #ffffff;
    }

    .btn-edit-album:active {
        transform: translateY(0) scale(1);
    }

    .album-header {
        transition: all 0.3s ease;
    }

    .album-header:hover {
        border-color: rgba(var(--secondary-color-rgb, 255, 107, 53), 0.3) !important;
    }

    .album-cover-thumb {
        animation: fadeIn 0.5s ease;
    }

    .album-title-display {
        animation: slideInLeft 0.5s ease;
    }

    .album-description {
        animation: slideInLeft 0.6s ease;
    }

    /* Grid de imagens */
    .album-images-grid {
        animation: fadeIn 0.7s ease;
    }

    .album-image-item {
        animation: fadeInUp 0.5s ease;
        animation-fill-mode: both;
    }

    .album-image-item:nth-child(1) { animation-delay: 0.1s; }
    .album-image-item:nth-child(2) { animation-delay: 0.2s; }
    .album-image-item:nth-child(3) { animation-delay: 0.3s; }
    .album-image-item:nth-child(4) { animation-delay: 0.4s; }
    .album-image-item:nth-child(5) { animation-delay: 0.1s; }
    .album-image-item:nth-child(6) { animation-delay: 0.2s; }
    .album-image-item:nth-child(7) { animation-delay: 0.3s; }
    .album-image-item:nth-child(8) { animation-delay: 0.4s; }

    .album-image-wrapper {
        transition: transform 0.3s ease;
    }

    .album-image-link {
        transition: all 0.3s ease;
        border-radius: 12px !important;
        overflow: hidden;
    }

    .album-image-link:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2) !important;
        border-color: var(--secondary-color, #ff6b35) !important;
    }

    .album-image-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .album-image-link:hover .album-image-container img {
        transform: scale(1.1);
    }

    .album-image-overlay {
        background: rgba(0, 0, 0, 0.5);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .album-image-link:hover .album-image-overlay {
        opacity: 1;
    }

    .album-image-link:hover .album-image-overlay i {
        opacity: 1;
    }

    .select-toggle {
        transition: all 0.3s ease;
    }

    .select-toggle:hover {
        background: rgba(255, 255, 255, 1) !important;
        transform: scale(1.1);
    }

    .select-target.selected .select-toggle {
        background: var(--secondary-color, #ff6b35) !important;
        border-color: var(--secondary-color, #ff6b35) !important;
        color: white;
    }

    .btn-delete-image {
        transition: all 0.3s ease;
    }

    .btn-delete-image:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    .admin-upload-section {
        animation: slideInUp 0.5s ease;
    }

    /* Animações */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .album-header {
            flex-direction: column;
            text-align: center;
        }

        .album-cover-thumb {
            width: 80px !important;
            min-width: 80px !important;
            margin: 0 auto;
        }

        .album-title-display {
            font-size: 1.5rem !important;
        }

        #album-selection-toolbar {
            right: 8px !important;
            bottom: 80px !important;
            min-width: 200px !important;
            padding: 12px 14px !important;
        }
    }
</style>
@endpush
@endsection
