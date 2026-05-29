@extends('dashboard.layout')

@section('title', 'Quản lý tài khoản - IELTS Focus')
@section('meta_description', 'Quản lý thông tin tài khoản, mục tiêu band IELTS và thời gian học mỗi ngày.')

@section('dashboard_content')
    <header class="hero-panel p-4 p-lg-5">
        <span class="eyebrow">Tài khoản</span>
        <h1 class="display-title">Quản lý tài khoản học viên</h1>
        <p class="lead-copy mb-0">Cập nhật thông tin cá nhân, mục tiêu band và thời lượng học mỗi ngày.</p>
    </header>

    <form class="card" method="POST" action="{{ route('account.update') }}">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tên tài khoản</label>
                    <input class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Band mục tiêu</label>
                    <select class="form-select" name="target_band">
                        @foreach (['5.0', '5.5', '6.0', '6.5', '7.0', '7.5', '8.0+'] as $band)
                            <option value="{{ $band }}" @selected(old('target_band', $user->target_band) === $band)>{{ $band }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phút học mỗi ngày</label>
                    <input class="form-control" type="number" min="10" max="180" name="study_minutes_per_day" value="{{ old('study_minutes_per_day', $user->study_minutes_per_day) }}" required>
                </div>
            </div>

            <button class="btn btn-primary mt-3" type="submit">Lưu tài khoản</button>
        </div>
    </form>
@endsection
