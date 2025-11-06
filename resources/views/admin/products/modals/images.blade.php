<!-- Modal de Edição de Imagens -->
<div class="modal fade" id="imagesModal" tabindex="-1" aria-labelledby="imagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagesModalLabel">
                    <i class="bi bi-images me-2"></i>Gerenciar Imagens do Produto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="imagesForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="imagesProductId" name="product_id">
                    
                    <!-- Imagem de Destaque -->
                    <div class="mb-4">
                        <label for="featuredImageInput" class="form-label">
                            <i class="bi bi-star-fill text-warning me-1"></i>Imagem de Destaque
                        </label>
                        
                        <!-- Imagem Atual de Destaque -->
                        <div id="currentFeaturedImageContainer" style="display: none;">
                            <div class="mb-2">
                                <img id="currentFeaturedImagePreview" 
                                     src="" 
                                     alt="Imagem de destaque atual" 
                                     class="img-thumbnail" 
                                     style="max-width: 300px;"
                                     onerror="this.style.display='none';">
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-check-circle text-success"></i> <span id="currentFeaturedImageName">Imagem atual</span>
                                </div>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="remove_featured_image" 
                                       name="remove_featured_image" 
                                       value="1">
                                <label class="form-check-label text-danger" for="remove_featured_image">
                                    <i class="bi bi-trash"></i> Remover imagem de destaque
                                </label>
                            </div>
                        </div>
                        
                        <!-- Alerta quando não há imagem -->
                        <div id="noFeaturedImageAlert" class="alert alert-warning mb-2" style="display: none;">
                            <i class="bi bi-exclamation-triangle"></i> Nenhuma imagem de destaque cadastrada
                        </div>
                        
                        <!-- Input de Nova Imagem de Destaque -->
                        <input type="file" 
                               class="form-control" 
                               id="featuredImageInput" 
                               name="featured_image" 
                               accept="image/*">
                        <small class="text-muted">Deixe em branco para manter a imagem atual. Formatos: JPG, PNG, GIF, WEBP, AVIF. Máximo: 10MB</small>
                        
                        <!-- Preview da Nova Imagem de Destaque -->
                        <div id="newFeaturedImagePreview" style="display: none;" class="mt-2"></div>
                    </div>
                    
                    <!-- Imagens Adicionais -->
                    <div class="mb-4">
                        <label for="additionalImagesInput" class="form-label">
                            <i class="bi bi-images me-1"></i>Imagens Adicionais
                        </label>
                        
                        <!-- Imagens Atuais Adicionais -->
                        <div id="currentAdditionalImagesContainer" style="display: none;">
                            <label class="form-label small mb-2">Imagens Atuais:</label>
                            <div class="row mb-3" id="currentAdditionalImagesList">
                                <!-- Imagens atuais serão carregadas aqui -->
                            </div>
                        </div>
                        
                        <!-- Alerta quando não há imagens adicionais -->
                        <div id="noAdditionalImagesAlert" class="alert alert-info mb-2" style="display: none;">
                            <i class="bi bi-info-circle"></i> Nenhuma imagem adicional cadastrada
                        </div>
                        
                        <!-- Input de Novas Imagens Adicionais -->
                        <input type="file" 
                               class="form-control" 
                               id="additionalImagesInput" 
                               name="additional_images[]" 
                               multiple 
                               accept="image/*">
                        <small class="text-muted">Você pode selecionar múltiplas imagens. Formatos aceitos: JPG, PNG, GIF, WEBP, AVIF (máx. 10MB cada)</small>
                        
                        <!-- Preview das Novas Imagens Adicionais -->
                        <div id="newAdditionalImagesPreview" style="display: none;" class="mt-3">
                            <label class="form-label small mb-2">Novas Imagens:</label>
                            <div class="row" id="newAdditionalImagesList">
                                <!-- Preview de novas imagens será exibido aqui -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="saveImages()">
                    <i class="bi bi-save me-1"></i>Salvar Imagens
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imagesModal = document.getElementById('imagesModal');
    if (imagesModal) {
        imagesModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            
            document.getElementById('imagesModalLabel').innerHTML = 
                '<i class="bi bi-images me-2"></i>Gerenciar Imagens - ' + productName;
            document.getElementById('imagesProductId').value = productId;
            
            loadProductImages(productId);
        });
        
        // Limpar formulário ao fechar
        imagesModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('imagesForm').reset();
            document.getElementById('currentFeaturedImageContainer').style.display = 'none';
            document.getElementById('noFeaturedImageAlert').style.display = 'none';
            document.getElementById('newFeaturedImagePreview').style.display = 'none';
            document.getElementById('currentAdditionalImagesContainer').style.display = 'none';
            document.getElementById('noAdditionalImagesAlert').style.display = 'none';
            document.getElementById('newAdditionalImagesPreview').style.display = 'none';
            document.getElementById('currentAdditionalImagesList').innerHTML = '';
            document.getElementById('newAdditionalImagesList').innerHTML = '';
        });
    }
    
    // Preview da imagem de destaque - seguindo padrão do banner
    const featuredImageInput = document.getElementById('featuredImageInput');
    if (featuredImageInput) {
        featuredImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Criar ou atualizar preview
                    let preview = document.getElementById('new-featured-image-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'new-featured-image-preview';
                        preview.className = 'img-thumbnail mt-2';
                        preview.style.maxWidth = '300px';
                        document.getElementById('newFeaturedImagePreview').appendChild(preview);
                    }
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    document.getElementById('newFeaturedImagePreview').style.display = 'block';
                    
                    // Mostrar mensagem de sucesso
                    let successMsg = document.getElementById('new-featured-image-success');
                    if (!successMsg) {
                        successMsg = document.createElement('div');
                        successMsg.id = 'new-featured-image-success';
                        successMsg.className = 'alert alert-success mt-2';
                        document.getElementById('newFeaturedImagePreview').appendChild(successMsg);
                    }
                    successMsg.innerHTML = '<i class="bi bi-check-circle"></i> Nova imagem de destaque selecionada: ' + file.name;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Preview das imagens adicionais
    const additionalImagesInput = document.getElementById('additionalImagesInput');
    if (additionalImagesInput) {
        additionalImagesInput.addEventListener('change', function(e) {
            const files = e.target.files;
            const previewContainer = document.getElementById('newAdditionalImagesPreview');
            const previewList = document.getElementById('newAdditionalImagesList');
            
            previewList.innerHTML = '';
            
            if (files && files.length > 0) {
                previewContainer.style.display = 'block';
                
                Array.from(files).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const col = document.createElement('div');
                            col.className = 'col-md-3 mb-2';
                            col.innerHTML = `
                                <div class="position-relative">
                                    <img src="${e.target.result}" 
                                         class="img-thumbnail" 
                                         style="width: 100%; height: 100px; object-fit: cover;">
                                    <span class="badge bg-success position-absolute top-0 start-0">Nova</span>
                                </div>
                            `;
                            previewList.appendChild(col);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                previewContainer.style.display = 'none';
            }
        });
    }
});

