@extends('admin.layouts.app')

@section('title', 'Configurações de Destaques')

@push('styles')
<style>
.highlights-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2px;
}

.video-card {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    position: relative;
}

.video-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border-color: #667eea;
}

.video-preview {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    border-radius: 16px;
    overflow: hidden;
    position: relative;
    aspect-ratio: 9/16;
    max-height: 280px;
}

.video-preview::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.video-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: white;
    text-align: center;
    padding: 20px;
}

.upload-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.upload-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.upload-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.upload-btn:hover::before {
    left: 100%;
}

.url-input-group {
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.url-input-group:focus-within {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.url-input {
    border: none;
    padding: 10px 15px;
    font-size: 14px;
}

.url-download-btn {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
    color: white;
    padding: 10px 15px;
    transition: all 0.3s ease;
}

.url-download-btn:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #64748b;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.action-btn.info:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border-color: #3b82f6;
}

.action-btn.success:hover {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border-color: #10b981;
}

.action-btn.danger:hover {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    border-color: #ef4444;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.status-badge.danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.status-badge.warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.config-card {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(10px);
}

.config-input {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: white;
}

.config-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

.save-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 14px 28px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
}

.save-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

.preview-section {
    background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
}

.video-title {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
}

.alert-modern {
    border-radius: 16px;
    border: none;
    padding: 20px;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    color: #1e40af;
}

.alert-modern.alert-success {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    color: #166534;
}

.alert-modern.alert-danger {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    color: #991b1b;
}

.alert-modern.alert-warning {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    color: #92400e;
}

