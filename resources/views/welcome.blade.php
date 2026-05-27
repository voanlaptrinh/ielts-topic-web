@extends('layouts.app')

@section('content')
    <header class="page-header">
        <h1>Ôn IELTS miễn phí</h1>
        <p class="text-muted">Chọn một mục trong thanh điều hướng để bắt đầu học chủ đề, từ vựng, từ điển hoặc làm bài luyện tập.</p>
    </header>

    <div class="row row-cols-1 row-cols-md-3 g-3">
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5">Chủ đề IELTS</h2>
                    <p class="text-muted">Luyện Speaking và Writing qua câu hỏi, bài mẫu và mẹo cải thiện band điểm.</p>
                    <a class="btn btn-primary" href="{{ route('topics.index') }}">Xem chủ đề</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5">Từ vựng</h2>
                    <p class="text-muted">Tra cứu từ học thuật, xem ví dụ và ôn nhanh bằng flashcard.</p>
                    <a class="btn btn-primary" href="{{ route('vocabularies.index') }}">Học từ vựng</a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h5">Bài test</h2>
                    <p class="text-muted">Làm bài theo cấp độ để kiểm tra từ vựng, loại từ và thành phần câu.</p>
                    <a class="btn btn-primary" href="{{ route('tests.index') }}">Làm bài</a>
                </div>
            </div>
        </div>
    </div>
@endsection
