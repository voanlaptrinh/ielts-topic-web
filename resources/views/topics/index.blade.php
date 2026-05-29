@extends('layouts.app')

@section('title', 'IELTS Focus - Lộ trình tự học IELTS cho người Việt')
@section('meta_description', 'IELTS Focus là nền tảng tự học IELTS bằng tiếng Việt với topic Speaking/Writing, từ vựng học thuật, từ điển, flashcard, bài test và lịch sử lỗi sai.')

@push('head')
    <script type="application/ld+json">
        {!! json_encode($structuredData['website'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode($structuredData['organization'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode($structuredData['faq'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

@section('content')
    <section class="hero-panel p-4 p-lg-5">
        <div class="hero-grid">
            <div>
                <span class="eyebrow">Mục tiêu: lựa chọn IELTS số 1 cho người Việt</span>
                <h1 class="display-title">Tự học IELTS theo đúng việc cần làm hôm nay.</h1>
                <p class="lead-copy">IELTS Focus gom topic, từ vựng, từ điển, flashcard, bài test và lịch sử lỗi sai vào một lộ trình tiếng Việt rõ ràng. Mỗi ngày bạn biết nên học gì, làm bài nào và ôn lại lỗi nào.</p>

                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ $learningPlan['recommended_route'] }}">Học theo gợi ý hôm nay</a>
                    <a class="btn btn-outline-primary" href="{{ route('vocabularies.index') }}">Tra từ IELTS</a>
                    <a class="btn btn-outline-primary" href="{{ route('tests.index') }}">Làm bài kiểm tra</a>
                </div>

                <div class="trust-strip">
                    @foreach ($qualitySignals as $signal)
                        <span class="trust-pill">{{ $signal }}</span>
                    @endforeach
                </div>
            </div>

            <aside class="today-panel">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <span class="eyebrow">25 phút hôm nay</span>
                        <h2 class="h5 mb-0">Kế hoạch cá nhân</h2>
                    </div>
                    <span class="plan-score">
                        {{ $learningPlan['accuracy'] !== null ? $learningPlan['accuracy'] . '%' : 'Mới' }}
                    </span>
                </div>

                <div class="today-steps">
                    @foreach ($learningPlan['today'] as $step)
                        <a class="today-step" href="{{ $step['route'] }}">
                            <span class="today-step-time">{{ $step['time'] }}</span>
                            <span>
                                <strong>{{ $step['title'] }}</strong>
                                <small>{{ $step['description'] }}</small>
                            </span>
                        </a>
                    @endforeach
                </div>
            </aside>
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

    <section class="market-grid mb-4">
        @foreach ($marketEdges as $edge)
            <article class="market-card">
                <h2 class="h5">{{ $edge['title'] }}</h2>
                <p>{{ $edge['description'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="soft-panel mb-4">
        <div class="section-heading-row">
            <div>
                <span class="eyebrow">Tổng hợp từ website IELTS uy tín</span>
                <h2 class="h3 mb-0">Những gì British Council, IDP và các nền tảng lớn có, web này đã gom thành lộ trình học.</h2>
            </div>
            <a class="btn btn-outline-primary" href="{{ route('prep.index') }}">Mở Prep Hub</a>
        </div>
        <div class="benchmark-grid mt-3">
            @foreach ($officialPrepSummary as $item)
                <article class="benchmark-item">
                    <span class="benchmark-source">{{ $item['source'] }}</span>
                    <p class="mb-1"><strong>Điểm cần có:</strong> {{ $item['lesson'] }}</p>
                    <p class="mb-0"><strong>Đã đưa vào web:</strong> {{ $item['implemented'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="learning-command mb-4">
        <div class="learning-command-main">
            <span class="eyebrow">Chiến lược cạnh tranh</span>
            <h2 class="h3 mb-3">Không chỉ có tài liệu, trang này dẫn người học đến hành động tiếp theo.</h2>
            <div class="benchmark-grid">
                @foreach ($benchmarkWins as $item)
                    <article class="benchmark-item">
                        <span class="benchmark-source">{{ $item['source'] }}</span>
                        <p class="mb-1"><strong>Họ mạnh:</strong> {{ $item['strength'] }}.</p>
                        <p class="mb-0"><strong>Mình vượt lên:</strong> {{ $item['our_move'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>

        <aside class="weak-review-panel">
            <span class="eyebrow">Ôn tập cách quãng</span>
            <h2 class="h5">Việc cần xem lại</h2>
            @if ($learningPlan['weak_items']->isNotEmpty())
                <div class="vstack gap-2">
                    @foreach ($learningPlan['weak_items'] as $item)
                        <div class="review-item">
                            <span class="badge text-bg-danger">{{ $item['test_type'] }}</span>
                            <strong>{{ $item['prompt'] }}</strong>
                            @if ($item['correct'])
                                <small>Đáp án đúng: {{ $item['correct'] }}</small>
                            @endif
                        </div>
                    @endforeach
                </div>
                <a class="btn btn-outline-primary btn-sm mt-3" href="{{ route('history.index') }}">Mở lịch sử lỗi</a>
            @else
                <p class="text-muted mb-3">Làm một bài test để hệ thống tự tạo danh sách lỗi cần ôn. Người mới sẽ bắt đầu bằng flashcard và bài cấp độ gợi ý.</p>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('vocabularies.flashcards') }}">Ôn flashcard</a>
            @endif
        </aside>
    </section>

    <section class="skill-track-strip mb-4">
        @foreach ($skillTracks as $track)
            <div class="skill-track">
                <strong>{{ $track['label'] }}</strong>
                <span>{{ $track['value'] }}</span>
            </div>
        @endforeach
    </section>

    <section class="growth-section mb-4">
        <div class="section-heading-row">
            <div>
                <span class="eyebrow">Kế hoạch để cạnh tranh top đầu</span>
                <h2 class="h3 mb-0">Top 1 cần sản phẩm tốt, nội dung dày và dữ liệu học thật.</h2>
            </div>
            <a class="btn btn-outline-primary" href="{{ route('tests.index') }}">Bắt đầu luyện</a>
        </div>
        <div class="growth-grid">
            @foreach ($growthRoadmap as $step)
                <article class="growth-card">
                    <span class="growth-stage">{{ $step['stage'] }}</span>
                    <h3 class="h5">{{ $step['title'] }}</h3>
                    <p>{{ $step['description'] }}</p>
                </article>
            @endforeach
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

    <section class="mb-4" id="topic-bank">
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

    <section class="faq-section mb-4">
        <div class="section-heading-row">
            <div>
                <span class="eyebrow">Câu hỏi thường gặp</span>
                <h2 class="h3 mb-0">Người học cần biết gì trước khi bắt đầu?</h2>
            </div>
        </div>
        <div class="faq-list">
            @foreach ($faqs as $faq)
                <details class="faq-item">
                    <summary>{{ $faq['question'] }}</summary>
                    <p>{{ $faq['answer'] }}</p>
                </details>
            @endforeach
        </div>
    </section>
@endsection
