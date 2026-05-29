@extends('admin.layout')

@section('title', 'Quản lý đề Reading/Listening - IELTS Focus')
@section('admin_eyebrow', 'Nội dung luyện thi')
@section('admin_title', 'Đề Reading/Listening')

@section('admin_content')
    <div class="admin-toolbar mb-3">
        <div class="d-flex gap-2 flex-wrap">
            <a class="btn btn-sm {{ ! $skill ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.practice-tests.index') }}">Tất cả</a>
            <a class="btn btn-sm {{ $skill === 'reading' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.practice-tests.index', ['skill' => 'reading']) }}">Reading</a>
            <a class="btn btn-sm {{ $skill === 'listening' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.practice-tests.index', ['skill' => 'listening']) }}">Listening</a>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.practice-tests.create') }}">Tạo đề mới</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Đề</th>
                        <th>Kỹ năng</th>
                        <th>Thời gian</th>
                        <th>Câu hỏi</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tests as $test)
                        <tr>
                            <td>
                                <strong>{{ $test->title }}</strong>
                                <div class="text-muted small">{{ $test->slug }}</div>
                            </td>
                            <td>{{ ucfirst($test->skill) }}</td>
                            <td>{{ $test->duration_minutes }} phút</td>
                            <td>{{ $test->questions_count }}</td>
                            <td>
                                <span class="badge {{ $test->is_published ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $test->is_published ? 'Đang public' : 'Nháp' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.practice-tests.edit', $test) }}">Sửa</a>
                                <form class="d-inline" method="POST" action="{{ route('admin.practice-tests.destroy', $test) }}" onsubmit="return confirm('Xóa đề này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Chưa có đề Reading/Listening.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $tests->links() }}</div>
@endsection
