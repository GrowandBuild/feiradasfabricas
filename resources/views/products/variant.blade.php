@extends('layouts.app')

@section('title', $pageTitle)
@section('meta_description', $metaDescription)

@section('content')
    <div class="container py-5">
        @php($linkDept = $currentDepartmentSlug ?? request()->get('department') ?? null)
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products') }}">Produtos</a></li>
            @if($product->categories->count() > 0)
                <li class="breadcrumb-item">
                    <a href="{{ route('products', ['category' => $product->categories->first()->slug]) }}">
                        {{ $product->categories->first()->name }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item"><a href="{{ route('product', $product->slug) }}{{ $linkDept ? '?department='.$linkDept : '' }}">{{ $product->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $variation->color }} {{ $variation->storage }} {{ $variation->ram }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="bg-white p-3 rounded shadow-sm">
                @php $images = $variantImages; @endphp
                @if(!empty($images))
                    <img src="{{ $images[0] }}" alt="{{ $pageTitle }}" class="img-fluid rounded w-100" id="main-variant-img">
                    <div class="d-flex gap-2 mt-2 flex-wrap">
                        @foreach($images as $i)
                            <img src="{{ $i }}" class="rounded border" style="width:76px;height:76px;object-fit:contain;cursor:pointer" onclick="document.getElementById('main-variant-img').src='{{ $i }}'">
                        @endforeach
                    </div>
                @else
                    <img src="{{ asset('images/no-image.svg') }}" class="img-fluid rounded w-100" alt="Sem imagem">
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <h1 class="h3 mb-2">{{ $pageTitle }}</h1>
            <p class="text-muted">{{ $metaDescription }}</p>
            <div class="mb-3">
                <span class="h4 text-primary">R$ {{ number_format($variation->price ?: $product->price, 2, ',', '.') }}</span>
                @if($variation->in_stock)
                    <span class="badge bg-success ms-2">Em estoque</span>
                @else
                    <span class="badge bg-secondary ms-2">Fora de estoque</span>
                @endif
            </div>
            <ul class="list-unstyled mb-3">
                @if($variation->color)<li><strong>Cor:</strong> {{ $variation->color }}</li>@endif
                @if($variation->storage)<li><strong>Armazenamento:</strong> {{ $variation->storage }}</li>@endif
                @if($variation->ram)<li><strong>RAM:</strong> {{ $variation->ram }}</li>@endif
                @if($variation->sku)<li><strong>SKU:</strong> {{ $variation->sku }}</li>@endif
            </ul>

            <x-add-to-cart :product="$product" :variationId="$variation->id" :showQuantity="true" buttonText="Adicionar ao Carrinho" buttonClass="btn btn-primary btn-lg w-100" />

            <div class="mt-4">
                <a href="{{ route('product', $product->slug) }}{{ $linkDept ? '?department='.$linkDept : '' }}" class="btn btn-outline-secondary w-100">Ver outras variações</a>
            </div>
        </div>
    </div>

    @if(!empty($relatedProducts) && $relatedProducts->count())
        <div class="mt-5">
            <h3 class="mb-3">Relacionados</h3>
            <div class="row">
                @foreach($relatedProducts as $rp)
                    <div class="col-md-3 mb-3">
                        <a class="text-decoration-none" href="{{ route('product', $rp->slug) }}{{ $linkDept ? '?department='.$linkDept : '' }}">
                            <div class="card h-100">
                                <img src="{{ $rp->first_image }}" class="card-img-top" style="height:180px;object-fit:contain" alt="{{ $rp->name }}">
                                <div class="card-body">
                                    <h6 class="card-title">{{ Str::limit($rp->name, 60) }}</h6>
                                    <span class="text-primary fw-semibold">R$ {{ number_format($rp->price, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script type="application/ld+json">{!! json_encode($schemaProduct, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!}</script>
@endsection
