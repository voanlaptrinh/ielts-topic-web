@extends('layouts.app')

@section('title', $topic->title . ' - IELTS Focus')

@section('content')
    <a class="btn btn-link ps-0 mb-3" href="{{ route('topics.index') }}">Quay lại danh sách</a>

    <header class="hero-panel p-4 p-lg-5">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="badge text-bg-primary">{{ $topic->part }}</span>
            <span class="badge text-bg-success">{{ $topic->difficulty }}</span>
        </div>
        <h1 class="display-title">{{ $topic->title }}</h1>
        <p class="lead-copy mb-0">{{ $topic->description }}</p>
    </header>

    <div class="row g-4">
        <section class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="section-title">Câu hỏi luyện tập</h2>
                    <ol class="vstack gap-3 mb-0 mt-3">
                        @foreach ($topic->questions as $question)
                            <li>{{ $question }}</li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </section>

        <section class="col-lg-7">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="section-title">Bài mẫu</h2>
                    <p class="mb-0 mt-3">{{ $topic->sample_answer }}</p>
                </div>
            </div>
        </section>
    </div>

    @if ($topic->tips)
        <section class="soft-panel mt-4">
            <h2 class="section-title">Mẹo cải thiện band điểm</h2>
            <div class="row g-3 mt-1">
                @foreach ($topic->tips as $tip)
                    <div class="col-md-6">
                        <div class="metric-card">{{ $tip }}</div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
