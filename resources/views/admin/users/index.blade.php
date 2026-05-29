@extends('admin.layout')

@section('title', 'Quản lý user - IELTS Focus')
@section('admin_eyebrow', 'Người dùng')
@section('admin_title', 'Quản lý user')

@section('admin_content')
    <form class="admin-toolbar mb-3" method="GET" action="{{ route('admin.users.index') }}">
        <input class="form-control" name="q" value="{{ $search }}" placeholder="Tìm theo tên hoặc email" style="max-width: 360px">
        <button class="btn btn-primary" type="submit">Tìm</button>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Quyền</th>
                        <th>Bài đã làm</th>
                        <th>Ngày tạo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                <div class="text-muted small">{{ $user->email }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $user->is_admin ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $user->is_admin ? 'Admin' : 'User' }}
                                </span>
                            </td>
                            <td>{{ $user->test_attempts_count }}</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                @unless ($user->is(auth()->user()))
                                    <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}">
                                        @csrf
                                        @method('PUT')
                                        <button class="btn btn-sm btn-outline-primary" type="submit">
                                            {{ $user->is_admin ? 'Gỡ admin' : 'Cấp admin' }}
                                        </button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
@endsection
