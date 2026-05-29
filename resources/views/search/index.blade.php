@extends('layouts.app')

@section('title', 'Tìm kiếm IELTS - IELTS Focus')
@section('meta_description', 'Tìm kiếm topic, từ vựng, đề luyện Reading, Listening, Writing và Speaking trên IELTS Focus.')

@section('content')
    <header class="page-header">
        <h1>Tìm kiếm toàn site</h1>
        <p class="text-muted">Tìm topic, từ vựng và đề luyện 4 kỹ năng.</p>
    </header>

    <form class="soft-panel mb-4" method="GET" action="{{ route('search.index') }}">
        <div class="input-group">
            <input class="form-control" name="q" value="{{ $query }}" placeholder="Nhập keyword IELTS, topic, từ vựng...">
            <button class="btn btn-primary" type="submit">Tìm</button>
        </div>
    </form>

    @if ($query !== '')
        <div class="row g-4">
            <section class="col-lg-4">
                <h2 class="section-title">Topic</h2>
                @forelse ($topics as $topic)
                    <a class="admin-list-item mt-2" href="{{ route('topics.show', $topic->slug) }}">
                        <strong>{{ $topic->title }}</strong>
                        <span>{{ $topic->part }} · {{ $topic->difficulty }}</span>
                    </a>
                @empty
                    <div class="empty-state mt-2">Không có topic phù hợp.</div>
                @endforelse
            </section>
            <section class="col-lg-4">
                <h2 class="section-title">Từ vựng</h2>
                @forelse ($vocabularies as $word)
                    <a class="admin-list-item mt-2" href="{{ route('vocabularies.show', $word->word) }}">
                        <strong>{{ $word->word }}</strong>
                        <span>{{ $word->meaning_vi }}</span>
                    </a>
                @empty
                    <div class="empty-state mt-2">Không có từ vựng phù hợp.</div>
                @endforelse
            </section>
            <section class="col-lg-4">
                <h2 class="section-title">Đề luyện</h2>
                @forelse ($practiceTests as $test)
                    <a class="admin-list-item mt-2" href="{{ route('tests.practice.show', [$test->skill, $test]) }}">
                        <strong>{{ $test->title }}</strong>
                        <span>{{ ucfirst($test->skill) }} · {{ $test->duration_minutes }} phút</span>
                    </a>
                @empty
                    <div class="empty-state mt-2">Không có đề phù hợp.</div>
                @endforelse
            </section>
        </div>
    @endif
@endsection
