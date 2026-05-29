@extends('layouts.app')

@section('title', 'IELTS Prep Hub - IELTS Focus')
@section('meta_description', 'Trung tâm luyện IELTS đủ 4 kỹ năng: study plan theo band, band criteria, task types, common mistakes và mock test checklist.')

@section('content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">IELTS Prep Hub</span>
        <h1 class="display-title">Trung tâm luyện IELTS đủ 4 kỹ năng.</h1>
        <p class="lead-copy mb-0">Một nơi để học theo lộ trình, hiểu tiêu chí band, làm bài timed practice và review lỗi sai sau mỗi lần luyện.</p>
    </header>

    <section class="resource-list mb-4">
        @foreach ($skills as $skill)
            <article class="card study-card">
                <div class="card-body d-flex flex-column">
                    <span class="badge text-bg-success align-self-start">{{ $skill['name'] }}</span>
                    <h2 class="h5 mt-3">{{ $skill['name'] }} practice</h2>
                    <p class="text-muted">{{ $skill['focus'] }}</p>
                    <div class="vstack gap-2 mb-3">
                        @foreach ($skill['tasks'] as $task)
                            <span class="option-card p-2">{{ $task }}</span>
                        @endforeach
                    </div>
                    <a class="btn btn-primary align-self-start mt-auto" href="{{ $skill['route'] }}">Vào luyện {{ $skill['name'] }}</a>
                </div>
            </article>
        @endforeach
    </section>

    <section class="soft-panel mb-4">
        <div class="section-heading-row">
            <div>
                <span class="eyebrow">Tổng hợp chuẩn luyện IELTS</span>
                <h2 class="section-title mb-0">Những module nên có trên một web học IELTS nghiêm túc.</h2>
            </div>
        </div>
        <div class="benchmark-grid mt-3">
            @foreach ($officialBenchmarks as $benchmark)
                <article class="benchmark-item">
                    <span class="benchmark-source">{{ $benchmark['name'] }}</span>
                    <p class="mb-1"><strong>Mục đích:</strong> {{ $benchmark['purpose'] }}</p>
                    <p class="mb-0"><strong>Dùng trên web:</strong> {{ $benchmark['use_in_site'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <div class="row g-4">
        <section class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="eyebrow">Study plan</span>
                    <h2 class="section-title mt-2">Lộ trình theo band</h2>
                    @foreach ($plans as $plan)
                        <article class="review-item mt-3">
                            <span class="badge text-bg-primary">{{ $plan['target'] }}</span>
                            <strong>{{ $plan['plan'] }}</strong>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="eyebrow">Band criteria</span>
                    <h2 class="section-title mt-2">Tiêu chí cần theo dõi</h2>
                    @foreach ($criteria as $skill => $items)
                        <article class="review-item mt-3">
                            <span class="badge text-bg-primary">{{ $skill }}</span>
                            <strong>{{ implode(' · ', $items) }}</strong>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="eyebrow">Common mistakes</span>
                    <h2 class="section-title mt-2">Lỗi thường gặp</h2>
                    @foreach ($mistakes as $skill => $mistake)
                        <article class="review-item mt-3">
                            <span class="badge text-bg-danger">{{ $skill }}</span>
                            <strong>{{ $mistake }}</strong>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <span class="eyebrow">Mock test</span>
                    <h2 class="section-title mt-2">Checklist làm đề như thi thật</h2>
                    <div class="vstack gap-2 mt-3">
                        @foreach ($mockChecklist as $item)
                            <div class="option-card p-3">{{ $item }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
