@php
    $feedbacks = $product->approvedFeedbacks()->with('customer')->orderBy('created_at', 'desc')->get();
    $isLoggedIn = auth('customer')->check();
@endphp

<div class="product-feedbacks-section mt-5" id="feedbacks-section">
    <div class="feedbacks-header">
        <h3 class="feedbacks-title">
            <i class="bi bi-chat-heart-fill"></i> Feedbacks dos Clientes
        </h3>
        <p class="feedbacks-subtitle">Veja o que nossos clientes estão dizendo sobre este produto</p>
    </div>

    <!-- Formulário para adicionar feedback (apenas para clientes logados) -->
    @if($isLoggedIn)
        <div class="feedback-form-card">
            <div class="feedback-form-header">
                <h5 class="feedback-form-title">
                    <i class="bi bi-pencil-square"></i> Deixe seu feedback
                </h5>
            </div>
            <form id="feedback-form" enctype="multipart/form-data" class="feedback-form">
                @csrf
                
                <div class="form-group-feedback">
                    <label for="feedback-text" class="form-label-feedback">
                        <i class="bi bi-chat-text"></i> Seu comentário (opcional)
                    </label>
                    <textarea name="text" id="feedback-text" class="form-control-feedback" rows="4" placeholder="Compartilhe sua experiência com este produto..."></textarea>
                </div>

                <div class="form-group-feedback">
                    <label for="feedback-image" class="form-label-feedback">
                        <i class="bi bi-camera"></i> Foto usando o produto (opcional)
                    </label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="image" id="feedback-image" class="file-input-feedback" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                        <label for="feedback-image" class="file-label-feedback">
                            <i class="bi bi-cloud-upload"></i> Escolher imagem
                        </label>
                        <small class="file-hint">Formatos: JPEG, PNG, JPG, GIF, WEBP • Máximo 5MB</small>
                    </div>
                    <div id="image-preview" class="image-preview-container"></div>
                </div>

                <div class="feedback-info-box">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Você pode adicionar apenas texto, apenas imagem, ou ambos.</span>
                </div>

                <button type="submit" class="btn-submit-feedback" id="submit-feedback">
                    <i class="bi bi-send-fill"></i> Enviar Feedback
                </button>
            </form>
        </div>
    @else
        <div class="feedback-login-prompt">
            <i class="bi bi-person-circle"></i>
            <div class="login-prompt-content">
                <strong>Faça login para compartilhar sua experiência!</strong>
                <p>Entre com sua conta para deixar um feedback sobre este produto.</p>
                <a href="{{ route('customer.login') }}" class="btn-login-prompt">
                    <i class="bi bi-box-arrow-in-right"></i> Fazer Login
                </a>
            </div>
        </div>
    @endif

    <!-- Lista de Feedbacks -->
    <div id="feedbacks-list" class="feedbacks-grid">
        @if($feedbacks->count() > 0)
            @foreach($feedbacks as $feedback)
                <div class="feedback-card">
                    <div class="feedback-card-header">
                        <div class="feedback-author">
                            <div class="author-avatar">
                                @if($feedback->customer)
                                    <i class="bi bi-person-fill"></i>
                                @else
                                    <i class="bi bi-shield-check-fill"></i>
                                @endif
                            </div>
                            <div class="author-info">
                                <strong class="author-name">{{ $feedback->author_name }}</strong>
                                @if($feedback->customer)
                                    <span class="author-badge customer-badge">
                                        <i class="bi bi-person-check"></i> Cliente Verificado
                                    </span>
                                @else
                                    <span class="author-badge admin-badge">
                                        <i class="bi bi-shield-check"></i> Administrador
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="feedback-date">
                            <i class="bi bi-calendar3"></i>
                            {{ $feedback->created_at->format('d/m/Y') }}
                        </div>
                    </div>

                    @if($feedback->text)
                        <div class="feedback-text">
                            <p>{{ $feedback->text }}</p>
                        </div>
                    @endif

                    @if($feedback->image)
                        <div class="feedback-image-wrapper">
                            <img src="{{ $feedback->image_url }}" 
                                 alt="Feedback de {{ $feedback->author_name }}" 
                                 class="feedback-image" 
                                 loading="lazy"
                                 onclick="openImageModal(this.src, '{{ $feedback->author_name }}')">
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="feedbacks-empty">
                <div class="empty-icon">
                    <i class="bi bi-chat-heart"></i>
                </div>
                <h4>Ainda não há feedbacks</h4>
                <p>Seja o primeiro a compartilhar sua experiência com este produto!</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Feedbacks Section */
    .product-feedbacks-section {
        margin-top: 3rem;
        padding: 2rem 0;
        border-top: 2px solid #e8eaed;
    }

    .feedbacks-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .feedbacks-title {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .feedbacks-title i {
        color: #ff6b35;
        font-size: 2.2rem;
    }

    .feedbacks-subtitle {
        color: #666;
        font-size: 1rem;
        margin: 0;
    }

    /* Form Card */
    .feedback-form-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 2px solid #e8eaed;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 3rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .feedback-form-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e8eaed;
    }

    .feedback-form-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .feedback-form-title i {
        color: #ff6b35;
    }

    .form-group-feedback {
        margin-bottom: 1.5rem;
    }

    .form-label-feedback {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }

    .form-label-feedback i {
        color: #ff6b35;
    }

    .form-control-feedback {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e8eaed;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s ease;
        resize: vertical;
    }

    .form-control-feedback:focus {
        outline: none;
        border-color: #ff6b35;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .file-upload-wrapper {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .file-input-feedback {
        display: none;
    }

    .file-label-feedback {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        background: linear-gradient(135deg, #ff6b35 0%, #ff8c5a 100%);
        color: white;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        width: fit-content;
    }

    .file-label-feedback:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    }

    .file-hint {
        color: #666;
        font-size: 0.85rem;
    }

    .image-preview-container {
        margin-top: 1rem;
    }

    .image-preview-container img {
        max-width: 200px;
        max-height: 200px;
        border-radius: 10px;
        border: 2px solid #e8eaed;
        object-fit: cover;
    }

    .feedback-info-box {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: linear-gradient(135deg, rgba(255, 107, 53, 0.1) 0%, rgba(255, 107, 53, 0.05) 100%);
        border-left: 4px solid #ff6b35;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        color: #333;
    }

    .feedback-info-box i {
        color: #ff6b35;
        font-size: 1.25rem;
    }

    .btn-submit-feedback {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 2rem;
        background: linear-gradient(135deg, #ff6b35 0%, #ff8c5a 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    }

    .btn-submit-feedback:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
    }

    .btn-submit-feedback:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Login Prompt */
    .feedback-login-prompt {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 2rem;
        background: linear-gradient(135deg, rgba(52, 131, 250, 0.1) 0%, rgba(52, 131, 250, 0.05) 100%);
        border: 2px solid rgba(52, 131, 250, 0.2);
        border-radius: 16px;
        margin-bottom: 3rem;
    }

    .feedback-login-prompt i {
        font-size: 3rem;
        color: #3483fa;
    }

    .login-prompt-content {
        flex: 1;
    }

    .login-prompt-content strong {
        display: block;
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .login-prompt-content p {
        color: #666;
        margin-bottom: 1rem;
    }

    .btn-login-prompt {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: #3483fa;
        color: white;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-login-prompt:hover {
        background: #2968c8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 131, 250, 0.3);
        color: white;
        text-decoration: none;
    }

    /* Feedbacks Grid */
    .feedbacks-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    @media (max-width: 768px) {
        .feedbacks-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Feedback Card */
    .feedback-card {
        background: white;
        border: 2px solid #e8eaed;
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .feedback-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        border-color: #ff6b35;
    }

    .feedback-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e8eaed;
    }

    .feedback-author {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
    }

    .author-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ff6b35 0%, #ff8c5a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .author-info {
        flex: 1;
        min-width: 0;
    }

    .author-name {
        display: block;
        font-size: 1rem;
        color: #333;
        margin-bottom: 0.25rem;
    }

    .author-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .customer-badge {
        background: rgba(52, 131, 250, 0.1);
        color: #3483fa;
    }

    .admin-badge {
        background: rgba(255, 107, 53, 0.1);
        color: #ff6b35;
    }

    .feedback-date {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #999;
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .feedback-text {
        margin-bottom: 1rem;
        color: #555;
        line-height: 1.6;
    }

    .feedback-text p {
        margin: 0;
    }

    .feedback-image-wrapper {
        margin-top: 1rem;
        border-radius: 12px;
        overflow: hidden;
        background: #f8f9fa;
        position: relative;
    }

    .feedback-image {
        width: 100%;
        height: auto;
        max-height: 300px;
        object-fit: cover;
        display: block;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .feedback-image:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .feedback-image-wrapper::after {
        content: '\F4C4';
        font-family: 'bootstrap-icons';
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.9);
        color: #ff6b35;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .feedback-image-wrapper:hover::after {
        opacity: 1;
    }

    /* Image Modal */
    .image-modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        animation: fadeIn 0.3s ease;
    }

    .image-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-modal-content {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        border-radius: 8px;
        animation: zoomIn 0.3s ease;
    }

    .image-modal-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 2.5rem;
        font-weight: bold;
        cursor: pointer;
        background: rgba(255, 255, 255, 0.1);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .image-modal-close:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: rotate(90deg);
    }

    .image-modal-caption {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        color: white;
        background: rgba(0, 0, 0, 0.6);
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-size: 1rem;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    /* Empty State */
    .feedbacks-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-icon {
        font-size: 4rem;
        color: #ccc;
        margin-bottom: 1rem;
    }

    .feedbacks-empty h4 {
        color: #333;
        margin-bottom: 0.5rem;
    }

    .feedbacks-empty p {
        color: #666;
    }
</style>
@endpush

@push('scripts')
<script>
    // Função para abrir modal de imagem
    function openImageModal(imageSrc, authorName) {
        // Criar modal se não existir
        let modal = document.getElementById('feedback-image-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'feedback-image-modal';
            modal.className = 'image-modal';
            modal.innerHTML = `
                <span class="image-modal-close" onclick="closeImageModal()">&times;</span>
                <img class="image-modal-content" src="" alt="">
                <div class="image-modal-caption"></div>
            `;
            document.body.appendChild(modal);
            
            // Fechar ao clicar fora da imagem
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeImageModal();
                }
            });
            
            // Fechar com ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    closeImageModal();
                }
            });
        }
        
        // Preencher modal
        const img = modal.querySelector('.image-modal-content');
        const caption = modal.querySelector('.image-modal-caption');
        img.src = imageSrc;
        img.alt = `Feedback de ${authorName}`;
        caption.textContent = `Feedback de ${authorName}`;
        
        // Mostrar modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeImageModal() {
        const modal = document.getElementById('feedback-image-modal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('feedback-form');
        const imageInput = document.getElementById('feedback-image');
        const imagePreview = document.getElementById('image-preview');

        // Preview da imagem
        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.innerHTML = '';
                }
            });
        }

        // Envio do formulário
        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const submitBtn = document.getElementById('submit-feedback');
                const originalText = submitBtn.innerHTML;

                // Validação
                const text = formData.get('text');
                const image = formData.get('image');

                if (!text && !image) {
                    showToast('Você deve fornecer pelo menos um texto ou uma imagem.', 'error');
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';

                try {
                    const response = await fetch('{{ route("product.feedback.store", $product) }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        showToast(data.message, 'success');
                        form.reset();
                        imagePreview.innerHTML = '';
                        
                        // Recarregar feedbacks após 1 segundo
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast(data.message || 'Erro ao enviar feedback.', 'error');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    showToast('Erro ao enviar feedback. Tente novamente.', 'error');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }

        function showToast(message, type = 'success') {
            const colors = {
                success: '#28a745',
                error: '#dc3545',
                info: '#6c757d'
            };
            
            const toast = document.createElement('div');
            toast.style.cssText = `position: fixed; top: 20px; right: 20px; background: ${colors[type] || colors.success}; color: white; padding: 1rem 1.5rem; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 10000; animation: slideInRight 0.3s ease; max-width: 300px;`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    });
</script>
@endpush
