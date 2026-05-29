@extends('layouts.app')

@section('title', 'Luyện IELTS Reading - IELTS Focus')
@section('meta_description', 'Luyện IELTS Reading theo dạng bài: True/False/Not Given, Matching Headings và Summary Completion.')

@section('content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">IELTS Reading</span>
        <h1 class="display-title">Luyện Reading theo chiến thuật làm bài.</h1>
        <p class="lead-copy mb-0">Tập trung vào cách đọc câu hỏi, tìm keyword, paraphrase và xác định bẫy thường gặp.</p>
    </header>

    <section class="resource-list mb-4">
        @foreach ($tasks as $task)
            <article class="card study-card">
                <div class="card-body">
                    <span class="badge text-bg-primary">{{ $task['type'] }}</span>
                    <p class="mt-3 mb-0 text-muted">{{ $task['focus'] }}</p>
                </div>
            </article>
        @endforeach
    </section>

    <section class="mb-4">
        <div class="section-heading-row">
            <div>
                <span class="eyebrow">Practice tests</span>
                <h2 class="section-title">Đề Reading đang có</h2>
            </div>
        </div>
        <div class="resource-list">
            @forelse ($tests as $test)
                <article class="card study-card">
                    <div class="card-body">
                        <span class="badge text-bg-primary">{{ $test->level }}</span>
                        <h3 class="h5 mt-3">{{ $test->title }}</h3>
                        <p class="text-muted">{{ $test->description }}</p>
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <span class="text-muted small">{{ $test->duration_minutes }} phút · {{ $test->questions_count }} câu</span>
                            <a class="btn btn-primary btn-sm" href="{{ route('tests.practice.show', [$test->skill, $test]) }}">Làm bài</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="soft-panel">
                    <p class="mb-0 text-muted">Chưa có đề Reading public. Admin có thể tạo trong mục Đề Reading/Listening.</p>
                </div>
            @endforelse
        </div>
        <div class="mt-3">{{ $tests->links() }}</div>
    </section>

    <section class="soft-panel">
        <h2 class="section-title">Bài đọc mẫu ngắn</h2>
        <p class="mt-3">Urban green spaces can improve public health by encouraging exercise and reducing stress. However, access to parks is often unequal, especially in rapidly growing cities.</p>
        <div class="vstack gap-2">
            <div class="option-card p-3">Statement: Green spaces may benefit health. <strong>True</strong></div>
            <div class="option-card p-3">Statement: Every citizen has equal park access. <strong>False</strong></div>
            <div class="option-card p-3">Statement: The text gives the exact cost of building parks. <strong>Not Given</strong></div>
        </div>
    </section>
@endsection
