@extends('layouts.app')

@section('title', 'Contato - Feira das Fábricas')

@section('content')
<div class="container py-5">
    <!-- Header da Página -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold text-dark mb-3">
                <i class="fas fa-headphones text-warning me-3"></i>
                Entre em Contato
            </h1>
            <p class="lead text-muted">
                Estamos aqui para ajudar! Entre em contato conosco através dos canais abaixo ou use o formulário.
            </p>
        </div>
    </div>

    <!-- Mensagens de Sucesso/Erro -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informações de Contato -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="h4 mb-4 text-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações de Contato
                    </h3>

                    <div class="contact-info mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="contact-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Telefone</h6>
                                <p class="mb-0 text-muted">(11) 9999-9999</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="contact-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">WhatsApp</h6>
                                <p class="mb-0 text-muted">(11) 99999-9999</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="contact-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Email</h6>
                                <p class="mb-0 text-muted">contato@feiradasfabricas.com</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="contact-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Horário de Atendimento</h6>
                                <p class="mb-0 text-muted">Seg - Sex: 8h às 18h</p>
                                <p class="mb-0 text-muted">Sáb: 8h às 12h</p>
                            </div>
                        </div>
                    </div>

                    <!-- Redes Sociais -->
                    <div class="social-links">
                        <h6 class="mb-3">Siga-nos nas redes sociais</h6>
                        <div class="d-flex gap-3">
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="btn btn-outline-info btn-sm">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="btn btn-outline-success btn-sm">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário de Contato -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="h4 mb-4 text-primary">
                        <i class="fas fa-paper-plane me-2"></i>
                        Envie sua Mensagem
                    </h3>

                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nome Completo *</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telefone</label>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}"
                                       placeholder="(11) 99999-9999">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Tipo de Contato *</label>
                                <select class="form-select @error('type') is-invalid @enderror" 
                                        id="type" 
                                        name="type" 
                                        required>
                                    <option value="">Selecione...</option>
                                    <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>Dúvida Geral</option>
                                    <option value="support" {{ old('type') == 'support' ? 'selected' : '' }}>Suporte Técnico</option>
                                    <option value="sales" {{ old('type') == 'sales' ? 'selected' : '' }}>Vendas</option>
                                    <option value="b2b" {{ old('type') == 'b2b' ? 'selected' : '' }}>Conta B2B</option>
                                    <option value="complaint" {{ old('type') == 'complaint' ? 'selected' : '' }}>Reclamação</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Assunto *</label>
                            <input type="text" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject') }}" 
                                   placeholder="Digite o assunto da sua mensagem"
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label">Mensagem *</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" 
                                      name="message" 
                                      rows="6" 
                                      placeholder="Descreva sua dúvida, sugestão ou solicitação..."
                                      required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>
                                Enviar Mensagem
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Rápido -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="h4 mb-4 text-primary">
                        <i class="fas fa-question-circle me-2"></i>
                        Perguntas Frequentes
                    </h3>

                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                    Qual o prazo de entrega?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    O prazo de entrega varia de acordo com a região e produto. Em média, entregamos em 3-7 dias úteis para a capital e 5-10 dias úteis para o interior.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                    Como funciona a garantia?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Oferecemos garantia de 1 ano para produtos eletrônicos e 6 meses para acessórios. A garantia cobre defeitos de fabricação.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                    Vocês fazem vendas para empresas?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Sim! Temos condições especiais para empresas com CNPJ. Entre em contato para conhecer nossos preços B2B e condições de pagamento.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                    Como posso acompanhar meu pedido?
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Após a confirmação do pagamento, você receberá um código de rastreamento por email e SMS. Use esse código para acompanhar sua entrega.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.contact-icon {
    transition: transform 0.3s ease;
}

.contact-icon:hover {
    transform: scale(1.1);
}

.card {
    border-radius: 15px;
}

.btn {
    border-radius: 8px;
}

.accordion-button {
    border-radius: 8px !important;
}

.accordion-item {
    border-radius: 8px !important;
    margin-bottom: 10px;
    border: 1px solid #e9ecef !important;
}
</style>
@endsection
