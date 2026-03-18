@extends('admin.layouts.app')

@section('title', 'Biblioteca de Vídeos')

@push('styles')
<style>
.media-library-container {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 20px;
    padding: 2px;
}

.upload-area {
    border: 3px dashed #cbd5e1;
    border-radius: 16px;
    padding: 40px;
    text-align: center;
    transition: all 0.3s ease;
    background: white;
    cursor: pointer;
}

.upload-area:hover {
    border-color: #667eea;
    background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
}

.upload-area.dragover {
    border-color: #667eea;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    transform: scale(1.02);
}

.video-grid-item {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #e2e8f0;
    position: relative;
}

.video-grid-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    border-color: #667eea;
}

.video-thumbnail {
    aspect-ratio: 16/9;
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    position: relative;
    overflow: hidden;
}

.video-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.video-grid-item:hover .video-overlay {
    opacity: 1;
}

.play-button {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #667eea;
    transition: all 0.3s ease;
}

.play-button:hover {
    background: white;
    transform: scale(1.1);
}

.video-info {
    padding: 16px;
}

.video-title {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.video-meta {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 8px;
}

.video-actions {
    display: flex;
    gap: 8px;
}

.action-btn {
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #64748b;
    font-size: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
}

.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.action-btn.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.action-btn.danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border-color: #ef4444;
}

.upload-form {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
}

.search-bar {
    background: white;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 24px;
    border: 1px solid #e2e8f0;
}

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid #e2e8f0;
    border-radius: 50%;
    border-top-color: #667eea;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.modal-content {
    background: white;
    border-radius: 20px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.highlight-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
    margin-top: 16px;
}

.highlight-option {
    padding: 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.highlight-option:hover {
    border-color: #667eea;
    background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
}

.highlight-option.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #64748b;
}

.empty-state-icon {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
}
</style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="media-library-container p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="mb-1" style="font-size: 28px; font-weight: 800; color: #1e293b;">
                            <i class="fas fa-photo-video me-3"></i>Biblioteca de Vídeos
                        </h1>
                        <p class="mb-0 text-muted">Gerencie seus vídeos e atribua aos destaques</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" style="border-radius: 12px; padding: 12px 24px;" onclick="showUploadForm()">
                            <i class="fas fa-plus me-2"></i>Novo Vídeo
                        </button>
                    </div>
                </div>
                
                <!-- Upload Form (Hidden by default) -->
                <div id="uploadForm" class="upload-form" style="display: none;">
                    <h3 class="mb-4">Enviar Novo Vídeo</h3>
                    <form id="videoUploadForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="upload-area" id="uploadArea">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3" style="color: #667eea;"></i>
                                    <h4>Arraste e solte o vídeo aqui</h4>
                                    <p class="text-muted mb-3">ou clique para selecionar</p>
                                    <input type="file" id="videoFile" accept="video/*" style="display: none;">
                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('videoFile').click()">
                                        <i class="fas fa-folder-open me-2"></i>Selecionar Arquivo
                                    </button>
                                    <div class="mt-3">
                                        <small class="text-muted">Formatos: MP4, MOV, AVI, WEBM (máx. 100MB)</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Título do Vídeo</label>
                                <input type="text" name="title" class="form-control" placeholder="Dê um título para o vídeo">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Descrição</label>
                                <input type="text" name="description" class="form-control" placeholder="Breve descrição (opcional)">
                            </div>
                            
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload me-2"></i>Enviar Vídeo
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="hideUploadForm()">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Search and Filter -->
                <div class="search-bar">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Buscar vídeos...">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="text-muted" id="videoCount">Carregando...</span>
                        </div>
                    </div>
                </div>
                
                <!-- Videos Grid -->
                <div id="videosGrid" class="row g-3">
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-video empty-state-icon"></i>
                            <h4>Nenhum vídeo encontrado</h4>
                            <p>Comece enviando seu primeiro vídeo na biblioteca</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Video Preview Modal -->
