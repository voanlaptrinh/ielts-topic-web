@extends('layouts.app')

@section('title', 'Từ điển Anh - Việt - IELTS Focus')

@section('content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">English - Vietnamese dictionary</span>
        <h1 class="display-title">Tra nghĩa, loại từ, biến thể và ngữ cảnh dùng trong IELTS.</h1>
        <p class="lead-copy mb-0">Tìm theo từ gốc, biến thể như running/studies/went, cụm từ hoặc định nghĩa tiếng Anh. Khi đăng nhập, các từ đã mở sẽ được lưu vào tiến độ học tập.</p>
    </header>

    @php($dictionaryTabActive = $search !== '')

    <section class="dictionary-tabs">
        <ul class="nav nav-tabs dictionary-tab-nav" id="dictionaryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link {{ $dictionaryTabActive ? '' : 'active' }}"
                    id="translate-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#translate-pane"
                    type="button"
                    role="tab"
                    aria-controls="translate-pane"
                    aria-selected="{{ $dictionaryTabActive ? 'false' : 'true' }}"
                >Dịch</button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link {{ $dictionaryTabActive ? 'active' : '' }}"
                    id="lookup-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#lookup-pane"
                    type="button"
                    role="tab"
                    aria-controls="lookup-pane"
                    aria-selected="{{ $dictionaryTabActive ? 'true' : 'false' }}"
                >Từ điển</button>
            </li>
        </ul>

        <div class="tab-content dictionary-tab-content">
            <div
                class="tab-pane fade {{ $dictionaryTabActive ? '' : 'show active' }}"
                id="translate-pane"
                role="tabpanel"
                aria-labelledby="translate-tab"
                tabindex="0"
            >
                <section
                    class="google-translate-form"
                    data-google-translate
                    data-translate-url="{{ route('dictionary.translate') }}"
                >
                    <div class="translate-toolbar">
                        <span class="eyebrow">Google Cloud Translate</span>
                        <div class="translate-language-pair">
                            <span>Tiếng Anh</span>
                            <span aria-hidden="true">→</span>
                            <span>Tiếng Việt</span>
                        </div>
                    </div>

                    <div class="google-translate-grid">
                        <label class="google-translate-box">
                            <span class="translate-box-label">Nhập văn bản</span>
                            <textarea
                                class="google-translate-input"
                                rows="6"
                                maxlength="5000"
                                placeholder="Nhập từ, câu hoặc đoạn văn tiếng Anh để dịch..."
                                data-google-translate-input
                            ></textarea>
                        </label>

                        <div class="google-translate-box google-translate-output" aria-live="polite">
                            <span class="translate-box-label">Bản dịch</span>
                            <div class="google-translate-result" data-google-translate-result>Nhập văn bản để bắt đầu dịch.</div>
                            <div class="google-translate-status" data-google-translate-status></div>
                        </div>
                    </div>
                </section>
            </div>

            <div
                class="tab-pane fade {{ $dictionaryTabActive ? 'show active' : '' }}"
                id="lookup-pane"
                role="tabpanel"
                aria-labelledby="lookup-tab"
                tabindex="0"
            >
                <form
                    class="search-panel mb-4"
                    action="{{ route('dictionary.index') }}"
                    method="GET"
                    data-dictionary-search
                    data-search-url="{{ route('dictionary.search') }}"
                >
                    <div class="row g-2">
                        <div class="col-lg-10">
                            <input
                                class="form-control"
                                name="q"
                                value="{{ $search }}"
                                placeholder="Nhập từ cần tra, ví dụ: run, running, education, sustainable"
                                autocomplete="off"
                                data-dictionary-input
                            >
                        </div>
                        <div class="col-lg-2 d-grid">
                            <button class="btn btn-primary" type="submit">Tra từ</button>
                        </div>
                    </div>
                    <div class="dictionary-live-status mt-2" data-dictionary-status aria-live="polite"></div>
                </form>

                <div class="dictionary-results-wrap position-relative" data-dictionary-results>
                    @include('dictionary._results')
                </div>
            </div>
        </div>
    </section>
@endsection
