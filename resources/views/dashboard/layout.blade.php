@extends('layouts.app')

@section('content')
    <div class="learner-shell">
        <aside class="learner-sidebar">
            <div class="learner-profile">
                <strong>{{ auth()->user()->name }}</strong>
                <span>{{ auth()->user()->email }}</span>
            </div>
            <nav class="learner-nav" aria-label="Learner navigation">
                <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Tổng quan</a>
                <a class="{{ request()->routeIs('history.*') ? 'active' : '' }}" href="{{ route('history.index') }}">Bài đã làm</a>
                <a href="{{ route('tests.index') }}">Luyện bài</a>
                <a href="{{ route('vocabularies.flashcards') }}">Flashcard</a>
                <a href="{{ route('dictionary.index') }}">Từ điển</a>
            </nav>
        </aside>

        <section class="learner-workspace">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @yield('dashboard_content')
        </section>
    </div>
@endsection
