@extends('layouts.app')

@section('title', $config['name'] . ' - IELTS Focus')

@section('content')
    <a class="btn btn-link ps-0 mb-3" href="{{ route('tests.index') }}">Quay lại 6 cấp độ</a>

    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">{{ $config['band'] }}</span>
        <h1 class="display-title">{{ $config['name'] }}</h1>
        <p class="lead-copy mb-0">{{ $config['description'] }}</p>
    </header>

    @php
        $corePractices = [
            ['badge' => 'Từ vựng', 'title' => 'Chọn nghĩa đúng', 'description' => 'Ôn từ theo cấp độ, nộp bài để xem điểm và lý do sai.', 'route' => route('tests.vocabulary', $level)],
            ['badge' => 'Ngữ pháp', 'title' => 'Xác định loại từ', 'description' => 'Phân biệt danh từ, động từ, tính từ và trạng từ trong ngữ cảnh.', 'route' => route('tests.grammar', $level)],
            ['badge' => 'Câu', 'title' => 'Thành phần câu', 'description' => 'Nhận diện chủ ngữ, vị ngữ, tân ngữ, bổ ngữ và trạng ngữ.', 'route' => route('tests.sentence-role', $level)],
            ['badge' => 'Từ điển', 'title' => 'Chọn từ theo định nghĩa', 'description' => 'Đọc định nghĩa tiếng Anh và chọn từ phù hợp nhất.', 'route' => route('tests.definition', $level)],
            ['badge' => 'Chính tả', 'title' => 'Chọn từ viết đúng', 'description' => 'Phân biệt từ đúng với các phương án gây nhiễu gần giống nhau.', 'route' => route('tests.spelling', $level)],
            ['badge' => 'Ngữ cảnh', 'title' => 'Điền từ vào câu', 'description' => 'Dựa vào câu ví dụ để chọn từ còn thiếu.', 'route' => route('tests.example-completion', $level)],
            ['badge' => 'IELTS', 'title' => 'Tổng hợp theo dạng đề', 'description' => 'Luyện ý chính, tiêu đề, hoàn thành câu và suy luận theo format IELTS.', 'route' => route('tests.ielts-format', $level)],
        ];
    @endphp

    <section class="resource-list">
        @foreach ($corePractices as $practice)
            <article class="card study-card">
                <div class="card-body d-flex flex-column">
                    <span class="badge text-bg-primary align-self-start">{{ $practice['badge'] }}</span>
                    <h2 class="h5 mt-3">{{ $practice['title'] }}</h2>
                    <p class="text-muted flex-grow-1">{{ $practice['description'] }}</p>
                    <a class="btn btn-primary align-self-start" href="{{ $practice['route'] }}">Làm bài</a>
                </div>
            </article>
        @endforeach

        @foreach ($skillPractices as $skill => $practice)
            <article class="card study-card">
                <div class="card-body d-flex flex-column">
                    <span class="badge text-bg-success align-self-start">{{ $practice['badge'] }}</span>
                    <h2 class="h5 mt-3">{{ $practice['title'] }}</h2>
                    <p class="text-muted flex-grow-1">{{ $practice['description'] }}</p>
                    <a class="btn btn-primary align-self-start" href="{{ route('tests.skill', [$level, $skill]) }}">Làm bài</a>
                </div>
            </article>
        @endforeach
    </section>
@endsection
