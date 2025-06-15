@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="product__content">
        <div class="tab-list__container">
            <div class="tab-list">
                <div class="tab-list__item">
                    <a href="/" class="{{ request()->get('page') !==    'mylist' ? 'active' : '' }}">おすすめ</a>
                </div>
                <div class="tab-list__item">
                    <a href="/?page=mylist" class="{{ request()->get    ('page') === 'mylist' ? 'active' : '' }}">マイリスト</a>
                </div>
            </div>
        </div>
        <div class="products-list">
            <div class="product-contents">
                @foreach ($products as $product)
                <div class="product-content">
                    <a href="/products/{{ $product->id }}" class="product-link">
                        <img src="{{ Str::startsWith($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" alt="商品画像" class="img-content" />
                        <div class="detail-content">
                            <p>{{$product->name}}</p>
                            @if (in_array($product->id, $soldProductIds))
                            <span class="sold-label">Sold</span>
                            @endif
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection