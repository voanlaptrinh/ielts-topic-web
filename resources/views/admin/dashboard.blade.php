@extends('admin.layout')

@section('title', 'Admin - IELTS Focus')
@section('meta_description', 'Khu vực quản trị IELTS Focus.')
@section('admin_eyebrow', 'Tổng quan')
@section('admin_title', 'Tổng quan quản trị')

@section('admin_content')
    <section class="row g-3 mb-4">
        <div class="col-6 col-lg-4">
            <div class="metric-card">
                <span class="metric-value">{{ number_format($stats['topics']) }}</span>
                <span class="metric-label">Topic</span>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="metric-card">
                <span class="metric-value">{{ number_format($stats['practiceTests']) }}</span>
                <span class="metric-label">Đề RL</span>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="metric-card">
                <span class="metric-value">{{ number_format($stats['vocabularies']) }}</span>
                <span class="metric-label">Từ vựng</span>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="metric-card">
                <span class="metric-value">{{ number_format($stats['users']) }}</span>
                <span class="metric-label">User</span>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="metric-card">
                <span class="metric-value">{{ number_format($stats['faqs']) }}</span>
                <span class="metric-label">FAQ</span>
            </div>
        </div>
    </section>

    <section class="admin-action-grid mb-4">
        <a class="admin-action-card" href="{{ route('admin.topics.index') }}">
            <strong>Quản lý topic IELTS</strong>
            <span>Sửa tiêu đề, câu hỏi, bài mẫu và tips.</span>
        </a>
        <a class="admin-action-card" href="{{ route('admin.vocabularies.index') }}">
            <strong>Quản lý từ vựng</strong>
            <span>Sửa nghĩa, ví dụ, chủ đề, level và synonyms.</span>
        </a>
        <a class="admin-action-card" href="{{ route('admin.faqs.index') }}">
            <strong>Quản lý FAQ trang home</strong>
            <span>Thêm, sửa, xóa và sắp xếp câu hỏi thường gặp.</span>
        </a>
    </section>

    <div class="row g-4">
        <section class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="section-title">Topic mới cập nhật</h2>
                    <div class="vstack gap-2 mt-3">
                        @foreach ($recentTopics as $topic)
                            <a class="admin-list-item" href="{{ route('admin.topics.edit', $topic) }}">
                                <strong>{{ $topic->title }}</strong>
                                <span>{{ $topic->part }} · {{ $topic->difficulty }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
        <section class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="section-title">Từ mới cập nhật</h2>
                    <div class="vstack gap-2 mt-3">
                        @foreach ($recentWords as $word)
                            <a class="admin-list-item" href="{{ route('admin.vocabularies.edit', $word) }}">
                                <strong>{{ $word->word }}</strong>
                                <span>{{ $word->topic }} · {{ $word->level }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
