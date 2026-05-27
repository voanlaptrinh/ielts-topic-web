@extends('layouts.app')

@section('title', 'Từ vựng IELTS - IELTS Focus')

@section('content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">Vocabulary bank</span>
        <h1 class="display-title">Từ điển IELTS miễn phí</h1>
        <p class="lead-copy mb-0">Tra nhanh nghĩa tiếng Việt, định nghĩa tiếng Anh, phát âm, ví dụ và nhóm từ đồng nghĩa để ôn đúng ngữ cảnh.</p>
    </header>

    <form class="search-panel mb-4" action="{{ route('vocabularies.index') }}" method="GET">
        <div class="row g-2 align-items-end">
            <div class="col-lg-5">
                <label class="form-label fw-semibold" for="q">Từ khóa</label>
                <input id="q" class="form-control" name="q" value="{{ $search }}" placeholder="Ví dụ: sustainable, education, môi trường">
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label fw-semibold" for="level">Level</label>
                <select id="level" class="form-select" name="level">
                    <option value="">Tất cả</option>
                    @foreach ($levels as $level)
                        <option value="{{ $level }}" @selected($selectedLevel === $level)>{{ $level }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label class="form-label fw-semibold" for="topic">Chủ đề</label>
                <select id="topic" class="form-select" name="topic">
                    <option value="">Tất cả</option>
                    @foreach ($topics as $topic)
                        <option value="{{ $topic }}" @selected($selectedTopic === $topic)>{{ $topic }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 d-grid">
                <button class="btn btn-primary" type="submit">Tra từ</button>
            </div>
        </div>
    </form>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h2 class="section-title mb-0">{{ number_format($words->total()) }} từ phù hợp</h2>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('vocabularies.flashcards') }}">Ôn bằng flashcard</a>
    </div>

    <section class="resource-list">
        @forelse ($words as $word)
            <article class="card study-card">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between gap-3 align-items-start">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge text-bg-primary">{{ $word->topic }}</span>
                            <span class="badge text-bg-success">{{ $word->level }}</span>
                        </div>
                        <span class="footer-note">{{ $word->part_of_speech }}</span>
                    </div>
                    <h3 class="h5 mt-3">{{ $word->word }}</h3>
                    <p class="text-muted mb-2">{{ $word->phonetic }}</p>
                    <p class="flex-grow-1">{{ $word->meaning_vi }}</p>
                    <a class="btn btn-primary align-self-start" href="{{ route('vocabularies.show', $word->word) }}">Xem giải thích</a>
                </div>
            </article>
        @empty
            <div class="empty-state">
                <h3 class="h5">Không tìm thấy từ phù hợp</h3>
                <p class="mb-0 text-muted">Thử bỏ bớt bộ lọc hoặc tìm bằng từ khóa ngắn hơn.</p>
            </div>
        @endforelse
    </section>

    <nav class="d-flex justify-content-between align-items-center gap-3 mt-4">
        @if ($words->onFirstPage())
            <span class="text-muted">Trang trước</span>
        @else
            <a class="btn btn-outline-primary" href="{{ $words->previousPageUrl() }}">Trang trước</a>
        @endif

        <span class="text-muted">Trang {{ $words->currentPage() }} / {{ $words->lastPage() }}</span>

        @if ($words->hasMorePages())
            <a class="btn btn-outline-primary" href="{{ $words->nextPageUrl() }}">Trang sau</a>
        @else
            <span class="text-muted">Trang sau</span>
        @endif
    </nav>
@endsection
