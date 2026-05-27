@extends('layouts.app')

@section('title', 'Từ điển Anh - Việt - IELTS Focus')

@section('content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">English - Vietnamese dictionary</span>
        <h1 class="display-title">Tra nghĩa, loại từ và ngữ cảnh dùng trong IELTS.</h1>
        <p class="lead-copy mb-0">Tìm theo từ gốc, cụm từ hoặc định nghĩa tiếng Anh. Khi đăng nhập, các từ đã mở sẽ được lưu vào tiến độ học tập.</p>
    </header>

    <form class="search-panel mb-4" action="{{ route('dictionary.index') }}" method="GET">
        <div class="row g-2">
            <div class="col-lg-10">
                <input class="form-control" name="q" value="{{ $search }}" placeholder="Nhập từ cần tra, ví dụ: run, education, sustainable">
            </div>
            <div class="col-lg-2 d-grid">
                <button class="btn btn-primary" type="submit">Tra từ</button>
            </div>
        </div>
    </form>

    <section class="resource-list">
        @forelse ($words as $item)
            <article class="card study-card">
                <div class="card-body d-flex flex-column">
                    <h2 class="h5">{{ $item->word }}</h2>
                    <p class="text-muted">{{ str_replace(',', ', ', $item->parts_of_speech) }}</p>
                    <p class="mb-3">{{ $item->senses_count }} nghĩa / cách dùng</p>
                    <div class="metric-card mb-3">
                        <span class="metric-value">{{ $item->senses_count }}</span>
                        <span class="metric-label">nghĩa / cách dùng</span>
                    </div>
                    <a class="btn btn-primary align-self-start" href="{{ route('dictionary.show', $item->normalized_word) }}">Xem nghĩa tiếng Việt</a>
                </div>
            </article>
        @empty
            <div class="empty-state">
                <h3 class="h5">Không tìm thấy</h3>
                <p class="mb-0 text-muted">Thử tìm bằng từ gốc, ví dụ: study, run, policy.</p>
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
