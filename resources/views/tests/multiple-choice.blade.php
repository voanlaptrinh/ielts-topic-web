@extends('layouts.app')

@section('title', $title . ' - IELTS Focus')
@section('meta_description', $subtitle . ' Làm bài, chấm điểm ngay và xem giải thích lỗi sai trên IELTS Focus.')

@section('content')
    @php($durationSeconds = max(300, $questions->count() * 45))
    <header class="page-header">
        <h1>{{ $title }}</h1>
        <p class="text-muted">{{ $subtitle }}</p>
    </header>

    @include('shared._guest_save_notice')

    <form method="POST" action="{{ $action }}" data-timed-test data-duration-seconds="{{ $durationSeconds }}">
        @csrf
        @include('tests._timer', ['durationSeconds' => $durationSeconds])

        <div class="vstack gap-3">
            @foreach ($questions as $index => $question)
                <article class="card">
                    <div class="card-body">
                        <span class="badge text-bg-primary">Câu {{ $index + 1 }}</span>
                        <h3 class="h5 mt-3">{{ $question['prompt'] }}</h3>

                        @if (! empty($question['detail']))
                            <p class="text-muted">{{ $question['detail'] }}</p>
                        @endif

                        <div class="vstack gap-2 mt-3">
                            <input type="hidden" name="answers[{{ $question['id'] }}]" value="Chưa chọn">
                            @foreach ($question['options'] as $option)
                                <label class="form-check option-card p-3">
                                    <input class="form-check-input ms-0 mt-1" type="radio" name="answers[{{ $question['id'] }}]" value="{{ $option }}" required>
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
