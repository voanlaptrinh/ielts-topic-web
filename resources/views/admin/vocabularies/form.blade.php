@extends('admin.layout')

@section('title', ($vocabulary->exists ? 'Sửa từ vựng' : 'Thêm từ vựng') . ' - IELTS Focus')
@section('admin_eyebrow', 'Từ vựng')
@section('admin_title', $vocabulary->exists ? 'Sửa từ vựng' : 'Thêm từ vựng')

@section('admin_content')
    <div class="admin-header-row">
        <p class="text-muted mb-0">Dữ liệu này dùng cho trang tra từ, flashcard, quiz và sitemap.</p>
        <a class="btn btn-outline-primary" href="{{ route('admin.vocabularies.index') }}">Quay lại</a>
    </div>

    <form class="card" method="POST" action="{{ $action }}">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif
        <div class="card-body admin-form-grid">
            <label class="admin-field">
                <span>Từ</span>
                <input class="form-control" name="word" value="{{ old('word', $vocabulary->word) }}" required>
            </label>
            <label class="admin-field">
                <span>Phiên âm</span>
                <input class="form-control" name="phonetic" value="{{ old('phonetic', $vocabulary->phonetic) }}">
            </label>
            <label class="admin-field">
                <span>Loại từ</span>
                <input class="form-control" name="part_of_speech" value="{{ old('part_of_speech', $vocabulary->part_of_speech) }}" required>
            </label>
            <label class="admin-field">
                <span>Level</span>
                <input class="form-control" name="level" value="{{ old('level', $vocabulary->level ?: 'B1') }}" required>
            </label>
            <label class="admin-field">
                <span>Topic</span>
                <input class="form-control" name="topic" value="{{ old('topic', $vocabulary->topic) }}">
            </label>
            <label class="admin-field admin-field-wide">
                <span>Nghĩa tiếng Việt</span>
                <textarea class="form-control" name="meaning_vi" rows="3" required>{{ old('meaning_vi', $vocabulary->meaning_vi) }}</textarea>
            </label>
            <label class="admin-field admin-field-wide">
                <span>Định nghĩa tiếng Anh</span>
                <textarea class="form-control" name="definition_en" rows="4" required>{{ old('definition_en', $vocabulary->definition_en) }}</textarea>
            </label>
            <label class="admin-field admin-field-wide">
                <span>Ví dụ tiếng Anh</span>
                <textarea class="form-control" name="example_en" rows="3" required>{{ old('example_en', $vocabulary->example_en) }}</textarea>
            </label>
            <label class="admin-field admin-field-wide">
                <span>Ví dụ tiếng Việt</span>
                <textarea class="form-control" name="example_vi" rows="3" required>{{ old('example_vi', $vocabulary->example_vi) }}</textarea>
            </label>
            <label class="admin-field admin-field-wide">
                <span>Từ đồng nghĩa, ngăn cách bằng dấu phẩy hoặc mỗi dòng một từ</span>
                <textarea class="form-control" name="synonyms_text" rows="4">{{ old('synonyms_text', implode(', ', $vocabulary->synonyms ?? [])) }}</textarea>
            </label>
            <div class="admin-field-wide">
                <button class="btn btn-primary" type="submit">Lưu từ vựng</button>
            </div>
        </div>
    </form>
@endsection
