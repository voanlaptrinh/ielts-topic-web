@extends('layouts.app')

@section('title', 'Luyện IELTS Speaking - IELTS Focus')
@section('meta_description', 'Luyện IELTS Speaking Part 1, Part 2, Part 3 theo topic, cue card và câu trả lời mẫu.')

@section('content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">IELTS Speaking</span>
        <h1 class="display-title">Luyện nói theo topic và cue card.</h1>
        <p class="lead-copy mb-0">Chuẩn bị idea, ghi câu trả lời nháp và lưu lại lịch sử luyện tập để theo dõi tiến bộ.</p>
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
                <h2 class="section-title">Bộ câu hỏi Speaking đang có</h2>
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
                            <a class="btn btn-primary btn-sm" href="{{ route('tests.practice.show', [$test->skill, $test]) }}">Luyện nói</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="soft-panel"><p class="mb-0 text-muted">Chưa có bộ câu hỏi Speaking public. Admin có thể tạo trong mục Đề Reading/Listening.</p></div>
            @endforelse
        </div>
        <div class="mt-3">{{ $tests->links() }}</div>
    </section>
@endsection