function loadProductImages(productId) {
    fetch(`/admin/products/${productId}/images`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderExistingImages(data.images, data.featured_image);
            } else {
                alert('Erro ao carregar imagens: ' + (data.message || 'Erro desconhecido'));
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao carregar imagens');
        });
}

function renderExistingImages(images, featuredImage) {
    // Renderizar imagem de destaque
    const featuredContainer = document.getElementById('currentFeaturedImageContainer');
    const noFeaturedAlert = document.getElementById('noFeaturedImageAlert');
    
    if (featuredImage) {
        const previewImg = document.getElementById('currentFeaturedImagePreview');
        const imageName = document.getElementById('currentFeaturedImageName');
        
        previewImg.src = featuredImage + '?v=' + Date.now();
        previewImg.style.display = 'block';
        
        // Extrair nome do arquivo da URL
        const fileName = featuredImage.split('/').pop().split('?')[0];
        imageName.textContent = 'Imagem atual: ' + fileName;
        
        featuredContainer.style.display = 'block';
        noFeaturedAlert.style.display = 'none';
    } else {
        featuredContainer.style.display = 'none';
        noFeaturedAlert.style.display = 'block';
    }
    
    // Renderizar imagens adicionais
    const additionalContainer = document.getElementById('currentAdditionalImagesContainer');
    const noAdditionalAlert = document.getElementById('noAdditionalImagesAlert');
    const additionalList = document.getElementById('currentAdditionalImagesList');
    additionalList.innerHTML = '';
    
    // Filtrar imagens adicionais (todas exceto a de destaque)
    const additionalImages = images.filter(img => img !== featuredImage);
    
    if (additionalImages.length > 0) {
        additionalContainer.style.display = 'block';
        noAdditionalAlert.style.display = 'none';
        
        additionalImages.forEach((image, index) => {
            const fileName = image.split('/').pop().split('?')[0];
            const col = document.createElement('div');
            col.className = 'col-md-3 mb-2';
            col.innerHTML = `
                <div class="position-relative">
                    <img src="${image}?v=${Date.now()}" 
                         class="img-thumbnail" 
                         style="width: 100%; height: 100px; object-fit: cover;"
                         onerror="this.style.display='none';">
                    <div class="small text-muted mt-1">
                        <i class="bi bi-check-circle text-success"></i> ${fileName}
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input remove-additional-image" 
                               type="checkbox" 
                               data-image-path="${image}"
                               id="remove_additional_${index}">
                        <label class="form-check-label text-danger small" for="remove_additional_${index}">
                            <i class="bi bi-trash"></i> Remover
                        </label>
                    </div>
                    <input type="hidden" name="existing_additional_images[]" value="${image}" class="existing-additional-image-input">
                </div>
            `;
            additionalList.appendChild(col);
        });
    } else {
        additionalContainer.style.display = 'none';
        noAdditionalAlert.style.display = 'block';
    }
}

