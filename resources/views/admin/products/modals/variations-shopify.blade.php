<!-- Modal de Variações - Estilo Shopify -->
<div class="modal fade" id="variationsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Moderno -->
            <div class="modal-header border-0 bg-gradient py-4">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <h5 class="modal-title mb-1 fw-bold">
                            <i class="bi bi-layers me-2 text-primary"></i>
                            Gerenciador de Variações
                        </h5>
                        <small class="text-muted">Crie e gerencie todas as variações do seu produto</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
            </div>

            <div class="modal-body p-0">
                <input type="hidden" id="variationsProductId" value="{{ $product->id ?? '' }}">

                <!-- Tabs Modernas -->
                <div class="border-bottom">
                    <div class="nav nav-tabs nav-tabs-lModern px-4 pt-3" id="variationsTabs" role="tablist">
                        <button class="nav-link active rounded-top-3 px-4 py-3" id="tab-generator" data-bs-toggle="tab" data-bs-target="#generator" type="button" role="tab">
                            <i class="bi bi-magic me-2"></i>
                            <span class="fw-medium">Gerador Rápido</span>
                        </button>
                        <button class="nav-link rounded-top-3 px-4 py-3" id="tab-existing" data-bs-toggle="tab" data-bs-target="#existing" type="button" role="tab">
                            <i class="bi bi-grid-3x3-gap me-2"></i>
                            <span class="fw-medium">Variações Ativas</span>
                        </button>
                        <button class="nav-link rounded-top-3 px-4 py-3" id="tab-help" data-bs-toggle="tab" data-bs-target="#help" type="button" role="tab">
                            <i class="bi bi-question-circle me-2"></i>
                            <span class="fw-medium">Ajuda</span>
                        </button>
                    </div>
                </div>

                <div class="tab-content p-4" id="variationsTabsContent">
                    <!-- Aba Gerador Rápido -->
                    <div class="tab-pane fade show active" id="generator" role="tabpanel">
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light rounded-4">
                                    <div class="card-body p-4">
                                        <h6 class="card-title mb-3 fw-bold">
                                            <i class="bi bi-lightbulb text-warning me-2"></i>
                                            Como funciona?
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="badge bg-primary rounded-circle p-2 me-3">
                                                        <i class="bi bi-plus-lg text-white"></i>
                                                    </div>
                                                    <div>
                                                        <strong>Adicione Atributos</strong>
                                                        <p class="text-muted small mb-0">Tamanho, Cor, Material, etc.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="badge bg-success rounded-circle p-2 me-3">
                                                        <i class="bi bi-gear text-white"></i>
                                                    </div>
                                                    <div>
                                                        <strong>Defina Valores</strong>
                                                        <p class="text-muted small mb-0">P, M, G | Azul, Preto</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="badge bg-info rounded-circle p-2 me-3">
                                                        <i class="bi bi-lightning text-white"></i>
                                                    </div>
                                                    <div>
                                                        <strong>Gere Automático</strong>
                                                        <p class="text-muted small mb-0">Cria todas as combinações</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Container de Atributos -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">
                                    <i class="bi bi-tags me-2"></i>
                                    Atributos do Produto
                                </h6>
                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" onclick="addAttribute()">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Adicionar Atributo
                                </button>
                            </div>
                            
                            <div id="attributesContainer" class="space-y-3">
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
                            </div>
                        </div>

                        <!-- Configurações Gerais -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4">
                            <div class="card-body p-4">
                                <h6 class="card-title mb-3 fw-bold">
                                    <i class="bi bi-sliders me-2"></i>
                                    Configurações Gerais
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Preço Base (R$)</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text border-0 bg-light">R$</span>
                                            <input type="number" id="priceInput" class="form-control border-0 bg-light" step="0.01" placeholder="99.90">
                                        </div>
                                        <small class="text-muted">Aplicado a todas as variações</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Estoque Inicial</label>
                                        <div class="input-group input-group-lg">
                                            <input type="number" id="stockInput" class="form-control border-0 bg-light" value="10">
                                            <span class="input-group-text border-0 bg-light">unidades</span>
                                        </div>
                                        <small class="text-muted">Aplicado a todas as variações</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-flex gap-3 justify-content-end">
                            <button id="simplePreviewBtn" type="button" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                                <i class="bi bi-eye me-2"></i>
                                Pré-visualizar
                            </button>
                            <button id="simpleGenerateBtn" type="button" class="btn btn-primary btn-lg rounded-pill px-4">
                                <i class="bi bi-magic me-2"></i>
                                Gerar Variações
                            </button>
                            <div id="simpleSpinner" class="spinner-border text-primary d-none ms-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        
                        <div id="simpleResult" class="mt-4"></div>
                    </div>

                    <!-- Aba Variações Existentes -->
                    <div class="tab-pane fade" id="existing" role="tabpanel">
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h6 class="mt-3 text-muted">Variações Existentes</h6>
                            <p class="text-muted">Carregando variações do produto...</p>
                        </div>
                        <div id="variationsList" class="d-none"></div>
                    </div>

                    <!-- Aba Ajuda -->
                    <div class="tab-pane fade" id="help" role="tabpanel">
                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <div class="card border-0 shadow-sm rounded-3">
                                    <div class="card-body p-4">
                                        <h6 class="card-title mb-4 fw-bold">
                                            <i class="bi bi-question-circle me-2"></i>
                                            Dicas Rápidas
                                        </h6>
                                        
                                        <div class="accordion accordion-flush" id="helpAccordion">
                                            <div class="accordion-item border-0">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#help1">
                                                        <i class="bi bi-tag me-2 text-primary"></i>
                                                        Quais atributos posso usar?
                                                    </button>
                                                </h2>
                                                <div id="help1" class="accordion-collapse collapse show" data-bs-parent="#helpAccordion">
                                                    <div class="accordion-body">
                                                        <strong>Qualquer atributo!</strong> O sistema é totalmente flexível:<br><br>
                                                        • <strong>Roupas:</strong> Tamanho, Cor, Material, Voltagem<br>
                                                        • <strong>Celulares:</strong> RAM, Armazenamento, Cor<br>
                                                        • <strong>Móveis:</strong> Cor, Material, Dimensões<br>
                                                        • <strong>Alimentos:</strong> Sabor, Tamanho, Peso
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="accordion-item border-0">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#help2">
                                                        <i class="bi bi-calculator me-2 text-success"></i>
                                                        Como funciona a combinação?
                                                    </button>
                                                </h2>
                                                <div id="help2" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                                    <div class="accordion-body">
                                                        O sistema cria automaticamente todas as combinações possíveis.<br><br>
                                                        <strong>Exemplo:</strong><br>
                                                        Tamanho: P, M, G<br>
                                                        Cor: Azul, Preto<br><br>
                                                        Resultado: 6 variações (P-Azul, P-Preto, M-Azul, M-Preto, G-Azul, G-Preto)
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="accordion-item border-0">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#help3">
                                                        <i class="bi bi-gear me-2 text-info"></i>
                                                        Posso editar depois?
                                                    </button>
                                                </h2>
                                                <div id="help3" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                                                    <div class="accordion-body">
                                                        <strong>Sim!</strong> Após gerar as variações, você pode:<br><br>
                                                        • Editar preço e estoque individualmente<br>
                                                        • Ativar/desativar variações específicas<br>
                                                        • Excluir variações não desejadas<br>
                                                        • Modificar SKUs automaticamente
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Moderno -->
            <div class="modal-footer border-0 bg-light py-3">
                <button type="button" class="btn btn-secondary btn-lg rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Estilos Modernos Shopify -->
