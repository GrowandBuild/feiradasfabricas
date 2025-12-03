@extends('layouts.app')

@section('title', $album->title)

@section('content')
<div class="container py-4">
    <a href="{{ route('albums.index') }}" class="btn btn-outline-secondary btn-sm mb-3">Voltar</a>

    <div class="d-flex align-items-center gap-3 mb-3">
        <div class="ratio ratio-1x1" style="width:80px">
            <img src="{{ $album->cover_url ?? asset('images/no-image.svg') }}" class="w-100 h-100 rounded" style="object-fit:cover;" loading="lazy">
        </div>
        <div>
            <h1 class="h4 m-0">{{ $album->title }}</h1>
            @if($album->description)
                <div class="text-muted">{{ $album->description }}</div>
            @endif
        </div>
    </div>

    @if($album->images->count() === 0)
        <div class="alert alert-secondary">Nenhuma imagem neste álbum.</div>
    @else
        <div class="row g-3">
            @foreach($album->images as $image)
            <div class="col-6 col-md-4 col-lg-3 position-relative">
                @if(auth()->guard('admin')->check())
                    <button type="button" class="btn btn-sm btn-danger position-absolute m-2 btn-delete-image" style="z-index:6; right:0;" data-image-id="{{ $image->id }}">Remover</button>
                @endif

                <div class="select-target position-relative" data-image-id="{{ $image->id }}" style="z-index:1">
                    <a href="{{ Storage::url($image->path) }}" target="_blank" rel="noopener" class="d-block border rounded overflow-hidden">
                        <img src="{{ Storage::url($image->path) }}" class="w-100" style="aspect-ratio:1/1; object-fit:cover;" alt="{{ $image->alt ?? $album->title }}" loading="lazy">
                    </a>

                    @if(auth()->guard('admin')->check())
                        <button type="button" class="select-toggle btn btn-sm btn-light position-absolute" style="top:8px; left:8px; z-index:5; width:34px; height:34px; padding:0; border-radius:50%; display:flex; align-items:center; justify-content:center; border:1px solid rgba(0,0,0,0.08);">
                            <i class="bi bi-square" aria-hidden="true"></i>
                        </button>
                        <div class="selected-check position-absolute" style="top:8px; right:8px; z-index:5; display:none;">
                            <span class="badge bg-success"><i class="bi bi-check-lg"></i></span>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @endif
    @if(auth()->guard('admin')->check())
        <!-- Selection toolbar for admins -->
        <div id="album-selection-toolbar" class="position-fixed shadow-lg d-none" style="right:16px; bottom:90px; z-index:2000; background:#fff; border-radius:12px; padding:10px 14px; min-width:220px;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <strong id="selected-count">0</strong>
                    <div class="small text-muted">imagens selecionadas</div>
                </div>
                <div>
                    <button id="create-product-from-selected" class="btn btn-primary btn-sm">Criar produto</button>
                </div>
            </div>
        </div>
    @endif

    @if(auth()->guard('admin')->check())
        <hr class="my-4">
        <div class="card p-3">
            <h5 class="mb-3">Adicionar imagens (admin)</h5>
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
                </div>
                <div>
                    <button class="btn btn-primary btn-sm">Enviar imagens</button>
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
@endsection
