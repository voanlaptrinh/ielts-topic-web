@extends('layouts.app')

@section('content')
    @php($durationSeconds = max(300, $questions->count() * 45))
    <header class="page-header">
        <h1>Bài test loại từ - {{ $config['name'] }}</h1>
        <p class="text-muted">{{ $config['band'] }}. Đọc từ và định nghĩa, chọn loại từ đúng. Hệ thống sẽ giải thích nếu sai.</p>
    </header>

    @include('shared._guest_save_notice')

    <form method="POST" action="{{ route('tests.grammar.submit', $level) }}" data-timed-test data-duration-seconds="{{ $durationSeconds }}">
        @csrf
        @include('tests._timer', ['durationSeconds' => $durationSeconds])

        <div class="vstack gap-3">
            @foreach ($questions as $index => $entry)
                <article class="card">
                    <div class="card-body">
                        <span class="badge text-bg-primary">Câu {{ $index + 1 }}</span>
                        <h3 class="h5 mt-3">{{ $entry->word }}</h3>
                        <p>{{ $entry->definition_vi }}</p>
                        <p class="text-muted">Định nghĩa gốc: {{ $entry->definition }}</p>

                        <div class="vstack gap-2 mt-3">
                            <input type="hidden" name="answers[{{ $entry->id }}]" value="Chưa chọn">
                            @foreach ($options as $option)
                                <label class="form-check border rounded p-3">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="answers[{{ $entry->id }}]" value="{{ $option }}" required>
                                    <span class="form-check-label">{{ ['noun' => 'danh từ', 'verb' => 'động từ', 'adjective' => 'tính từ', 'adverb' => 'trạng từ'][$option] }}</span>
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