<style>
.modal-header.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.nav-tabs-lModern .nav-link {
    border: none;
    background: transparent;
    color: #6c757d;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
}

.nav-tabs-lModern .nav-link:hover {
    color: #495057;
    background: rgba(102, 126, 234, 0.1);
}

.nav-tabs-lModern .nav-link.active {
    color: #667eea;
    background: rgba(102, 126, 234, 0.1);
    border-bottom-color: #667eea;
    font-weight: 600;
}

.form-control-lg.border-0.bg-light:focus {
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    border: 1px solid #667eea !important;
}

.input-group-lg .input-group-text.border-0.bg-light {
    background: #f8f9fa !important;
    border: 1px solid #e9ecef;
}

.btn-lg.rounded-pill {
    border-radius: 50px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-lg.rounded-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.card.border-0.shadow-sm {
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.card.border-0.shadow-sm:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.accordion-button:not(.collapsed) {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}

.badge.rounded-circle {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.space-y-3 > * + * {
    margin-top: 1rem;
}

.attribute-row .card {
    transition: all 0.3s ease;
}

.attribute-row .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.modal-content.border-0.shadow-lg {
    border-radius: 20px;
    overflow: hidden;
}

.btn-close-white {
    filter: brightness(0) invert(1);
}

/* Animações */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.tab-pane.active .card {
    animation: fadeInUp 0.5s ease forwards;
}

.tab-pane.active .card:nth-child(2) {
    animation-delay: 0.1s;
}

.tab-pane.active .card:nth-child(3) {
    animation-delay: 0.2s;
}
</style>

<!-- JavaScript Professional -->
<script>
// Inicialização será feita pelo simple-variations.js
// Funções addAttribute e removeAttribute estão definidas em /js/admin/simple-variations.js
</script>
