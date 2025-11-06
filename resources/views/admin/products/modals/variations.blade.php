<!-- Modal de Gerenciamento de Variações -->
<div class="modal fade" id="variationsModal" tabindex="-1" aria-labelledby="variationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="variationsModalLabel">Gerenciar Variações</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="variationsProductId" value="">
                
                <!-- Abas -->
                <ul class="nav nav-tabs mb-4" id="variationsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="colors-tab" data-bs-toggle="tab" data-bs-target="#colors" type="button" role="tab">
                            <i class="bi bi-palette me-1"></i>Cores
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="rams-tab" data-bs-toggle="tab" data-bs-target="#rams" type="button" role="tab">
                            <i class="bi bi-memory me-1"></i>RAM
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="storages-tab" data-bs-toggle="tab" data-bs-target="#storages" type="button" role="tab">
                            <i class="bi bi-hdd me-1"></i>Armazenamento
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock" type="button" role="tab">
                            <i class="bi bi-box-seam me-1"></i>Estoque
                        </button>
                    </li>
                </ul>
                
                <!-- Conteúdo das Abas -->
                <div class="tab-content" id="variationsTabContent">
                    <!-- Aba Cores -->
                    <div class="tab-pane fade show active" id="colors" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Adicionar Nova Cor</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newColor" placeholder="Ex: Preto, Branco, Azul">
                                <button class="btn btn-primary" type="button" onclick="addNewVariationType(document.getElementById('variationsProductId').value, 'color')">
                                    <i class="bi bi-plus-circle me-1"></i>Adicionar
                                </button>
                            </div>
                        </div>
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                            <label class="form-label fw-bold mb-3">Cores Disponíveis</label>
                            <div id="colorsList">
                                <p class="text-muted text-center">Carregando...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aba RAM -->
                    <div class="tab-pane fade" id="rams" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Adicionar Nova RAM</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newRam" placeholder="Ex: 4GB, 8GB, 16GB">
                                <button class="btn btn-primary" type="button" onclick="addNewVariationType(document.getElementById('variationsProductId').value, 'ram')">
                                    <i class="bi bi-plus-circle me-1"></i>Adicionar
                                </button>
                            </div>
                        </div>
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                            <label class="form-label fw-bold mb-3">RAMs Disponíveis</label>
                            <div id="ramsList">
                                <p class="text-muted text-center">Carregando...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aba Armazenamento -->
                    <div class="tab-pane fade" id="storages" role="tabpanel">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Adicionar Novo Armazenamento</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newStorage" placeholder="Ex: 128GB, 256GB, 512GB">
                                <button class="btn btn-primary" type="button" onclick="addNewVariationType(document.getElementById('variationsProductId').value, 'storage')">
                                    <i class="bi bi-plus-circle me-1"></i>Adicionar
                                </button>
                            </div>
                        </div>
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                            <label class="form-label fw-bold mb-3">Armazenamentos Disponíveis</label>
                            <div id="storagesList">
                                <p class="text-muted text-center">Carregando...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Aba Estoque -->
                    <div class="tab-pane fade" id="stock" role="tabpanel">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold mb-0">Gerenciar Estoque por Variação</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="updateAllStock()">
                                    <i class="bi bi-check-all me-1"></i>Salvar Todos
                                </button>
                            </div>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Edite o estoque de cada variação abaixo. Clique em "Salvar Todos" para aplicar todas as alterações.
                            </div>
                        </div>
                        <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                            <label class="form-label fw-bold mb-3">Variações e Estoque</label>
                            <div id="stockList">
                                <p class="text-muted text-center">Carregando...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


