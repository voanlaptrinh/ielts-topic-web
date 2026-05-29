@extends('admin.layout')

@section('title', 'Quản lý topic - IELTS Focus')
@section('admin_eyebrow', 'Nội dung')
@section('admin_title', 'Quản lý topic')

@section('admin_content')
    <div class="admin-header-row">
        <p class="text-muted mb-0">Chỉnh sửa topic Speaking/Writing, câu hỏi, bài mẫu và tips hiển thị ngoài website.</p>
        <a class="btn btn-primary" href="{{ route('admin.topics.create') }}">Thêm topic</a>
    </div>

    <section class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Tiêu đề</th>
                            <th>Part</th>
                            <th>Độ khó</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topics as $topic)
                            <tr>
                                <td>
                                    <strong>{{ $topic->title }}</strong>
                                    <div class="text-muted small">{{ $topic->slug }}</div>
                                </td>
                                <td>{{ $topic->part }}</td>
                                <td>{{ $topic->difficulty }}</td>
                                <td class="text-end">
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('topics.show', $topic->slug) }}">Xem</a>
                                    <a class="btn btn-primary btn-sm" href="{{ route('admin.topics.edit', $topic) }}">Sửa</a>
                                    <form class="d-inline" method="POST" action="{{ route('admin.topics.destroy', $topic) }}" onsubmit="return confirm('Xóa topic này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $topics->links() }}
        </div>
    </section>
@endsection
