@extends('emails.layout')

@section('content')
<h2>🏢 Novo Cadastro B2B Aguardando Aprovação</h2>

<p>Uma nova empresa solicitou cadastro B2B e está aguardando sua aprovação.</p>

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
            <span class="detail-label">Inscrição Estadual</span>
            <span class="detail-value">{{ $customer->ie ?? 'Não informado' }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Pessoa de Contato</span>
            <span class="detail-value">{{ $customer->contact_person }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Departamento</span>
            <span class="detail-value">{{ $customer->department ?? 'Não informado' }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Status</span>
            <span class="detail-value">
                <span class="status-badge status-pending">Pendente</span>
            </span>
        </div>
    </div>
</div>

<div class="order-info">
    <h3>👤 Dados do Representante</h3>
    
    <div class="order-details">
        <div class="detail-item">
            <span class="detail-label">Nome Completo</span>
            <span class="detail-value">{{ $customer->first_name }} {{ $customer->last_name }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Email</span>
            <span class="detail-value">{{ $customer->email }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Telefone</span>
            <span class="detail-value">{{ $customer->phone }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Data do Cadastro</span>
            <span class="detail-value">{{ $customer->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</div>

<div class="order-info">
    <h3>📍 Endereço da Empresa</h3>
    
    <div class="order-details">
        <div class="detail-item" style="grid-column: 1 / -1;">
            <span class="detail-label">Endereço Completo</span>
            <span class="detail-value">
                {{ $customer->address }}, {{ $customer->number }}<br>
                @if($customer->complement)
                    {{ $customer->complement }}<br>
                @endif
                {{ $customer->neighborhood }}<br>
                {{ $customer->city }}/{{ $customer->state }}<br>
                CEP: {{ $customer->zip_code }}
            </span>
        </div>
    </div>
</div>

<div style="background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h4 style="color: #856404; margin-top: 0;">⚠️ Ação Necessária</h4>
    <p style="margin-bottom: 15px;">
        Este cadastro está <strong>pendente de aprovação</strong>. Acesse o painel administrativo para revisar e aprovar ou rejeitar a solicitação.
    </p>
    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="button" style="background-color: #ffc107; color: #000; margin-right: 10px;">
            👁️ Ver Detalhes
        </a>
        <a href="{{ route('admin.customers.index', ['b2b_status' => 'pending']) }}" class="button" style="background-color: #667eea;">
            📋 Ver Todos Pendentes
        </a>
    </div>
</div>

<h3>📝 Próximos Passos</h3>
<ol>
    <li><strong>Revisar Documentos:</strong> Verifique se o CNPJ e dados estão corretos</li>
    <li><strong>Validar Empresa:</strong> Confirme se a empresa está ativa e válida</li>
    <li><strong>Aprovar ou Rejeitar:</strong> Acesse o painel para tomar a decisão</li>
    <li><strong>Notificar Cliente:</strong> O cliente receberá um email automático após a decisão</li>
</ol>

<div style="background-color: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h4 style="color: #004085; margin-top: 0;">💡 Dicas de Aprovação</h4>
    <ul style="margin-bottom: 0;">
        <li>Verifique o CNPJ na Receita Federal</li>
        <li>Confirme se a empresa está ativa</li>
        <li>Analise o histórico e credibilidade</li>
        <li>Verifique o limite de crédito necessário</li>
    </ul>
</div>

<p style="margin-top: 30px;">
    <strong>Link Direto:</strong> <a href="{{ route('admin.customers.edit', $customer->id) }}">{{ route('admin.customers.edit', $customer->id) }}</a>
</p>

<p>Equipe {{ $company_name ?? 'Feira das Fábricas' }}</p>
@endsection

