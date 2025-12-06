<!-- Modal de Gerenciamento de Variações -->
<div class="modal fade" id="variationsModal" tabindex="-1" aria-labelledby="variationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="variationsModalLabel">
                    <i class="bi bi-list-ul me-2"></i>Gerenciar Variações
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="variationsProductId" value="">

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="mb-0">Gerenciar Variações</h6>
                        <small class="text-muted">— selecione e edite rapidamente</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input id="variationSearch" class="form-control form-control-sm" type="search" placeholder="Pesquisar valor..." style="min-width:220px;">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshVariationsBtn">Atualizar</button>
                    </div>
                </div>

                <div class="row gx-3">
                    <div class="col-md-4">
                        <div class="border rounded p-3 mb-3">
                            <label class="form-label fw-bold">Adicionar</label>
                            <div class="mb-2">
                                <input type="text" class="form-control form-control-sm" id="newVariationValue" placeholder="Ex: Preto / 8GB / 128GB">
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-sm" type="button" id="addGenericBtn">Adicionar como novo valor</button>
                                <button class="btn btn-outline-secondary btn-sm" type="button" id="openVariationsManagerBtn">Abrir Gerenciador Completo</button>
                            </div>
                            <hr />
                            <label class="form-label fw-bold">Filtros</label>
                            <div class="d-flex flex-column gap-2">
                                <select id="filterType" class="form-select form-select-sm">
                                    <option value="">Todos os tipos</option>
                                    <option value="color">Cores</option>
                                    <option value="ram">RAM</option>
                                    <option value="storage">Armazenamento</option>
                                </select>
                                <div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="onlyActiveToggle">
                                        <label class="form-check-label small" for="onlyActiveToggle">Apenas ativos</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded p-3" style="max-height:420px; overflow:auto;">
                            <label class="form-label fw-bold">Ações em Massa</label>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm" type="button" id="bulkAddSelectedBtn">Adicionar valores selecionados como variações</button>
                                <button class="btn btn-outline-danger btn-sm" type="button" id="bulkDeleteInactiveBtn">Excluir Desativadas</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <ul class="nav nav-tabs mb-3" id="variationsTabs" role="tablist">
                            <li class="nav-item" role="presentation"><button class="nav-link active" id="colors-tab" data-bs-toggle="tab" data-bs-target="#colors" type="button" role="tab"><i class="bi bi-palette me-1"></i>Cores</button></li>
                            <li class="nav-item" role="presentation"><button class="nav-link" id="rams-tab" data-bs-toggle="tab" data-bs-target="#rams" type="button" role="tab"><i class="bi bi-memory me-1"></i>RAM</button></li>
                            <li class="nav-item" role="presentation"><button class="nav-link" id="storages-tab" data-bs-toggle="tab" data-bs-target="#storages" type="button" role="tab"><i class="bi bi-hdd me-1"></i>Armazenamento</button></li>
                            <li class="nav-item" role="presentation"><button class="nav-link" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock" type="button" role="tab"><i class="bi bi-box-seam me-1"></i>Estoque</button></li>
                        </ul>

                        <div class="tab-content" id="variationsTabContent">
                            <div class="tab-pane fade show active" id="colors" role="tabpanel">
                                <div id="colorsList" class="row row-cols-1 row-cols-md-2 g-2 align-items-start">
                                    <div class="col"><p class="text-center text-muted my-4">Carregando...</p></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="rams" role="tabpanel">
                                <div id="ramsList" class="row g-2 row-cols-1 row-cols-md-2">
                                    <div class="col"><p class="text-center text-muted my-4">Carregando...</p></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="storages" role="tabpanel">
                                <div id="storagesList" class="row g-2 row-cols-1 row-cols-md-2">
                                    <div class="col"><p class="text-center text-muted my-4">Carregando...</p></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="stock" role="tabpanel">
                                <div id="stockList">
                                    <p class="text-muted text-center my-4">Carregando...</p>
                                </div>
                            </div>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small text-muted mb-1">Preço B2C</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-cart"></i></span>
                    <input type="text" class="form-control variation-price" data-variation-id="${variation.id}" value="${variation.price ?? ''}">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label small text-muted mb-1">Preço B2B</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                    <input type="text" class="form-control variation-b2b" data-variation-id="${variation.id}" value="${variation.b2b_price ?? ''}">
                </div>
            </div>
        </div>
    `;
    return div;
}

function toggleVariationType(productId, type, value, enabled, el) {
    if (!productId || !type || !value) {
        showVariationMessage('error', 'Erro: Dados inválidos');
        return;
    }

    // Element that initiated the toggle (if provided)
    const toggle = el || null;
    const originalState = toggle ? toggle.checked : !enabled;
    if (toggle) toggle.disabled = true;

    fetch(`/admin/products/${productId}/variations/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ type, value, enabled })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (toggle) toggle.disabled = false;
        if (data.success) {
            // Feedback visual
            const message = data.message || 'Variação atualizada com sucesso!';
            showVariationMessage('success', message);
            // Recarregar variações para atualizar o estado
            loadVariations(productId);
        } else {
            // Reverter o toggle se falhou
            if (toggle) toggle.checked = !enabled;
            showVariationMessage('error', 'Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        if (toggle) toggle.disabled = false;
        // Reverter o toggle se falhou
        if (toggle) toggle.checked = !enabled;
        showVariationMessage('error', 'Erro ao atualizar variação. Verifique sua conexão.');
    });
}