<div id="videoModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header border-0 p-4">
            <h5 class="modal-title">
                <i class="fas fa-video me-2"></i>
                <span id="modalVideoTitle">Visualizar Vídeo</span>
            </h5>
            <button type="button" class="btn-close" onclick="closeVideoModal()"></button>
        </div>
        <div class="modal-body p-4">
            <div class="ratio ratio-16x9 mb-3">
                <video id="modalVideo" controls style="width: 100%; height: 100%; border-radius: 12px;">
                </video>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Descrição:</label>
                <p id="modalVideoDescription" class="text-muted">Sem descrição</p>
            </div>
            <div class="row text-sm">
                <div class="col-6">
                    <strong>Tamanho:</strong> <span id="modalVideoSize">-</span>
                </div>
                <div class="col-6">
                    <strong>Enviado em:</strong> <span id="modalVideoDate">-</span>
                </div>
            </div>
            
            <hr>
            
            <h6 class="fw-semibold mb-3">Atribuir a Destaque:</h6>
            <div class="highlight-selector" id="highlightSelector">
                <!-- Highlight options will be generated by JavaScript -->
            </div>
            
            <div class="mt-3">
                <button class="btn btn-primary w-100" onclick="assignToHighlight()">
                    <i class="fas fa-link me-2"></i>Atribuir ao Destaque Selecionado
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let currentVideo = null;
let allVideos = [];

document.addEventListener('DOMContentLoaded', function() {
    loadVideos();
    setupUploadArea();
    setupSearch();
});

function loadVideos() {
    fetch('/admin/media-library/videos')
        .then(response => response.json())
        .then(videos => {
            allVideos = videos;
            renderVideos(videos);
            updateVideoCount(videos.length);
        })
        .catch(error => {
            console.error('Error loading videos:', error);
            showError('Erro ao carregar vídeos');
        });
}

