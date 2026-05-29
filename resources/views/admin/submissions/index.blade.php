@extends('admin.layout')

@section('title', 'Chấm bài Writing/Speaking - IELTS Focus')
@section('admin_eyebrow', 'Feedback')
@section('admin_title', 'Chấm bài học viên')

@section('admin_content')
    <div class="admin-toolbar mb-3">
        <div class="d-flex gap-2 flex-wrap">
            <a class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.submissions.index', ['status' => 'pending']) }}">Chờ chấm</a>
            <a class="btn btn-sm {{ $status === 'reviewed' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.submissions.index', ['status' => 'reviewed']) }}">Đã chấm</a>
            <a class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.submissions.index', ['status' => 'all']) }}">Tất cả</a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Học viên</th>
                        <th>Bài</th>
                        <th>Điểm</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $submission)
                        <tr>
                            <td>
                                <strong>{{ $submission->user->name }}</strong>
                                <div class="text-muted small">{{ $submission->user->email }}</div>
                            </td>
                            <td>
                                <strong>{{ $submission->test_type }}</strong>
                                <div class="text-muted small">{{ $submission->level }} · {{ $submission->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td>{{ $submission->band_score ? 'Band ' . $submission->band_score : 'Chưa có' }}</td>
                            <td>
                                <span class="badge {{ $submission->reviewed_at ? 'text-bg-success' : 'text-bg-warning' }}">
                                    {{ $submission->reviewed_at ? 'Đã chấm' : 'Chờ chấm' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-primary" href="{{ route('admin.submissions.edit', $submission) }}">Mở bài</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Chưa có bài Writing/Speaking cần chấm.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $submissions->links() }}</div>
@endsection
