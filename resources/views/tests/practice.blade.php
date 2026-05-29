@extends('layouts.app')

@section('title', $practiceTest->title . ' - IELTS Focus')
@section('meta_description', ($practiceTest->description ?: $practiceTest->title) . ' Làm bài có giới hạn thời gian, tự nộp và chấm điểm ngay.')

@section('content')
    @php($durationSeconds = $practiceTest->duration_minutes * 60)

    <header class="page-header">
        <span class="eyebrow">{{ ucfirst($practiceTest->skill) }} · {{ $practiceTest->level }}</span>
        <h1>{{ $practiceTest->title }}</h1>
        @if ($practiceTest->description)
            <p class="text-muted">{{ $practiceTest->description }}</p>
        @endif
    </header>

    @include('shared._guest_save_notice')

    @if ($practiceTest->audioUrl())
        <section class="soft-panel mb-4">
            <h2 class="section-title">Audio</h2>
            <audio class="w-100 mt-3" controls src="{{ $practiceTest->audioUrl() }}"></audio>
        </section>
    @endif

    @if ($practiceTest->passage)
        <section class="soft-panel mb-4">
            <h2 class="section-title">Reading passage</h2>
            <div class="practice-text mt-3">{!! nl2br(e($practiceTest->passage)) !!}</div>
        </section>
    @endif

    @if ($practiceTest->transcript)
        <details class="soft-panel mb-4">
            <summary class="section-title">Transcript</summary>
            <div class="practice-text mt-3">{!! nl2br(e($practiceTest->transcript)) !!}</div>
        </details>
    @endif

    <form method="POST" action="{{ route('tests.practice.submit', [$practiceTest->skill, $practiceTest]) }}" data-timed-test data-duration-seconds="{{ $durationSeconds }}">
        @csrf
        @include('tests._timer', ['durationSeconds' => $durationSeconds])

        <div class="vstack gap-3">
            @foreach ($practiceTest->questions as $index => $question)
                <article class="card">
                    <div class="card-body">
                        <span class="badge text-bg-primary">Câu {{ $index + 1 }}</span>
                        <h3 class="h5 mt-3">{{ $question->prompt }}</h3>

                        <input type="hidden" name="answers[{{ $question->id }}]" value="Chưa chọn">

                        @if (in_array($practiceTest->skill, ['writing', 'speaking'], true))
                            <textarea class="form-control mt-3" name="answers[{{ $question->id }}]" rows="{{ $practiceTest->skill === 'writing' ? 10 : 5 }}" placeholder="{{ $practiceTest->skill === 'writing' ? 'Viết bài của bạn ở đây' : 'Ghi câu trả lời nháp hoặc transcript luyện nói' }}"></textarea>
                        @elseif ($question->question_type === 'short_answer' || empty($question->options))
                            <input class="form-control mt-3" name="answers[{{ $question->id }}]" placeholder="Nhập đáp án">
                        @else
                            <div class="vstack gap-2 mt-3">
                                @foreach ($question->options as $option)
                                    <label class="form-check option-card p-3">
                                        <input class="form-check-input ms-0 mt-1" type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}">
                                        <span class="form-check-label">{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-3">
            <button class="btn btn-primary" type="submit">Nộp bài và chấm điểm</button>
        </div>
    </form>
@endsection
