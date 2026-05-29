@extends('layouts.app')

@section('title', 'Luyện IELTS Listening - IELTS Focus')
@section('meta_description', 'Luyện IELTS Listening theo kỹ năng nghe thông tin, multiple choice và map labelling.')

@section('content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">IELTS Listening</span>
        <h1 class="display-title">Luyện nghe theo kỹ năng cần trong phòng thi.</h1>
        <p class="lead-copy mb-0">Bắt đầu bằng transcript và dạng câu hỏi; sau này có thể bổ sung upload audio từ admin.</p>
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
                <h2 class="section-title">Đề Listening đang có</h2>
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
                    <p class="mb-0 text-muted">Chưa có đề Listening public. Admin có thể tạo trong mục Đề Reading/Listening.</p>
                </div>
            @endforelse
        </div>
        <div class="mt-3">{{ $tests->links() }}</div>
    </section>

    <section class="soft-panel">
        <h2 class="section-title">Transcript mẫu</h2>
        <p class="mt-3">Good morning. I would like to book a study room for Friday afternoon. The room should have a projector and space for six students.</p>
        <div class="vstack gap-2">
            <div class="option-card p-3">Day: <strong>Friday</strong></div>
            <div class="option-card p-3">Equipment: <strong>projector</strong></div>
            <div class="option-card p-3">Group size: <strong>six students</strong></div>
        </div>
    </section>
@endsection
