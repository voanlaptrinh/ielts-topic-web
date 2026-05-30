@extends('layouts.app')

@section('title', 'Từ vựng IELTS - IELTS Focus')
@section('meta_description', 'Tra cứu và ôn tập từ vựng IELTS theo nghĩa tiếng Việt, định nghĩa, ví dụ, chủ đề, flashcard và quiz nhanh.')

@section('content')
    <header class="vocabulary-hero">
        <div class="vocabulary-hero-copy">
            <span>Vocabulary bank</span>
            <h1>Tra từ vựng IELTS nhanh và trực tiếp</h1>
            <p>Gõ từ khóa để xem nghĩa tiếng Việt, định nghĩa, ví dụ, chủ đề và level của từ vựng mà không cần tải lại trang.</p>
        </div>
        <div class="vocabulary-hero-art" aria-hidden="true">
            <div class="vocabulary-art-book">
                <strong>Aa</strong>
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="vocabulary-art-lens"></div>
            <div class="vocabulary-art-stack">
                <span>VOCAB</span>
                <span>IELTS</span>
            </div>
        </div>
    </header>

    @php($reviewTabActive = request()->filled('topic') && ! request()->filled('q'))

    <section class="dictionary-tabs">
        <ul class="nav nav-tabs dictionary-tab-nav" id="vocabularyTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link {{ $reviewTabActive ? '' : 'active' }}"
                    id="vocabulary-search-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#vocabulary-search-pane"
                    type="button"
                    role="tab"
                    aria-controls="vocabulary-search-pane"
                    aria-selected="{{ $reviewTabActive ? 'false' : 'true' }}"
                >Tra từ</button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link {{ $reviewTabActive ? 'active' : '' }}"
                    id="vocabulary-review-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#vocabulary-review-pane"
                    type="button"
                    role="tab"
                    aria-controls="vocabulary-review-pane"
                    aria-selected="{{ $reviewTabActive ? 'true' : 'false' }}"
                >Ôn theo lĩnh vực</button>
            </li>
        </ul>

        <div class="tab-content dictionary-tab-content">
            <div
                class="tab-pane fade {{ $reviewTabActive ? '' : 'show active' }}"
                id="vocabulary-search-pane"
                role="tabpanel"
                aria-labelledby="vocabulary-search-tab"
                tabindex="0"
            >
                <div class="vocabulary-search-shell">
                    <div class="vocabulary-search-main">
                        <form
                            class="vocabulary-search-panel mb-3"
                            action="{{ route('vocabularies.index') }}"
                            method="GET"
                            data-vocabulary-search
                            data-search-url="{{ route('vocabularies.search') }}"
                        >
                            <div class="vocabulary-search-grid">
                                <label class="vocabulary-search-field">
                                    <span class="translate-box-label">Từ cần tra</span>
                                    <input
                                        class="vocabulary-search-input"
                                        name="q"
                                        value="{{ $search }}"
                                        placeholder="Nhập từ tiếng Anh hoặc nghĩa tiếng Việt..."
                                        autocomplete="off"
                                        data-vocabulary-input
                                    >
                                </label>

                                <div class="vocabulary-search-buttons">
                                    <button class="btn btn-primary" type="submit"><x-ui-icon name="search" /> Tra từ</button>
                                    <a class="btn btn-outline-primary" href="{{ route('vocabularies.flashcards') }}"><x-ui-icon name="book" /> Flashcard</a>
                                    <a class="btn btn-outline-primary" href="{{ route('vocabularies.quiz') }}"><x-ui-icon name="check" /> Quiz nhanh</a>
                                </div>
                            </div>

                            <div class="vocabulary-search-actions">
                                <div class="dictionary-live-status" data-vocabulary-status aria-live="polite"></div>
                            </div>
                        </form>

                        <div class="vocabulary-results-wrap" data-vocabulary-results data-lazy-list>
                            @include('vocabularies._results')
                        </div>
                    </div>

                    <aside class="vocabulary-side-stack">
                        <section class="vocabulary-side-card">
                            <h2>Mẹo tìm kiếm</h2>
                            <ul class="vocabulary-tip-list">
                                <li><span><x-ui-icon name="search" /></span><div><strong>Tìm theo từ tiếng Anh</strong><small>environment, sustainable</small></div></li>
                                <li><span><x-ui-icon name="language" /></span><div><strong>Tìm theo nghĩa tiếng Việt</strong><small>môi trường, học thuật</small></div></li>
                                <li><span><x-ui-icon name="book" /></span><div><strong>Tìm theo định nghĩa</strong><small>natural world, academic word</small></div></li>
                                <li><span><x-ui-icon name="list" /></span><div><strong>Tìm theo chủ đề</strong><small>education, technology, health</small></div></li>
                            </ul>
                        </section>

                        <section class="vocabulary-side-card vocabulary-practice-card">
                            <div>
                                <h2>Học chủ động hơn</h2>
                                <p>Luyện tập qua Flashcard và Quiz để ghi nhớ từ vựng hiệu quả hơn.</p>
                                <div class="d-grid gap-2">
                                    <a class="btn btn-outline-primary" href="{{ route('vocabularies.flashcards') }}"><x-ui-icon name="book" /> Flashcard</a>
                                    <a class="btn btn-primary" href="{{ route('vocabularies.quiz') }}"><x-ui-icon name="check" /> Quiz nhanh</a>
                                </div>
                            </div>
                            <div class="vocabulary-mini-cards" aria-hidden="true">
                                <span>Aa</span>
                                <span>?</span>
                            </div>
                        </section>
                    </aside>
                </div>
            </div>

            <div
                class="tab-pane fade {{ $reviewTabActive ? 'show active' : '' }}"
                id="vocabulary-review-pane"
                role="tabpanel"
                aria-labelledby="vocabulary-review-tab"
                tabindex="0"
            >
                <div class="vocabulary-review-shell">
                    @include('vocabularies._topic_review')
                    <aside class="vocabulary-side-card vocabulary-review-help">
                        <h2>Cách ôn hiệu quả</h2>
                        <ul class="vocabulary-tip-list">
                            <li><span><x-ui-icon name="check" /></span><div><strong>Nhìn từ trước</strong><small>Đoán nghĩa trước khi xem giải thích.</small></div></li>
                            <li><span><x-ui-icon name="edit" /></span><div><strong>Tự nhập đáp án</strong><small>Gõ nghĩa tiếng Việt để kiểm tra trí nhớ.</small></div></li>
                            <li><span><x-ui-icon name="refresh" /></span><div><strong>Ôn lại từ sai</strong><small>Lặp lại bằng flashcard hoặc quiz nhanh.</small></div></li>
                        </ul>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
