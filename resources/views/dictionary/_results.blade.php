@if (! $search)
    <section class="empty-state">
        <h2 class="h5">Nhập một từ tiếng Anh để tra nghĩa</h2>
        <p class="text-muted mb-0">Ví dụ: environment, sustainable, running, education.</p>
    </section>
@else
    @php
        $primaryVocabulary = $vocabularyMatches->first();
        $primaryEntry = $exactEntries->first();
        $primaryWord = $primaryVocabulary?->word ?? $primaryEntry?->word;
        $vocabularyMeaning = $primaryVocabulary?->meaning_vi;
        $primaryMeaning = $vocabularyMeaning ?: $primaryEntry?->definition_vi;
        $primaryDefinition = $primaryVocabulary?->definition_en ?? $primaryEntry?->definition;
        $primaryExample = $primaryVocabulary?->example_en ?? ($primaryEntry?->examples[0] ?? null);
    @endphp

    <section class="dictionary-translate-panel mb-4">
        <div class="dictionary-translate-column">
            <span class="eyebrow">Từ cần tra</span>
            <div class="translate-text">{{ $search }}</div>
            @if ($resolvedWord && $resolvedWord !== $search)
                <p class="text-muted mb-0">Đã quy về từ gốc "{{ $resolvedWord }}".</p>
            @endif
        </div>

        <div class="dictionary-translate-column">
            <span class="eyebrow">Nghĩa tiếng Việt</span>
            @if ($primaryMeaning)
                <div class="translate-text">{{ $primaryMeaning }}</div>
                @if ($primaryWord)
                    <p class="text-muted mb-1">{{ $primaryWord }}</p>
                @endif
                @if ($primaryDefinition)
                    <p class="mb-1"><strong>Định nghĩa:</strong> {{ $primaryDefinition }}</p>
                @endif
                @if ($primaryExample)
                    <p class="text-muted mb-0">{{ $primaryExample }}</p>
                @endif
            @else
                <div class="translate-text">Chưa tìm thấy nghĩa trực tiếp</div>
                <p class="text-muted mb-0">Thử nhập từ gốc hoặc một từ gần nghĩa hơn.</p>
            @endif
        </div>
    </section>

    @if ($vocabularyMatches->isNotEmpty())
        <section class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between gap-3 align-items-start">
                    <div>
                        <span class="eyebrow">Từ vựng IELTS</span>
                        <h2 class="h4 mt-2 mb-0">Kết quả từ bộ từ vựng</h2>
                    </div>
                </div>

                <div class="vstack gap-3 mt-3">
                    @foreach ($vocabularyMatches as $word)
                        <article class="soft-panel">
                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                <div>
                                    <h3 class="h5 mb-1">{{ $word->word }}</h3>
                                    <p class="mb-1"><strong>{{ $word->meaning_vi }}</strong></p>
                                </div>
                                <a class="btn btn-outline-primary btn-sm align-self-start" href="{{ route('vocabularies.show', $word->word) }}">Mở thẻ từ</a>
                            </div>
                            <p class="mb-1">{{ $word->definition_en }}</p>
                            <p class="text-muted mb-0">{{ $word->example_en }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($exactEntries->isNotEmpty())
        <section class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between gap-3 align-items-start">
                    <div>
                        <span class="eyebrow">Từ điển</span>
                        <h2 class="h4 mt-2 mb-1">{{ $exactEntries->first()->word }}</h2>
                        <p class="text-muted mb-0">{{ $exactEntries->count() }} nghĩa khớp trực tiếp.</p>
                    </div>
                    <a class="btn btn-primary align-self-start" href="{{ route('dictionary.show', str_replace(' ', '_', $exactEntries->first()->normalized_word)) }}">Xem đầy đủ</a>
                </div>

                <div class="vstack gap-3 mt-3">
                    @foreach ($exactEntries as $entry)
                        <article class="soft-panel">
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="badge text-bg-primary">{{ $entry->part_of_speech }}</span>
                            </div>
                            <p class="mb-1"><strong>{{ $entry->definition_vi }}</strong></p>
                            <p class="text-muted mb-0">{{ $entry->definition }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section>
        <div class="d-flex flex-wrap justify-content-between gap-2 align-items-end mb-3">
            <div>
                <span class="eyebrow">Liên quan</span>
                <h2 class="h4 mb-0">Các mục gần với "{{ $search }}"</h2>
            </div>
            <span class="text-muted">{{ $words->total() }} kết quả</span>
        </div>

        <div class="resource-list">
            @forelse ($words as $item)
                <article class="card study-card">
                    <div class="card-body d-flex flex-column">
                        <h3 class="h5">{{ $item->word }}</h3>
                        <p class="text-muted">{{ str_replace(',', ', ', $item->parts_of_speech) }}</p>
                        <p class="mb-3">{{ $item->senses_count }} nghĩa / cách dùng</p>
                        <a class="btn btn-outline-primary align-self-start" href="{{ route('dictionary.show', str_replace(' ', '_', $item->normalized_word)) }}">Xem nghĩa</a>
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <h3 class="h5">Không tìm thấy mục từ phù hợp</h3>
                    <p class="text-muted">Thử tra bằng từ gốc hoặc một từ gần nghĩa hơn. Ví dụ: study, run, policy.</p>

                    @if ($suggestions->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            @foreach ($suggestions as $suggestion)
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('dictionary.show', str_replace(' ', '_', $suggestion->normalized_word)) }}">
                                    {{ $suggestion->word }} ({{ $suggestion->senses_count }})
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforelse
        </div>
    </section>

    @if ($words->hasPages())
        <nav class="dictionary-pagination d-flex justify-content-between align-items-center gap-3 mt-4">
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
@endif
