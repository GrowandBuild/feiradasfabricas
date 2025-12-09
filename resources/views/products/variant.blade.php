@extends('layouts.app')

@section('title', 'Variações removidas')

@section('content')
    <div class="container py-5">
        <div class="alert alert-warning">
            <h4 class="alert-heading">Sistema de variações removido</h4>
            <p>O sistema de variações e atributos foi removido deste projeto. Por favor, acesse a página do produto principal.</p>
            @if(isset($product) && $product)
                <a href="{{ route('product', $product->slug) }}" class="btn btn-primary">Ir para o produto</a>
            @else
                <a href="{{ route('products') }}" class="btn btn-secondary">Voltar à listagem de produtos</a>
            @endif
        </div>
    </div>
@endsection
