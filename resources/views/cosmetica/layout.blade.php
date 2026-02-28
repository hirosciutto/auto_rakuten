<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'COSMETICA')</title>
    <link rel="icon" href="{{ asset('assets/cropped-icon-32x32.png') }}" sizes="32x32" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/cosmetica.css'])
</head>
<body>
    <header class="c-header">
        <div class="c-container">
            <div class="c-header__inner">
                <a href="{{ url('/') }}" aria-label="COSMETICA トップへ">
                    <img
                        src="{{ asset('assets/title.png') }}"
                        alt="COSMETICA"
                        class="c-header__logo"
                        width=""
                        height="40"
                        decoding="async"
                    />
                </a>
                <div class="c-header__search">
                    <form action="{{ url('/') }}" method="get" role="search">
                        <label for="header-search" class="visually-hidden">キーワードで検索</label>
                        <input
                            type="search"
                            id="header-search"
                            name="keyword"
                            class="c-header__search-input"
                            placeholder="キーワードで検索"
                            value="{{ request('keyword', '') }}"
                            autocomplete="off"
                            aria-label="キーワードで検索"
                        />
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="c-footer">
        <div class="c-container">
            <p>&copy; {{ date('Y') }} COSMETICA. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
