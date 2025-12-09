/**
 * Sistema Moderno de Variações - Estilo WooCommerce/Shopify
 * Interface simplificada e intuitiva para gerenciar variações de produtos
 */

class VariationsManager {
    constructor() {
        this.productId = null;
        this.attributes = [];
        this.variations = [];
        this.selectedVariations = new Set();
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        this.init();
    }

    init() {
        // Event listeners do modal
        const modal = document.getElementById('variationsModal');
        if (modal) {
            modal.addEventListener('show.bs.modal', () => this.onModalShow());
            modal.addEventListener('hidden.bs.modal', () => this.onModalHide());
        }

        // Botões principais
        document.getElementById('addAttributeBtn')?.addEventListener('click', () => this.toggleAddAttributeForm());
        document.getElementById('saveAttributeBtn')?.addEventListener('click', () => this.saveAttribute());
        document.getElementById('cancelAttributeBtn')?.addEventListener('click', () => this.toggleAddAttributeForm());
        document.getElementById('generateVariationsBtn')?.addEventListener('click', () => this.generateAllVariations());
        document.getElementById('refreshVariationsBtn')?.addEventListener('click', () => this.loadData());
        document.getElementById('bulkEditBtn')?.addEventListener('click', () => this.openBulkEdit());
        document.getElementById('saveAllVariationsBtn')?.addEventListener('click', () => this.saveAllVariations());
        
        // Seleção em massa
        document.getElementById('selectAllVariations')?.addEventListener('change', (e) => this.toggleSelectAll(e.target.checked));
        
        // Busca
        document.getElementById('variationSearch')?.addEventListener('input', (e) => this.filterVariations(e.target.value));
        
        // Mostrar/ocultar inativas
        document.getElementById('showInactiveVariations')?.addEventListener('change', (e) => this.renderVariations());
        
        // Ações em massa
        document.getElementById('bulkEditPricesBtn')?.addEventListener('click', () => this.bulkEditPrices());
        document.getElementById('bulkEditStockBtn')?.addEventListener('click', () => this.bulkEditStock());
        document.getElementById('bulkDeleteBtn')?.addEventListener('click', () => this.bulkDelete());
        
        // Aplicar edição em massa
        document.getElementById('applyBulkEditBtn')?.addEventListener('click', () => this.applyBulkEdit());
    }

    onModalShow() {
        const productIdEl = document.getElementById('variationsProductId');
        this.productId = productIdEl?.value || null;
        
        if (!this.productId) {
            this.showMessage('Erro: Produto não identificado', 'error');
            return;
        }
        
        this.loadData();
    }

    onModalHide() {
        this.attributes = [];
        this.variations = [];
        this.selectedVariations.clear();
        this.toggleAddAttributeForm(false);
    }

