@extends('layouts.app')

@section('content')
    <header class="page-header">
        <h1>Bài test thành phần câu - {{ $config['name'] }}</h1>
        <p class="text-muted">{{ $config['band'] }}. Xác định cụm được gạch ra là chủ ngữ, vị ngữ, tân ngữ, bổ ngữ hay trạng ngữ.</p>
    </header>

    <form method="POST" action="{{ route('tests.sentence-role.submit', $level) }}">
        @csrf

        <div class="vstack gap-3">
            @foreach ($questions as $index => $question)
                <article class="card">
                    <div class="card-body">
                        <span class="badge text-bg-primary">Câu {{ $index + 1 }}</span>
                        <h3 class="h5 mt-3">{{ $question['sentence'] }}</h3>
                        <p>Cần xác định: <strong>{{ $question['target'] }}</strong></p>

                        <input type="hidden" name="correct[{{ $index }}]" value="{{ $question['correct'] }}">
                        <input type="hidden" name="explanations[{{ $index }}]" value="{{ $question['explanation'] }}">
                        <input type="hidden" name="targets[{{ $index }}]" value="{{ $question['target'] }}">

                        <div class="vstack gap-2 mt-3">
                            @foreach ($options as $option)
                                <label class="form-check border rounded p-3">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="answers[{{ $index }}]" value="{{ $option }}" required>
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
