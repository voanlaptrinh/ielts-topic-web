@extends('admin.layout')

@section('title', 'Quản lý FAQ - IELTS Focus')
@section('admin_eyebrow', 'Trang home')
@section('admin_title', 'Quản lý câu hỏi thường gặp')

@section('admin_content')
    <div class="admin-header-row">
        <p class="text-muted mb-0">Thêm, sửa, xóa và sắp xếp các câu hỏi thường gặp hiển thị ở cuối trang home.</p>
        <a class="btn btn-primary" href="{{ route('admin.faqs.create') }}">Thêm FAQ</a>
    </div>

    <section class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 90px;">Thứ tự</th>
                            <th>Câu hỏi</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($faqs as $faq)
                            <tr>
                                <td>{{ $faq->position }}</td>
                                <td>
                                    <strong>{{ $faq->question }}</strong>
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit($faq->answer, 110) }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $faq->is_published ? 'text-bg-success' : 'text-bg-warning' }}">
                                        {{ $faq->is_published ? 'Đang hiện' : 'Đang ẩn' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-primary btn-sm" href="{{ route('admin.faqs.edit', $faq) }}">Sửa</a>
                                    <form class="d-inline" method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" onsubmit="return confirm('Xóa FAQ này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" type="submit">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">Chưa có FAQ nào. Hãy thêm câu hỏi đầu tiên.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $faqs->links() }}
        </div>
    </section>
@endsection
