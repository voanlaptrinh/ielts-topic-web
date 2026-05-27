@extends('layouts.app')

@section('title', $title . ' - IELTS Focus')

@section('content')
    @php
        $percent = $total > 0 ? round(($score / $total) * 100) : 0;
        $wrongResults = collect($results)->reject(fn ($result) => $result['is_correct']);
    @endphp

    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">Kết quả luyện tập</span>
        <h1 class="display-title">{{ $title }}</h1>
        <p class="lead-copy mb-0">Điểm: {{ $score }} / {{ $total }}</p>
        <div class="row g-3 mt-3">
            <div class="col-6 col-lg-3">
                <div class="metric-card">
                    <span class="metric-value">{{ $score }}/{{ $total }}</span>
                    <span class="metric-label">Điểm</span>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="metric-card">
                    <span class="metric-value">{{ $percent }}%</span>
                    <span class="metric-label">Độ chính xác</span>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="metric-card">
                    <span class="metric-value">{{ $wrongResults->count() }}</span>
                    <span class="metric-label">Lỗi cần xem lại</span>
                </div>
            </div>
        </div>
    </header>

    <div class="vstack gap-3">
        @foreach ($results as $index => $result)
            <article class="card">
                <div class="card-body">
                    <span class="badge text-bg-primary">Câu {{ $index + 1 }}</span>
                    <span class="badge {{ $result['is_correct'] ? 'text-bg-success' : 'text-bg-danger' }}">
                        {{ $result['is_correct'] ? 'Đúng' : 'Sai' }}
                    </span>
                    <h2 class="h5 mt-3">{{ $result['word'] }}</h2>
                    <p><strong>Bạn chọn:</strong> {{ $result['answer'] }}</p>
                    <p><strong>Đáp án đúng:</strong> {{ $result['correct'] }}</p>
                    <p class="mb-0"><strong>Giải thích:</strong> {{ $result['explanation'] }}</p>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-4 d-flex flex-wrap gap-2">
        <a class="btn btn-primary" href="{{ route('tests.index') }}">Làm bài khác</a>
        @auth
            <a class="btn btn-outline-primary" href="{{ route('history.index') }}">Xem tiến độ</a>
        @endauth
    </div>
@endsection
