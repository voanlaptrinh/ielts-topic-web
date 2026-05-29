@extends('dashboard.layout')

@section('title', 'Dashboard học tập - IELTS Focus')
@section('meta_description', 'Dashboard cá nhân giúp người học IELTS theo dõi mục tiêu band, bài đã làm, lỗi sai và từ đã tra.')

@section('dashboard_content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">Không gian học cá nhân</span>
        <h1 class="display-title">Hôm nay nên học gì?</h1>
        <p class="lead-copy mb-0">Theo dõi mục tiêu band, xem lỗi cần ôn và tiếp tục bài luyện phù hợp với tiến độ của bạn.</p>
    </header>

    <section class="dashboard-grid mb-4">
        <form class="goal-panel" method="POST" action="{{ route('dashboard.goal') }}">
            @csrf
            @method('PUT')
            <span class="eyebrow">Mục tiêu</span>
            <div class="goal-controls">
                <label>
                    <span>Band mục tiêu</span>
                    <select class="form-select" name="target_band">
                        @foreach (['5.0', '5.5', '6.0', '6.5', '7.0', '7.5', '8.0+'] as $band)
                            <option value="{{ $band }}" @selected(auth()->user()->target_band === $band)>{{ $band }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Phút/ngày</span>
                    <input class="form-control" name="study_minutes_per_day" type="number" min="10" max="180" value="{{ auth()->user()->study_minutes_per_day }}">
                </label>
            </div>
            <button class="btn btn-primary btn-sm align-self-start" type="submit">Lưu mục tiêu</button>
        </form>

        <div class="today-panel">
            <span class="eyebrow">Gợi ý tiếp theo</span>
            <h2 class="h5 mt-2">Làm bài cấp {{ $recommendedLevel }}</h2>
            <p class="text-muted">Dựa trên độ chính xác gần đây: {{ $summary['accuracy'] }}%.</p>
            <a class="btn btn-primary btn-sm" href="{{ route('tests.level', $recommendedLevel) }}">Vào bài luyện</a>
        </div>
    </section>

    <section class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><div class="metric-card"><span class="metric-value">{{ $summary['attempts'] }}</span><span class="metric-label">Bài gần đây</span></div></div>
        <div class="col-6 col-lg-3"><div class="metric-card"><span class="metric-value">{{ $summary['accuracy'] }}%</span><span class="metric-label">Độ chính xác</span></div></div>
        <div class="col-6 col-lg-3"><div class="metric-card"><span class="metric-value">{{ $summary['wrong_count'] }}</span><span class="metric-label">Lỗi cần ôn</span></div></div>
        <div class="col-6 col-lg-3"><div class="metric-card"><span class="metric-value">{{ $summary['lookups'] }}</span><span class="metric-label">Từ đã tra</span></div></div>
    </section>

    <div class="row g-4">
        <section class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h2 class="section-title">Lỗi cần xem lại</h2>
                    @forelse ($wrongItems as $item)
                        <article class="review-item mt-3">
                            <span class="badge text-bg-danger">{{ $item['test_type'] }} · {{ $item['level'] }}</span>
                            <strong>{{ $item['word'] }}</strong>
                            <small>Bạn chọn: {{ $item['answer'] }} · Đúng: {{ $item['correct'] }}</small>
                            @if ($item['explanation'])
                                <small>{{ $item['explanation'] }}</small>
                            @endif
                        </article>
                    @empty
                        <div class="empty-state mt-3">Chưa có lỗi sai. Làm một bài test để dashboard bắt đầu phân tích.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="col-lg-5">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="section-title">Phân tích kỹ năng</h2>
                    <p class="text-muted mb-0">Kỹ năng cần ưu tiên: {{ $weakestSkill }}.</p>
                    @forelse ($skillBreakdown as $skill)
                        <div class="skill-row mt-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $skill['skill'] }}</strong>
                                <span>{{ $skill['accuracy'] }}%</span>
                            </div>
                            <div class="progress mt-2" role="progressbar" aria-valuenow="{{ $skill['accuracy'] }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: {{ $skill['accuracy'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state mt-3">Làm bài 4 kỹ năng để dashboard phân tích điểm yếu.</div>
                    @endforelse
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="section-title">Lộ trình 30 ngày</h2>
                    @foreach ($roadmap as $item)
                        <article class="review-item mt-3">
                            <span class="badge text-bg-primary">{{ $item['label'] }}</span>
                            <strong>{{ $item['task'] }}</strong>
                        </article>
                    @endforeach
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h2 class="section-title">Từ đã tra gần đây</h2>
                    @forelse ($lookups as $lookup)
                        <a class="admin-list-item mt-2" href="{{ route('dictionary.show', $lookup->normalized_word) }}">
                            <strong>{{ $lookup->word }}</strong>
                            <span>{{ $lookup->created_at->format('d/m/Y H:i') }}</span>
                        </a>
                    @empty
                        <div class="empty-state mt-3">Các từ bạn tra sẽ xuất hiện ở đây.</div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
@endsection
