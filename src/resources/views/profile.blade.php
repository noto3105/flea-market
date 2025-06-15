@extends('layouts.header')

@section('css')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')
<div class="conatiner">
    <div class="profile__title">
        <h1 class="profile__title-logo">プロフィール設定</h1>
    </div>
    <form class="profile_form" action="/mypage/profile" method="post" enctype="multipart/form-data">
        @csrf
        <div class="user__img">
            <img src="{{ asset('storage/' . $profile->img_url) }}" alt="">
            <label for="img_url" class="img__upload-button">画像を選択する</label>
            <input type="file" id="img_url" name="img_url" accept="image/png, image/jpeg" />
        </div>
        <div class="form__content">
            <label for="name">ユーザー名</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
        </div>
        <div class="form__content">
            <label for="postcode">郵便番号</label>
            <input type="text" id="postcode" name="postcode" value="{{ old('postcode', $profile->postcode) }}">
        </div>
        <div class="form__content">
            <label for="address">住所</label>
            <input type="text" id="address" name="address" value="{{ old('address', $profile->address) }}">
        </div>
        <div class="form__content">
            <label for="building">建物名</label>
            <input type="text" id="building" name="building" value="{{ old('building', $profile->building) }}">
        </div>
        <div class="submit">
            <button type="submit" class="submit__button">更新する</button>
        </div>
    </form>
</div>
@endsection