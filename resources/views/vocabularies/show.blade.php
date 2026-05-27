@extends('layouts.app')

@section('title', $vocabulary->word . ' - IELTS Focus')

@section('content')
    <a class="btn btn-link ps-0 mb-3" href="{{ route('vocabularies.index') }}">Quay lại từ điển</a>

    <article class="hero-panel p-4 p-lg-5">
        <div class="d-flex flex-wrap justify-content-between gap-3 align-items-start">
            <div class="d-flex flex-wrap gap-2">
                <span class="badge text-bg-primary">{{ $vocabulary->topic }}</span>
                <span class="badge text-bg-success">{{ $vocabulary->level }}</span>
            </div>
            <span class="badge text-bg-warning">{{ $vocabulary->part_of_speech }}</span>
        </div>

        <h1 class="display-title">{{ $vocabulary->word }}</h1>
        <p class="lead-copy mb-0">{{ $vocabulary->phonetic }}</p>
    </article>

    <div class="row g-4">
        <section class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="section-title">Nghĩa tiếng Việt</h2>
                    <p class="fs-5 mb-0 mt-3">{{ $vocabulary->meaning_vi }}</p>
                </div>
            </div>
        </section>

        <section class="col-lg-7">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="section-title">Giải thích tiếng Anh</h2>
                    <p class="mb-0 mt-3">{{ $vocabulary->definition_en }}</p>
                </div>
            </div>
        </section>

        <section class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="section-title">Ví dụ IELTS</h2>
                    <p class="mt-3">{{ $vocabulary->example_en }}</p>
                    <p class="text-muted mb-0">{{ $vocabulary->example_vi }}</p>
                </div>
            </div>
        </section>

        <section class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="section-title">Từ đồng nghĩa</h2>
                    @if ($vocabulary->synonyms)
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            @foreach ($vocabulary->synonyms as $synonym)
                                <span class="badge text-bg-primary">{{ $synonym }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0 mt-3">Chưa có từ đồng nghĩa cho mục này.</p>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