.loading-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="config-card p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="me-3">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-video fa-2x text-white"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="mb-1" style="font-size: 28px; font-weight: 800; color: #1e293b;">Configurações de Destaques</h1>
                        <p class="mb-0" style="color: #64748b;">Personalize a seção de vídeos em destaque do seu site</p>
                    </div>
                </div>
                
                <form action="{{ route('admin.highlights.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #374151;">
                                <i class="fas fa-heading me-2"></i>Título da Seção
                            </label>
                            <input type="text" 
                                   class="form-control config-input" 
                                   id="highlights_section_title" 
                                   name="highlights_section_title" 
                                   value="{{ setting('highlights_section_title', 'Destaques') }}"
                                   placeholder="Destaques"
                                   maxlength="100">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #374151;">
                                <i class="fas fa-align-center me-2"></i>Subtítulo da Seção
                            </label>
                            <input type="text" 
                                   class="form-control config-input" 
                                   id="highlights_section_subtitle" 
                                   name="highlights_section_subtitle" 
                                   value="{{ setting('highlights_section_subtitle', 'Conheça nossos produtos em destaque') }}"
                                   placeholder="Conheça nossos produtos em destaque"
                                   maxlength="200">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>As alterações serão refletidas imediatamente na página inicial</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2" style="border-radius: 12px; padding: 12px 24px;">
                                <i class="fas fa-arrow-left me-2"></i>Voltar
                            </a>
                            <button type="submit" class="save-btn">
                                <i class="fas fa-save me-2"></i>Salvar Configurações
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Videos Section -->
    <div class="row">
        <div class="col-12">
            <div class="config-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h2 class="mb-1" style="font-size: 24px; font-weight: 700; color: #1e293b;">
                            <i class="fas fa-film me-3"></i>Gerenciar Vídeos
                        </h2>
                        <p class="mb-0 text-muted">Upload direto ou adição via URL - super fácil e rápido!</p>
                    </div>
                    <div class="text-end">
                        <div class="badge bg-primary" style="font-size: 14px; padding: 8px 16px; border-radius: 20px;">
                            <i class="fas fa-video me-2"></i>10 Vídeos
                        </div>
                    </div>
                </div>
                
                <div class="alert-modern">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3" style="color: #3b82f6;"></i>
                        <div>
                            <strong class="d-block mb-2">Como adicionar vídeos - Agora é super fácil!</strong>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <strong><i class="fas fa-upload me-2"></i>Upload Direto:</strong> Clique em "Enviar Arquivo" para fazer upload do seu vídeo
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-link me-2"></i>Via URL:</strong> Cole a URL do vídeo e clique em "Baixar"
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-file-video me-2"></i>Formato:</strong> MP4, MOV, AVI (até 50MB)
                                </div>
                                <div class="col-md-6">
                                    <strong><i class="fas fa-mobile-alt me-2"></i>Recomendado:</strong> Aspect ratio 9:16, 10-30 segundos
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="highlights-container p-3 mt-4">
                    <div class="row g-3">
                        @for($i = 1; $i <= 10; $i++)
                            <div class="col-md-6 col-lg-4">
                                <div class="video-card p-3" id="video-card-{{ $i }}">
                                    <div class="text-center">
                                        <div class="mb-3">
                                            <div class="video-preview" id="video-preview-{{ $i }}">
                                                @if(file_exists(public_path("videos/highlight{$i}.mp4")))
                                                    <video style="width: 100%; height: 100%; object-fit: cover;" muted loop autoplay>
                                                        <source src="{{ asset("videos/highlight{$i}.mp4") }}?t={{ time() }}" type="video/mp4">
                                                    </video>
                                                @else
                                                    <div class="video-placeholder">
                                                        <div>
                                                            <i class="fas fa-video fa-3x mb-3" style="opacity: 0.7;"></i>
                                                            <div class="fw-bold">Vídeo {{ $i }}</div>
                                                            <div class="small opacity-75">Não encontrado</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <h6 class="video-title">
                                            @php
                                                $titles = [
                                                    1 => 'Promoção Especial',
                                                    2 => 'Novidades', 
                                                    3 => 'Mais Vendidos',
                                                    4 => 'Ofertas',
                                                    5 => 'Smartphones',
                                                    6 => 'Tablets',
                                                    7 => 'Notebooks',
                                                    8 => 'Fones',
                                                    9 => 'Carregadores',
                                                    10 => 'Acessórios'
                                                ];
                                            @endphp
                                            {{ $titles[$i] }}
                                        </h6>
                                        <p class="mb-3" id="status-{{ $i }}">
                                            @if(file_exists(public_path("videos/highlight{$i}.mp4")))
                                                <span class="status-badge success">Arquivo encontrado</span>
                                            @else
                                                <span class="status-badge danger">Arquivo ausente</span>
                                            @endif
                                        </p>
                                        
                                        <!-- Upload Form -->
                                        <div class="mb-2">
                                            <input type="file" 
                                                   id="file-{{ $i }}" 
                                                   accept="video/mp4,video/mov,video/avi" 
                                                   style="display: none;"
                                                   onchange="uploadVideo({{ $i }})">
                                            <button class="upload-btn w-100" onclick="document.getElementById('file-{{ $i }}').click()">
                                                <i class="fas fa-upload me-2"></i>Enviar Arquivo
                                            </button>
                                        </div>
                                        
                                        <!-- URL Input -->
                                        <div class="mb-3">
                                            <div class="url-input-group d-flex">
                                                <input type="url" 
                                                       id="url-{{ $i }}" 
                                                       class="form-control url-input flex-grow-1" 
                                                       placeholder="URL do vídeo">
                                                <button class="url-download-btn" onclick="addFromUrl({{ $i }})">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button class="action-btn info" onclick="showVideoInfo({{ $i }})" title="Informações">
                                                <i class="fas fa-info"></i>
                                            </button>
                                            @if(file_exists(public_path("videos/highlight{$i}.mp4")))
                                                <a href="{{ asset("videos/highlight{$i}.mp4") }}" target="_blank" class="action-btn success" title="Visualizar">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <button class="action-btn danger" onclick="deleteVideo({{ $i }})" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Preview Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="preview-section">
                <div class="text-center">
                    <h3 class="mb-3" style="font-size: 20px; font-weight: 700;">
                        <i class="fas fa-eye me-2"></i>Prévia da Seção
                    </h3>
                    <div class="preview-section p-4 rounded-3">
                        <h2 class="preview-title" style="font-size: 1.6rem; margin-bottom: 5px;">
                            {{ setting('highlights_section_title', 'Destaques') }}
                        </h2>
                        <p class="preview-subtitle mb-4" style="font-size: 0.85rem; opacity: 0.8;">
                            {{ setting('highlights_section_subtitle', 'Conheça nossos produtos em destaque') }}
                        </p>
                        
                        <!-- Mini preview dos vídeos -->
                        <div class="row g-2 justify-content-center">
                            @for($i = 1; $i <= 10; $i++)
                                <div class="col-auto">
                                    <div class="mini-highlight" style="background: linear-gradient(135deg, #374151 0%, #1f2937 100%); aspect-ratio: 9/16; border-radius: 8px; position: relative; overflow: hidden; width: 60px;">
                                        @if(file_exists(public_path("videos/highlight{$i}.mp4")))
                                            <video style="width: 100%; height: 100%; object-fit: cover;" muted loop autoplay>
                                                <source src="{{ asset("videos/highlight{$i}.mp4") }}?t={{ time() }}" type="video/mp4">
                                            </video>
                                        @else
                                            <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.8)); padding: 2px; color: white; font-size: 6px; text-align: center;">
                                                {{ $i }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('highlights_section_title');
    const subtitleInput = document.getElementById('highlights_section_subtitle');
    const previewTitle = document.querySelector('.preview-title');
    const previewSubtitle = document.querySelector('.preview-subtitle');
    
    titleInput.addEventListener('input', function() {
        previewTitle.textContent = this.value || 'Destaques';
    });
    
    subtitleInput.addEventListener('input', function() {
        previewSubtitle.textContent = this.value || 'Conheça nossos produtos em destaque';
    });
});

