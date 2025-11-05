@extends('emails.layout')

@section('content')
<h2>🧪 Email de Teste</h2>

<p>Este é um email de teste para verificar se as configurações de email estão funcionando corretamente.</p>

<div class="order-info">
    <h3>✅ Configurações de Email</h3>
    
    <div class="order-details">
        <div class="detail-item">
            <span class="detail-label">Data do Teste</span>
            <span class="detail-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Status</span>
            <span class="detail-value">
                <span class="status-badge status-confirmed">Funcionando</span>
            </span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Remetente</span>
            <span class="detail-value">{{ $company_name ?? 'Feira das Fábricas' }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Email de Origem</span>
            <span class="detail-value">{{ $company_email ?? 'contato@feiradasfabricas.com' }}</span>
        </div>
    </div>
</div>

<div style="background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h4 style="color: #155724; margin-top: 0;">🎉 Sucesso!</h4>
    <p style="margin-bottom: 0;">
        {{ $test_message }}
    </p>
</div>

<h3>📧 Templates de Email Disponíveis</h3>
<ul>
    <li><strong>Confirmação de Pedido:</strong> Enviado quando um pedido é criado</li>
    <li><strong>Confirmação de Pagamento:</strong> Enviado quando o pagamento é confirmado</li>
    <li><strong>Notificação de Envio:</strong> Enviado quando o pedido é despachado</li>
    <li><strong>Nota Fiscal:</strong> Enviado com a nota fiscal em anexo</li>
    <li><strong>Confirmação de Entrega:</strong> Enviado quando o pedido é entregue</li>
</ul>

<h3>⚙️ Configurações Testadas</h3>
<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div>
            <strong>SMTP Host:</strong><br>
            <small>{{ setting('smtp_host', 'Não configurado') }}</small>
        </div>
        <div>
            <strong>SMTP Port:</strong><br>
            <small>{{ setting('smtp_port', 'Não configurado') }}</small>
        </div>
        <div>
            <strong>Encryption:</strong><br>
            <small>{{ setting('smtp_encryption', 'Não configurado') }}</small>
        </div>
        <div>
            <strong>Username:</strong><br>
            <small>{{ setting('smtp_username', 'Não configurado') ? 'Configurado' : 'Não configurado' }}</small>
        </div>
    </div>
</div>

<h3>📱 Próximos Passos</h3>
<ol>
    <li><strong>Configurar Templates:</strong> Personalize os templates conforme necessário</li>
    <li><strong>Testar Automatização:</strong> Faça um pedido de teste para verificar o fluxo</li>
    <li><strong>Configurar SEFAZ:</strong> Configure as notas fiscais se necessário</li>
    <li><strong>Monitorar Logs:</strong> Acompanhe os logs de envio de email</li>
</ol>

<div style="background-color: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <h4 style="color: #004085; margin-top: 0;">💡 Dicas Importantes</h4>
    <ul style="margin-bottom: 0;">
        <li>Mantenha as configurações SMTP atualizadas</li>
        <li>Teste regularmente o envio de emails</li>
        <li>Monitore a caixa de spam dos clientes</li>
        <li>Configure SPF e DKIM no DNS se possível</li>
    </ul>
</div>

<h3>🔧 Configurações Avançadas</h3>
<p>Para melhorar a entrega dos emails, considere:</p>
<ul>
    <li>Configurar SPF no DNS do domínio</li>
    <li>Implementar DKIM para autenticação</li>
    <li>Usar serviços como Mailgun ou SendGrid</li>
    <li>Configurar DMARC para proteção</li>
</ul>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('admin.settings.index') }}" class="button">Configurações de Email</a>
</div>

<p><strong>Problemas com o email?</strong> Verifique as configurações SMTP e entre em contato com o suporte técnico.</p>

<p>Equipe {{ $company_name ?? 'Feira das Fábricas' }}</p>
@endsection
