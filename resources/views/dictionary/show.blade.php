@extends('layouts.app')

@section('title', $word . ' - Từ điển IELTS Focus')

@section('content')
    <a class="btn btn-link ps-0 mb-3" href="{{ route('dictionary.index', ['q' => $searchedWord]) }}">Quay lại kết quả tra cứu</a>

    <header class="page-header">
        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-start">
            <div>
                <span class="eyebrow">Dictionary entry</span>
                <h1 class="display-title mb-2">{{ $word }}</h1>
                <p class="text-muted mb-0">
                    {{ $entries->count() }} nghĩa / cách dùng.
                    @if ($searchedWord !== $resolvedWord)
                        Từ bạn tra "{{ $searchedWord }}" đã được quy về từ gốc "{{ $resolvedWord }}".
                    @endif
                </p>
            </div>

            <form class="search-panel flex-grow-1" style="max-width: 420px" action="{{ route('dictionary.index') }}" method="GET">
                <div class="input-group">
                    <input class="form-control" name="q" value="{{ $searchedWord }}" placeholder="Tra từ khác">
                    <button class="btn btn-primary" type="submit">Tra</button>
                </div>
            </form>
        </div>
    </header>

    @if ($vocabulary)
        <section class="card mb-4">
            <div class="card-body">
                <span class="eyebrow">IELTS vocabulary</span>
                <h2 class="h4 mt-2">{{ $vocabulary->word }}</h2>
                <p class="mb-1"><strong>Nghĩa học IELTS:</strong> {{ $vocabulary->meaning_vi }}</p>
                <p class="mb-1"><strong>Định nghĩa:</strong> {{ $vocabulary->definition_en }}</p>
                <p class="mb-0 text-muted">{{ $vocabulary->example_en }}</p>
            </div>
        </section>
    @endif

    <div class="vstack gap-3">
        @foreach ($entries as $entry)
            <article class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="badge text-bg-primary">{{ $grammarNotes[$entry->id]['label'] }}</span>
                        <span class="badge text-bg-success">{{ $entry->part_of_speech }}</span>
                        <span class="badge text-bg-warning">{{ $entry->source }}</span>
                    </div>

                    <section class="mt-3">
                        <h2 class="h4">Loại từ và vai trò trong câu</h2>
                        <p><strong>{{ $grammarNotes[$entry->id]['label'] }}:</strong> {{ $grammarNotes[$entry->id]['role'] }}</p>
                        <p class="text-muted mb-0">Mẫu dùng: {{ $grammarNotes[$entry->id]['pattern'] }}</p>
                    </section>

                    <section class="mt-3">
                        <h2 class="h4">Nghĩa / ngữ cảnh tiếng Việt</h2>
                        <p class="mb-0">{{ $entry->definition_vi }}</p>
                    </section>

                    <section class="mt-3">
                        <h2 class="h4">Định nghĩa gốc tiếng Anh</h2>
                        <p class="mb-0">{{ $entry->definition }}</p>
                    </section>

                    @if ($entry->examples)
                        <section class="mt-3">
                            <h3 class="h5">Ví dụ tiếng Anh</h3>
                            <ul class="mb-0">
                                @foreach ($entry->examples as $example)
                                    <li>{{ $example }}</li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($entry->synonyms)
                        <section class="mt-3">
                            <h3 class="h5">Từ liên quan / đồng nghĩa</h3>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($entry->synonyms as $synonym)
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('dictionary.show', str_replace(' ', '_', strtolower($synonym))) }}">{{ $synonym }}</a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>
            </article>
        @endforeach
    </div>

    @if ($relatedWords->isNotEmpty())
        <section class="card mt-4">
            <div class="card-body">
                <h2 class="section-title">Tra tiếp các từ liên quan</h2>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @foreach ($relatedWords as $relatedWord)
                        <a class="btn btn-outline-primary btn-sm" href="{{ $relatedWord['url'] }}">{{ $relatedWord['word'] }}</a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
