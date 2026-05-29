@extends('admin.layout')

@section('title', ($topic->exists ? 'Sửa topic' : 'Thêm topic') . ' - IELTS Focus')
@section('admin_eyebrow', 'Topic IELTS')
@section('admin_title', $topic->exists ? 'Sửa topic' : 'Thêm topic')

@section('admin_content')
    <div class="admin-header-row">
        <p class="text-muted mb-0">Mỗi dòng câu hỏi/tip sẽ được lưu thành một mục riêng trên trang topic.</p>
        <a class="btn btn-outline-primary" href="{{ route('admin.topics.index') }}">Quay lại</a>
    </div>

    <form class="card" method="POST" action="{{ $action }}">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif
        <div class="card-body admin-form-grid">
            <label class="admin-field">
                <span>Tiêu đề</span>
                <input class="form-control" name="title" value="{{ old('title', $topic->title) }}" required>
            </label>
            <label class="admin-field">
                <span>Slug</span>
                <input class="form-control" name="slug" value="{{ old('slug', $topic->slug) }}" placeholder="Tự tạo nếu để trống">
            </label>
            <label class="admin-field">
                <span>Part</span>
                <input class="form-control" name="part" value="{{ old('part', $topic->part) }}" required>
            </label>
            <label class="admin-field">
                <span>Độ khó</span>
                <input class="form-control" name="difficulty" value="{{ old('difficulty', $topic->difficulty) }}" required>
            </label>
            <label class="admin-field admin-field-wide">
                <span>Mô tả</span>
                <textarea class="form-control" name="description" rows="3" required>{{ old('description', $topic->description) }}</textarea>
            </label>
            <label class="admin-field admin-field-wide">
                <span>Câu hỏi luyện tập, mỗi dòng một câu</span>
                <textarea class="form-control" name="questions_text" rows="6" required>{{ old('questions_text', implode("\n", $topic->questions ?? [])) }}</textarea>
            </label>
            <label class="admin-field admin-field-wide">
                <span>Bài mẫu</span>
                <textarea class="form-control" name="sample_answer" rows="8" required>{{ old('sample_answer', $topic->sample_answer) }}</textarea>
            </label>
            <label class="admin-field admin-field-wide">
                <span>Tips cải thiện band điểm, mỗi dòng một tip</span>
                <textarea class="form-control" name="tips_text" rows="5">{{ old('tips_text', implode("\n", $topic->tips ?? [])) }}</textarea>
            </label>
            <div class="admin-field-wide">
                <button class="btn btn-primary" type="submit">Lưu topic</button>
            </div>
        </div>
    </form>
@endsection