function uploadVideo(number) {
    const fileInput = document.getElementById(`file-${number}`);
    const file = fileInput.files[0];
    
    if (!file) return;
    
    const formData = new FormData();
    formData.append('video', file);
    
    // Show loading with animation
    const statusEl = document.getElementById(`status-${number}`);
    const originalContent = statusEl.innerHTML;
    statusEl.innerHTML = '<span class="status-badge warning"><i class="fas fa-spinner fa-spin me-2"></i>Enviando...</span>';
    
    // Add loading animation to card
    const card = document.getElementById(`video-card-${number}`);
    card.style.opacity = '0.7';
    card.style.pointerEvents = 'none';
    
    fetch(`/admin/highlights/upload/${number}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        card.style.opacity = '1';
        card.style.pointerEvents = 'auto';
        
        if (data.success) {
            statusEl.innerHTML = '<span class="status-badge success"><i class="fas fa-check me-2"></i>Arquivo encontrado</span>';
            updateVideoPreview(number, data.url);
            showAlert('success', data.message);
            
            // Add success animation
            card.style.animation = 'pulse 0.5s ease';
            setTimeout(() => card.style.animation = '', 500);
        } else {
            statusEl.innerHTML = '<span class="status-badge danger"><i class="fas fa-times me-2"></i>Erro no upload</span>';
            showAlert('danger', data.message || 'Erro ao fazer upload');
        }
    })
    .catch(error => {
        card.style.opacity = '1';
        card.style.pointerEvents = 'auto';
        statusEl.innerHTML = '<span class="status-badge danger"><i class="fas fa-times me-2"></i>Erro no upload</span>';
        showAlert('danger', 'Erro ao fazer upload: ' + error.message);
    });
}

function addFromUrl(number) {
    const urlInput = document.getElementById(`url-${number}`);
    const url = urlInput.value.trim();
    
    if (!url) {
        showAlert('warning', 'Por favor, digite uma URL válida');
        urlInput.focus();
        return;
    }
    
    // Show loading with animation
    const statusEl = document.getElementById(`status-${number}`);
    const originalContent = statusEl.innerHTML;
    statusEl.innerHTML = '<span class="status-badge warning"><i class="fas fa-download fa-spin me-2"></i>Baixando...</span>';
    
    // Add loading animation to card
    const card = document.getElementById(`video-card-${number}`);
    card.style.opacity = '0.7';
    card.style.pointerEvents = 'none';
    
    fetch(`/admin/highlights/add-from-url/${number}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ url: url })
    })
    .then(response => response.json())
    .then(data => {
        card.style.opacity = '1';
        card.style.pointerEvents = 'auto';
        
        if (data.success) {
            statusEl.innerHTML = '<span class="status-badge success"><i class="fas fa-check me-2"></i>Arquivo encontrado</span>';
            updateVideoPreview(number, data.url);
            urlInput.value = '';
            showAlert('success', data.message);
            
            // Add success animation
            card.style.animation = 'pulse 0.5s ease';
            setTimeout(() => card.style.animation = '', 500);
        } else {
            statusEl.innerHTML = '<span class="status-badge danger"><i class="fas fa-times me-2"></i>Erro ao baixar</span>';
            showAlert('danger', data.message || 'Erro ao baixar vídeo');
        }
    })
    .catch(error => {
        card.style.opacity = '1';
        card.style.pointerEvents = 'auto';
        statusEl.innerHTML = '<span class="status-badge danger"><i class="fas fa-times me-2"></i>Erro ao baixar</span>';
        showAlert('danger', 'Erro ao baixar: ' + error.message);
    });
}

