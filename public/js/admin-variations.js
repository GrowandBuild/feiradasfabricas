/**
 * Admin Variations - Simplificado e Funcional
 */

(function() {
    'use strict';

    const CONFIG = {
        API: {
            bulkAdd: '/admin/products/{id}/variations/bulk-add'
        },
        SELECTORS: {
            modal: '#variationsModal',
            productId: '#variationsProductId',
            attributesContainer: '#attributesContainer',
            generateBtn: '#simpleGenerateBtn',
            previewBtn: '#simplePreviewBtn',
            resultDiv: '#simpleResult',
            spinner: '#simpleSpinner',
            generatorContent: '#generator'
        }
    };

    const Utils = {
        $(selector) {
            return document.querySelector(selector);
        },

        $$(selector) {
            return Array.from(document.querySelectorAll(selector));
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
            errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            
            if (container) {
                container.appendChild(errorDiv);
                setTimeout(() => errorDiv.remove(), 5000);
            }
        },

        showSuccess(message, container) {
            const successDiv = document.createElement('div');
            successDiv.className = 'alert alert-success alert-dismissible fade show';
            successDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i>' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            
            if (container) {
                container.appendChild(successDiv);
                setTimeout(() => successDiv.remove(), 3000);
            }
        }
    };

    const AttributeManager = {
        addAttribute() {
            const container = Utils.$(CONFIG.SELECTORS.attributesContainer);
            if (!container) return;

            const attributeId = Date.now();
            const attributeHTML = '<div class="attribute-row" data-attribute-id="' + attributeId + '"><div class="card border-0 shadow-sm rounded-3"><div class="card-body p-3"><div class="row g-3 align-items-center"><div class="col-md-4"><label class="form-label small fw-medium text-muted">Nome do Atributo</label><input type="text" class="form-control form-control-lg border-0 bg-light attribute-name" placeholder="Ex: Tamanho, Cor, Material"></div><div class="col-md-6"><label class="form-label small fw-medium text-muted">Valores (separados por vírgula)</label><input type="text" class="form-control form-control-lg border-0 bg-light attribute-values" placeholder="Ex: P, M, G, GG"></div><div class="col-md-2"><label class="form-label small fw-medium text-muted d-block">Ações</label><button type="button" class="btn btn-outline-danger btn-sm w-100 rounded-pill" onclick="removeAttribute(this)"><i class="bi bi-trash"></i></button></div></div></div></div></div>';
            
            container.insertAdjacentHTML('beforeend', attributeHTML);
        },

        removeAttribute(button) {
            const attributeRow = button.closest('.attribute-row');
            if (attributeRow) {
                attributeRow.remove();
            }
        },

        collectAttributes() {
            const rows = Utils.$$('.attribute-row');
            const attributes = [];

            rows.forEach(row => {
                const nameInput = row.querySelector('.attribute-name');
                const valuesInput = row.querySelector('.attribute-values');
                
                if (nameInput && valuesInput && nameInput.value.trim()) {
                    const values = valuesInput.value.split(',').map(v => v.trim()).filter(v => v.length > 0);
                    
                    if (values.length > 0) {
                        attributes.push({
                            name: nameInput.value.trim(),
                            values: values
                        });
                    }
                }
            });

            return attributes;
        },

        generateCombinations(attributes) {
            if (attributes.length === 0) return [];

            const cartesian = (arrays) => {
                if (arrays.length === 0) return [[]];
                if (arrays.length === 1) return arrays[0].map(item => [item]);
                
                const [first, ...rest] = arrays;
                const combinations = cartesian(rest);
                
                return first.flatMap(item => combinations.map(combination => [item, ...combination]));
            };

            const valueArrays = attributes.map(attr => attr.values);
            const combinations = cartesian(valueArrays);
            
            return combinations.map(combination => {
                const variation = {};
                attributes.forEach((attr, index) => {
                    variation[attr.name.toLowerCase()] = combination[index];
                });
                return variation;
            });
        }
    };

    const VariationGenerator = {
        async handleGenerate() {
            const attributes = AttributeManager.collectAttributes();
            
            if (attributes.length === 0) {
                Utils.showError('Adicione pelo menos um atributo para gerar variações.', Utils.$(CONFIG.SELECTORS.generatorContent));
                return;
            }

            const price = this.getBasePrice();
            const stock = this.getBaseStock();

            if (!price || price <= 0) {
                Utils.showError('Informe um preço base válido.', Utils.$(CONFIG.SELECTORS.generatorContent));
                return;
            }

            const combinations = AttributeManager.generateCombinations(attributes);
            
            if (combinations.length === 0) {
                Utils.showError('Não foi possível gerar combinações com os atributos informados.', Utils.$(CONFIG.SELECTORS.generatorContent));
                return;
            }

            const generateBtn = Utils.$(CONFIG.SELECTORS.generateBtn);
            const spinner = Utils.$(CONFIG.SELECTORS.spinner);
            
            if (generateBtn) generateBtn.disabled = true;
            if (spinner) spinner.classList.remove('d-none');
            
            try {
                const variations = combinations.map(combination => ({
                    ...combination,
                    price: price,
                    stock_quantity: stock,
                    in_stock: stock > 0,
                    is_active: true
                }));

                await this.saveVariations(variations);
                
                Utils.showSuccess(variations.length + ' variações geradas com sucesso!', Utils.$(CONFIG.SELECTORS.generatorContent));
                
                this.clearForm();
                
            } catch (error) {
                console.error('Error generating variations:', error);
                Utils.showError('Erro ao gerar variações: ' + error.message, Utils.$(CONFIG.SELECTORS.generatorContent));
            } finally {
                if (generateBtn) generateBtn.disabled = false;
                if (spinner) spinner.classList.add('d-none');
            }
        },

        async saveVariations(variations) {
            const productId = this.getProductId();
            if (!productId) throw new Error('Product ID not found');

            const url = CONFIG.API.bulkAdd.replace('{id}', productId);
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    combinations: variations
                })
            });

            if (!response.ok) {
                throw new Error('HTTP ' + response.status + ': ' + response.statusText);
            }

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error saving variations');
            }

            return data;
        },

        getBasePrice() {
            const input = Utils.$('#priceInput');
            const value = input ? parseFloat(input.value.replace(/[^\d.,]/g, '').replace(',', '.')) : 0;
            return isNaN(value) ? 0 : value;
        },

        getBaseStock() {
            const input = Utils.$('#stockInput');
            const value = input ? parseInt(input.value) : 0;
            return isNaN(value) ? 0 : value;
        },

        getProductId() {
            const input = Utils.$(CONFIG.SELECTORS.productId);
            return input ? input.value : null;
        },

        getCSRFToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : '';
        },

        clearForm() {
            const container = Utils.$(CONFIG.SELECTORS.attributesContainer);
            if (container) {
                container.innerHTML = '';
            }
            
            const priceInput = Utils.$('#priceInput');
            const stockInput = Utils.$('#stockInput');
            
            if (priceInput) priceInput.value = '';
            if (stockInput) stockInput.value = '10';
            
            const resultDiv = Utils.$(CONFIG.SELECTORS.resultDiv);
            if (resultDiv) resultDiv.innerHTML = '';
            
            AttributeManager.addAttribute();
        }
    };

    const VariationsManager = {
        init() {
            this.setupEventListeners();
            AttributeManager.addAttribute();
        },

        setupEventListeners() {
            const generateBtn = Utils.$(CONFIG.SELECTORS.generateBtn);
            if (generateBtn) {
                generateBtn.addEventListener('click', () => VariationGenerator.handleGenerate());
            }
        },

        addAttribute() {
            AttributeManager.addAttribute();
        },

        removeAttribute(button) {
            AttributeManager.removeAttribute(button);
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => VariationsManager.init(), 500);
        });
    } else {
        setTimeout(() => VariationsManager.init(), 500);
    }

    window.VariationsManager = VariationsManager;
    window.addAttribute = () => VariationsManager.addAttribute();
    window.removeAttribute = (btn) => VariationsManager.removeAttribute(btn);

})();
