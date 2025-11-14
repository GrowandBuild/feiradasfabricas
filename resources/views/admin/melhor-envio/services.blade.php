@extends('admin.layouts.app')

@section('title', 'Melhor Envio - Serviços')
@section('page-icon', 'bi bi-box-seam')
@section('page-title', 'Serviços do Melhor Envio')
@section('page-description')
Gerencie quais serviços/transportadoras serão usados nas cotações. Similar ao WooCommerce: marque/desmarque e salve.
@endsection

@section('content')
<div class="row">
  <div class="col-12 col-xl-9">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span>Serviços Disponíveis @if($sandbox)<span class="badge bg-secondary ms-2">Sandbox</span>@endif</span>
        <a href="{{ route('admin.melhor-envio.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Voltar</a>
      </div>
      <form method="POST" action="{{ route('admin.melhor-envio.services.save') }}">
        @csrf
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr>
                <th style="width:60px">Ativo</th>
                <th>ID</th>
                <th>Serviço</th>
                <th>Transportadora</th>
                <th>Prazo (dias)</th>
              </tr>
            </thead>
            <tbody>
              @foreach($services as $s)
              <tr>
                <td>
                  <input type="checkbox" name="service_ids[]" value="{{ $s['id'] }}" {{ $s['enabled'] ? 'checked' : '' }}>
                </td>
                <td><code>{{ $s['id'] }}</code></td>
                <td>{{ $s['name'] }}</td>
                <td>{{ $s['company'] }}</td>
                <td>{{ $s['delivery_time'] ?? '-' }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="p-3 border-top bg-light d-flex justify-content-between align-items-center">
          <div>
            <small class="text-muted">Selecione os serviços que serão considerados nas cotações. Se nenhum for marcado, o sistema usará o fallback padrão.</small><br>
            @if($error)<small class="text-danger">Aviso: {{ $error }}</small>@endif
          </div>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Salvar serviços</button>
        </div>
      </form>
    </div>
  </div>
  <div class="col-12 col-xl-3">
    <div class="card">
      <div class="card-header">Ajuda</div>
      <div class="card-body small">
        <p>Esta lista é obtida da API (<code>/shipment/companies</code>). Se não foi possível carregar, exibimos uma lista base de serviços comuns.</p>
        <ul>
          <li>Desmarcar um serviço -> ele deixa de aparecer na cotação.</li>
          <li>Marcar -> incluído na próxima requisição.</li>
          <li>Alterações impactam o cache; a próxima cotação força revalidação.</li>
        </ul>
        <p class="mb-0">Para adicionar markup ou regras avançadas (ocultar acima de X dias), podemos criar extensões depois.</p>
      </div>
    </div>
  </div>
</div>
@endsection