function deleteVideo(number) {
    // Modern confirmation dialog
    const confirmDialog = document.createElement('div');
    confirmDialog.className = 'modal fade show';
    confirmDialog.style.display = 'block';
    confirmDialog.style.backgroundColor = 'rgba(0,0,0,0.5)';
    confirmDialog.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-trash fa-2x text-white"></i>
                        </div>
                    </div>
                    <h5 class="mb-2">Excluir Vídeo ${number}?</h5>
                    <p class="text-muted mb-4">Esta ação não pode ser desfeita. O vídeo será permanentemente removido.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-secondary" onclick="this.closest('.modal').remove()" style="border-radius: 12px; padding: 10px 24px;">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button class="btn btn-danger" onclick="confirmDelete(${number})" style="border-radius: 12px; padding: 10px 24px;">
                            <i class="fas fa-trash me-2"></i>Excluir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(confirmDialog);
}

function confirmDelete(number) {
    // Remove modal
    document.querySelector('.modal').remove();
    
    // Show loading
    const statusEl = document.getElementById(`status-${number}`);
    statusEl.innerHTML = '<span class="status-badge warning"><i class="fas fa-spinner fa-spin me-2"></i>Excluindo...</span>';
    
    fetch(`/admin/highlights/${number}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusEl.innerHTML = '<span class="status-badge danger"><i class="fas fa-times me-2"></i>Arquivo ausente</span>';
            
            const previewEl = document.getElementById(`video-preview-${number}`);
            previewEl.innerHTML = `
                <div class="video-placeholder">
                    <div>
                        <i class="fas fa-video fa-3x mb-3" style="opacity: 0.7;"></i>
                        <div class="fw-bold">Vídeo ${number}</div>
                        <div class="small opacity-75">Não encontrado</div>
                    </div>
                </div>
            `;
            
            // Remove delete button and external link with animation
            const card = document.getElementById(`video-card-${number}`);
            const deleteBtn = card.querySelector('.fa-trash')?.closest('button');
            const linkBtn = card.querySelector('.fa-external-link-alt')?.closest('a');
            
            if (deleteBtn) {
                deleteBtn.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => deleteBtn.remove(), 300);
            }
            if (linkBtn) {
                linkBtn.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => linkBtn.remove(), 300);
            }
            
            showAlert('success', data.message);
        } else {
            statusEl.innerHTML = '<span class="status-badge success"><i class="fas fa-check me-2"></i>Arquivo encontrado</span>';
            showAlert('danger', data.message || 'Erro ao excluir vídeo');
        }
    })
    .catch(error => {
        statusEl.innerHTML = '<span class="status-badge success"><i class="fas fa-check me-2"></i>Arquivo encontrado</span>';
        showAlert('danger', 'Erro ao excluir: ' + error.message);
    });
}

function updateVideoPreview(number, videoUrl) {
    const previewEl = document.getElementById(`video-preview-${number}`);
    previewEl.innerHTML = `
        <video style="width: 100%; height: 100%; object-fit: cover;" muted loop autoplay>
            <source src="${videoUrl}?t=${Date.now()}" type="video/mp4">
        </video>
    `;
    
    // Add delete button and external link if they don't exist
    const card = document.getElementById(`video-card-${number}`);
    const actionsDiv = card.querySelector('.d-flex.gap-2');
    
    if (!actionsDiv.querySelector('.fa-trash')) {
        const deleteBtn = document.createElement('button');
        deleteBtn.className = 'action-btn danger';
        deleteBtn.onclick = () => deleteVideo(number);
        deleteBtn.title = 'Excluir';
        deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
        deleteBtn.style.animation = 'fadeIn 0.3s ease';
        actionsDiv.appendChild(deleteBtn);
    }
    
    if (!actionsDiv.querySelector('.fa-external-link-alt')) {
        const linkBtn = document.createElement('a');
        linkBtn.href = videoUrl;
        linkBtn.target = '_blank';
        linkBtn.className = 'action-btn success';
        linkBtn.title = 'Visualizar';
        linkBtn.innerHTML = '<i class="fas fa-external-link-alt"></i>';
        linkBtn.style.animation = 'fadeIn 0.3s ease';
        actionsDiv.insertBefore(linkBtn, actionsDiv.lastElementChild);
    }
}

function showAlert(type, message) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert-dismissible');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertHtml = `
        <div class="alert alert-modern alert-${type} alert-dismissible fade show position-fixed" 
             role="alert" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideInRight 0.3s ease;">
            <div class="d-flex align-items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'danger' ? 'fa-exclamation-circle' : 'fa-info-circle'} fa-2x me-3"></i>
                <div class="flex-grow-1">
                    <strong class="d-block">${type === 'success' ? 'Sucesso!' : type === 'danger' ? 'Erro!' : 'Atenção!'}</strong>
                    <div>${message}</div>
                </div>
                <button type="button" class="btn-close ms-3" data-bs-dismiss="alert"></button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert-dismissible');
        if (alert) {
            alert.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => alert.remove(), 300);
        }
    }, 5000);
}

