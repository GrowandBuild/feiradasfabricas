/**
 * Admin Badge Form JavaScript
 * Handles form interactions, preview updates, and validation
 */

(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        initializeTooltips();
        initializeSizeSelection();
        initializePositionSelection();
        initializeImagePreview();
        initializeLivePreview();
    });

    function initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    function initializeSizeSelection() {
        const sizeCards = document.querySelectorAll('.size-template-card');
        const hiddenInput = document.getElementById('selected_size_template');
        const customFields = document.getElementById('customSizeFields');

        sizeCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove active from all
                sizeCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                
                const template = this.dataset.template;
                hiddenInput.value = template;
                
                // Show/hide custom size fields
                if (template === 'custom') {
                    customFields.style.display = 'block';
                } else {
                    customFields.style.display = 'none';
                }
                
                updatePreview();
            });
        });

        // Set initial active state
        const initialSize = hiddenInput.value;
        const initialCard = document.querySelector(`[data-template="${initialSize}"]`);
        if (initialCard) {
            initialCard.classList.add('active');
        }
    }

    function initializePositionSelection() {
        const positionCards = document.querySelectorAll('.position-option-card');
        const hiddenInput = document.getElementById('selected_position');

        positionCards.forEach(card => {
            card.addEventListener('click', function() {
                positionCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                hiddenInput.value = this.dataset.position;
                updatePreview();
            });
        });

        // Set initial active state
        const initialPosition = hiddenInput.value;
        const initialCard = document.querySelector(`[data-position="${initialPosition}"]`);
        if (initialCard) {
            initialCard.classList.add('active');
        }
    }

    function initializeImagePreview() {
        const imageInput = document.getElementById('image');
        if (!imageInput) return;

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImg = document.getElementById('previewImg');
                    const imagePreview = document.getElementById('imagePreview');
                    
                    if (previewImg && imagePreview) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    
                    // Also update current image if exists
                    const currentImage = document.getElementById('currentImage');
                    if (currentImage) {
                        currentImage.src = e.target.result;
                    }
                    
                    updatePreview();
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function initializeLivePreview() {
        const previewElements = document.querySelectorAll('[data-preview]');
        previewElements.forEach(el => {
            el.addEventListener('input', updatePreview);
            el.addEventListener('change', updatePreview);
        });

        // Initial preview update
        updatePreview();
    }

    function updatePreview() {
        const preview = document.getElementById('previewBadge');
        if (!preview) return;

        const title = document.getElementById('title')?.value || '';
        const text = document.getElementById('text')?.value || '';
        const imageInput = document.getElementById('image');
        const previewImg = document.getElementById('previewImg');
        const currentImage = document.getElementById('currentImage');
        const bgColor = document.getElementById('background_color')?.value || '#ffffff';
        const textColor = document.getElementById('text_color')?.value || '#1f2937';

        let html = '';
        let hasImage = false;

        // Check for new image preview
        if (previewImg && previewImg.src && previewImg.src !== window.location.href) {
            html += `<img src="${previewImg.src}" alt="Preview" style="max-width: 100%; max-height: 100px; border-radius: 8px; margin-bottom: 8px; display: block;">`;
            hasImage = true;
        }
        // Check for current image (edit mode)
        else if (currentImage && currentImage.src) {
            html += `<img src="${currentImage.src}" alt="Preview" style="max-width: 100%; max-height: 100px; border-radius: 8px; margin-bottom: 8px; display: block;">`;
            hasImage = true;
        }

        if (title) {
            html += `<div style="font-weight: 700; color: ${textColor}; margin-bottom: 4px; font-size: 14px;">${escapeHtml(title)}</div>`;
        }

        if (text) {
            html += `<div style="color: ${textColor}; font-size: 12px; line-height: 1.4;">${escapeHtml(text)}</div>`;
        }

        if (!html) {
            html = '<div class="text-center text-muted py-3"><i class="bi bi-image fs-4 d-block mb-2"></i><small>Adicione conteúdo para ver o preview</small></div>';
        }

        preview.innerHTML = html;
        preview.style.backgroundColor = bgColor;
        preview.style.color = textColor;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
})();

