<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coachtechフリマアプリ</title>
    <link rel="stylesheet" href="{{asset('css/sanitize.css')}}">
    <link rel="stylesheet" href="{{asset('css/common.css')}}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__left">
                <a href="/"><img src="/image/logo.png" alt="coachtechロゴ"></a>
                <form class="search-form" action="/" method="get">
                @csrf
                    <input type="text" name="keyword" class="keyword" placeholder="何をお探しですか？">
                </form>
            </div>
            <div class="header__nav">
                @auth
                    <form action="/logout" method="POST" style="display:inline;">
                    @csrf
                        <button type="submit" class="log-btn">ログアウト</button>
                    </form>
                    <a href="/mypage">マイページ</a>
                    <a class="sell-btn" href="/sell">出品</a>
                @endauth

                @guest
                    <a class="log-btn" href="/login">ログイン</a>
                    <a href="/mypage">マイページ</a>
                    <a class="sell-btn" href="/sell">出品</a>
                @endguest
            </div>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>