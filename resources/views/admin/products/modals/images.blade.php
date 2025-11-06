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
                        <label class="form-label fw-semibold">
                            <i class="bi bi-star-fill text-warning me-1"></i>Imagem de Destaque
                        </label>
                        <div class="mb-2">
                            <div id="featuredImagePreview" class="mb-2">
                                <img id="featuredImagePreviewImg" src="" alt="Preview" 
                                     class="img-thumbnail" 
                                     style="max-width: 200px; max-height: 200px; display: none;">
                            </div>
                            <input type="file" 
                                   class="form-control" 
                                   id="featuredImageInput" 
                                   name="featured_image" 
                                   accept="image/*">
                            <small class="text-muted">Formatos aceitos: JPG, PNG, GIF, WEBP, AVIF (máx. 10MB)</small>
                        </div>
                    </div>
                    
                    <!-- Imagens Adicionais -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-images me-1"></i>Imagens Adicionais
                        </label>
                        <div class="mb-2">
                            <input type="file" 
                                   class="form-control" 
                                   id="additionalImagesInput" 
                                   name="additional_images[]" 
                                   multiple 
                                   accept="image/*">
                            <small class="text-muted">Você pode selecionar múltiplas imagens. Formatos aceitos: JPG, PNG, GIF, WEBP, AVIF (máx. 10MB cada)</small>
                        </div>
                        
                        <!-- Container de Imagens Existentes -->
                        <div id="existingImagesContainer" class="mt-3">
                            <label class="form-label small">Imagens Atuais:</label>
                            <div class="row" id="existingImagesList">
                                <!-- Imagens existentes serão carregadas aqui -->
                            </div>
                        </div>
                        
                        <!-- Preview de Novas Imagens -->
                        <div id="newImagesPreview" class="mt-3" style="display: none;">
                            <label class="form-label small">Novas Imagens:</label>
                            <div class="row" id="newImagesList">
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
            document.getElementById('featuredImagePreviewImg').style.display = 'none';
            document.getElementById('newImagesPreview').style.display = 'none';
            document.getElementById('existingImagesList').innerHTML = '';
            document.getElementById('newImagesList').innerHTML = '';
        });
    }
    
    // Preview da imagem de destaque
    const featuredImageInput = document.getElementById('featuredImageInput');
    if (featuredImageInput) {
        featuredImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImg = document.getElementById('featuredImagePreviewImg');
                    previewImg.src = e.target.result;
                    previewImg.style.display = 'block';
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
            const previewContainer = document.getElementById('newImagesPreview');
            const previewList = document.getElementById('newImagesList');
            
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
    const container = document.getElementById('existingImagesList');
    container.innerHTML = '';
    
    if (!images || images.length === 0) {
        container.innerHTML = '<div class="col-12"><p class="text-muted text-center">Nenhuma imagem cadastrada</p></div>';
        return;
    }
    
    images.forEach((image, index) => {
        const isFeatured = featuredImage && image === featuredImage;
        const col = document.createElement('div');
        col.className = 'col-md-3 mb-2';
        col.innerHTML = `
            <div class="position-relative">
                <img src="${image}" 
                     class="img-thumbnail ${isFeatured ? 'border-warning border-2' : ''}" 
                     style="width: 100%; height: 100px; object-fit: cover;">
                ${isFeatured ? '<span class="badge bg-warning position-absolute top-0 start-0">Destaque</span>' : ''}
                <button type="button" 
                        class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                        onclick="removeExistingImage('${image}', ${index})">
                    <i class="bi bi-x"></i>
                </button>
                <input type="hidden" name="existing_images[]" value="${image}" class="existing-image-input">
            </div>
        `;
        container.appendChild(col);
    });
}

function removeExistingImage(imagePath, index) {
    if (confirm('Tem certeza que deseja remover esta imagem?')) {
        const container = document.getElementById('existingImagesList');
        const imageItem = container.children[index];
        if (imageItem) {
            imageItem.remove();
            
            // Se não há mais imagens, mostrar mensagem
            if (container.children.length === 0) {
                container.innerHTML = '<div class="col-12"><p class="text-muted text-center">Nenhuma imagem cadastrada</p></div>';
            }
        }
    }
}

function saveImages() {
    const productId = document.getElementById('imagesProductId').value;
    const form = document.getElementById('imagesForm');
    const formData = new FormData(form);
    
    // Adicionar imagens existentes que não foram removidas
    const existingInputs = document.querySelectorAll('.existing-image-input');
    existingInputs.forEach(input => {
        formData.append('existing_images[]', input.value);
    });
    
    // Mostrar loading
    const saveBtn = event.target;
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
    
    fetch(`/admin/products/${productId}/update-images`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Imagens atualizadas com sucesso!');
            // Fechar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('imagesModal'));
            if (modal) {
                modal.hide();
            }
            // Recarregar página para atualizar miniaturas
            location.reload();
        } else {
            alert('❌ Erro ao salvar imagens: ' + (data.message || 'Erro desconhecido'));
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('❌ Erro ao salvar imagens. Tente novamente.');
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    });
}
</script>

