@extends('layouts.app')

@section('title', $vocabulary->word . ' - IELTS Focus')

@section('content')
    <a class="btn btn-link ps-0 mb-3" href="{{ route('vocabularies.index', ['q' => $vocabulary->word]) }}">Quay lại tra từ vựng</a>

    <article class="vocabulary-detail-hero mb-4">
        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-start">
            <div>
                <span class="eyebrow">IELTS vocabulary</span>
                <h1 class="display-title mb-2">{{ $vocabulary->word }}</h1>
                <p class="lead-copy mb-0">{{ $vocabulary->phonetic }} · {{ $vocabulary->part_of_speech }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge text-bg-primary">{{ $vocabulary->topic }}</span>
                <span class="badge text-bg-success">{{ $vocabulary->level }}</span>
            </div>
        </div>
    </article>

    <section class="vocabulary-detail-grid">
        <article class="vocabulary-detail-panel vocabulary-detail-panel-main">
            <span class="translate-box-label">Nghĩa tiếng Việt</span>
            <p class="vocabulary-detail-meaning">{{ $vocabulary->meaning_vi }}</p>
        </article>

        <article class="vocabulary-detail-panel">
            <span class="translate-box-label">Giải thích tiếng Anh</span>
            <p class="mb-0 mt-3">{{ $vocabulary->definition_en }}</p>
        </article>

        <article class="vocabulary-detail-panel vocabulary-detail-panel-wide">
            <span class="translate-box-label">Ví dụ IELTS</span>
            <p class="mt-3 mb-2">{{ $vocabulary->example_en }}</p>
            <p class="text-muted mb-0">{{ $vocabulary->example_vi }}</p>
        </article>

        <article class="vocabulary-detail-panel">
            <span class="translate-box-label">Từ đồng nghĩa</span>
            @if ($vocabulary->synonyms)
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @foreach ($vocabulary->synonyms as $synonym)
                        <a class="badge text-bg-primary" href="{{ route('vocabularies.index', ['q' => $synonym]) }}">{{ $synonym }}</a>
                    @endforeach
                </div>
            @else
                <p class="text-muted mb-0 mt-3">Chưa có từ đồng nghĩa cho mục này.</p>
            @endif
        </article>
    </section>
@endsection
