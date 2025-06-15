@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mylist.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="user__info">
        <img src="{{ asset('storage/' . $profile->img_url) }}" alt="プロフィール画像">
        <h1>{{ $user->name }}</h1>
        <a href="/mypage/profile">プロフィール編集</a>
    </div>
    <div class="tab-list__container">
        <div class="tab-list">
            <div class="tab-list__item">
                <a href="/mypage?tab=sell" class="{{ request('tab') !== 'buy' ? 'active' : '' }}">出品した商品</a>
            </div>
            <div class="tab-list__item">
                <a href="/mypage?tab=buy" class="{{ request('tab') === 'buy' ? 'active' : '' }}">購入した商品</a>
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
                    </div>
                </a>
            </div>
            @endforeach
        </div>        
    </div>
</div>
@endsection