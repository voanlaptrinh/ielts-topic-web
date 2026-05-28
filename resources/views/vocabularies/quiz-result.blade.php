@extends('layouts.app')

@section('title', 'Kết quả quiz từ vựng - IELTS Focus')
@section('meta_description', 'Xem kết quả quiz từ vựng IELTS, đáp án đúng, lỗi sai và giải thích để ôn tập hiệu quả hơn.')

@section('content')
    <header class="page-header">
        <h1>Kết quả quiz từ vựng</h1>
        <p class="lead">Điểm: {{ $score }} / {{ $total }}</p>
        @auth
            <p class="text-muted">Kết quả đã được lưu vào lịch sử học tập.</p>
        @else
            <p class="text-muted">Đăng nhập để lưu lịch sử làm quiz và xem lại lỗi sai sau này.</p>
        @endauth
    </header>

    @if ($wrongResults->isNotEmpty())
        <section class="card p-3 mb-3">
            <h2 class="h4">Các lỗi cần xem lại</h2>
            <div class="vstack gap-3">
                @foreach ($wrongResults as $result)
                    <article class="border-top pt-3">
                        <h3 class="h5">{{ $result['word'] }}</h3>
                        <p><strong>Bạn chọn:</strong> {{ $result['answer'] }}</p>
                        <p><strong>Đáp án đúng:</strong> {{ $result['correct'] }}</p>
                        <p class="mb-0"><strong>Giải thích:</strong> {{ $result['explanation'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    @else
        <div class="alert alert-success">Bạn làm đúng toàn bộ câu hỏi.</div>
    @endif

    <section class="card p-3">
        <h2 class="h4">Chi tiết đáp án</h2>
        <div class="vstack gap-3">
            @foreach ($results as $index => $result)
                <article class="border-top pt-3">
                    <span class="badge text-bg-primary">Câu {{ $index + 1 }}</span>
                    <span class="badge {{ $result['is_correct'] ? 'text-bg-success' : 'text-bg-danger' }}">
                        {{ $result['is_correct'] ? 'Đúng' : 'Sai' }}
                    </span>
                    <h3 class="h5 mt-2">{{ $result['word'] }}</h3>
                    <p><strong>Bạn chọn:</strong> {{ $result['answer'] }}</p>
                    <p><strong>Đáp án đúng:</strong> {{ $result['correct'] }}</p>
                    <p class="mb-0"><strong>Giải thích:</strong> {{ $result['explanation'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <div class="mt-3 d-flex gap-2">
        <a class="btn btn-primary" href="{{ route('vocabularies.quiz') }}">Làm quiz mới</a>
        @auth
            <a class="btn btn-outline-secondary" href="{{ route('history.index') }}">Xem lịch sử</a>
        @endauth
    </div>
@endsection
