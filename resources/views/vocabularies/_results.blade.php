@if ($featuredWord)
    <section class="vocabulary-feature mb-3">
        <div>
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mt-2">
                <div>
                    <h2 class="vocabulary-feature-word">{{ $featuredWord->word }}</h2>
                    <p class="vocabulary-feature-meta">{{ $featuredWord->phonetic }} · {{ $featuredWord->part_of_speech }}</p>
                </div>
                <div class="vocabulary-pill-row">
                    <span>{{ $featuredWord->topic }}</span>
                    <span>{{ $featuredWord->level }}</span>
                </div>
            </div>
        </div>

        <div class="vocabulary-feature-grid">
            <div>
                <span class="translate-box-label">Nghĩa tiếng Việt</span>
                <p class="vocabulary-feature-meaning">{{ $featuredWord->meaning_vi }}</p>
            </div>
            <div>
                <span class="translate-box-label">Định nghĩa English</span>
                <p class="mb-0">{{ $featuredWord->definition_en }}</p>
            </div>
        </div>

        <div class="vocabulary-example">
            <span class="translate-box-label">Ví dụ IELTS</span>
            <p class="mb-1">{{ $featuredWord->example_en }}</p>
            <p class="text-muted mb-0">{{ $featuredWord->example_vi }}</p>
        </div>

        <a class="btn btn-primary btn-sm align-self-end" href="{{ route('vocabularies.show', $featuredWord->word) }}">Xem đầy đủ <x-ui-icon name="arrow-right" /></a>
    </section>
@elseif (! $search)
    <section class="empty-state mb-4">
        <h2 class="h5">Nhập từ để tra trong bộ từ vựng IELTS</h2>
        <p class="text-muted mb-0">Ví dụ: sustainable, allocate, education, academic hoặc nghĩa tiếng Việt cần tìm.</p>
    </section>
@endif

<div class="vocabulary-result-head">
    <span>
        @if ($search)
            Đã tìm thấy {{ number_format($words->total()) }} kết quả cho “{{ $search }}”
        @else
            {{ number_format($words->total()) }} từ vựng trong kho
        @endif
    </span>
    @if ($search)
        <a class="btn btn-outline-primary btn-sm" href="{{ route('vocabularies.index') }}">Xóa lọc</a>
    @endif
</div>

<section class="vocabulary-result-list">
    @forelse ($words as $word)
        <article class="vocabulary-result-item">
            <div class="vocabulary-result-main">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <h3 class="h5 mb-0">{{ $word->word }}</h3>
                    <span class="footer-note">{{ $word->part_of_speech }}</span>
                </div>
                <p class="mb-1"><strong>{{ $word->meaning_vi }}</strong></p>
                <p class="text-muted mb-0">{{ $word->definition_en }}</p>
            </div>

            <div class="vocabulary-result-meta">
                <div class="vocabulary-pill-row justify-content-lg-end">
                    <span>{{ $word->topic }}</span>
                    <span>{{ $word->level }}</span>
                </div>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('vocabularies.show', $word->word) }}">Chi tiết</a>
            </div>
        </article>
    @empty
        <div class="empty-state">
            <h3 class="h5">Không tìm thấy từ phù hợp</h3>
            <p class="mb-0 text-muted">Thử bỏ bớt bộ lọc hoặc tìm bằng từ khóa ngắn hơn.</p>
        </div>
    @endforelse
</section>

@if ($words->lastPage() > 1)
    <nav class="d-flex justify-content-between align-items-center gap-3 mt-4 vocabulary-pagination">
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
@endif

@if ($words->hasMorePages())
    <div class="load-more-wrap vocabulary-load-more">
        <button class="btn btn-outline-primary" type="button" data-lazy-load-more data-next-url="{{ $words->nextPageUrl() }}">
            Tải thêm từ vựng
        </button>
    </div>
@endif
