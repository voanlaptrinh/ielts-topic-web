@extends('admin.layout')

@section('title', ($faq->exists ? 'Sửa FAQ' : 'Thêm FAQ') . ' - IELTS Focus')
@section('admin_eyebrow', 'FAQ trang home')
@section('admin_title', $faq->exists ? 'Sửa câu hỏi thường gặp' : 'Thêm câu hỏi thường gặp')

@section('admin_content')
    <div class="admin-header-row">
        <p class="text-muted mb-0">FAQ đang bật sẽ hiển thị ở cuối trang home và trong dữ liệu SEO FAQPage.</p>
        <a class="btn btn-outline-primary" href="{{ route('admin.faqs.index') }}">Quay lại</a>
    </div>

    <form class="card" method="POST" action="{{ $action }}">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <div class="card-body admin-form-grid">
            <label class="admin-field admin-field-wide">
                <span>Câu hỏi</span>
                <input class="form-control" name="question" value="{{ old('question', $faq->question) }}" required>
                @error('question') <small class="text-danger">{{ $message }}</small> @enderror
            </label>

            <label class="admin-field admin-field-wide">
                <span>Câu trả lời</span>
                <textarea class="form-control" name="answer" rows="7" required>{{ old('answer', $faq->answer) }}</textarea>
                @error('answer') <small class="text-danger">{{ $message }}</small> @enderror
            </label>

            <label class="admin-field">
                <span>Thứ tự hiển thị</span>
                <input class="form-control" name="position" type="number" min="0" max="999" value="{{ old('position', $faq->position ?? 0) }}" required>
                @error('position') <small class="text-danger">{{ $message }}</small> @enderror
            </label>

            <label class="admin-field">
                <span>Hiển thị</span>
                <span class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="is_published" value="1" @checked(old('is_published', $faq->is_published ?? true))>
                    <span class="form-check-label">Bật FAQ này ngoài trang home</span>
                </span>
            </label>

            <div class="admin-field-wide">
                <button class="btn btn-primary" type="submit">Lưu FAQ</button>
            </div>
        </div>
    </form>
@endsection