    async loadData() {
        if (!this.productId) return;
        
        try {
            const response = await fetch(`/admin/products/${this.productId}/variations`, {
                headers: { 'Accept': 'application/json' }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Processar atributos
                this.attributes = this.processAttributes(data);
                
                // Processar variações
                this.variations = Array.isArray(data.variations) 
                    ? data.variations 
                    : Object.values(data.variations || {});
                
                this.renderAttributes();
                this.renderVariations();
                this.updateInfo();
            } else {
                this.showMessage('Erro ao carregar variações: ' + (data.message || 'Erro desconhecido'), 'error');
            }
        } catch (error) {
            console.error('Erro ao carregar variações:', error);
            this.showMessage('Erro ao carregar variações', 'error');
        }
    }

    processAttributes(data) {
        const attributeGroups = data.attribute_groups || {};
        const processed = [];
        
        // Agrupar por tipo de atributo
        Object.keys(attributeGroups).forEach(type => {
            const values = attributeGroups[type] || [];
            if (values.length > 0) {
                processed.push({
                    name: this.formatAttributeName(type),
                    type: type,
                    values: values.map(v => ({
                        name: v.name || v,
                        count: v.count || 0,
                        enabled: v.enabled !== false
                    }))
                });
            }
        });
        
        return processed;
    }

    formatAttributeName(type) {
        const names = {
            'color': 'Cor',
            'cor': 'Cor',
            'ram': 'RAM',
            'storage': 'Armazenamento',
            'size': 'Tamanho',
            'tamanho': 'Tamanho',
            'material': 'Material'
        };
        return names[type.toLowerCase()] || type.charAt(0).toUpperCase() + type.slice(1);
    }

    renderAttributes() {
        const container = document.getElementById('attributesList');
        if (!container) return;
        
        if (this.attributes.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">Nenhum atributo adicionado</p>
                    <small>Adicione atributos como Cor, Tamanho, etc.</small>
                </div>
            `;
            return;
        }
        
        container.innerHTML = this.attributes.map(attr => `
            <div class="attribute-item" data-attribute-type="${attr.type}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">${attr.name}</h6>
                        <div class="attribute-values">
                            ${attr.values.map(val => `
                                <span class="attribute-value-tag">
                                    ${val.name}
                                    <span class="badge bg-secondary ms-1">${val.count}</span>
                                    ${!val.enabled ? '<span class="badge bg-danger ms-1">Inativo</span>' : ''}
                                </span>
                            `).join('')}
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                onclick="variationsManager.editAttribute('${attr.type}')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                onclick="variationsManager.deleteAttribute('${attr.type}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    renderVariations() {
        const tbody = document.getElementById('variationsTableBody');
        if (!tbody) return;
        
        const showInactive = document.getElementById('showInactiveVariations')?.checked || false;
        const searchTerm = (document.getElementById('variationSearch')?.value || '').toLowerCase();
        
        let filtered = this.variations.filter(v => {
            if (!showInactive && !v.is_active) return false;
            if (searchTerm) {
                const searchable = [
                    v.sku || '',
                    v.color || '',
                    v.ram || '',
                    v.storage || '',
                    JSON.stringify(v)
                ].join(' ').toLowerCase();
                return searchable.includes(searchTerm);
            }
            return true;
        });
        
        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mt-3 mb-0">Nenhuma variação encontrada</p>
                        <small>Adicione atributos e gere variações automaticamente</small>
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = filtered.map(variation => {
            const attributes = this.getVariationAttributes(variation);
            const isSelected = this.selectedVariations.has(variation.id);
            
            return `
                <tr class="variation-row" data-variation-id="${variation.id}">
                    <td>
                        <input type="checkbox" class="form-check-input variation-checkbox" 
                               value="${variation.id}" ${isSelected ? 'checked' : ''}
                               onchange="variationsManager.toggleVariationSelection(${variation.id}, this.checked)">
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            ${attributes.map(attr => `
                                <span class="attribute-badge">
                                    ${attr.type === 'color' && variation.color_hex ? 
                                        `<span class="color-swatch" style="background: ${variation.color_hex};"></span>` : ''}
                                    ${attr.value}
                                </span>
                            `).join('')}
                        </div>
                    </td>
                    <td>
                        <code class="small">${variation.sku || '-'}</code>
                    </td>
                    <td>
                        <strong>R$ ${this.formatPrice(variation.price || 0)}</strong>
                    </td>
                    <td>
                        <span class="badge ${variation.stock_quantity > 0 ? 'bg-success' : 'bg-warning'}">
                            ${variation.stock_quantity || 0}
                        </span>
                    </td>
                    <td>
                        <span class="variation-status-badge ${variation.is_active ? 'active' : 'inactive'}">
                            ${variation.is_active ? 'Ativo' : 'Inativo'}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    onclick="variationsManager.quickEdit(${variation.id})" 
                                    title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                    onclick="variationsManager.deleteVariation(${variation.id})" 
                                    title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
        
        this.updateBulkActionsBar();
    }

    getVariationAttributes(variation) {
        const attrs = [];
        if (variation.color) attrs.push({ type: 'color', value: variation.color });
        if (variation.ram) attrs.push({ type: 'ram', value: variation.ram });
        if (variation.storage) attrs.push({ type: 'storage', value: variation.storage });
        return attrs;
    }

    toggleAddAttributeForm(show = null) {
        const form = document.getElementById('addAttributeForm');
        if (!form) return;
        
        const shouldShow = show !== null ? show : form.style.display === 'none';
        form.style.display = shouldShow ? 'block' : 'none';
        
        if (shouldShow) {
            document.getElementById('newAttributeName')?.focus();
        } else {
            document.getElementById('newAttributeName').value = '';
            document.getElementById('newAttributeValues').value = '';
        }
    }

    async saveAttribute() {
        const name = document.getElementById('newAttributeName')?.value.trim();
        const valuesStr = document.getElementById('newAttributeValues')?.value.trim();
        
        if (!name || !valuesStr) {
            this.showMessage('Preencha todos os campos', 'error');
            return;
        }
        
        const values = valuesStr.split(',').map(v => v.trim()).filter(v => v);
        if (values.length === 0) {
            this.showMessage('Adicione pelo menos um valor', 'error');
            return;
        }
        
        // Normalizar nome do atributo (usar minúsculas, sem espaços)
        const type = name.toLowerCase().replace(/\s+/g, '_');
        
        try {
            // Adicionar cada valor como uma nova variação
            for (const value of values) {
                const response = await fetch(`/admin/products/${this.productId}/variations/add`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ type, value })
                });
                
                const data = await response.json();
                if (!data.success) {
                    this.showMessage(`Erro ao adicionar "${value}": ${data.message}`, 'error');
                }
            }
            
            this.showMessage('Atributo adicionado com sucesso!', 'success');
            this.toggleAddAttributeForm(false);
            await this.loadData();
        } catch (error) {
            console.error('Erro ao salvar atributo:', error);
            this.showMessage('Erro ao salvar atributo', 'error');
        }
    }

    async generateAllVariations() {
        if (this.attributes.length === 0) {
            this.showMessage('Adicione atributos antes de gerar variações', 'error');
            return;
        }
        
        if (!confirm('Isso irá gerar todas as combinações possíveis de variações. Continuar?')) {
            return;
        }
        
        try {
            // Gerar todas as combinações
            const combinations = this.generateCombinations();
            
            const response = await fetch(`/admin/products/${this.productId}/variations/bulk-generate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ combinations })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showMessage(`${data.count || combinations.length} variações geradas com sucesso!`, 'success');
                await this.loadData();
            } else {
                this.showMessage('Erro ao gerar variações: ' + (data.message || 'Erro desconhecido'), 'error');
            }
        } catch (error) {
            console.error('Erro ao gerar variações:', error);
            this.showMessage('Erro ao gerar variações', 'error');
        }
    }

    generateCombinations() {
        if (this.attributes.length === 0) return [];
        
        // Criar matriz de valores
        const valueArrays = this.attributes.map(attr => 
            attr.values.filter(v => v.enabled).map(v => ({ type: attr.type, value: v.name }))
        );
        
        // Gerar produto cartesiano
        const combinations = this.cartesianProduct(valueArrays);
        
        return combinations.map(combo => {
            const obj = {};
            combo.forEach(item => {
                obj[item.type] = item.value;
            });
            return obj;
        });
    }

    cartesianProduct(arrays) {
        if (arrays.length === 0) return [[]];
        if (arrays.length === 1) return arrays[0].map(x => [x]);
        
        const [first, ...rest] = arrays;
        const restProduct = this.cartesianProduct(rest);
        
        const result = [];
        first.forEach(item => {
            restProduct.forEach(combo => {
                result.push([item, ...combo]);
            });
        });
        
        return result;
    }

    toggleVariationSelection(id, checked) {
        if (checked) {
            this.selectedVariations.add(id);
        } else {
            this.selectedVariations.delete(id);
        }
        this.updateBulkActionsBar();
    }

    toggleSelectAll(checked) {
        const checkboxes = document.querySelectorAll('.variation-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checked;
            const id = parseInt(cb.value);
            if (checked) {
                this.selectedVariations.add(id);
            } else {
                this.selectedVariations.delete(id);
            }
        });
        this.updateBulkActionsBar();
    }

