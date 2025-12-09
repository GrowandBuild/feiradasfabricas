/**
 * Gerador Simples de Variações - Formulário Amigável e Flexível
 * Permite qualquer tipo de atributo para qualquer produto
 */

class SimpleVariationGenerator {
    constructor() {
        this.productId = null;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        this.init();
    }

    init() {
        // Event listeners do formulário simples
        document.getElementById('simpleGenerateBtn')?.addEventListener('click', () => this.generateVariations());
        document.getElementById('simplePreviewBtn')?.addEventListener('click', () => this.previewVariations());
    }

    generateCombinations() {
        const attributes = this.getAttributes();
        const price = parseFloat(document.getElementById('priceInput')?.value || '0');
        const stock = parseInt(document.getElementById('stockInput')?.value || '0');

        // Arrays de valores por atributo
        const arrays = attributes.map(attr => {
            const values = attr.values.map(value => ({ 
                [attr.name]: value 
            }));
            return values;
        });

        // Gerar combinações (produto cartesiano)
        const combinations = this.cartesianProduct(arrays);

        return combinations.map(combo => {
            const variation = {};
            
            // Garantir que combo seja sempre um array
            const items = Array.isArray(combo) ? combo : [combo];
            
            // Processar cada item (que deve ser um objeto)
            items.forEach(item => {
                if (item && typeof item === 'object') {
                    Object.assign(variation, item);
                } else {
                    console.warn('Item inválido nas combinações:', item);
                }
            });
            
            // Adicionar campos comuns
            if (price > 0) variation.price = price;
            if (stock > 0) variation.stock_quantity = stock;
            
            return variation;
        });
    }

    getAttributes() {
        const attributes = [];
        const rows = document.querySelectorAll('.attribute-row');
        
        rows.forEach(row => {
            const nameInput = row.querySelector('.attribute-name');
            const valuesInput = row.querySelector('.attribute-values');
            
            if (nameInput && valuesInput && nameInput.value.trim() && valuesInput.value.trim()) {
                const name = this.normalizeAttributeName(nameInput.value.trim());
                const values = valuesInput.value
                    .split(',')
                    .map(v => v.trim())
                    .filter(v => v.length > 0);
                
                if (values.length > 0) {
                    attributes.push({ name, values });
                }
            }
        });
        
        return attributes;
    }

