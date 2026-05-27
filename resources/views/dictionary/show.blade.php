@extends('layouts.app')

@section('content')
    <a class="btn btn-link ps-0 mb-3" href="{{ route('dictionary.index') }}">Quay lại từ điển</a>

    <header class="page-header">
        <h1>{{ $word }}</h1>
        <p class="text-muted">{{ $entries->count() }} nghĩa / cách dùng. Phần tiếng Việt được hiển thị trước để người học dễ nắm ý.</p>
    </header>

    <div class="vstack gap-3">
        @foreach ($entries as $entry)
            <article class="card">
                <div class="card-body">
                    <span class="badge text-bg-primary">{{ $entry->part_of_speech }}</span>

                    <section class="mt-3">
                        <h2 class="h4">Loại từ và vai trò trong câu</h2>
                        <p><strong>{{ $grammarNotes[$entry->id]['label'] }}:</strong> {{ $grammarNotes[$entry->id]['role'] }}</p>
                        <p class="text-muted">Mẫu dùng: {{ $grammarNotes[$entry->id]['pattern'] }}</p>
                    </section>

                    <section class="mt-3">
                        <h2 class="h4">Nghĩa / ngữ cảnh tiếng Việt</h2>
                        <p>{{ $entry->definition_vi }}</p>
                    </section>

                    <section class="mt-3">
                        <h2 class="h4">Định nghĩa gốc tiếng Anh</h2>
                        <p>{{ $entry->definition }}</p>
                    </section>

                    @if ($entry->examples)
                        <section class="mt-3">
                            <h3 class="h5">Ví dụ tiếng Anh</h3>
                            <ul class="mb-0">
                                @foreach ($entry->examples as $example)
                                    <li>{{ $example }}</li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($entry->synonyms)
                        <section class="mt-3">
                            <h3 class="h5">Từ liên quan / đồng nghĩa</h3>
                            <p class="mb-0">{{ implode(', ', $entry->synonyms) }}</p>
                        </section>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endsection
