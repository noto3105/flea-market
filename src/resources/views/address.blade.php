@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="address__content">
        <div class="address-form__heading">
            <h1 class="address-form__heading-title">住所の変更</h1>
        </div>
        <form class="address-form" action="/address" method="post">
            @csrf
            <div class="form__group">
                <div class="form__group-title">
                    <p class="form__label--item">郵便番号</p>
                </div>
                <div>
                    <div class="form__input--text">
                        <input type="text" name="postcode" value="{{ old('postcode') }}">
                    </div>
                    <div class="form__error">
                        @error('postcode')
                        {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <p class="form__label--item">住所</p>
                </div>
                <div class="form__input--text">
                   <input type="text" name="address" value="{{ old('address') }}">
                </div>
               <div class="form__error">
                    @error('address')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <p class="form__label--item">建物名</p>
                </div>
                <div class="form__input--text">
                   <input type="text" name="building" value="{{ old('building') }}">
                </div>
               <div class="form__error">
                    @error('building')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit" type="submit">更新する</button>
            </div>
        </form>
    </div>
</div>
@endsection