<section class="vocabulary-review-layout">
    <aside class="vocabulary-topic-list">
        <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
            <span class="eyebrow">Lĩnh vực / topic</span>
            <span class="footer-note">{{ number_format($topicGroups->sum('total')) }} từ</span>
        </div>

        <div class="vocabulary-topic-buttons">
            @foreach ($topicGroups as $topic)
                <a
                    class="vocabulary-topic-button {{ $activeTopic === $topic->topic ? 'active' : '' }}"
                    href="{{ route('vocabularies.index', ['topic' => $topic->topic]) }}"
                >
                    <span>{{ $topic->topic }}</span>
                    <strong>{{ $topic->total }}</strong>
                </a>
            @endforeach
        </div>
    </aside>

    <div class="vocabulary-topic-study">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <span class="eyebrow">Ôn từ theo topic</span>
                <h2 class="h4 mt-2 mb-1">{{ $activeTopic ?: 'Chưa có topic' }}</h2>
                <p class="text-muted mb-0">{{ number_format($topicWords->count()) }} từ trong nhóm này.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-primary btn-sm" href="{{ route('vocabularies.flashcards') }}">Flashcard</a>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('vocabularies.quiz') }}">Quiz nhanh</a>
            </div>
        </div>

        <section class="vocabulary-topic-word-grid">
            @forelse ($topicWords as $word)
                <article class="vocabulary-topic-word" data-vocabulary-practice-card>
                    <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                        <div>
                            <h3 class="h5 mb-1">{{ $word->word }}</h3>
                            <small>{{ $word->phonetic }} · {{ $word->part_of_speech }}</small>
                        </div>
                        <span class="vocabulary-level-pill">{{ $word->level }}</span>
                    </div>
                    <p class="text-muted mb-2">{{ $word->definition_en }}</p>
                    <p class="vocabulary-topic-example mb-3">{{ $word->example_en }}</p>

                    <label class="translate-box-label" for="practice-word-{{ $word->id }}">Nhập nghĩa tiếng Việt</label>
                    <div class="vocabulary-practice-answer">
                        <input
                            id="practice-word-{{ $word->id }}"
                            class="form-control"
                            type="text"
                            autocomplete="off"
                            placeholder="Nhập nghĩa tiếng Việt..."
                            data-vocabulary-practice-input
                            data-answer="{{ mb_strtolower($word->meaning_vi) }}"
                        >
                        <button class="btn btn-primary btn-sm" type="button" data-vocabulary-practice-check>Kiểm tra</button>
                    </div>

                    <div class="vocabulary-practice-feedback" data-vocabulary-practice-feedback aria-live="polite"></div>
                    <a class="vocabulary-practice-link" href="{{ route('vocabularies.show', $word->word) }}">Xem giải thích</a>
                </article>
            @empty
                <div class="empty-state">
                    <h3 class="h5">Chưa có từ trong topic này</h3>
                    <p class="text-muted mb-0">Hãy seed thêm dữ liệu hoặc chọn topic khác.</p>
                </div>
            @endforelse
        </section>
    </div>
</section>
