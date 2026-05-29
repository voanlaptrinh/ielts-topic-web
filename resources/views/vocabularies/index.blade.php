@extends('layouts.app')

@section('title', 'Từ vựng IELTS - IELTS Focus')
@section('meta_description', 'Tra cứu và ôn tập từ vựng IELTS theo nghĩa tiếng Việt, định nghĩa, ví dụ, chủ đề, flashcard và quiz nhanh.')

@section('content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">Vocabulary bank</span>
        <h1 class="display-title">Tra từ vựng IELTS theo kiểu nhanh và trực tiếp.</h1>
        <p class="lead-copy mb-0">Gõ từ khóa để xem nghĩa tiếng Việt, định nghĩa, ví dụ, chủ đề và level mà không cần tải lại trang.</p>
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
                <form
                    class="vocabulary-search-panel mb-4"
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
                    </div>

                    <div class="vocabulary-search-actions">
                        <div class="dictionary-live-status" data-vocabulary-status aria-live="polite"></div>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('vocabularies.flashcards') }}">Flashcard</a>
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('vocabularies.quiz') }}">Quiz nhanh</a>
                            <button class="btn btn-primary btn-sm" type="submit">Tra từ</button>
                        </div>
                    </div>
                </form>

                <div class="vocabulary-results-wrap" data-vocabulary-results data-lazy-list>
                    @include('vocabularies._results')
                </div>
            </div>

            <div
                class="tab-pane fade {{ $reviewTabActive ? 'show active' : '' }}"
                id="vocabulary-review-pane"
                role="tabpanel"
                aria-labelledby="vocabulary-review-tab"
                tabindex="0"
            >
                @include('vocabularies._topic_review')
            </div>
        </div>
    </section>
@endsection
