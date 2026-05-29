@extends('layouts.app')

@section('title', 'Luyện IELTS Writing - IELTS Focus')
@section('meta_description', 'Luyện IELTS Writing Task 1, Task 2, bài mẫu theo band và lưu lịch sử bài viết khi đăng nhập.')

@section('content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">IELTS Writing</span>
        <h1 class="display-title">Luyện viết theo tiêu chí chấm IELTS.</h1>
        <p class="lead-copy mb-0">Viết bài, lưu lịch sử, đối chiếu với gợi ý và bài mẫu để cải thiện task response, coherence, grammar và vocabulary.</p>
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

    <section>
        <div class="section-heading-row">
            <div>
                <span class="eyebrow">Practice tests</span>
                <h2 class="section-title">Đề Writing đang có</h2>
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
                            <span class="text-muted small">{{ $test->duration_minutes }} phút · {{ $test->questions_count }} task</span>
                            <a class="btn btn-primary btn-sm" href="{{ route('tests.practice.show', [$test->skill, $test]) }}">Viết bài</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="soft-panel"><p class="mb-0 text-muted">Chưa có đề Writing public. Admin có thể tạo trong mục Đề Reading/Listening.</p></div>
            @endforelse
        </div>
        <div class="mt-3">{{ $tests->links() }}</div>
    </section>
@endsection
