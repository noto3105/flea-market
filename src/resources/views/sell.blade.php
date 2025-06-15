@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="header__inner">
        <h1 class="header__logo">商品の出品</h1>
    </div>
    <form action="/sell" class="sell__form" method="post" enctype="multipart/form-data">
        @csrf
        <div class="img">
            <p>商品画像</p>
            <div class="img__select">
            <label for="img_url" class="img__upload-button">画像を選択する</label>
            <input type="file" id="image" name="image" accept="image/png, image/jpeg" />
            </div>
        </div>
        <div class="detail-form">
            <div class="detail__title">
                <h2>商品の詳細</h2>
            </div>
            <div class="category">
                <div class="category__title">
                    <p>カテゴリー</p>
                </div>
                <div class="category__select">
                    @foreach ($categories as $category)
                        <input type="radio" name="categories[]" id="category_{{ $category->id }}" value="{{ $category->id }}">
                        <label for="category_{{ $category->id }}" class="category__button">{{ $category->category }}</label>
                    @endforeach
                </div>
            </div>
            <div class="condition">
                <div class="condition__title">
                    <p>商品の状態</p>
                </div>
                <div class="condition__select">
                    <select name="condition_id">
                        <option value="">選択してください</option>
                        @foreach ($conditions as $condition)
                            <option value="{{ $condition->id }}">{{ $condition->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="item__description">
        <h2>商品名と説明</h2>

        <div class="form__group">
            <label for="name">商品名</label>
            <input type="text" class="input__box" id="name" name="name">
        </div>

        <div class="form__group">
            <label for="brand">ブランド名</label>
            <input type="text" class="input__box" id="brand_name" name="brand_name">
        </div>

        <div class="form__group">
            <label for="description">商品の説明</label>
            <textarea class="input__box" id="description" name="description"></textarea>
        </div>

        <div class="form__group">
            <label for="price">販売価格</label>
            <input type="number" class="input__box" id="price" name="price" placeholder="¥">
        </div>
    </div>

    <div class="sell__button">
        <button type="submit" class="submit__button">出品する</button>
    </div>
    </form>
</div>
@endsection
