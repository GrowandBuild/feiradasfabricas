/**
 * Utilities Professional
 */
const Utils = {
    $(selector, context = document) {
        return context.querySelector(selector);
    },

    $$(selector, context = document) {
        return Array.from(context.querySelectorAll(selector));
    },

    formatCurrency(value) {
        if (value == null) return '';
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    },

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    showLoading(element) {
        if (element) element.classList.add('loading');
    },

    hideLoading(element) {
        if (element) element.classList.remove('loading');
    },
    
    showError(message, container) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger alert-dismissible fade show';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        if (container) {
            container.appendChild(errorDiv);
            setTimeout(() => errorDiv.remove(), 5000);
        }
    },

    showSuccess(message, container) {
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success alert-dismissible fade show';
        successDiv.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        if (container) {
            container.appendChild(successDiv);
            setTimeout(() => successDiv.remove(), 3000);
        }
    },

    animate(element, properties, duration = 300) {
        if (!element) return Promise.resolve();
        
        return new Promise(resolve => {
            const originalTransition = element.style.transition;
            element.style.transition = `all ${duration}ms cubic-bezier(0.4, 0, 0.2, 1)`;
            
            Object.assign(element.style, properties);
            
            setTimeout(() => {
                element.style.transition = originalTransition;
                resolve();
            }, duration);
        });
    }
};
