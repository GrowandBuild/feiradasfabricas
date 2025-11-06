@extends('emails.layout')

@section('content')
@if($customer->b2b_status === 'approved')
    <h2>✅ Seu Cadastro B2B foi Aprovado!</h2>
    <p>Parabéns! Sua solicitação de cadastro B2B foi <strong>aprovada</strong> e agora você tem acesso a preços especiais e condições diferenciadas.</p>
@elseif($customer->b2b_status === 'rejected')
    <h2>❌ Status do seu Cadastro B2B</h2>
    <p>Infelizmente, sua solicitação de cadastro B2B foi <strong>rejeitada</strong>.</p>
@else
    <h2>⏳ Status do seu Cadastro B2B</h2>
    <p>O status do seu cadastro B2B foi atualizado.</p>
@endif

<div class="order-info">
    <h3>📋 Informações da Empresa</h3>
    
    <div class="order-details">
        <div class="detail-item">
            <span class="detail-label">Nome da Empresa</span>
            <span class="detail-value"><strong>{{ $customer->company_name }}</strong></span>
        </div>
        <div class="detail-item">
            <span class="detail-label">CNPJ</span>
            <span class="detail-value">{{ $customer->cnpj }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Status</span>
            <span class="detail-value">
                <span class="status-badge" style="background-color: {{ $status_bg_color }}; color: {{ $status_color }};">
                    {{ $status_label }}
                </span>
            </span>
        </div>
    </div>
</div>

@if($customer->b2b_status === 'approved')
    <div style="background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 20px; margin: 20px 0;">
        <h4 style="color: #155724; margin-top: 0;">🎉 Bem-vindo ao Programa B2B!</h4>
        <p style="margin-bottom: 15px;">
            Agora você pode aproveitar todas as vantagens de ser um cliente B2B:
        </p>
        <ul style="margin-bottom: 0;">
            <li>✅ <strong>Preços especiais</strong> para empresas</li>
            <li>✅ <strong>Condições diferenciadas</strong> de pagamento</li>
            <li>✅ <strong>Atendimento prioritário</strong></li>
            <li>✅ <strong>Acesso a produtos exclusivos</strong></li>
            @if($customer->credit_limit)
                <li>✅ <strong>Limite de crédito:</strong> R$ {{ number_format($customer->credit_limit, 2, ',', '.') }}</li>
            @endif
        </ul>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('home') }}" class="button">Acessar a Loja</a>
    </div>
@elseif($customer->b2b_status === 'rejected')
    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 20px; margin: 20px 0;">
        <h4 style="color: #721c24; margin-top: 0;">📝 Informações Importantes</h4>
        @if($customer->b2b_notes)
            <p style="margin-bottom: 15px;">
                <strong>Observações:</strong><br>
                {{ $customer->b2b_notes }}
            </p>
        @endif
        <p style="margin-bottom: 0;">
            Se você acredita que houve um erro ou deseja mais informações sobre a rejeição, entre em contato conosco através do email <strong>{{ $company_email }}</strong> ou telefone <strong>{{ $company_phone }}</strong>.
        </p>
    </div>
@else
    <div style="background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 20px; margin: 20px 0;">
        <h4 style="color: #856404; margin-top: 0;">⏳ Aguardando Aprovação</h4>
        <p style="margin-bottom: 0;">
            Seu cadastro ainda está em análise. Você receberá uma nova notificação assim que o status for atualizado.
        </p>
    </div>
@endif

<div class="order-info">
    <h3>👤 Seus Dados de Acesso</h3>
    
    <div class="order-details">
        <div class="detail-item">
            <span class="detail-label">Email</span>
            <span class="detail-value">{{ $customer->email }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Nome</span>
            <span class="detail-value">{{ $customer->first_name }} {{ $customer->last_name }}</span>
        </div>
    </div>
</div>

@if($customer->b2b_status === 'approved')
    <div style="background-color: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; padding: 20px; margin: 20px 0;">
        <h4 style="color: #004085; margin-top: 0;">💡 Como Começar</h4>
        <ol style="margin-bottom: 0;">
            <li>Acesse o site com seu email e senha cadastrados</li>
            <li>Navegue pelos produtos e veja os preços especiais B2B</li>
            <li>Adicione produtos ao carrinho e finalize seu pedido</li>
            <li>Aproveite as condições especiais de pagamento</li>
        </ol>
    </div>
@endif

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('customer.login') }}" class="button">Fazer Login</a>
</div>

<p>Se você tiver alguma dúvida, entre em contato conosco através do email <strong>{{ $company_email }}</strong> ou telefone <strong>{{ $company_phone }}</strong>.</p>

<p>Equipe {{ $company_name ?? 'Feira das Fábricas' }}</p>
@endsection