function renderVideos(videos) {
    const grid = document.getElementById('videosGrid');
    
    if (videos.length === 0) {
        grid.innerHTML = `
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-video empty-state-icon"></i>
                    <h4>Nenhum vídeo encontrado</h4>
                    <p>Comece enviando seu primeiro vídeo na biblioteca</p>
                </div>
            </div>
        `;
        return;
    }
    
    grid.innerHTML = videos.map(video => `
        <div class="col-md-6 col-lg-4">
            <div class="video-grid-item">
                <div class="video-thumbnail">
                    <img src="${video.thumbnail}" alt="${video.title}">
                    <div class="video-overlay">
                        <div class="play-button">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                </div>
                <div class="video-info">
                    <div class="video-title" title="${video.title}">${video.title}</div>
                    <div class="video-meta">
                        <i class="fas fa-file me-1"></i>${video.size}
                        <span class="ms-2"><i class="fas fa-calendar me-1"></i>${formatDate(video.uploaded_at)}</span>
                    </div>
                    <div class="video-actions">
                        <button class="action-btn primary" onclick="openVideoModal('${video.id}')">
                            <i class="fas fa-eye me-1"></i>Visualizar
                        </button>
                        <button class="action-btn danger" onclick="deleteVideo('${video.id}')">
                            <i class="fas fa-trash me-1"></i>Excluir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function setupUploadArea() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('videoFile');
    
    uploadArea.addEventListener('click', () => fileInput.click());
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0 && files[0].type.startsWith('video/')) {
            fileInput.files = files;
            updateUploadArea(files[0]);
        }
    });
    
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            updateUploadArea(e.target.files[0]);
        }
    });
    
    document.getElementById('videoUploadForm').addEventListener('submit', handleUpload);
}

function updateUploadArea(file) {
    const uploadArea = document.getElementById('uploadArea');
    uploadArea.innerHTML = `
        <i class="fas fa-video fa-3x mb-3" style="color: #10b981;"></i>
        <h4>${file.name}</h4>
        <p class="text-muted">Tamanho: ${formatFileSize(file.size)}</p>
        <button type="button" class="btn btn-outline-danger" onclick="clearFileSelection()">
            <i class="fas fa-times me-2"></i>Remover Arquivo
        </button>
    `;
}

function clearFileSelection() {
    document.getElementById('videoFile').value = '';
    location.reload(); // Simple way to reset the upload area
}

function handleUpload(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const fileInput = document.getElementById('videoFile');
    
    if (!fileInput.files.length) {
        showError('Por favor, selecione um arquivo de vídeo');
        return;
    }
    
    formData.append('video', fileInput.files[0]);
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="loading-spinner me-2"></span>Enviando...';
    submitBtn.disabled = true;
    
    fetch('/admin/media-library/upload', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            hideUploadForm();
            loadVideos();
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        showError('Erro ao enviar vídeo: ' + error.message);
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function setupSearch() {
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        const filtered = allVideos.filter(video => 
            video.title.toLowerCase().includes(query) ||
            video.description.toLowerCase().includes(query)
        );
        renderVideos(filtered);
        updateVideoCount(filtered.length);
    });
}

function openVideoModal(videoId) {
    const video = allVideos.find(v => v.id === videoId);
    if (!video) return;
    
    currentVideo = video;
    
    document.getElementById('modalVideoTitle').textContent = video.title;
    document.getElementById('modalVideoDescription').textContent = video.description || 'Sem descrição';
    document.getElementById('modalVideoSize').textContent = video.size;
    document.getElementById('modalVideoDate').textContent = formatDate(video.uploaded_at);
    document.getElementById('modalVideo').src = video.url;
    
    // Generate highlight selector
    const selector = document.getElementById('highlightSelector');
    selector.innerHTML = '';
    for (let i = 1; i <= 10; i++) {
        const option = document.createElement('div');
        option.className = 'highlight-option';
        option.dataset.highlight = i;
        option.innerHTML = `
            <i class="fas fa-video fa-2x mb-2"></i>
            <div class="fw-bold">Destaque ${i}</div>
        `;
        option.addEventListener('click', () => selectHighlight(i));
        selector.appendChild(option);
    }
    
    document.getElementById('videoModal').style.display = 'flex';
}

function closeVideoModal() {
    document.getElementById('videoModal').style.display = 'none';
    document.getElementById('modalVideo').pause();
    currentVideo = null;
}

function selectHighlight(number) {
    document.querySelectorAll('.highlight-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    document.querySelector(`[data-highlight="${number}"]`).classList.add('selected');
}

function assignToHighlight() {
    if (!currentVideo) return;
    
    const selected = document.querySelector('.highlight-option.selected');
    if (!selected) {
        showError('Por favor, selecione um destaque');
        return;
    }
    
    const highlightNumber = selected.dataset.highlight;
    
    fetch('/admin/media-library/assign-to-highlight', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            video_url: currentVideo.url,
            highlight_number: highlightNumber
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            closeVideoModal();
            // Redirect to highlights page to see the result
            setTimeout(() => {
                window.location.href = '/admin/highlights';
            }, 1500);
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        showError('Erro ao atribuir vídeo: ' + error.message);
    });
}

function deleteVideo(videoId) {
    if (!confirm('Tem certeza que deseja excluir este vídeo? Esta ação não pode ser desfeita.')) {
        return;
    }
    
    fetch(`/admin/media-library/${videoId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccess(data.message);
            loadVideos();
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        showError('Erro ao excluir vídeo: ' + error.message);
    });
}

function showUploadForm() {
    document.getElementById('uploadForm').style.display = 'block';
}

function hideUploadForm() {
    document.getElementById('uploadForm').style.display = 'none';
    document.getElementById('videoUploadForm').reset();
    document.getElementById('videoFile').value = '';
    location.reload();
}

function updateVideoCount(count) {
    document.getElementById('videoCount').textContent = `${count} vídeo${count !== 1 ? 's' : ''}`;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}

function formatFileSize(bytes) {
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}

function showSuccess(message) {
    showAlert('success', message);
}

function showError(message) {
    showAlert('danger', message);
}

function showAlert(type, message) {
    const existingAlerts = document.querySelectorAll('.alert-dismissible');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             role="alert" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease;">
            <div class="d-flex align-items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} fa-2x me-3"></i>
                <div class="flex-grow-1">
                    <strong class="d-block">${type === 'success' ? 'Sucesso!' : 'Erro!'}</strong>
                    <div>${message}</div>
                </div>
                <button type="button" class="btn-close ms-3" data-bs-dismiss="alert"></button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    setTimeout(() => {
        const alert = document.querySelector('.alert-dismissible');
        if (alert) {
            alert.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => alert.remove(), 300);
        }
    }, 5000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>
@endsection
