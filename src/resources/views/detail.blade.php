@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="product__detail">
    <div class="product__image-area">
        <img class="product__image" src="{{ Str::startsWith($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}" alt="商品画像">
    </div>
    <div class="product__description">
        <div class="product__title">
            <h1 class="product__title-name">{{ $product->name }}</h1>
            <p class="brand__name">{{ $product->brand_name ?? '' }}</p>
        </div>
        <div class="price">
            <h2 class="price-tag">￥{{ $product->price }}<span>(税込み)</span></h2>
        </div>
        <div class="evaluation">
            <form action="/products/{{ $product->id }}/like" method="POST" class="like-form">
                @csrf
                <button type="submit" class="like-button {{ $isLiked ? 'liked' : '' }}">☆</button>
                <span class="like-count">{{ $likesCount }}</span>
            </form>
            <img class="evaluation__item" src="/image/comment.png" alt="">
            <p class="evaluation__num">{{ $commentsCount }}</p>
        </div>
        <div class="buy__form">
            <form class="buy__form-btn" action="/purchase/{{ $product->id }}" method="get">
                @csrf
                <button>購入手続きへ</button>
            </form>
        </div>
        <div class="descrioption">
            <h2 class="descrioption__header">商品説明</h2>
            <p>{{ $product->description }}</p>
        </div>
        <div class="info">
            <h2 class="info__header">商品の情報</h2>
            <p class="info__category">カテゴリー:
                @foreach($categories as $category)
                <span>{{$category->category}}</span>
                @endforeach
            </p>
            <p class="info__condtion">商品の状態:
                <span>{{$condition->name}}</span></p>
        </div>
        <div class="comments">
            <h2 class="comments__header">コメント ({{ $comments->count() }})</h2>
            @foreach($comments as $comment)
            <div class="comment__box">
                <img class="comment__avatar" src="{{ asset($comment->user->profile->img_url ?? 'img/default.png') }}" alt="ユーザー画像">
                <div class="comment__body">
                    <p class="comment__user">{{ $comment->user->name }}</p>
                    <p class="comment__text">{{ $comment->comment }}</p>
                </div>
            </div>
            @endforeach

            <form class="comment__form" action="{{ route('products.comment', ['product' => $product->id]) }}" method="post">
                @csrf
                <label for="comment">商品へのコメント</label>
                <textarea name="comment"></textarea>
                    @error('comment')
                    {{ $message }}
                    @enderror
                <button type="submit">コメントを送信する</button>
            </form>
        </div>
    </div>
</div>
@endsection