function openColorImagesModal(colorName) {
    const productId = document.getElementById('variationsProductId').value;
    if (!productId || !colorName) {
        showVariationMessage('error', 'Erro: informações insuficientes para carregar imagens.');
        return;
    }

    currentColorBeingEdited = colorName;

    const modalTitle = document.getElementById('colorImagesModalLabel');
    if (modalTitle) {
        modalTitle.innerHTML = `<i class="bi bi-image me-2"></i>Imagens - ${colorName}`;
    }

    const grid = document.getElementById('colorImagesGrid');
    const emptyState = document.getElementById('colorImagesEmptyState');
    const saveBtn = document.getElementById('saveColorImagesBtn');

    if (!variationProductImages || variationProductImages.length === 0) {
        if (grid) grid.innerHTML = '';
        if (grid) grid.classList.add('d-none');
        if (emptyState) {
            emptyState.classList.remove('d-none');
            emptyState.innerHTML = '<div class="alert alert-info mb-0"><i class="bi bi-info-circle me-1"></i>Adicione imagens ao produto para vinculá-las às cores.</div>';
        }
        if (saveBtn) saveBtn.disabled = true;
    } else {
        if (grid) {
            grid.classList.remove('d-none');
            const selectedForColor = variationColorImagesMap[colorName] || [];
            const selectedLookup = Object.fromEntries(selectedForColor.map(value => [value, true]));

            grid.innerHTML = variationProductImages.map((imagePath, index) => {
                const imageUrl = variationProductImagesUrls[index] || '';
                const isChecked = !!selectedLookup[imagePath];
                return `
                    <div class="col-6 col-md-4 col-lg-3 mb-3">
                        <div class="card variation-image-card h-100 ${isChecked ? 'selected' : ''}" data-image-card>
                            <div class="position-relative">
                                <img src="${imageUrl}" class="card-img-top" alt="Imagem da variação" style="height: 120px; object-fit: cover;">
                                <div class="selection-overlay"><i class="bi bi-check-lg"></i></div>
                                <div class="form-check position-absolute top-0 end-0 m-2 bg-white rounded-pill px-2">
                                    <input class="form-check-input color-image-checkbox" type="checkbox" value="${imagePath}" ${isChecked ? 'checked' : ''}>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            grid.querySelectorAll('.color-image-checkbox').forEach(checkbox => {
                const card = checkbox.closest('[data-image-card]');
                if (!card) {
                    return;
                }

                checkbox.addEventListener('change', function() {
                    card.classList.toggle('selected', this.checked);
                });
            });
        }

        if (emptyState) emptyState.classList.add('d-none');
        if (saveBtn) saveBtn.disabled = false;
    }

    const modalElement = document.getElementById('colorImagesModal');
    if (modalElement) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }
}

function saveColorImages() {
    const productId = document.getElementById('variationsProductId').value;
    if (!productId || !currentColorBeingEdited) {
        showVariationMessage('error', 'Erro: nenhuma cor selecionada.');
        return;
    }

    const modalElement = document.getElementById('colorImagesModal');
    const saveBtn = document.getElementById('saveColorImagesBtn');
    const checkboxes = modalElement ? modalElement.querySelectorAll('.color-image-checkbox') : [];
    const selectedImages = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);

    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando';
    }

    fetch(`/admin/products/${productId}/variations/color-images`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            color: currentColorBeingEdited,
            images: selectedImages
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            variationColorImagesMap = data.color_images || {};
            variationColorImagesUrlsMap = data.color_images_urls || {};

            showVariationMessage('success', data.message || 'Imagens atualizadas com sucesso!');

            // Recarregar lista de variações para exibir indicadores atualizados
            loadVariations(productId);

            setTimeout(() => {
                if (modalElement) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modal.hide();
                }
            }, 300);
        } else {
            showVariationMessage('error', data.message || 'Erro ao salvar imagens da cor.');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showVariationMessage('error', 'Erro ao salvar imagens da cor.');
    })
    .finally(() => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Salvar';
        }
    });
}

function addNewVariationType(productId, type) {
    if (!productId || !type) {
        showVariationMessage('error', 'Erro: Dados inválidos');
        return;
    }
    
    const inputId = `new${type.charAt(0).toUpperCase() + type.slice(1)}`;
    const input = document.getElementById(inputId);
    if (!input) {
        showVariationMessage('error', 'Erro: Campo de entrada não encontrado');
        return;
    }
    
    const value = input.value.trim();
    
    if (!value) {
        showVariationMessage('error', 'Por favor, insira um valor');
        return;
    }
    
    // Desabilitar input e botão enquanto processa
    const addBtn = input.nextElementSibling;
    const originalBtnText = addBtn ? addBtn.innerHTML : '';
    if (addBtn) {
        addBtn.disabled = true;
        addBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Adicionando...';
    }
    input.disabled = true;
    
    fetch(`/admin/products/${productId}/variations/add`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ type, value })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        input.disabled = false;
        if (addBtn) {
            addBtn.disabled = false;
            addBtn.innerHTML = originalBtnText;
        }
        
        if (data.success) {
            input.value = '';
            showVariationMessage('success', data.message || 'Variação adicionada com sucesso!');
            // Recarregar variações para mostrar o novo item
            loadVariations(productId);
        } else {
            showVariationMessage('error', 'Erro: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        input.disabled = false;
        if (addBtn) {
            addBtn.disabled = false;
            addBtn.innerHTML = originalBtnText;
        }
        showVariationMessage('error', 'Erro ao adicionar variação. Verifique sua conexão.');
    });
}

function updateAllStock(event) {
    const productId = document.getElementById('variationsProductId').value;
    if (!productId) {
        showVariationMessage('error', 'Erro: ID do produto não encontrado');
        return Promise.resolve({ success: false });
    }
    
    const stockInputs = document.querySelectorAll('.stock-input[data-variation-id]');
    const updates = [];
    
    stockInputs.forEach(input => {
        const variationId = input.getAttribute('data-variation-id');
        const stockQuantity = parseInt(input.value) || 0;
        const statusSelect = document.querySelector(`.stock-status[data-variation-id="${variationId}"]`);
        const costInput = document.querySelector(`.variation-cost[data-variation-id="${variationId}"]`);
        const priceInput = document.querySelector(`.variation-price[data-variation-id="${variationId}"]`);
        const b2bInput = document.querySelector(`.variation-b2b[data-variation-id="${variationId}"]`);
        const inStock = statusSelect ? statusSelect.value === '1' : true;
        
        updates.push({
            variation_id: variationId,
            stock_quantity: stockQuantity,
            in_stock: inStock,
            cost_price: costInput ? parseCurrencyValue(costInput.value) : null,
            price: priceInput ? parseCurrencyValue(priceInput.value) : null,
            b2b_price: b2bInput ? parseCurrencyValue(b2bInput.value) : null
        });
    });
    
    if (updates.length === 0) {
        if (event && event.target) {
            showVariationMessage('error', 'Nenhuma variação para atualizar');
        }
        return Promise.resolve({ success: false, message: 'Nenhuma variação para atualizar' });
    }
    
    // Mostrar loading no botão se houver evento
    let saveBtn = null;
    let originalText = '';
    if (event && event.target) {
        saveBtn = event.target;
        originalText = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
    }
    
    // Timeout para evitar requisições muito longas
    const timeoutId = setTimeout(() => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
        showVariationMessage('error', '⏱️ A requisição está demorando muito. Verifique sua conexão.');
    }, 60000); // 60 segundos
    
    return fetch(`/admin/products/${productId}/variations/update-stock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ updates })
    })
    .then(response => {
        clearTimeout(timeoutId);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showVariationMessage('success', `✅ Estoque de ${data.updated} variação(ões) atualizado(s) com sucesso!`);
            // Recarregar variações para atualizar o estado
            loadVariations(productId);
        } else {
            showVariationMessage('error', '❌ Erro: ' + (data.message || 'Erro desconhecido'));
        }
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
        return data;
    })
    .catch(error => {
        clearTimeout(timeoutId);
        console.error('Erro:', error);
        showVariationMessage('error', '❌ Erro ao atualizar estoque. Verifique sua conexão.');
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
        return { success: false, error: error };
    });
}