    normalizeAttributeName(name) {
        // Converter para formato amigável (slug)
        return name.toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '_');
    }

    cartesianProduct(arrays) {
        if (arrays.length === 0) return [[]];
        if (arrays.length === 1) {
            // Para um único array, retornar cada item como array
            return arrays[0].map(item => [item]);
        }
        
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

    async generateVariations() {
        const combinations = this.generateCombinations();
        
        if (combinations.length === 0) {
            this.showMessage('Preencha pelo menos um atributo com valores', 'error');
            return;
        }

        const productId = document.getElementById('variationsProductId')?.value;
        if (!productId) {
            this.showMessage('Produto não identificado', 'error');
            return;
        }

        this.showSpinner(true);

        try {
            const response = await fetch(`/admin/products/${productId}/variations/bulk-add`, {
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
                this.showMessage(`${combinations.length} variações criadas com sucesso!`, 'success');
                this.clearForm();
                
                // Recarregar variações existentes se houver listener
                if (window.variationsManager) {
                    window.variationsManager.loadData();
                }
            } else {
                this.showMessage('Erro: ' + (data.message || 'Erro desconhecido'), 'error');
            }
        } catch (error) {
            console.error('Erro ao gerar variações:', error);
            this.showMessage('Erro ao gerar variações', 'error');
        } finally {
            this.showSpinner(false);
        }
    }

    previewVariations() {
        const combinations = this.generateCombinations();
        
        if (combinations.length === 0) {
            this.showMessage('Preencha pelo menos um atributo para visualizar', 'error');
            return;
        }

        const resultDiv = document.getElementById('simpleResult');
        
        if (combinations.length > 50) {
            resultDiv.innerHTML = `
                <div class="alert alert-warning">
                    <strong>Atenção:</strong> Serão criadas ${combinations.length} variações.
                    Isso pode afetar o desempenho. Deseja continuar?
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success" onclick="simpleGenerator.generateVariations()">
                        Sim, criar ${combinations.length} variações
                    </button>
                    <button class="btn btn-secondary" onclick="this.parentElement.parentElement.innerHTML=''">
                        Cancelar
                    </button>
                </div>
            `;
        } else {
            const preview = combinations.map((combo, index) => {
                const attrs = [];
                Object.keys(combo).forEach(key => {
                    if (key !== 'price' && key !== 'stock_quantity') {
                        attrs.push(`${this.formatAttributeName(key)}: ${combo[key]}`);
                    }
                });
                if (combo.price) attrs.push(`Preço: R$${combo.price}`);
                if (combo.stock_quantity) attrs.push(`Estoque: ${combo.stock_quantity}`);
                
                return `<div class="border-bottom pb-2 mb-2">
                    <strong>#${index + 1}</strong> - ${attrs.join(' | ')}
                </div>`;
            }).join('');

            resultDiv.innerHTML = `
                <div class="alert alert-info">
                    <strong>Pré-visualização:</strong> ${combinations.length} variações serão criadas
                </div>
                <div class="border p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                    ${preview}
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button class="btn btn-success" onclick="simpleGenerator.generateVariations()">
                        Confirmar Criação
                    </button>
                    <button class="btn btn-secondary" onclick="this.parentElement.parentElement.innerHTML=''">
                        Fechar
                    </button>
                </div>
            `;
        }
    }

    formatAttributeName(key) {
        const names = {
            'cor': 'Cor',
            'color': 'Cor',
            'tamanho': 'Tamanho',
            'size': 'Tamanho',
            'material': 'Material',
            'ram': 'RAM',
            'storage': 'Armazenamento',
            'armazenamento': 'Armazenamento',
            'voltagem': 'Voltagem',
            'peso': 'Peso',
            'modelo': 'Modelo'
        };
        return names[key] || key.charAt(0).toUpperCase() + key.slice(1);
    }

    clearForm() {
        // Limpar apenas os campos de preço e estoque
        document.getElementById('priceInput').value = '';
        document.getElementById('stockInput').value = '10';
        document.getElementById('simpleResult').innerHTML = '';
        
        // Limpar atributos mantendo apenas uma linha vazia com a estrutura correta
        const container = document.getElementById('attributesContainer');
        container.innerHTML = `
            <div class="attribute-row">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label small fw-medium text-muted">Nome do Atributo</label>
                                <input type="text" class="form-control form-control-lg border-0 bg-light attribute-name" placeholder="Ex: Tamanho, Cor, Material">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-medium text-muted">Valores (separados por vírgula)</label>
                                <input type="text" class="form-control form-control-lg border-0 bg-light attribute-values" placeholder="Ex: P, M, G, GG">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-medium text-muted d-block">Ações</label>
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 rounded-pill" onclick="removeAttribute(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    showSpinner(show) {
        const spinner = document.getElementById('simpleSpinner');
        const button = document.getElementById('simpleGenerateBtn');
        
        if (show) {
            spinner.classList.remove('d-none');
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-gear-wide-connected me-1"></i> Gerando...';
        } else {
            spinner.classList.add('d-none');
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-gear-wide-connected me-1"></i> Gerar Variações';
        }
    }

    showMessage(message, type = 'info') {
        const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const resultDiv = document.getElementById('simpleResult');
        resultDiv.innerHTML = '';
        resultDiv.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
}

// Debug: verificar se as funções foram registradas
console.log('SimpleVariationsJS carregado, funções disponíveis:', {
    addAttribute: typeof window.addAttribute,
    removeAttribute: typeof window.removeAttribute
});

// Inicializar quando o DOM estiver pronto
let simpleGenerator;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM loaded, initializing simple generator');
        simpleGenerator = new SimpleVariationGenerator();
        window.simpleGenerator = simpleGenerator;
        
        // Garantir que o primeiro atributo seja adicionado ao abrir o modal
        setTimeout(() => {
            const container = document.getElementById('attributesContainer');
            if (container && container.children.length === 0) {
                addAttribute();
                console.log('Primeiro atributo adicionado automaticamente no modal');
            }
        }, 100);
    });
} else {
    console.log('DOM already loaded, initializing simple generator');
    simpleGenerator = new SimpleVariationGenerator();
    window.simpleGenerator = simpleGenerator;
    
    // Garantir que o primeiro atributo seja adicionado
    setTimeout(() => {
        const container = document.getElementById('attributesContainer');
        if (container && container.children.length === 0) {
            addAttribute();
            console.log('Primeiro atributo adicionado automaticamente no modal');
        }
    }, 100);
}

// Também adicionar atributo quando o modal for aberto
document.addEventListener('shown.bs.modal', function (e) {
    if (e.target.id === 'variationsModal') {
        const container = document.getElementById('attributesContainer');
        if (container && container.children.length === 0) {
            addAttribute();
            console.log('Primeiro atributo adicionado ao abrir modal');
        }
    }
});
