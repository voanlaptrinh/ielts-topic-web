@extends('layouts.app')

@section('title', 'IELTS Focus - Ôn IELTS thực tế')

@section('content')
    <section class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">Lộ trình IELTS tự học</span>
        <h1 class="display-title">Ôn topic, từ vựng và dạng bài IELTS trong một nơi.</h1>
        <p class="lead-copy">Chọn chủ đề để luyện Speaking/Writing, tra từ theo ngữ cảnh, làm quiz nhanh và theo dõi lỗi sai sau mỗi lần luyện.</p>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <a class="btn btn-primary" href="{{ route('tests.index') }}">Bắt đầu luyện bài</a>
            <a class="btn btn-outline-primary" href="{{ route('vocabularies.index') }}">Tra từ IELTS</a>
        </div>

        <div class="row g-3 mt-4">
            <div class="col-6 col-lg-3">
                <div class="metric-card">
                    <span class="metric-value">{{ number_format($stats['topics']) }}</span>
                    <span class="metric-label">Chủ đề Speaking/Writing</span>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="metric-card">
                    <span class="metric-value">{{ number_format($stats['vocabularies']) }}</span>
                    <span class="metric-label">Từ vựng IELTS chọn lọc</span>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="metric-card">
                    <span class="metric-value">{{ number_format($stats['dictionary_words']) }}</span>
                    <span class="metric-label">Mục từ trong từ điển</span>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="metric-card">
                    <span class="metric-value">6</span>
                    <span class="metric-label">Cấp độ luyện tập</span>
                </div>
            </div>
        </div>
    </section>

    @auth
        @if ($recentAttempts->isNotEmpty())
            <section class="soft-panel mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h2 class="section-title mb-0">Lần luyện gần đây</h2>
                    <a class="btn btn-outline-primary btn-sm" href="{{ route('history.index') }}">Xem tiến độ</a>
                </div>
                <div class="row g-3">
                    @foreach ($recentAttempts as $attempt)
                        <div class="col-md-4">
                            <div class="metric-card">
                                <span class="metric-label">{{ $attempt->test_type }} - {{ $attempt->level }}</span>
                                <span class="metric-value mt-2">{{ $attempt->score }}/{{ $attempt->total }}</span>
                                <span class="footer-note">{{ $attempt->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    @endauth

    <section class="mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
            <div>
                <span class="eyebrow">Kho topic</span>
                <h2 class="h3 mb-0">Chọn chủ đề để luyện trả lời</h2>
            </div>
            <a class="btn btn-outline-primary" href="{{ route('vocabularies.quiz') }}">Quiz từ vựng nhanh</a>
        </div>

        <div class="resource-list">
            @foreach ($topics as $topic)
                <article class="card study-card">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge text-bg-primary">{{ $topic->part }}</span>
                            <span class="badge text-bg-success">{{ $topic->difficulty }}</span>
                        </div>
                        <h3 class="h5 mt-3">{{ $topic->title }}</h3>
                        <p class="text-muted flex-grow-1">{{ $topic->description }}</p>
                        <a class="btn btn-primary align-self-start" href="{{ route('topics.show', $topic->slug) }}">Ôn topic này</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
