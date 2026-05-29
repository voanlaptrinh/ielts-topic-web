@extends('admin.layout')

@php
    $oldQuestions = collect(old('questions'));
    $rows = $oldQuestions->isNotEmpty()
        ? $oldQuestions
        : $questions->map(fn ($question) => [
            'prompt' => $question->prompt,
            'question_type' => $question->question_type,
            'options_text' => implode("\n", $question->options ?? []),
            'correct_answer' => $question->correct_answer,
            'explanation' => $question->explanation,
        ]);
    $rows = $rows->pad(max(8, $rows->count() + 2), []);
@endphp

@section('title', ($practiceTest->exists ? 'Sửa đề' : 'Tạo đề') . ' - IELTS Focus')
@section('admin_eyebrow', 'Reading/Listening')
@section('admin_title', $practiceTest->exists ? 'Sửa đề luyện tập' : 'Tạo đề luyện tập')

@section('admin_content')
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="admin-form">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-8">
                        <label class="form-label">Tiêu đề</label>
                        <input class="form-control" name="title" value="{{ old('title', $practiceTest->title) }}" required>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">Slug</label>
                        <input class="form-control" name="slug" value="{{ old('slug', $practiceTest->slug) }}" placeholder="Tự tạo nếu để trống">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kỹ năng</label>
                        <select class="form-select" name="skill" required>
                            <option value="reading" @selected(old('skill', $practiceTest->skill) === 'reading')>Reading</option>
                            <option value="listening" @selected(old('skill', $practiceTest->skill) === 'listening')>Listening</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Level</label>
                        <input class="form-control" name="level" value="{{ old('level', $practiceTest->level) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Thời gian làm bài/phút</label>
                        <input class="form-control" type="number" min="1" max="180" name="duration_minutes" value="{{ old('duration_minutes', $practiceTest->duration_minutes) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Mô tả ngắn</label>
                        <textarea class="form-control" name="description" rows="2">{{ old('description', $practiceTest->description) }}</textarea>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">Reading passage</label>
                        <textarea class="form-control" name="passage" rows="8">{{ old('passage', $practiceTest->passage) }}</textarea>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">Listening transcript</label>
                        <textarea class="form-control" name="transcript" rows="8">{{ old('transcript', $practiceTest->transcript) }}</textarea>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">Audio Listening</label>
                        <input class="form-control" type="file" name="audio_file" accept=".mp3,.wav,.ogg,.m4a,.mp4,audio/*">
                        @if ($practiceTest->audio_path)
                            <div class="text-muted small mt-1">Đang có file: {{ $practiceTest->audio_path }}</div>
                        @endif
                    </div>
                    <div class="col-lg-6 d-flex align-items-end">
                        <label class="form-check">
                            <input type="hidden" name="is_published" value="0">
                            <input class="form-check-input" type="checkbox" name="is_published" value="1" @checked(old('is_published', $practiceTest->is_published))>
                            <span class="form-check-label">Public cho user làm bài</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="section-title mb-3">Câu hỏi, đáp án, giải thích</h2>
        <div class="vstack gap-3">
            @foreach ($rows as $index => $question)
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-8">
                                <label class="form-label">Câu {{ $index + 1 }}</label>
                                <textarea class="form-control" name="questions[{{ $index }}][prompt]" rows="2">{{ $question['prompt'] ?? '' }}</textarea>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label">Dạng câu</label>
                                <select class="form-select" name="questions[{{ $index }}][question_type]">
                                    <option value="multiple_choice" @selected(($question['question_type'] ?? 'multiple_choice') === 'multiple_choice')>Multiple choice</option>
                                    <option value="short_answer" @selected(($question['question_type'] ?? '') === 'short_answer')>Short answer</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Options, mỗi dòng 1 lựa chọn</label>
                                <textarea class="form-control" name="questions[{{ $index }}][options_text]" rows="4">{{ $question['options_text'] ?? '' }}</textarea>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Đáp án đúng</label>
                                <input class="form-control" name="questions[{{ $index }}][correct_answer]" value="{{ $question['correct_answer'] ?? '' }}">
                                <label class="form-label mt-3">Giải thích</label>
                                <textarea class="form-control" name="questions[{{ $index }}][explanation]" rows="2">{{ $question['explanation'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="admin-form-actions mt-4">
            <button class="btn btn-primary" type="submit">Lưu đề</button>
            <a class="btn btn-outline-primary" href="{{ route('admin.practice-tests.index') }}">Quay lại</a>
        </div>
    </form>
@endsection