// Função para salvar e fechar o modal
function saveAndCloseModal() {
    const productId = document.getElementById('variationsProductId').value;
    
    // Verificar se estamos na aba de estoque e há alterações pendentes
    const activeTab = document.querySelector('#variationsTabs .nav-link.active');
    if (activeTab && activeTab.getAttribute('data-bs-target') === '#stock') {
        // Se estiver na aba de estoque, salvar todas as alterações primeiro
        const stockInputs = document.querySelectorAll('.stock-input[data-variation-id]');
        if (stockInputs.length > 0) {
            // Salvar estoque e depois fechar
            updateAllStock().then(data => {
                // Aguardar um pouco para mostrar a mensagem e depois fechar
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('variationsModal'));
                    if (modal) {
                        modal.hide();
                    }
                }, 1500);
            });
        } else {
            // Não há alterações, apenas fechar
            const modal = bootstrap.Modal.getInstance(document.getElementById('variationsModal'));
            if (modal) {
                modal.hide();
            }
        }
    } else {
        // Para outras abas, as alterações já foram salvas automaticamente
        // Apenas fechar o modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('variationsModal'));
        if (modal) {
            modal.hide();
        }
    }
}

// Função para exibir mensagens de feedback
function showVariationMessage(type, message) {
    // Remover mensagem anterior se existir
    const existingMessage = document.getElementById('variationMessage');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Criar nova mensagem
    const messageDiv = document.createElement('div');
    messageDiv.id = 'variationMessage';
    messageDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    messageDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    messageDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(messageDiv);
    
    // Remover automaticamente após 5 segundos
    setTimeout(() => {
        if (messageDiv && messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}

function initializeVariationPriceFields(stockItem) {
    if (!stockItem) {
        return;
    }

    const variationId = stockItem.getAttribute('data-variation-id');
    const costInput = stockItem.querySelector('.variation-cost');
    const priceInput = stockItem.querySelector('.variation-price');
    const b2bInput = stockItem.querySelector('.variation-b2b');
    const deleteBtn = stockItem.querySelector('.variation-delete');

    const inputs = [costInput, priceInput, b2bInput];

    inputs.forEach(input => {
        if (!input) {
            return;
        }
        const parsed = parseCurrencyValue(input.value);
        input.value = parsed !== null ? formatCurrencyValue(parsed) : '';
        input.addEventListener('focus', () => input.select());
    });

    if (costInput) {
        costInput.addEventListener('blur', function() {
            const parsedCost = parseCurrencyValue(this.value);
            if (parsedCost !== null) {
                this.value = formatCurrencyValue(parsedCost);
                const calculated = calculatePriceFromCost(parsedCost);
                if (priceInput) {
                    priceInput.value = formatCurrencyValue(calculated.price);
                }
                if (b2bInput) {
                    b2bInput.value = formatCurrencyValue(calculated.b2b);
                }
            } else {
                this.value = '';
                if (priceInput) priceInput.value = '';
                if (b2bInput) b2bInput.value = '';
            }
        });
    }

    [priceInput, b2bInput].forEach(input => {
        if (!input) {
            return;
        }
        input.addEventListener('blur', function() {
            const parsed = parseCurrencyValue(this.value);
            this.value = parsed !== null ? formatCurrencyValue(parsed) : '';
        });
    });

    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            const id = this.getAttribute('data-variation-id');
            const card = this.closest('.variation-stock-card');
            
            if (!id) {
                console.error('ID da variação não encontrado no atributo data-variation-id');
                showVariationMessage('error', 'Erro: ID da variação não encontrado.');
                return;
            }
            
            confirmAndDeleteVariation(id, card);
        });
    }
}