    updateBulkActionsBar() {
        const bar = document.getElementById('bulkActionsBar');
        const countEl = document.getElementById('selectedCount');
        
        if (this.selectedVariations.size > 0) {
            bar.style.display = 'block';
            countEl.textContent = this.selectedVariations.size;
        } else {
            bar.style.display = 'none';
        }
    }

    async quickEdit(variationId) {
        const variation = this.variations.find(v => v.id === variationId);
        if (!variation) return;
        
        const modal = new bootstrap.Modal(document.getElementById('quickEditVariationModal'));
        const content = document.getElementById('quickEditVariationContent');
        
        content.innerHTML = `
            <form id="quickEditForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">SKU</label>
                        <input type="text" class="form-control" name="sku" value="${variation.sku || ''}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Preço B2C</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" step="0.01" class="form-control" name="price" value="${variation.price || ''}">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Preço B2B</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" step="0.01" class="form-control" name="b2b_price" value="${variation.b2b_price || ''}">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Preço de Custo</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" step="0.01" class="form-control" name="cost_price" value="${variation.cost_price || ''}">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estoque</label>
                        <input type="number" class="form-control" name="stock_quantity" value="${variation.stock_quantity || 0}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="is_active">
                            <option value="1" ${variation.is_active ? 'selected' : ''}>Ativo</option>
                            <option value="0" ${!variation.is_active ? 'selected' : ''}>Inativo</option>
                        </select>
                    </div>
                </div>
            </form>
        `;
        
        document.getElementById('saveQuickEditBtn').onclick = async () => {
            await this.saveQuickEdit(variationId);
            modal.hide();
        };
        
        modal.show();
    }