function saveImages() {
    const productId = document.getElementById('imagesProductId').value;
    if (!productId) {
        alert('❌ Erro: ID do produto não encontrado');
        return;
    }
    
    // Verificar tamanho dos arquivos antes de enviar
    const featuredInput = document.getElementById('featuredImageInput');
    const additionalInput = document.getElementById('additionalImagesInput');
    const maxSize = 10 * 1024 * 1024; // 10MB
    
    if (featuredInput && featuredInput.files.length > 0) {
        const file = featuredInput.files[0];
        if (file.size > maxSize) {
            alert('❌ A imagem de destaque é muito grande. Tamanho máximo: 10MB');
            return;
        }
    }
    
    if (additionalInput && additionalInput.files.length > 0) {
        for (let i = 0; i < additionalInput.files.length; i++) {
            const file = additionalInput.files[i];
            if (file.size > maxSize) {
                alert(`❌ A imagem adicional "${file.name}" é muito grande. Tamanho máximo: 10MB`);
                return;
            }
        }
    }
    
    const form = document.getElementById('imagesForm');
    const formData = new FormData(form);
    
    // Adicionar imagem de destaque existente se não foi marcada para remover
    const removeFeatured = document.getElementById('remove_featured_image');
    if (!removeFeatured || !removeFeatured.checked) {
        const currentFeatured = document.getElementById('currentFeaturedImagePreview');
        if (currentFeatured && currentFeatured.src) {
            // Extrair caminho da imagem de destaque
            const featuredPath = extractImagePath(currentFeatured.src);
            if (featuredPath) {
                formData.append('existing_featured_image', featuredPath);
            }
        }
    }
    
    // Adicionar imagens adicionais existentes que não foram marcadas para remover
    const removeCheckboxes = document.querySelectorAll('.remove-additional-image:not(:checked)');
    removeCheckboxes.forEach(checkbox => {
        const imagePath = checkbox.getAttribute('data-image-path');
        if (imagePath) {
            const extractedPath = extractImagePath(imagePath);
            if (extractedPath) {
                formData.append('existing_additional_images[]', extractedPath);
            }
        }
    });
    
    // Mostrar loading - encontrar o botão corretamente
    const saveBtn = document.querySelector('#imagesModal .btn-primary');
    if (!saveBtn) {
        alert('❌ Erro: Botão de salvar não encontrado');
        return;
    }
    
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
    
    // Adicionar timeout para evitar requisições muito longas (30 segundos)
    const timeoutId = setTimeout(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
        alert('⏱️ A requisição está demorando muito (mais de 30 segundos). Verifique sua conexão e tente novamente.');
    }, 30000); // 30 segundos
    
    // Verificar se o token CSRF existe
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken || !csrfToken.content) {
        alert('❌ Erro: Token CSRF não encontrado. Recarregue a página e tente novamente.');
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
        return;
    }
    
    // Adicionar log para debug
    console.log('Enviando requisição para salvar imagens...', {
        productId: productId,
        formDataKeys: Array.from(formData.keys())
    });
    
    fetch(`/admin/products/${productId}/update-images`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        clearTimeout(timeoutId);
        console.log('Resposta recebida:', response.status, response.statusText);
        
        // Verificar se a resposta é JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('Resposta não é JSON:', text);
                throw new Error('Resposta do servidor não é JSON: ' + text.substring(0, 100));
            });
        }
        
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Dados recebidos:', data);
        
        if (data.success) {
            saveBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Salvo!';
            saveBtn.classList.remove('btn-primary');
            saveBtn.classList.add('btn-success');
            
            // Fechar modal após 1 segundo
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('imagesModal'));
                if (modal) {
                    modal.hide();
                }
                // Recarregar página para atualizar miniaturas
                location.reload();
            }, 1000);
        } else {
            alert('❌ Erro ao salvar imagens: ' + (data.message || 'Erro desconhecido'));
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        console.error('Erro completo:', error);
        console.error('Stack:', error.stack);
        
        let errorMessage = '❌ Erro ao salvar imagens. ';
        if (error.message) {
            errorMessage += error.message;
        } else {
            errorMessage += 'Verifique sua conexão e tente novamente.';
        }
        
        alert(errorMessage);
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    });
}

function extractImagePath(imageUrl) {
    if (!imageUrl) return null;
    
    // Se é URL absoluta, extrair o caminho
    if (imageUrl.startsWith('http')) {
        const parsed = new URL(imageUrl);
        let path = parsed.pathname;
        
        // Remover /storage/ se presente
        if (path.startsWith('/storage/')) {
            path = path.substring(9); // Remove '/storage/'
        } else if (path.startsWith('storage/')) {
            path = path.substring(8); // Remove 'storage/'
        }
        
        return path;
    }
    
    return imageUrl;
}
</script>
@endpush
