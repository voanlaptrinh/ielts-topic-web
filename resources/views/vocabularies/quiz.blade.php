@extends('layouts.app')

@section('content')
    <header class="page-header">
        <h1>Làm quiz từ vựng IELTS</h1>
        <p class="text-muted">Chọn nghĩa đúng của mỗi từ, nộp bài để xem điểm và xem lại lỗi sai.</p>
    </header>

    <form method="POST" action="{{ route('vocabularies.quiz.submit') }}">
        @csrf

        <div class="vstack gap-3">
            @foreach ($questions as $index => $question)
                <article class="card">
                    <div class="card-body">
                        <span class="badge text-bg-primary">Câu {{ $index + 1 }}</span>
                        <h3 class="h5 mt-3">{{ $question['word']->word }}</h3>
                        <p class="text-muted">{{ $question['word']->phonetic }} · {{ $question['word']->part_of_speech }}</p>

                        <div class="vstack gap-2">
                            @foreach ($question['options'] as $option)
                                <label class="form-check option-card p-3">
                                    <input class="form-check-input ms-0 mt-1" type="radio" name="answers[{{ $question['word']->id }}]" value="{{ $option }}" required>
                                    <span class="form-check-label">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Nộp bài và chấm điểm</button>
            <a class="btn btn-outline-secondary" href="{{ route('vocabularies.quiz') }}">Đổi bộ câu hỏi</a>
        </div>
    </form>
@endsection
