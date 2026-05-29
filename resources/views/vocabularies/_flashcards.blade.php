@foreach ($words as $word)
    <article class="card study-card flashcard" onclick="this.classList.toggle('open')">
        <div class="card-body d-flex flex-column">
            <div class="d-flex flex-wrap justify-content-between gap-2">
                <span class="badge text-bg-primary">{{ $word->topic }}</span>
                <span class="footer-note">{{ $word->part_of_speech }}</span>
            </div>
            <h2 class="h4 mt-3">{{ $word->word }}</h2>
            <p class="text-muted">{{ $word->phonetic }}</p>
            <button class="btn btn-outline-primary btn-sm align-self-start mt-auto" type="button">Lật thẻ</button>

            <div class="answer">
                <p><strong>Nghĩa:</strong> {{ $word->meaning_vi }}</p>
                <p><strong>Giải thích:</strong> {{ $word->definition_en }}</p>
                <p class="mb-0"><strong>Ví dụ:</strong> {{ $word->example_en }}</p>
            </div>
        </div>
    </article>
@endforeach
