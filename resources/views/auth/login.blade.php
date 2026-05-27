@extends('layouts.app')

@section('title', 'Đăng nhập - IELTS Focus')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <header class="hero-panel p-4">
                <span class="eyebrow">Tài khoản học tập</span>
                <h1 class="h2 fw-bold mt-2">Đăng nhập</h1>
                <p class="lead-copy mb-0">Lưu lịch sử tra từ, điểm bài luyện và xem lại lỗi sai bất cứ lúc nào.</p>
            </header>

            <form class="card" method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="email">Email</label>
                        <input id="email" class="form-control" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                        @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="password">Mật khẩu</label>
                        <input id="password" class="form-control" name="password" type="password" autocomplete="current-password" required>
                        @error('password') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <button class="btn btn-primary" type="submit">Đăng nhập</button>
                        <a class="btn btn-link" href="{{ route('register') }}">Tạo tài khoản mới</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
