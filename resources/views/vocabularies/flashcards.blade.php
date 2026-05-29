@extends('layouts.app')

@section('title', 'Flashcard từ vựng - IELTS Focus')
@section('meta_description', 'Ôn từ vựng IELTS bằng flashcard với nghĩa tiếng Việt, giải thích tiếng Anh và ví dụ theo ngữ cảnh.')

@section('content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">Flashcards</span>
        <h1 class="display-title">Ôn từ bằng flashcard</h1>
        <p class="lead-copy mb-0">Bấm vào từng thẻ hoặc nút “Lật thẻ” để xem nghĩa, giải thích và ví dụ.</p>
    </header>

    <section class="resource-list" data-lazy-list>
        @include('vocabularies._flashcards')
    </section>

    @if ($words->hasMorePages())
        <div class="load-more-wrap">
            <button class="btn btn-outline-primary" type="button" data-lazy-load-more data-next-url="{{ $words->nextPageUrl() }}">
                Tải thêm flashcard
            </button>
        </div>
    @endif
@endsection
