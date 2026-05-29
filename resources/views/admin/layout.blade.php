<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin - IELTS Focus')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
<div class="admin-shell" data-admin-shell>
    <aside class="admin-sidebar">
        <div class="admin-sidebar-brand">
            <span class="admin-brand-mark">IF</span>
            <div>
                <strong>IELTS Focus</strong>
                <small>Khu quản trị</small>
            </div>
        </div>

        <nav class="admin-sidebar-nav" aria-label="Admin navigation">
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <span>Tổng quan</span>
            </a>
            <a class="{{ request()->routeIs('admin.topics.*') ? 'active' : '' }}" href="{{ route('admin.topics.index') }}">
                <span>Chủ đề IELTS</span>
            </a>
            <a class="{{ request()->routeIs('admin.practice-tests.*') ? 'active' : '' }}" href="{{ route('admin.practice-tests.index') }}">
                <span>Đề Reading/Listening</span>
            </a>
            <a class="{{ request()->routeIs('admin.submissions.*') ? 'active' : '' }}" href="{{ route('admin.submissions.index') }}">
                <span>Chấm bài</span>
            </a>
            <a class="{{ request()->routeIs('admin.vocabularies.*') ? 'active' : '' }}" href="{{ route('admin.vocabularies.index') }}">
                <span>Từ vựng</span>
            </a>
            <a class="{{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}" href="{{ route('admin.faqs.index') }}">
                <span>FAQ trang home</span>
            </a>
            <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                <span>User</span>
            </a>
            <a href="{{ route('topics.index') }}">
                <span>Xem website</span>
            </a>
        </nav>
    </aside>

    <section class="admin-workspace" data-admin-workspace>
        <header class="admin-topbar">
            <div>
                <span class="eyebrow" data-admin-eyebrow>@yield('admin_eyebrow', 'Quản trị')</span>
                <h1 data-admin-title>@yield('admin_title', 'Tổng quan')</h1>
            </div>
            <div class="admin-topbar-user">
                <span>{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-primary btn-sm" type="submit">Đăng xuất</button>
                </form>
            </div>
        </header>

        @include('admin.partials.status')

        <div class="admin-content" data-admin-content>
            @yield('admin_content')
        </div>
    </section>
</div>
</body>
</html>
