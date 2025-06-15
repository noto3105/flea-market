@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/buy.css') }}">
@endsection

@section('content')
<form class="container" action="/buy" method="post">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <div class="left__content">
        <div class="product__info">
            <div class="product__image">
                <img src="{{ $product->image }}" alt="商品画像">
            </div>
            <div class="product">
                <h2 class="product__name">{{ $product->name }}</h2>
                <h2 class="product__price">￥{{ $product->price }}</h2>
            </div>
        </div>
        <hr>
        <div class="payment__method">
            <label for="payment_method">支払い方法</label>
            <select name="payment_method"   id="payment_method">
                <optional value="">選択してください<optional>
                <option value="credit">クレジットカード</option>
                <option value="convenience_store">コンビニ払い</option>                
            </select>
        </div>
        <hr>
        <div class="address">
            <p>配送先</p>
            <p>〒 {{ $user->profile->postcode ?? 'XXX-YYYY' }}</p>
            <p>{{ $user->profile->address ?? 'ここには住所と建物が入ります' }}</p>
            <p>{{ optional($user->profile)->building }}</p>                
            <a href="/purchase/address/{{ $product->id }}">変更する</a>
        </div>
    </div>
    <div class="right__wrapper">
    <div class="right__content">
            <div class="summary">
                <p class="summary-item">
                    商品代金 <span>￥{{ number_format($product->price) }}</span>
                </p>
                <hr class="summary-divider">
                <p class="summary-item">
                    支払い方法 
                    <span>
                    {{ old('payment_method')=='credit' ? 'クレジットカード' 
                        : old('payment_method')=='convenience_store' ? 'コンビニ払い' : '未選択' }}
                    </span>
                </p>
            </div>
        </div>
        <div class="right__button">
            <button type="submit" class="buy__button">購入する</button>
        </div>
    </div>
</form>
@endsection