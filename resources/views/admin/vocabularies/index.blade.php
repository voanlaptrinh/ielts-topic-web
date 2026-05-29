@extends('admin.layout')

@section('title', 'Quản lý từ vựng - IELTS Focus')
@section('admin_eyebrow', 'Nội dung')
@section('admin_title', 'Quản lý từ vựng')

@section('admin_content')
    <div class="admin-header-row">
        <p class="text-muted mb-0">Tìm, thêm và sửa từ vựng đang xuất hiện ở tra cứu, flashcard và quiz.</p>
        <a class="btn btn-primary" href="{{ route('admin.vocabularies.create') }}">Thêm từ</a>
    </div>

    <form class="search-panel mb-3" method="GET" action="{{ route('admin.vocabularies.index') }}">
        <div class="row g-2">
            <div class="col-lg-10">
                <input class="form-control" name="q" value="{{ $search }}" placeholder="Tìm theo từ, nghĩa hoặc topic">
            </div>
            <div class="col-lg-2 d-grid">
                <button class="btn btn-primary" type="submit">Tìm</button>
            </div>
        </div>
    </form>

    <section class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Từ</th>
                            <th>Nghĩa</th>
                            <th>Topic</th>
                            <th>Level</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($words as $word)
                            <tr>
                                <td>
                                    <strong>{{ $word->word }}</strong>
                                    <div class="text-muted small">{{ $word->part_of_speech }}</div>
                                </td>
                                <td>{{ Str::limit($word->meaning_vi, 80) }}</td>
                                <td>{{ $word->topic }}</td>
                                <td>{{ $word->level }}</td>
                                <td class="text-end">
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('vocabularies.show', $word->word) }}">Xem</a>
                                    <a class="btn btn-primary btn-sm" href="{{ route('admin.vocabularies.edit', $word) }}">Sửa</a>
                                    <form class="d-inline" method="POST" action="{{ route('admin.vocabularies.destroy', $word) }}" onsubmit="return confirm('Xóa từ này?')">
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
            {{ $words->links() }}
        </div>
    </section>
@endsection
