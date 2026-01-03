{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title', 'Đăng ký')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/auth-login.css') }}">
@endpush

@php
  // Chỉ dùng lỗi validate của chính form register (nếu có)
@endphp

@section('content')
<section class="auth-page auth-wow auth-register" aria-label="Trang đăng ký">
  <div class="auth-shell">
    <div class="auth-card" role="region" aria-labelledby="authTitle">
      <div class="auth-glow" aria-hidden="true"></div>

      <header class="auth-card__header">
        <div class="auth-header__top">
          <div class="auth-brand auth-brand--register" aria-hidden="true">
            <span class="auth-brand__mark"></span>
            <span class="auth-brand__ring"></span>
          </div>

          <div class="auth-header__text">
            <h1 id="authTitle" class="auth-title">
              <span class="auth-title__gradient auth-title__gradient--register">Tạo tài khoản</span>
            </h1>
            <p class="auth-subtitle">
              Tạo tài khoản để quản lý To-Do theo tuần, theo trạng thái và theo tiến độ.
            </p>
          </div>
        </div>

        <div class="auth-header__chips" aria-hidden="true">
          <span class="chip chip--a">Fast</span>
          <span class="chip chip--b">Clean</span>
          <span class="chip chip--c">Productive</span>
          <span class="chip chip--d">Weekly</span>
        </div>
      </header>

      <div class="auth-card__body">
        <form class="auth-form" method="POST" action="{{ route('register') }}" novalidate>
          @csrf

          <div class="form-grid">
            {{-- Họ và tên --}}
            <div class="field">
              <label for="name" class="field__label">
                <span class="field__labelText">Họ và tên</span>
                <span class="field__labelHint">Tên hiển thị</span>
              </label>

              <div class="field__control @error('name') has-error @enderror">
                <span class="field__icon" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M12 12.2C14.3196 12.2 16.2 10.3196 16.2 8C16.2 5.6804 14.3196 3.8 12 3.8C9.6804 3.8 7.8 5.6804 7.8 8C7.8 10.3196 9.6804 12.2 12 12.2Z" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M4.6 20.2C5.7 16.9 8.5 15.1 12 15.1C15.5 15.1 18.3 16.9 19.4 20.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                  </svg>
                </span>

                <input id="name"
                       type="text"
                       class="field__input @error('name') is-invalid @enderror"
                       name="name"
                       value="{{ old('name') }}"
                       placeholder="Nguyễn Văn A"
                       required
                       autocomplete="name"
                       autofocus>

                <span class="field__shine" aria-hidden="true"></span>

                {{-- ✅ Lỗi hiển thị NGAY TRONG Ô --}}
                @error('name')
                  <span class="field__errorInline" role="alert">{{ $message }}</span>
                @enderror
              </div>
            </div>

            {{-- Email --}}
            <div class="field">
              <label for="email" class="field__label">
                <span class="field__labelText">Email</span>
                <span class="field__labelHint">Dùng để đăng nhập</span>
              </label>

              <div class="field__control @error('email') has-error @enderror">
                <span class="field__icon" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M4 7.5C4 6.11929 5.11929 5 6.5 5H17.5C18.8807 5 20 6.11929 20 7.5V16.5C20 17.8807 18.8807 19 17.5 19H6.5C5.11929 19 4 17.8807 4 16.5V7.5Z" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M6 8L12 12.2L18 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>

                <input id="email"
                       type="email"
                       class="field__input @error('email') is-invalid @enderror"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="name@example.com"
                       required
                       autocomplete="email">

                <span class="field__shine" aria-hidden="true"></span>

                {{-- ✅ Lỗi hiển thị NGAY TRONG Ô --}}
                @error('email')
                  <span class="field__errorInline" role="alert">{{ $message }}</span>
                @enderror
              </div>
            </div>

            {{-- Mật khẩu --}}
            <div class="field">
              <label for="password" class="field__label">
                <span class="field__labelText">Mật khẩu</span>
                <span class="field__labelHint">Tối thiểu 8 ký tự</span>
              </label>

              <div class="field__control @error('password') has-error @enderror">
                <span class="field__icon" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M7.5 11V8.8C7.5 6.14903 9.64903 4 12.3 4C14.951 4 17.1 6.14903 17.1 8.8V11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M6.5 11H18.1C19.2046 11 20.1 11.8954 20.1 13V18.2C20.1 19.3046 19.2046 20.2 18.1 20.2H6.5C5.39543 20.2 4.5 19.3046 4.5 18.2V13C4.5 11.8954 5.39543 11 6.5 11Z" stroke="currentColor" stroke-width="1.8"/>
                  </svg>
                </span>

                <input id="password"
                       type="password"
                       class="field__input @error('password') is-invalid @enderror"
                       name="password"
                       placeholder="••••••••"
                       required
                       autocomplete="new-password">

                <span class="field__shine" aria-hidden="true"></span>

                {{-- ✅ Lỗi hiển thị NGAY TRONG Ô --}}
                @error('password')
                  <span class="field__errorInline" role="alert">{{ $message }}</span>
                @enderror
              </div>
            </div>

            {{-- Xác nhận mật khẩu --}}
            <div class="field">
              <label for="password-confirm" class="field__label">
                <span class="field__labelText">Xác nhận mật khẩu</span>
                <span class="field__labelHint">Nhập lại mật khẩu</span>
              </label>

              <div class="field__control">
                <span class="field__icon" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M8 12.5L10.5 15L16.5 9" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6.5 5.5H17.5C18.8807 5.5 20 6.61929 20 8V16C20 17.3807 18.8807 18.5 17.5 18.5H6.5C5.11929 18.5 4 17.3807 4 16V8C4 6.61929 5.11929 5.5 6.5 5.5Z" stroke="currentColor" stroke-width="1.8"/>
                  </svg>
                </span>

                <input id="password-confirm"
                       type="password"
                       class="field__input"
                       name="password_confirmation"
                       placeholder="••••••••"
                       required
                       autocomplete="new-password">

                <span class="field__shine" aria-hidden="true"></span>
              </div>
            </div>
          </div>

          <div class="auth-actions">
            <button type="submit" class="btn btn--primary btn--full btn--register">
              <span class="btn__txt">Đăng ký</span>
              <span class="btn__glow" aria-hidden="true"></span>
            </button>

            <a class="btn btn--ghost btn--full" href="{{ route('login') }}">
              Quay lại đăng nhập
            </a>
          </div>
        </form>
      </div>
    </div>

    <p class="auth-meta" aria-hidden="true">
      <span class="auth-meta__gradient auth-meta__gradient--register">Create account</span>
      <span class="auth-meta__dot">•</span>
      Weekly To-Do
    </p>
  </div>
</section>
@endsection
