<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'IELTS Focus giúp người học Việt Nam luyện IELTS theo lộ trình rõ ràng với topic, từ vựng, từ điển, bài test ngắn và lịch sử lỗi sai.')">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2457d6">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:site_name" content="IELTS Focus">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'IELTS Focus')">
    <meta property="og:description" content="@yield('meta_description', 'IELTS Focus giúp người học Việt Nam luyện IELTS theo lộ trình rõ ràng với topic, từ vựng, từ điển, bài test ngắn và lịch sử lỗi sai.')">
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Mở menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <div class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <a class="nav-link {{ request()->routeIs('topics.*') ? 'active' : '' }}" href="{{ route('topics.index') }}">Chủ đề</a>
                    <a class="nav-link {{ request()->routeIs('vocabularies.index', 'vocabularies.show') ? 'active' : '' }}" href="{{ route('vocabularies.index') }}">Từ vựng</a>
                    <a class="nav-link {{ request()->routeIs('dictionary.*') ? 'active' : '' }}" href="{{ route('dictionary.index') }}">Từ điển</a>
                    <a class="nav-link {{ request()->routeIs('vocabularies.flashcards') ? 'active' : '' }}" href="{{ route('vocabularies.flashcards') }}">Thẻ ôn từ</a>
                    <a class="nav-link {{ request()->routeIs('tests.*') ? 'active' : '' }}" href="{{ route('tests.index') }}">Luyện bài</a>
                    <a class="nav-link {{ request()->routeIs('prep.*') ? 'active' : '' }}" href="{{ route('prep.index') }}">Lộ trình</a>
                    <a class="nav-link {{ request()->routeIs('search.*') ? 'active' : '' }}" href="{{ route('search.index') }}">Tìm kiếm</a>
                    @auth
                        <div class="nav-item dropdown ms-lg-2">
                            <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->name }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-end account-menu">
                                @if (auth()->user()->isAdmin())
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">Trang quản lý</a>
                                @else
                                    <a class="dropdown-item" href="{{ route('account.index') }}">Quản lý tài khoản</a>
                                @endif
                                <a class="dropdown-item" href="{{ route('history.index') }}">Bài đã làm</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">Đăng xuất</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Đăng nhập</a>
                        <a class="btn btn-primary btn-sm ms-lg-2 mt-2 mt-lg-0" href="{{ route('register') }}">Tạo tài khoản</a>
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
                <p class="footer-copy">Nền tảng tự học IELTS bằng tiếng Việt, tập trung vào lộ trình, từ vựng, bài luyện ngắn và ôn lại lỗi sai thật.</p>
            </div>
            <nav class="footer-links" aria-label="Liên kết cuối trang">
                <a href="{{ route('topics.index') }}">Chủ đề IELTS</a>
                <a href="{{ route('vocabularies.index') }}">Từ vựng</a>
                <a href="{{ route('dictionary.index') }}">Từ điển</a>
                <a href="{{ route('tests.index') }}">Luyện bài</a>
                <a href="{{ route('prep.index') }}">Lộ trình</a>
                <a href="{{ route('search.index') }}">Tìm kiếm</a>
            </nav>
        </div>
    </footer>
</div>
</body>
</html>
