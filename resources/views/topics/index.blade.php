@extends('layouts.app')

@section('title', 'IELTS Focus - Tự học IELTS theo lộ trình rõ ràng')
@section('meta_description', 'IELTS Focus là nền tảng học IELTS toàn diện cho người Việt: học chủ đề, luyện 4 kỹ năng, ghi nhớ từ vựng và theo dõi tiến độ.')

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
    <section class="home-hero">
        <div class="home-hero-copy">
            <h1>Tự học IELTS thông minh <span>theo lộ trình rõ ràng</span></h1>
            <p>IELTS Focus là nền tảng học IELTS toàn diện dành cho người Việt. Học theo chủ đề, luyện đủ 4 kỹ năng, ghi nhớ từ vựng và theo dõi tiến độ dễ dàng.</p>
            <div class="home-hero-actions">
                <a class="btn btn-primary" href="{{ route('tests.index') }}">Học theo gợi ý hôm nay</a>
                <a class="btn btn-outline-primary" href="{{ route('vocabularies.index') }}">Tra từ IELTS</a>
                <a class="btn btn-outline-primary" href="{{ route('tests.index') }}">Làm bài kiểm tra</a>
            </div>
        </div>

        <div class="home-hero-visual">
            <img src="{{ asset('images/home-hero-ielts.png') }}" alt="Học viên luyện IELTS trên IELTS Focus">
        </div>
    </section>

    <section class="home-top-grid">
        <article class="home-card learning-plan-card">
            <h2>Kế hoạch học hôm nay</h2>
            <div class="home-plan-list">
                @foreach ($learningPlan['today'] as $step)
                    <a class="home-plan-step" href="{{ $step['route'] }}">
                        <span>{{ $step['number'] }}</span>
                        <strong>{{ $step['title'] }}</strong>
                        <small>{{ $step['description'] }}</small>
                    </a>
                @endforeach
            </div>
            <a class="home-text-link" href="{{ route('tests.index') }}">Xem chi tiết kế hoạch <x-ui-icon name="arrow-right" /></a>
        </article>

        <article class="home-card system-stats-card">
            <h2>Thống kê hệ thống</h2>
            <div class="home-stat-grid">
                <div class="home-stat">
                    <span class="home-stat-icon"><x-ui-icon name="book" /></span>
                    <strong>{{ number_format($stats['topics']) }}+</strong>
                    <small>Chủ đề<br>Speaking & Writing</small>
                </div>
                <div class="home-stat">
                    <span class="home-stat-icon"><x-ui-icon name="layers" /></span>
                    <strong>{{ number_format($stats['vocabularies']) }}+</strong>
                    <small>Từ vựng<br>IELTS</small>
                </div>
                <div class="home-stat">
                    <span class="home-stat-icon"><x-ui-icon name="language" /></span>
                    <strong>{{ number_format($stats['dictionary_words']) }}+</strong>
                    <small>Mục từ<br>trong từ điển</small>
                </div>
                <div class="home-stat">
                    <span class="home-stat-icon"><x-ui-icon name="chart" /></span>
                    <strong>6</strong>
                    <small>Cấp độ<br>luyện tập</small>
                </div>
            </div>
        </article>
    </section>

    <section class="home-two-columns">
        <article class="home-card feature-card">
            <h2>Vì sao chọn IELTS Focus?</h2>
            <div class="home-feature-grid">
                @foreach ($marketEdges as $edge)
                    <div class="home-feature">
                        <span><x-ui-icon :name="$edge['icon']" /></span>
                        <div>
                            <strong>{{ $edge['title'] }}</strong>
                            <p>{{ $edge['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="home-card trusted-card">
            <h2>Tổng hợp tinh hoa từ các nền tảng IELTS uy tín</h2>
            <div class="trusted-logos">
                <strong>BRITISH<br>COUNCIL</strong>
                <strong class="idp-logo">idp</strong>
                <strong>ROAD TO<br>IELTS</strong>
            </div>
            <ul class="home-check-list">
                @foreach ($officialPrepSummary as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </article>
    </section>

    <section class="home-progress-grid">
        <article class="home-card strategy-card">
            <h2>Chiến lược học tập tại IELTS Focus</h2>
            <div class="strategy-flow">
                @foreach ($strategySteps as $step)
                    <div class="strategy-step">
                        <span><x-ui-icon :name="$step['icon']" /></span>
                        <small>{{ $step['label'] }}</small>
                    </div>
                    @if (! $loop->last)
                        <b><x-ui-icon name="arrow-right" /></b>
                    @endif
                @endforeach
            </div>
            <a class="btn btn-primary btn-sm" href="{{ route('tests.index') }}">Bắt đầu học ngay</a>
        </article>

        <article class="home-card review-card">
            <h2>Ôn tập lỗi sai</h2>
            <p>Hệ thống giúp bạn ghi nhớ và ôn lại những lỗi sai để không lặp lại.</p>
            <div class="review-counter">
                <span>0</span>
                <strong>Lỗi sai cần ôn tập</strong>
            </div>
            <div class="review-counter">
                <span>0</span>
                <strong>Thẻ ôn từ cần học</strong>
            </div>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('vocabularies.flashcards') }}">Xem ngay</a>
        </article>

        <article class="home-card empty-review-card">
            <div class="empty-review-illustration"><x-ui-icon name="search" /></div>
            <div>
                <h2>Chưa có dữ liệu lỗi sai</h2>
                <p>Hãy bắt đầu làm bài luyện tập hoặc học từ vựng để hệ thống ghi nhận và gợi ý ôn tập nhé!</p>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('tests.index') }}">Luyện bài ngay</a>
            </div>
        </article>
    </section>

    <section class="home-card course-track-section">
        <div class="home-section-heading">
            <h2>Lộ trình học IELTS theo mục tiêu</h2>
            <a href="{{ route('prep.index') }}">Xem lộ trình <x-ui-icon name="arrow-right" /></a>
        </div>
        <div class="course-track-grid">
            @foreach ($courseTracks as $track)
                <article class="course-track-card">
                    <span><x-ui-icon :name="$track['icon']" /></span>
                    <div>
                        <small>{{ $track['level'] }}</small>
                        <h3>{{ $track['title'] }}</h3>
                        <p>{{ $track['description'] }}</p>
                        <ul>
                            @foreach ($track['focus'] as $focus)
                                <li>{{ $focus }}</li>
                            @endforeach
                        </ul>
                        <a class="btn btn-outline-primary btn-sm" href="{{ $track['route'] }}">Bắt đầu</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="home-card test-library-section">
        <div class="home-section-heading">
            <h2>Thư viện bài thi mô phỏng</h2>
            <a href="{{ route('tests.index') }}">Vào phòng luyện <x-ui-icon name="arrow-right" /></a>
        </div>
        <div class="test-library-grid">
            @foreach ($testLibrary as $test)
                <a class="test-library-card" href="{{ $test['route'] }}">
                    <strong>{{ $test['skill'] }}</strong>
                    <p>{{ $test['description'] }}</p>
                    <div>
                        <span>{{ $test['time'] }}</span>
                        <span>{{ $test['parts'] }}</span>
                        <span>{{ $test['questions'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="home-card topic-section" id="topic-bank">
        <div class="home-section-heading">
            <h2>Kho topic IELTS</h2>
            <a href="{{ route('topics.index') }}#topic-bank">Xem tất cả chủ đề <x-ui-icon name="arrow-right" /></a>
        </div>
        <div class="home-topic-grid">
            @foreach ($topicCards as $topic)
                <article class="home-topic-card">
                    <img src="{{ $topic['image'] }}" alt="{{ $topic['title'] }}">
                    <div class="home-topic-body">
                        <h3>{{ $topic['title'] }}</h3>
                        <div class="topic-tags">
                            <span>Speaking, Writing</span>
                            <b>{{ $topic['difficulty'] }}</b>
                        </div>
                        <p>{{ $topic['description'] }}</p>
                        <a class="btn btn-outline-primary btn-sm" href="{{ $topic['slug'] ? route('topics.show', $topic['slug']) : route('tests.index') }}">Ôn topic</a>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="home-bottom-grid">
        <article class="home-card faq-home-card">
            <h2>Câu hỏi thường gặp</h2>
            <div class="home-faq-grid">
                @foreach ($faqs as $faq)
                    <details>
                        <summary>{{ $faq['question'] }}</summary>
                        <p>{{ $faq['answer'] }}</p>
                    </details>
                @endforeach
            </div>
        </article>

        <article class="home-card support-card">
            <div class="support-illustration"><x-ui-icon name="headset" /></div>
            <div>
                <h2>Bạn cần hỗ trợ thêm?</h2>
                <p>Đội ngũ của chúng tôi luôn sẵn sàng hỗ trợ bạn trên hành trình chinh phục IELTS.</p>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('dictionary.index') }}">Liên hệ hỗ trợ</a>
            </div>
        </article>
    </section>
@endsection