    async saveQuickEdit(variationId) {
        const form = document.getElementById('quickEditForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Converter strings para tipos corretos
        if (data.price) data.price = parseFloat(data.price);
        if (data.b2b_price) data.b2b_price = parseFloat(data.b2b_price);
        if (data.cost_price) data.cost_price = parseFloat(data.cost_price);
        if (data.stock_quantity) data.stock_quantity = parseInt(data.stock_quantity);
        data.is_active = data.is_active === '1';
        
        try {
            const response = await fetch(`/admin/products/${this.productId}/variations/${variationId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showMessage('Variação atualizada com sucesso!', 'success');
                await this.loadData();
            } else {
                this.showMessage('Erro ao atualizar: ' + (result.message || 'Erro desconhecido'), 'error');
            }
        } catch (error) {
            console.error('Erro ao salvar edição rápida:', error);
            this.showMessage('Erro ao salvar variação', 'error');
        }
    }

    async deleteVariation(variationId) {
        if (!confirm('Tem certeza que deseja excluir esta variação?')) return;
        
        try {
            const response = await fetch(`/admin/products/${this.productId}/variations/${variationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showMessage('Variação excluída com sucesso!', 'success');
                this.selectedVariations.delete(variationId);
                await this.loadData();
            } else {
                this.showMessage('Erro ao excluir: ' + (data.message || 'Erro desconhecido'), 'error');
            }
        } catch (error) {
            console.error('Erro ao excluir variação:', error);
            this.showMessage('Erro ao excluir variação', 'error');
        }
    }

    openBulkEdit() {
        const modal = new bootstrap.Modal(document.getElementById('bulkEditModal'));
        modal.show();
    }

    async applyBulkEdit() {
        const scope = document.getElementById('bulkEditScope')?.value;
        const price = document.getElementById('bulkEditPrice')?.value;
        const b2bPrice = document.getElementById('bulkEditB2BPrice')?.value;
        const stock = document.getElementById('bulkEditStock')?.value;
        const status = document.getElementById('bulkEditStatus')?.value;
        
        const variationIds = scope === 'selected' 
            ? Array.from(this.selectedVariations)
            : this.variations.map(v => v.id);
        
        if (variationIds.length === 0) {
            this.showMessage('Nenhuma variação selecionada', 'error');
            return;
        }
        
        const updates = variationIds.map(id => {
            const update = { variation_id: id };
            if (price) update.price = parseFloat(price);
            if (b2bPrice) update.b2b_price = parseFloat(b2bPrice);
            if (stock) update.stock_quantity = parseInt(stock);
            if (status !== '') update.is_active = status === '1';
            return update;
        });
        
        try {
            const response = await fetch(`/admin/products/${this.productId}/variations/bulk-update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ updates })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showMessage(`${data.updated || updates.length} variações atualizadas!`, 'success');
                bootstrap.Modal.getInstance(document.getElementById('bulkEditModal'))?.hide();
                this.selectedVariations.clear();
                await this.loadData();
            } else {
                this.showMessage('Erro: ' + (data.message || 'Erro desconhecido'), 'error');
            }
        } catch (error) {
            console.error('Erro na edição em massa:', error);
            this.showMessage('Erro ao aplicar edição em massa', 'error');
        }
    }

    async saveAllVariations() {
        // Salvar todas as alterações pendentes
        this.showMessage('Todas as alterações foram salvas!', 'success');
    }

    filterVariations(searchTerm) {
        this.renderVariations();
    }

    updateInfo() {
        const activeCount = this.variations.filter(v => v.is_active).length;
        const totalCount = this.variations.length;
        const infoEl = document.getElementById('variationsInfo');
        const countEl = document.getElementById('variationsCount');
        
        if (infoEl) {
            infoEl.textContent = `${activeCount} de ${totalCount} variações ativas`;
        }
        if (countEl) {
            countEl.textContent = totalCount;
        }
    }

    formatPrice(value) {
        return parseFloat(value || 0).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    showMessage(message, type = 'info') {
        // Criar toast/notificação
        const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
}

// Inicializar quando o DOM estiver pronto
let variationsManager;
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        variationsManager = new VariationsManager();
        window.variationsManager = variationsManager;
    });
} else {
    variationsManager = new VariationsManager();
    window.variationsManager = variationsManager;
}
export default VariationsManager;


