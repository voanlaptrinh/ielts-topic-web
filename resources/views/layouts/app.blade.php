<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'IELTS Focus giÃºp ngÆ°á»i há»c Viá»‡t Nam luyá»‡n IELTS theo lá»™ trÃ¬nh rÃµ rÃ ng vá»›i topic, tá»« vá»±ng, tá»« Ä‘iá»ƒn, bÃ i test ngáº¯n vÃ  lá»‹ch sá»­ lá»—i sai.')">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2457d6">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:site_name" content="IELTS Focus">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'IELTS Focus')">
    <meta property="og:description" content="@yield('meta_description', 'IELTS Focus giÃºp ngÆ°á»i há»c Viá»‡t Nam luyá»‡n IELTS theo lá»™ trÃ¬nh rÃµ rÃ ng vá»›i topic, tá»« vá»±ng, tá»« Ä‘iá»ƒn, bÃ i test ngáº¯n vÃ  lá»‹ch sá»­ lá»—i sai.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <title>@yield('title', 'IELTS Focus')</title>
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="app-shell" data-public-shell>
    <nav class="navbar navbar-expand-lg border-bottom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('topics.index') }}">IELTS Focus</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Má»Ÿ menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <div class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <a class="nav-link {{ request()->routeIs('topics.*') ? 'active' : '' }}" href="{{ route('topics.index') }}">Chá»§ Ä‘á»</a>
                    <a class="nav-link {{ request()->routeIs('vocabularies.index', 'vocabularies.show') ? 'active' : '' }}" href="{{ route('vocabularies.index') }}">Tá»« vá»±ng</a>
                    <a class="nav-link {{ request()->routeIs('dictionary.*') ? 'active' : '' }}" href="{{ route('dictionary.index') }}">Tá»« Ä‘iá»ƒn</a>
                    <a class="nav-link {{ request()->routeIs('vocabularies.flashcards') ? 'active' : '' }}" href="{{ route('vocabularies.flashcards') }}">Flashcard</a>
                    <a class="nav-link {{ request()->routeIs('tests.*') ? 'active' : '' }}" href="{{ route('tests.index') }}">Luyá»‡n bÃ i</a>
                    <a class="nav-link {{ request()->routeIs('prep.*') ? 'active' : '' }}" href="{{ route('prep.index') }}">Prep Hub</a>
                    <a class="nav-link {{ request()->routeIs('search.*') ? 'active' : '' }}" href="{{ route('search.index') }}">TÃ¬m kiáº¿m</a>
                    @auth
                        <div class="nav-item dropdown ms-lg-2">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->name }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-end account-menu">
                                @if (auth()->user()->isAdmin())
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">Trang quáº£n lÃ½</a>
                                @else
                                    <a class="dropdown-item" href="{{ route('account.index') }}">Quáº£n lÃ½ tÃ i khoáº£n</a>
                                @endif
                                <a class="dropdown-item" href="{{ route('history.index') }}">BÃ i Ä‘Ã£ lÃ m</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">ÄÄƒng xuáº¥t</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">ÄÄƒng nháº­p</a>
                        <a class="btn btn-primary btn-sm ms-lg-2 mt-2 mt-lg-0" href="{{ route('register') }}">Táº¡o tÃ i khoáº£n</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="container main-content" data-public-content>
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container site-footer-grid">
            <div>
                <a class="footer-brand" href="{{ route('topics.index') }}">IELTS Focus</a>
                <p class="footer-copy">Ná»n táº£ng tá»± há»c IELTS báº±ng tiáº¿ng Viá»‡t, táº­p trung vÃ o lá»™ trÃ¬nh, tá»« vá»±ng, bÃ i luyá»‡n ngáº¯n vÃ  Ã´n láº¡i lá»—i sai tháº­t.</p>
            </div>
            <nav class="footer-links" aria-label="LiÃªn káº¿t cuá»‘i trang">
                <a href="{{ route('topics.index') }}">Topic IELTS</a>
                <a href="{{ route('vocabularies.index') }}">Tá»« vá»±ng</a>
                <a href="{{ route('dictionary.index') }}">Tá»« Ä‘iá»ƒn</a>
                <a href="{{ route('tests.index') }}">Luyá»‡n bÃ i</a>
                <a href="{{ route('prep.index') }}">Prep Hub</a>
                <a href="{{ route('search.index') }}">TÃ¬m kiáº¿m</a>
            </nav>
        </div>
    </footer>
</div>
</body>
</html>
