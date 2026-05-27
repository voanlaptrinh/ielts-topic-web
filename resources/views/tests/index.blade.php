@extends('layouts.app')

@section('title', 'Luyện bài IELTS - IELTS Focus')

@section('content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">Practice levels</span>
        <h1 class="display-title">6 cấp độ luyện IELTS</h1>
        <p class="lead-copy mb-0">Mỗi cấp độ gom nhiều dạng bài: từ vựng, ngữ pháp, thành phần câu, chính tả, ngữ cảnh và dạng đọc hiểu IELTS.</p>
    </header>

    <section class="resource-list">
        @foreach ($levels as $key => $level)
            <article class="card study-card">
                <div class="card-body d-flex flex-column">
                    <span class="badge text-bg-primary align-self-start">{{ $level['band'] }}</span>
                    <h2 class="h5 mt-3">{{ $level['name'] }}</h2>
                    <p class="text-muted flex-grow-1">{{ $level['description'] }}</p>
                    <div class="metric-card mb-3">
                        <span class="metric-value">{{ $level['question_count'] }}</span>
                        <span class="metric-label">câu trong mỗi bài tập</span>
                    </div>
                    <a class="btn btn-primary align-self-start" href="{{ route('tests.level', $key) }}">Vào cấp độ này</a>
                </div>
            </article>
        @endforeach
    </section>
@endsection
