@extends('layouts.app')

@section('title', 'Tiến độ học tập - IELTS Focus')
@section('meta_description', 'Dashboard tiến độ IELTS Focus giúp theo dõi điểm bài luyện, độ chính xác, lỗi sai và các từ đã tra gần đây.')

@section('content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">Lịch sử học tập</span>
        <h1 class="display-title">Tiến độ của {{ auth()->user()->name }}</h1>
        <p class="lead-copy mb-0">Theo dõi bài đã làm, độ chính xác, lỗi sai và các từ đã tra gần đây.</p>
    </header>

    <section class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <span class="metric-value">{{ $summary['attempts'] }}</span>
                <span class="metric-label">Bài đã làm</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <span class="metric-value">{{ $summary['accuracy'] }}%</span>
                <span class="metric-label">Độ chính xác</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <span class="metric-value">{{ $summary['wrong_count'] }}</span>
                <span class="metric-label">Câu cần xem lại</span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <span class="metric-value">{{ $summary['lookups'] }}</span>
                <span class="metric-label">Từ đã tra gần đây</span>
            </div>
        </div>
    </section>

    <div class="row g-4">
        <section class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h2 class="section-title">Các bài đã làm</h2>
                    @forelse ($attempts as $attempt)
                        @php
                            $wrongDetails = collect($attempt->details)->reject(fn ($detail) => $detail['is_correct'] ?? false);
                        @endphp
                        <article class="border-top pt-3 mt-3">
                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                <div>
                                    <h3 class="h5">{{ $attempt->test_type }} - {{ $attempt->level }}</h3>
                                    <p class="mb-1"><strong>Điểm:</strong> {{ $attempt->score }} / {{ $attempt->total }}</p>
                                </div>
                                <p class="text-muted mb-0">{{ $attempt->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            @if ($wrongDetails->isNotEmpty())
                                <h4 class="h6 mt-3">Lỗi sai cần xem lại</h4>
                                <div class="vstack gap-2">
                                    @foreach ($wrongDetails->take(4) as $detail)
                                        <div class="soft-panel">
                                            <strong>{{ $detail['word'] }}</strong>
                                            <p class="mb-1"><strong>Bạn chọn:</strong> {{ $detail['answer'] ?? 'Chưa chọn' }}</p>
                                            <p class="mb-1"><strong>Đáp án đúng:</strong> {{ $detail['correct'] }}</p>
                                            @if (! empty($detail['explanation']))
                                                <p class="mb-0"><strong>Giải thích:</strong> {{ $detail['explanation'] }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-success mb-0 mt-3">Không có lỗi sai trong lần làm này.</div>
                            @endif
                        </article>
                    @empty
                        <div class="empty-state mt-3">
                            <h3 class="h5">Chưa có bài làm nào</h3>
                            <p class="mb-0 text-muted">Hãy làm một bài luyện để dashboard bắt đầu ghi nhận tiến độ.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="section-title">Các từ đã tra</h2>
                    @forelse ($lookups as $lookup)
                        <article class="border-top pt-3 mt-3">
                            <h3 class="h5">{{ $lookup->word }}</h3>
                            <p class="mb-1">{{ $lookup->senses_count }} nghĩa / cách dùng</p>
                            <p class="text-muted">{{ $lookup->created_at->format('d/m/Y H:i') }}</p>
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('dictionary.show', $lookup->normalized_word) }}">Tra lại</a>
                        </article>
                    @empty
                        <div class="empty-state mt-3">
                            <h3 class="h5">Chưa có từ nào được tra</h3>
                            <p class="mb-0 text-muted">Các từ bạn mở trong từ điển sẽ xuất hiện ở đây.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
@endsection
