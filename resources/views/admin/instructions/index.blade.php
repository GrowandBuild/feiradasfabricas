@extends('admin.layouts.app')

@section('title', 'Guia Completo do Sistema')
@section('page-title', '📚 Guia Completo do Sistema')
@section('page-icon', 'bi bi-book')
@section('page-description', 'Tutorial completo para dominar todas as funcionalidades')

@section('content')
<div class="instructions-container">
    <div class="row g-4">
        <!-- Navegação Lateral -->
        <div class="col-lg-3">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Índice</h6>
                </div>
                <div class="list-group list-group-flush" id="instructions-nav">
                    <a class="list-group-item list-group-item-action" href="#intro" data-section="intro">
                        <i class="bi bi-house-door me-2"></i>Introdução
                    </a>
                    <a class="list-group-item list-group-item-action" href="#config" data-section="config">
                        <i class="bi bi-gear me-2"></i>Configuração Inicial
                    </a>
                    <a class="list-group-item list-group-item-action" href="#catalogo" data-section="catalogo">
                        <i class="bi bi-box-seam me-2"></i>Catálogo
                    </a>
                    <a class="list-group-item list-group-item-action" href="#operacao" data-section="operacao">
                        <i class="bi bi-briefcase me-2"></i>Operação
                    </a>
                    <a class="list-group-item list-group-item-action" href="#marketing" data-section="marketing">
                        <i class="bi bi-megaphone me-2"></i>Marketing
                    </a>
                    <a class="list-group-item list-group-item-action" href="#entregas" data-section="entregas">
                        <i class="bi bi-truck me-2"></i>Entregas
                    </a>
                    <a class="list-group-item list-group-item-action" href="#sistema" data-section="sistema">
                        <i class="bi bi-cpu me-2"></i>Sistema
                    </a>
                    <a class="list-group-item list-group-item-action" href="#dicas" data-section="dicas">
                        <i class="bi bi-lightbulb me-2"></i>Dicas & Boas Práticas
                    </a>
                </div>
            </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="col-lg-9">
            <!-- INTRODUÇÃO -->
            <section id="intro" class="instruction-section mb-5">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-house-door me-2"></i>Bem-vindo ao Sistema!</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Este guia foi criado para você dominar completamente o sistema.</strong> Siga as instruções passo a passo e você estará operando como um profissional em pouco tempo!
                        </div>

                        <h5 class="mt-4 mb-3">🎯 O que você encontrará aqui:</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Configuração Inicial:</strong> Configure seu e-commerce do zero</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Gestão de Produtos:</strong> Adicione e gerencie seu catálogo completo</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Controle de Pedidos:</strong> Gerencie vendas e entregas</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>Marketing:</strong> Crie campanhas e promova seus produtos</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>PDV:</strong> Integre loja física com e-commerce</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i><strong>E muito mais!</strong></li>
                        </ul>

                        <div class="alert alert-warning mt-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Dica:</strong> Use o menu lateral para navegar rapidamente entre as seções. Cada tópico contém instruções detalhadas passo a passo.
                        </div>
                    </div>
                </div>
            </section>

            <!-- CONFIGURAÇÃO INICIAL -->
            <section id="config" class="instruction-section mb-5">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-gear me-2"></i>1. Configuração Inicial</h4>
                    </div>
                    <div class="card-body">
                        <p class="lead">Antes de começar a vender, é essencial configurar corretamente as informações básicas do seu e-commerce.</p>

                        <h5 class="mt-4 mb-3">📋 Passo 1: Acessar Configurações</h5>
                        <ol>
                            <li class="mb-2">No menu lateral, clique em <strong>"Sistema"</strong> → <strong>"Configurações"</strong></li>
                            <li class="mb-2">Você verá várias abas organizadas por categoria</li>
                        </ol>

                        <h5 class="mt-4 mb-3">🏢 Passo 2: Informações da Loja</h5>
                        <div class="bg-light p-3 rounded mb-3">
                            <strong>Na aba "Geral":</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Nome do Site:</strong> Nome que aparece no topo do site</li>
                                <li><strong>Logo:</strong> Faça upload do logo da sua empresa</li>
                                <li><strong>Email:</strong> Email de contato principal</li>
                                <li><strong>Telefone:</strong> Telefone para contato</li>
                                <li><strong>Endereço:</strong> Endereço completo da loja</li>
                            </ul>
                        </div>

                        <h5 class="mt-4 mb-3">💳 Passo 3: Configurar Pagamentos</h5>
                        <div class="bg-light p-3 rounded mb-3">
                            <strong>Na aba "Pagamentos":</strong>
                            <ul class="mb-0 mt-2">
                                <li>Configure métodos de pagamento aceitos (PIX, Cartão, Boleto)</li>
                                <li>Adicione chaves de API dos gateways de pagamento</li>
                                <li>Configure taxas e parcelamento</li>
                            </ul>
                        </div>

                        <h5 class="mt-4 mb-3">🚚 Passo 4: Configurar Frete</h5>
                        <div class="bg-light p-3 rounded mb-3">
                            <strong>Na aba "Frete":</strong>
                            <ul class="mb-0 mt-2">
                                <li>Configure métodos de entrega disponíveis</li>
                                <li>Defina valores de frete fixo ou por peso</li>
                                <li>Configure prazos de entrega</li>
                            </ul>
                        </div>

                        <h5 class="mt-4 mb-3">👥 Passo 5: Criar Usuários Administradores</h5>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Sistema"</strong> → <strong>"Usuários"</strong></li>
                            <li class="mb-2">Clique em <strong>"Novo Usuário"</strong></li>
                            <li class="mb-2">Preencha nome, email e senha</li>
                            <li class="mb-2">Defina o nível de acesso (Admin, Editor, etc.)</li>
                            <li class="mb-2">Salve o usuário</li>
                        </ol>

                        <div class="alert alert-success mt-4">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Pronto!</strong> Sua loja está configurada. Agora você pode começar a adicionar produtos.
                        </div>
                    </div>
                </div>
            </section>

            <!-- CATÁLOGO -->
            <section id="catalogo" class="instruction-section mb-5">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-box-seam me-2"></i>2. Gerenciamento de Catálogo</h4>
                    </div>
                    <div class="card-body">
                        <p class="lead">O catálogo é o coração do seu e-commerce. Aqui você gerencia produtos, categorias e atributos.</p>

                        <h5 class="mt-4 mb-3">📦 2.1. Criar Categorias</h5>
                        <p><strong>Por que criar categorias?</strong> Organize seus produtos para facilitar a navegação dos clientes.</p>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Catálogo"</strong> → <strong>"Categorias"</strong></li>
                            <li class="mb-2">Clique em <strong>"Nova Categoria"</strong></li>
                            <li class="mb-2">Preencha:
                                <ul>
                                    <li><strong>Nome:</strong> Ex: "Roupas", "Eletrônicos"</li>
                                    <li><strong>Slug:</strong> URL amigável (gerado automaticamente)</li>
                                    <li><strong>Descrição:</strong> Descrição da categoria</li>
                                    <li><strong>Imagem:</strong> Imagem representativa</li>
                                    <li><strong>Categoria Pai:</strong> Para criar subcategorias</li>
                                </ul>
                            </li>
                            <li class="mb-2">Salve a categoria</li>
                        </ol>

                        <div class="alert alert-info mt-3">
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>Dica:</strong> Crie uma hierarquia de categorias. Ex: "Roupas" → "Roupas Femininas" → "Vestidos"
                        </div>

                        <h5 class="mt-4 mb-3">🛍️ 2.2. Adicionar Produtos</h5>
                        <p><strong>Este é o passo mais importante!</strong> Produtos bem cadastrados vendem mais.</p>
                        
                        <h6 class="mt-3 mb-2">Passo a Passo Completo:</h6>
                        <ol>
                            <li class="mb-2"><strong>Vá em "Catálogo" → "Produtos" → "Novo Produto"</strong></li>
                            <li class="mb-2"><strong>Informações Básicas:</strong>
                                <ul>
                                    <li><strong>Nome:</strong> Nome completo e descritivo do produto</li>
                                    <li><strong>SKU:</strong> Código único do produto (obrigatório)</li>
                                    <li><strong>Descrição:</strong> Descrição detalhada (use formatação rica)</li>
                                    <li><strong>Descrição Curta:</strong> Resumo que aparece na listagem</li>
                                </ul>
                            </li>
                            <li class="mb-2"><strong>Preços:</strong>
                                <ul>
                                    <li><strong>Preço:</strong> Preço de venda (B2C)</li>
                                    <li><strong>Preço B2B:</strong> Preço para atacado (opcional)</li>
                                    <li><strong>Preço de Custo:</strong> Para controle interno</li>
                                </ul>
                            </li>
                            <li class="mb-2"><strong>Estoque:</strong>
                                <ul>
                                    <li><strong>Quantidade:</strong> Quantidade disponível</li>
                                    <li><strong>Estoque Mínimo:</strong> Alerta quando estoque estiver baixo</li>
                                    <li><strong>Gerenciar Estoque:</strong> Ative para controle automático</li>
                                </ul>
                            </li>
                            <li class="mb-2"><strong>Imagens:</strong>
                                <ul>
                                    <li>Adicione múltiplas imagens (primeira é a principal)</li>
                                    <li>Use imagens de alta qualidade</li>
                                    <li>Recomendação: 800x800px mínimo</li>
                                </ul>
                            </li>
                            <li class="mb-2"><strong>Categorias:</strong> Selecione uma ou mais categorias</li>
                            <li class="mb-2"><strong>Status:</strong> Ative para publicar o produto</li>
                            <li class="mb-2"><strong>Salve o produto</strong></li>
                        </ol>

                        <div class="bg-light p-3 rounded mt-3">
                            <strong>💡 Dicas para Produtos que Vendem:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Use nomes descritivos e com palavras-chave</li>
                                <li>Adicione pelo menos 3-5 imagens de diferentes ângulos</li>
                                <li>Escreva descrições detalhadas com benefícios</li>
                                <li>Use preços competitivos</li>
                                <li>Mantenha estoque atualizado</li>
                            </ul>
                        </div>

                        <h5 class="mt-4 mb-3">🎨 2.3. Variações de Produtos</h5>
                        <p>Produtos com variações (tamanho, cor, etc.) precisam de configuração especial.</p>
                        <ol>
                            <li class="mb-2">Ao criar/editar um produto, vá na aba <strong>"Variações"</strong></li>
                            <li class="mb-2">Clique em <strong>"Adicionar Variação"</strong></li>
                            <li class="mb-2">Preencha:
                                <ul>
                                    <li><strong>Nome:</strong> Ex: "Pequeno - Vermelho"</li>
                                    <li><strong>SKU:</strong> SKU único da variação</li>
                                    <li><strong>Preço:</strong> Preço específico (ou deixe vazio para usar o preço base)</li>
                                    <li><strong>Estoque:</strong> Estoque específico da variação</li>
                                </ul>
                            </li>
                            <li class="mb-2">Salve a variação</li>
                        </ol>

                        <h5 class="mt-4 mb-3">🏷️ 2.4. Atributos e Filtros</h5>
                        <p>Atributos ajudam os clientes a filtrar produtos (cor, tamanho, marca, etc.)</p>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Catálogo"</strong> → <strong>"Atributos"</strong></li>
                            <li class="mb-2">Crie atributos como: "Cor", "Tamanho", "Marca"</li>
                            <li class="mb-2">Adicione valores para cada atributo (Ex: Cor → Vermelho, Azul, Verde)</li>
                            <li class="mb-2">Associe os atributos aos produtos na aba "Atributos" do produto</li>
                        </ol>
                    </div>
                </div>
            </section>

            <!-- OPERAÇÃO -->
            <section id="operacao" class="instruction-section mb-5">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-briefcase me-2"></i>3. Operação e Vendas</h4>
                    </div>
                    <div class="card-body">
                        <p class="lead">Gerencie pedidos, clientes, feedbacks e muito mais nesta seção.</p>

                        <h5 class="mt-4 mb-3">🛒 3.1. Gerenciar Pedidos</h5>
                        <p><strong>Onde tudo acontece!</strong> Aqui você acompanha todas as vendas.</p>
                        
                        <h6 class="mt-3 mb-2">Visualizar Pedidos:</h6>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Operação"</strong> → <strong>"Pedidos"</strong></li>
                            <li class="mb-2">Você verá uma lista com todos os pedidos</li>
                            <li class="mb-2">Use os filtros para encontrar pedidos específicos:
                                <ul>
                                    <li>Por status (Pendente, Processando, Enviado, etc.)</li>
                                    <li>Por data</li>
                                    <li>Por cliente</li>
                                    <li>Por número do pedido</li>
                                </ul>
                            </li>
                        </ol>

                        <h6 class="mt-3 mb-2">Atualizar Status do Pedido:</h6>
                        <ol>
                            <li class="mb-2">Clique no pedido para ver detalhes</li>
                            <li class="mb-2">Altere o status conforme o progresso:
                                <ul>
                                    <li><strong>Pendente:</strong> Aguardando pagamento</li>
                                    <li><strong>Processando:</strong> Pagamento confirmado, preparando envio</li>
                                    <li><strong>Enviado:</strong> Produto enviado (adicione código de rastreamento)</li>
                                    <li><strong>Entregue:</strong> Cliente recebeu o produto</li>
                                    <li><strong>Cancelado:</strong> Pedido cancelado</li>
                                </ul>
                            </li>
                            <li class="mb-2">Adicione observações internas se necessário</li>
                            <li class="mb-2">Salve as alterações</li>
                        </ol>

                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Importante:</strong> Quando marcar como "Enviado", adicione o código de rastreamento. O cliente receberá uma notificação automática.
                        </div>

                        <h5 class="mt-4 mb-3">👥 3.2. Gerenciar Clientes</h5>
                        <p>Visualize e gerencie informações dos seus clientes.</p>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Operação"</strong> → <strong>"Clientes"</strong></li>
                            <li class="mb-2">Visualize lista de clientes cadastrados</li>
                            <li class="mb-2">Clique em um cliente para ver:
                                <ul>
                                    <li>Histórico de pedidos</li>
                                    <li>Endereços cadastrados</li>
                                    <li>Informações de contato</li>
                                    <li>Status da conta (Ativo/Inativo)</li>
                                </ul>
                            </li>
                            <li class="mb-2">Você pode editar informações ou desativar contas se necessário</li>
                        </ol>

                        <h5 class="mt-4 mb-3">💬 3.3. Feedbacks de Produtos</h5>
                        <p>Gerencie avaliações e feedbacks dos clientes sobre seus produtos.</p>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Operação"</strong> → <strong>"Feedbacks"</strong></li>
                            <li class="mb-2">Visualize todos os feedbacks recebidos</li>
                            <li class="mb-2">Aprove ou rejeite feedbacks:
                                <ul>
                                    <li>Feedbacks aprovados aparecem no site</li>
                                    <li>Feedbacks pendentes precisam de aprovação</li>
                                </ul>
                            </li>
                            <li class="mb-2">Você também pode criar feedbacks manualmente</li>
                        </ol>

                        <h5 class="mt-4 mb-3">💰 3.4. PDV - Ponto de Venda (Loja Física)</h5>
                        <p><strong>Integre sua loja física com o e-commerce!</strong> Sistema completo de caixa.</p>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Pré-requisito:</strong> Ative a sincronização em <strong>"Configurações"</strong> → <strong>"Loja Física"</strong>
                        </div>

                        <h6 class="mt-3 mb-2">Como usar o PDV:</h6>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Operação"</strong> → <strong>"PDV"</strong></li>
                            <li class="mb-2"><strong>Buscar Produto:</strong>
                                <ul>
                                    <li>Digite o nome ou SKU do produto na barra de busca</li>
                                    <li>Pressione Enter ou clique em "Buscar"</li>
                                    <li>Clique no produto para adicionar ao carrinho</li>
                                </ul>
                            </li>
                            <li class="mb-2"><strong>Gerenciar Carrinho:</strong>
                                <ul>
                                    <li>Ajuste quantidades com os botões + e -</li>
                                    <li>Remova itens clicando no ícone de lixeira</li>
                                    <li>Adicione desconto se necessário</li>
                                </ul>
                            </li>
                            <li class="mb-2"><strong>Finalizar Venda:</strong>
                                <ul>
                                    <li>Selecione forma de pagamento (Dinheiro, Cartão, PIX, etc.)</li>
                                    <li>Se cartão de crédito, escolha número de parcelas</li>
                                    <li>Opcionalmente, associe um cliente</li>
                                    <li>Adicione observações se necessário</li>
                                    <li>Clique em "Finalizar Venda"</li>
                                </ul>
                            </li>
                        </ol>

                        <div class="bg-light p-3 rounded mt-3">
                            <strong>✨ Benefícios do PDV Integrado:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Estoque unificado entre loja física e online</li>
                                <li>Histórico de vendas centralizado</li>
                                <li>Cupons funcionam em ambos os canais</li>
                                <li>Relatórios unificados</li>
                            </ul>
                        </div>

                        <h5 class="mt-4 mb-3">🎫 3.5. Cupons de Desconto</h5>
                        <p>Crie cupons para promover vendas e fidelizar clientes.</p>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Operação"</strong> → <strong>"Cupons"</strong></li>
                            <li class="mb-2">Clique em <strong>"Novo Cupom"</strong></li>
                            <li class="mb-2">Preencha:
                                <ul>
                                    <li><strong>Código:</strong> Código que o cliente digita (Ex: "PROMO10")</li>
                                    <li><strong>Tipo:</strong> Percentual ou Valor Fixo</li>
                                    <li><strong>Valor:</strong> 10% ou R$ 10,00</li>
                                    <li><strong>Validade:</strong> Data de início e fim</li>
                                    <li><strong>Uso Máximo:</strong> Quantas vezes pode ser usado</li>
                                    <li><strong>Valor Mínimo:</strong> Valor mínimo do pedido</li>
                                    <li><strong>Produtos/Categorias:</strong> Restringir a produtos específicos</li>
                                </ul>
                            </li>
                            <li class="mb-2">Salve o cupom</li>
                        </ol>

                        <div class="alert alert-success mt-3">
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>Dica de Marketing:</strong> Crie cupons sazonais (Black Friday, Natal) e compartilhe nas redes sociais!
                        </div>

                        <h5 class="mt-4 mb-3">🏆 3.6. Badges Promocionais</h5>
                        <p>Destaque produtos com badges visuais (Novo, Promoção, Mais Vendido, etc.)</p>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Operação"</strong> → <strong>"Badges Promocionais"</strong></li>
                            <li class="mb-2">Crie badges personalizados:
                                <ul>
                                    <li><strong>Nome:</strong> Ex: "Novo", "Promoção", "Mais Vendido"</li>
                                    <li><strong>Cor:</strong> Escolha uma cor de destaque</li>
                                    <li><strong>Ícone:</strong> Escolha um ícone (opcional)</li>
                                </ul>
                            </li>
                            <li class="mb-2">Associe badges aos produtos na edição do produto</li>
                        </ol>
                    </div>
                </div>
            </section>

            <!-- MARKETING -->
            <section id="marketing" class="instruction-section mb-5">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-megaphone me-2"></i>4. Marketing e Promoções</h4>
                    </div>
                    <div class="card-body">
                        <p class="lead">Ferramentas poderosas para atrair clientes e aumentar vendas.</p>

                        <h5 class="mt-4 mb-3">🖼️ 4.1. Banners</h5>
                        <p>Banners são imagens promocionais que aparecem em destaque no site.</p>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Marketing"</strong> → <strong>"Banners"</strong></li>
                            <li class="mb-2">Clique em <strong>"Novo Banner"</strong></li>
                            <li class="mb-2">Configure:
                                <ul>
                                    <li><strong>Posição:</strong> Onde o banner aparece (Hero, Lateral, etc.)</li>
                                    <li><strong>Imagem:</strong> Upload da imagem (recomendado: 1920x600px)</li>
                                    <li><strong>Link:</strong> URL de destino ao clicar</li>
                                    <li><strong>Departamento:</strong> Associar a um departamento específico</li>
                                    <li><strong>Ordem:</strong> Ordem de exibição</li>
                                    <li><strong>Ativo:</strong> Ative para publicar</li>
                                </ul>
                            </li>
                            <li class="mb-2">Salve o banner</li>
                        </ol>

                        <h5 class="mt-4 mb-3">📸 4.2. Álbuns de Fotos</h5>
                        <p>Organize e exiba galerias de fotos no site.</p>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Marketing"</strong> → <strong>"Álbuns"</strong></li>
                            <li class="mb-2">Crie um novo álbum</li>
                            <li class="mb-2">Adicione múltiplas fotos ao álbum</li>
                            <li class="mb-2">Configure título, descrição e ordem das fotos</li>
                        </ol>

                        <h5 class="mt-4 mb-3">📐 4.3. Sessões da Homepage</h5>
                        <p>Personalize a página inicial com seções customizadas.</p>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Sistema"</strong> → <strong>"Sessões"</strong></li>
                            <li class="mb-2">Crie seções como:
                                <ul>
                                    <li>Produtos em Destaque</li>
                                    <li>Novidades</li>
                                    <li>Mais Vendidos</li>
                                    <li>Ofertas Especiais</li>
                                </ul>
                            </li>
                            <li class="mb-2">Configure layout, produtos e ordem de exibição</li>
                        </ol>
                    </div>
                </div>
            </section>

            <!-- ENTREGAS -->
            <section id="entregas" class="instruction-section mb-5">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-truck me-2"></i>5. Entregas Regionais</h4>
                    </div>
                    <div class="card-body">
                        <p class="lead">Configure entregas personalizadas por região.</p>

                        <h5 class="mt-4 mb-3">🚚 Configurar Entregas Regionais</h5>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Entregas Regionais"</strong> no menu</li>
                            <li class="mb-2">Clique em <strong>"Nova Região"</strong></li>
                            <li class="mb-2">Configure:
                                <ul>
                                    <li><strong>Nome da Região:</strong> Ex: "Zona Sul", "Centro"</li>
                                    <li><strong>CEP Inicial e Final:</strong> Faixa de CEPs atendidos</li>
                                    <li><strong>Valor do Frete:</strong> Valor fixo ou por peso</li>
                                    <li><strong>Prazo de Entrega:</strong> Dias úteis</li>
                                </ul>
                            </li>
                            <li class="mb-2">Salve a região</li>
                        </ol>

                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Dica:</strong> Configure múltiplas regiões para oferecer frete diferenciado por área.
                        </div>
                    </div>
                </div>
            </section>

            <!-- SISTEMA -->
            <section id="sistema" class="instruction-section mb-5">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-cpu me-2"></i>6. Sistema e Configurações Avançadas</h4>
                    </div>
                    <div class="card-body">
                        <p class="lead">Configurações avançadas e gerenciamento do sistema.</p>

                        <h5 class="mt-4 mb-3">👤 6.1. Gerenciar Usuários</h5>
                        <p>Controle quem tem acesso ao painel administrativo.</p>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Sistema"</strong> → <strong>"Usuários"</strong></li>
                            <li class="mb-2">Crie novos usuários com diferentes níveis de acesso</li>
                            <li class="mb-2">Defina permissões (Admin, Editor, Visualizador)</li>
                            <li class="mb-2">Ative ou desative contas conforme necessário</li>
                        </ol>

                        <h5 class="mt-4 mb-3">⚙️ 6.2. Configurações Avançadas</h5>
                        <p>Acesse todas as configurações do sistema em um só lugar.</p>
                        <div class="bg-light p-3 rounded mb-3">
                            <strong>Principais abas de configuração:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Geral:</strong> Informações básicas da loja</li>
                                <li><strong>Pagamentos:</strong> Gateways e métodos de pagamento</li>
                                <li><strong>Frete:</strong> Configurações de entrega</li>
                                <li><strong>Email:</strong> Configurações de notificações</li>
                                <li><strong>Loja Física:</strong> Integração PDV e sincronização</li>
                                <li><strong>SEO:</strong> Otimização para buscadores</li>
                                <li><strong>Redes Sociais:</strong> Links e integrações</li>
                            </ul>
                        </div>

                        <h5 class="mt-4 mb-3">🏪 6.3. Integração Loja Física</h5>
                        <p>Conecte sua loja física com o e-commerce.</p>
                        <ol>
                            <li class="mb-2">Vá em <strong>"Configurações"</strong> → <strong>"Loja Física"</strong></li>
                            <li class="mb-2">Ative <strong>"Sincronização com Loja Física"</strong></li>
                            <li class="mb-2">Configure:
                                <ul>
                                    <li><strong>Nome da Loja:</strong> Identificação</li>
                                    <li><strong>Endereço:</strong> Localização</li>
                                    <li><strong>Sincronizar Estoque:</strong> Estoque unificado</li>
                                    <li><strong>Sincronizar Vendas:</strong> Vendas da loja física aparecem no sistema</li>
                                    <li><strong>Sincronizar Cupons:</strong> Cupons funcionam em ambos</li>
                                </ul>
                            </li>
                            <li class="mb-2">Salve as configurações</li>
                        </ol>

                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Importante:</strong> Você pode desativar a sincronização a qualquer momento sem perder dados.
                        </div>
                    </div>
                </div>
            </section>

            <!-- DICAS -->
            <section id="dicas" class="instruction-section mb-5">
                <div class="card">
                    <div class="card-header bg-gradient-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-lightbulb me-2"></i>7. Dicas & Boas Práticas</h4>
                    </div>
                    <div class="card-body">
                        <p class="lead">Aprenda com as melhores práticas para maximizar suas vendas!</p>

                        <h5 class="mt-4 mb-3">💡 Dicas Gerais</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <h6 class="text-primary"><i class="bi bi-images me-2"></i>Imagens de Qualidade</h6>
                                        <p class="mb-0 small">Use imagens de alta resolução (mínimo 800x800px). Mostre o produto de diferentes ângulos. Clientes compram mais quando veem o produto claramente.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-body">
                                        <h6 class="text-success"><i class="bi bi-pencil-square me-2"></i>Descrições Detalhadas</h6>
                                        <p class="mb-0 small">Escreva descrições completas com benefícios, especificações técnicas e informações relevantes. Use formatação (negrito, listas) para destacar pontos importantes.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <h6 class="text-warning"><i class="bi bi-box-seam me-2"></i>Controle de Estoque</h6>
                                        <p class="mb-0 small">Mantenha estoque sempre atualizado. Configure alertas de estoque mínimo. Produtos sem estoque perdem vendas e frustram clientes.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-body">
                                        <h6 class="text-info"><i class="bi bi-truck me-2"></i>Frete Competitivo</h6>
                                        <p class="mb-0 small">Ofereça opções de frete (rápido, econômico). Considere frete grátis para pedidos acima de determinado valor. Configure entregas regionais.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">📈 Estratégias de Vendas</h5>
                        <ul>
                            <li class="mb-2"><strong>Cupons Estratégicos:</strong> Crie cupons para datas especiais (Black Friday, Dia das Mães). Compartilhe nas redes sociais.</li>
                            <li class="mb-2"><strong>Badges Promocionais:</strong> Use badges para destacar produtos novos, em promoção ou mais vendidos.</li>
                            <li class="mb-2"><strong>Banners Atraentes:</strong> Crie banners com ofertas especiais. Atualize regularmente para manter o site dinâmico.</li>
                            <li class="mb-2"><strong>Feedbacks:</strong> Incentive clientes a deixarem feedbacks com fotos. Aprove feedbacks positivos rapidamente.</li>
                            <li class="mb-2"><strong>Produtos Relacionados:</strong> Configure produtos relacionados para aumentar o ticket médio.</li>
                        </ul>

                        <h5 class="mt-4 mb-3">⚡ Atalhos e Produtividade</h5>
                        <div class="bg-light p-3 rounded">
                            <ul class="mb-0">
                                <li><strong>Busca Rápida:</strong> Use a busca no topo para encontrar produtos, pedidos ou clientes rapidamente</li>
                                <li><strong>Filtros:</strong> Use filtros nas listagens para encontrar o que precisa</li>
                                <li><strong>Dashboard:</strong> Monitore métricas importantes no dashboard principal</li>
                                <li><strong>Notificações:</strong> Fique atento a pedidos pendentes e estoque baixo</li>
                            </ul>
                        </div>

                        <h5 class="mt-4 mb-3">🔒 Segurança e Backup</h5>
                        <div class="alert alert-warning">
                            <i class="bi bi-shield-check me-2"></i>
                            <strong>Importante:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Use senhas fortes para usuários administrativos</li>
                                <li>Mantenha o sistema atualizado</li>
                                <li>Faça backups regulares dos dados</li>
                                <li>Não compartilhe credenciais de acesso</li>
                            </ul>
                        </div>

                        <h5 class="mt-4 mb-3">📞 Suporte</h5>
                        <p>Se tiver dúvidas ou precisar de ajuda:</p>
                        <ul>
                            <li>Consulte este guia primeiro</li>
                            <li>Use a busca para encontrar tópicos específicos</li>
                            <li>Entre em contato com o suporte técnico se necessário</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Rodapé -->
            <div class="card bg-primary text-white mt-5">
                <div class="card-body text-center">
                    <h5 class="mb-3"><i class="bi bi-check-circle me-2"></i>Parabéns!</h5>
                    <p class="mb-0">Você agora tem todo o conhecimento necessário para dominar o sistema. Boas vendas! 🚀</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .instructions-container {
        padding: 1rem 0;
    }

    .instruction-section {
        scroll-margin-top: 100px;
    }

    .instruction-section .card {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border: none;
    }

    .instruction-section .card-header {
        border-bottom: 2px solid rgba(255,255,255,0.2);
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    #instructions-nav .list-group-item {
        border: none;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }

    #instructions-nav .list-group-item:hover {
        background-color: #f8f9fa;
        border-left-color: #667eea;
    }

    #instructions-nav .list-group-item.active {
        background-color: #e7f3ff;
        border-left-color: #667eea;
        font-weight: 600;
    }

    .instruction-section h5 {
        color: #333;
        font-weight: 600;
    }

    .instruction-section h6 {
        color: #555;
        font-weight: 600;
    }

    .instruction-section ol li,
    .instruction-section ul li {
        margin-bottom: 0.5rem;
    }

    .instruction-section .alert {
        border-left: 4px solid;
    }

    .instruction-section .bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Navegação suave
    const navLinks = document.querySelectorAll('#instructions-nav a');
    const sections = document.querySelectorAll('.instruction-section');

    // Atualizar link ativo ao rolar
    function updateActiveNav() {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (window.pageYOffset >= (sectionTop - 150)) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('data-section') === current) {
                link.classList.add('active');
            }
        });
    }

    // Scroll suave
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-section');
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Atualizar ao rolar
    window.addEventListener('scroll', updateActiveNav);
    updateActiveNav();
});
</script>
@endpush
@endsection


