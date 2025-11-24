<script>
let currentBrandId = null;
let selectedFile = null;

// Função para fazer upload do logo
function uploadLogo() {
    if (!selectedFile || !currentBrandId) {
        return;
    }

    const formData = new FormData();
    formData.append('logo', selectedFile);
    formData.append('_token', '{{ csrf_token() }}');

    // Desabilitar botão e mostrar loading
    const btn = document.getElementById('uploadLogoBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spinning"></i> Salvando...';

    // Fazer upload via AJAX
    fetch(`{{ url('admin/brands') }}/${currentBrandId}`, {
        method: 'POST',
        headers: {
            'X-HTTP-Method-Override': 'PUT',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fechar modal e recarregar página
            bootstrap.Modal.getInstance(document.getElementById('logoModal')).hide();
            location.reload();
        } else {
            throw new Error(data.message || 'Erro ao salvar logo');
        }
    })
    .catch(error => {
        alert('Erro ao salvar logo: ' + error.message);
        // Reabilitar botão
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Função para abrir o modal
function openLogoModal(brandId, brandName, currentLogoUrl) {
    currentBrandId = brandId;
    selectedFile = null;

    // Atualizar título
    document.getElementById('logoModalLabel').textContent = `Alterar Logo: ${brandName}`;

    // Mostrar logo atual ou placeholder
    const container = document.getElementById('currentLogoContainer');
    const removeBtn = document.getElementById('removeLogoBtn');

    if (currentLogoUrl) {
        container.innerHTML = `
            <p class="mb-2"><strong>Logo atual:</strong></p>
            <img src="${currentLogoUrl}" alt="${brandName}" class="img-fluid rounded shadow-sm" style="max-width: 150px; max-height: 150px; object-fit: contain;">
        `;
        removeBtn.style.display = 'inline-block';
    } else {
        container.innerHTML = `
            <p class="mb-2"><strong>Sem logo definido</strong></p>
            <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                <i class="bi bi-tag display-4 text-muted"></i>
            </div>
        `;
        removeBtn.style.display = 'none';
    }

    // Resetar preview e botão
    document.getElementById('previewContainer').style.display = 'none';
    document.getElementById('uploadLogoBtn').disabled = true;
    document.getElementById('logoFile').value = '';

    // Abrir modal sem backdrop
    const modalElement = document.getElementById('logoModal');
    const modal = new bootstrap.Modal(modalElement, {
        backdrop: false, // Sem backdrop
        keyboard: true,
        focus: true
    });
    modal.show();
}

// Função para mostrar preview
function showPreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('logoPreview').src = e.target.result;
        document.getElementById('previewContainer').style.display = 'block';
        document.getElementById('uploadLogoBtn').disabled = false;
    };
    reader.readAsDataURL(file);
}

// Event listener para seleção de arquivo
document.getElementById('logoFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        selectedFile = file;
        showPreview(file);
    }
});

// Event listener para colar imagem (Ctrl+V)
document.addEventListener('paste', function(e) {
    if (!document.getElementById('logoModal').classList.contains('show')) {
        return;
    }

    const items = e.clipboardData.items;
    for (let i = 0; i < items.length; i++) {
        if (items[i].type.indexOf('image') !== -1) {
            const file = items[i].getAsFile();
            if (file) {
                selectedFile = file;
                showPreview(file);
                e.preventDefault();
                break;
            }
        } else if (items[i].type === 'text/plain') {
            items[i].getAsString(function(text) {
                if (text.match(/\.(jpg|jpeg|png|gif|webp)/i)) {
                    fetch(text)
                    .then(response => response.blob())
                    .then(blob => {
                        const file = new File([blob], 'pasted_image.png', {type: 'image/png'});
                        selectedFile = file;
                        showPreview(file);
                    })
                    .catch(() => {});
                }
            });
        } else if (items[i].type === 'text/html') {
            items[i].getAsString(function(html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const img = doc.querySelector('img');
                if (img && img.src) {
                    fetch(img.src)
                    .then(response => response.blob())
                    .then(blob => {
                        const file = new File([blob], 'pasted_image.png', {type: 'image/png'});
                        selectedFile = file;
                        showPreview(file);
                    })
                    .catch(() => {});
                }
            });
        }
    }
});

// Event listener para upload manual
document.getElementById('uploadLogoBtn').addEventListener('click', function() {
    uploadLogo();
});

// Função para remover logo
function removeLogo() {
    if (!currentBrandId) {
        return;
    }

    if (!confirm('Tem certeza que deseja remover o logo desta marca?')) {
        return;
    }

    const formData = new FormData();
    formData.append('logo', '');
    formData.append('_token', '{{ csrf_token() }}');

    const btn = document.getElementById('removeLogoBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat spinning"></i> Removendo...';

    fetch(`{{ url('admin/brands') }}/${currentBrandId}`, {
        method: 'POST',
        headers: {
            'X-HTTP-Method-Override': 'PUT',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('logoModal')).hide();
            location.reload();
        } else {
            throw new Error(data.message || 'Erro ao remover logo');
        }
    })
    .catch(error => {
        alert('Erro ao remover logo: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Verificar elementos ao carregar
document.addEventListener('DOMContentLoaded', function() {
    console.log('Logo upload modal script loaded');
});
</script>

<style>
#logoModal {
    z-index: 1200 !important;
    position: fixed !important;
}

#logoModal.show {
    z-index: 1200 !important;
    display: block !important;
}

#logoModal .modal-dialog {
    z-index: 1201 !important;
    position: relative !important;
}

#logoModal .modal-content {
    z-index: 1202 !important;
    position: relative !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    border: 1px solid rgba(0, 0, 0, 0.125) !important;
}

.spinning {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>