function showVideoInfo(number) {
    const info = {
        1: { title: 'Promoção Especial', subtitle: 'Até 50% OFF' },
        2: { title: 'Novidades', subtitle: 'Lançamentos' },
        3: { title: 'Mais Vendidos', subtitle: 'Top Produtos' },
        4: { title: 'Ofertas', subtitle: 'Preços Baixos' },
        5: { title: 'Smartphones', subtitle: 'Últimos Modelos' },
        6: { title: 'Tablets', subtitle: 'Portáteis' },
        7: { title: 'Notebooks', subtitle: 'Alta Performance' },
        8: { title: 'Fones', subtitle: 'Som Premium' },
        9: { title: 'Carregadores', subtitle: 'Rápida Recarga' },
        10: { title: 'Acessórios', subtitle: 'Essenciais' }
    };
    
    const video = info[number];
    
    // Modern info modal
    const infoModal = document.createElement('div');
    infoModal.className = 'modal fade show';
    infoModal.style.display = 'block';
    infoModal.style.backgroundColor = 'rgba(0,0,0,0.5)';
    infoModal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-header border-0 p-4">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle me-2" style="color: #3b82f6;"></i>
                        Informações do Vídeo ${number}
                    </h5>
                    <button type="button" class="btn-close" onclick="this.closest('.modal').remove()"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                            <i class="fas fa-video fa-2x text-white"></i>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Título:</label>
                        <div class="form-control-plaintext fs-5">${video.title}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Subtítulo:</label>
                        <div class="form-control-plaintext fs-6">${video.subtitle}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Nome do arquivo:</label>
                        <div class="form-control-plaintext">
                            <code>highlight${number}.mp4</code>
                        </div>
                    </div>
                    <div class="alert alert-info" style="border-radius: 12px; border: none;">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Formato recomendado:</strong> MP4, aspect ratio 9:16, 10-30 segundos
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-primary" onclick="this.closest('.modal').remove()" style="border-radius: 12px; padding: 10px 24px;">
                        <i class="fas fa-check me-2"></i>Entendido
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(infoModal);
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
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.8); }
        to { opacity: 1; transform: scale(1); }
    }
    @keyframes fadeOut {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0; transform: scale(0.8); }
    }
`;
document.head.appendChild(style);
</script>
@endsection
