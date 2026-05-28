@extends('layouts.app')

@section('title', 'Tạo tài khoản - IELTS Focus')
@section('meta_description', 'Tạo tài khoản IELTS Focus miễn phí để lưu tiến độ học, lịch sử tra từ, điểm luyện tập và danh sách lỗi cần ôn.')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <header class="hero-panel p-4">
                <span class="eyebrow">Bắt đầu miễn phí</span>
                <h1 class="h2 fw-bold mt-2">Tạo tài khoản</h1>
                <p class="lead-copy mb-0">Theo dõi điểm, lỗi sai và các từ đã tra để ôn lại đúng phần mình còn yếu.</p>
            </header>

            <form class="card" method="POST" action="{{ route('register.store') }}">
                @csrf
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="name">Họ tên</label>
                        <input id="name" class="form-control" name="name" value="{{ old('name') }}" autocomplete="name" required>
                        @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="email">Email</label>
                        <input id="email" class="form-control" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                        @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="password">Mật khẩu</label>
                        <input id="password" class="form-control" name="password" type="password" autocomplete="new-password" required>
                        @error('password') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="password_confirmation">Nhập lại mật khẩu</label>
                        <input id="password_confirmation" class="form-control" name="password_confirmation" type="password" autocomplete="new-password" required>
                    </div>

                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <button class="btn btn-primary" type="submit">Tạo tài khoản</button>
                        <a class="btn btn-link" href="{{ route('login') }}">Đã có tài khoản</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