function confirmAndDeleteVariation(variationId, cardElement) {
    if (!variationId) {
        showVariationMessage('error', 'ID da variação não encontrado.');
        return;
    }

    // Converter o ID para número para garantir que seja válido
    const id = parseInt(variationId, 10);
    if (isNaN(id) || id <= 0) {
        console.error('ID da variação inválido:', variationId);
        showVariationMessage('error', 'ID da variação inválido.');
        return;
    }
    
    console.log('Excluindo variação com ID:', id);

    if (!confirm('Tem certeza que deseja remover esta variação? Esta ação não pode ser desfeita.')) {
        return;
    }

    const productId = document.getElementById('variationsProductId').value;
    if (!productId) {
        showVariationMessage('error', 'Erro: produto não identificado.');
        return;
    }

    const loaderClass = 'opacity-50';
    if (cardElement) {
        cardElement.classList.add(loaderClass);
    }

    fetch(`/admin/products/${productId}/variations/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const data = await response.json();
        
        if (!response.ok) {
            // Se a resposta não foi OK, mostrar mensagem de erro
            showVariationMessage('error', data.message || 'Não foi possível remover a variação.');
            return;
        }
        
        if (data.success) {
            showVariationMessage('success', data.message || 'Variação removida com sucesso.');
            loadVariations(productId);
        } else {
            showVariationMessage('error', data.message || 'Não foi possível remover a variação.');
        }
    })
    .catch(error => {
        console.error('Erro ao excluir variação:', error);
        showVariationMessage('error', 'Erro ao remover variação. Tente novamente.');
    })
    .finally(() => {
        if (cardElement) {
            cardElement.classList.remove(loaderClass);
        }
    });
}

function deleteInactiveVariations() {
    const productId = document.getElementById('variationsProductId').value;
    if (!productId) {
        showVariationMessage('error', 'Erro: produto não identificado.');
        return;
    }

    if (!confirm('⚠️ ATENÇÃO: Esta ação é IRREVERSÍVEL!\n\nTem certeza que deseja excluir TODAS as variações desativadas deste produto?\n\nEsta ação não pode ser desfeita.')) {
        return;
    }

    fetch(`/admin/products/${productId}/variations/inactive/delete-all`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const data = await response.json();
        
        if (!response.ok) {
            showVariationMessage('error', data.message || 'Não foi possível remover as variações desativadas.');
            return;
        }
        
        if (data.success) {
            showVariationMessage('success', data.message || 'Variações desativadas removidas com sucesso.');
            loadVariations(productId);
        } else {
            showVariationMessage('error', data.message || 'Não foi possível remover as variações desativadas.');
        }
    })
    .catch(error => {
        console.error('Erro ao excluir variações desativadas:', error);
        showVariationMessage('error', 'Erro ao remover variações desativadas. Tente novamente.');
    });
}
</script>

<style>
    .variation-stock-card {
        padding: 1rem;
        background: #fff;
        transition: box-shadow 0.15s ease;
    }

    .variation-stock-card:hover {
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    }

    .variation-stock-header small {
        font-size: 0.75rem;
    }

    .variation-stock-prices .form-control {
        font-weight: 500;
    }
</style>
 

