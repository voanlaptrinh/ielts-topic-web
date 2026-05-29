@extends('admin.layout')

@section('title', 'Review bài nộp - IELTS Focus')
@section('admin_eyebrow', $submission->test_type)
@section('admin_title', 'Review bài của ' . $submission->user->name)

@section('admin_content')
    <div class="row g-4">
        <section class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h2 class="section-title">Bài học viên đã nộp</h2>
                    @foreach ($submission->details ?? [] as $index => $detail)
                        <article class="soft-panel mt-3">
                            <span class="badge text-bg-primary">Câu {{ $index + 1 }}</span>
                            <h3 class="h5 mt-3">{{ $detail['word'] ?? 'Prompt' }}</h3>
                            <p><strong>Bài nộp:</strong></p>
                            <div class="practice-text">{!! nl2br(e($detail['answer'] ?? '')) !!}</div>
                            @if (! empty($detail['explanation']))
                                <p class="mt-3 mb-0"><strong>Gợi ý:</strong> {{ $detail['explanation'] }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="col-lg-5">
            <form class="card" method="POST" action="{{ route('admin.submissions.update', $submission) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <h2 class="section-title">Feedback</h2>
                    <label class="form-label mt-3">Band estimate</label>
                    <input class="form-control" type="number" step="0.5" min="0" max="9" name="band_score" value="{{ old('band_score', $submission->band_score) }}" required>

                    <div class="row g-2 mt-2">
                        @foreach ([
                            'task_response' => 'Task response',
                            'coherence' => 'Coherence',
                            'lexical_resource' => 'Lexical',
                            'grammar' => 'Grammar',
                            'fluency' => 'Fluency',
                            'pronunciation' => 'Pronunciation',
                        ] as $name => $label)
                            <div class="col-6">
                                <label class="form-label">{{ $label }}</label>
                                <input class="form-control" type="number" step="0.5" min="0" max="9" name="{{ $name }}" value="{{ old($name, $submission->criteria_scores[$name] ?? '') }}">
                            </div>
                        @endforeach
                    </div>

                    <label class="form-label mt-3">Nhận xét chi tiết</label>
                    <textarea class="form-control" name="feedback" rows="8" required>{{ old('feedback', $submission->feedback) }}</textarea>

                    <button class="btn btn-primary mt-3" type="submit">Gửi feedback</button>
                </div>
            </form>
        </section>
    </div>
@endsection
