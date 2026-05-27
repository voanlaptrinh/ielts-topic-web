@extends('layouts.app')

@section('content')
    <header class="page-header">
        <h1>Bài test từ vựng - {{ $config['name'] }}</h1>
        <p class="text-muted">{{ $config['band'] }}. Chọn nghĩa tiếng Việt đúng nhất, sau đó bấm nộp bài để xem điểm và lỗi sai.</p>
    </header>

    <form method="POST" action="{{ route('tests.vocabulary.submit', $level) }}">
        @csrf

        <div class="vstack gap-3">
            @foreach ($questions as $index => $question)
                <article class="card">
                    <div class="card-body">
                        <span class="badge text-bg-primary">Câu {{ $index + 1 }}</span>
                        <h3 class="h5 mt-3">{{ $question['prompt'] }}</h3>

                        <div class="vstack gap-2 mt-3">
                            @foreach ($question['options'] as $option)
                                <label class="form-check border rounded p-3">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="answers[{{ $question['id'] }}]" value="{{ $option }}" required>
                                    <span class="form-check-label">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-3">
            <button class="btn btn-primary" type="submit">Nộp bài và chấm điểm</button>
        </div>
    </form>
@endsection
