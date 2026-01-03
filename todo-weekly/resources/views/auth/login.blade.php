{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Đăng nhập')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/auth-login.css') }}">
@endpush

@section('content')
<section class="auth-page auth-wow" aria-label="Trang đăng nhập">
  <div class="auth-shell">
    <div class="auth-card" role="region" aria-labelledby="authTitle">
      <div class="auth-glow" aria-hidden="true"></div>

      <header class="auth-card__header">
        <div class="auth-header__top">
          <div class="auth-brand" aria-hidden="true">
            <span class="auth-brand__mark"></span>
            <span class="auth-brand__ring"></span>
          </div>

          <div class="auth-header__text">
            <h1 id="authTitle" class="auth-title">
              <span class="auth-title__gradient">Đăng nhập</span>
            </h1>
            <p class="auth-subtitle">
              Chào mừng bạn quay lại. Vui lòng nhập thông tin để tiếp tục.
            </p>
          </div>
        </div>

        <div class="auth-header__chips" aria-hidden="true">
          <span class="chip chip--a">Weekly</span>
          <span class="chip chip--b">To-Do</span>
          <span class="chip chip--c">Focus</span>
        </div>
      </header>

      <div class="auth-card__body">
        {{-- PUSH lỗi sang toast (không render lỗi trong form) --}}
        @php
          $toastMsg = session('toast');

          if (!$toastMsg && $errors->has('email')) {
            $toastMsg = 'Email hoặc mật khẩu không đúng.';
          }

          if ($toastMsg) {
            session()->flash('toast', is_array($toastMsg) ? $toastMsg[0] : $toastMsg);
            session()->flash('toast_type', 'danger');
            session()->flash('toast_title', 'Đăng nhập thất bại');
          }
        @endphp

        <form class="auth-form" method="POST" action="{{ route('login') }}" novalidate>
          @csrf

          <div class="form-grid">
            <div class="field">
              <label for="email" class="field__label">
                <span class="field__labelText">Email</span>
                <span class="field__labelHint">Work / personal</span>
              </label>

              <div class="field__control">
                <span class="field__icon" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M4 7.5C4 6.11929 5.11929 5 6.5 5H17.5C18.8807 5 20 6.11929 20 7.5V16.5C20 17.8807 18.8807 19 17.5 19H6.5C5.11929 19 4 17.8807 4 16.5V7.5Z" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M6 8L12 12.2L18 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </span>

                <input id="email"
                       type="email"
                       class="field__input"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="name@example.com"
                       required
                       autocomplete="email"
                       autofocus>
                <span class="field__shine" aria-hidden="true"></span>
              </div>
            </div>

            <div class="field">
              <label for="password" class="field__label">
                <span class="field__labelText">Mật khẩu</span>
                <span class="field__labelHint">Ít nhất 8 ký tự</span>
              </label>

              <div class="field__control">
                <span class="field__icon" aria-hidden="true">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M7.5 11V8.8C7.5 6.14903 9.64903 4 12.3 4C14.951 4 17.1 6.14903 17.1 8.8V11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M6.5 11H18.1C19.2046 11 20.1 11.8954 20.1 13V18.2C20.1 19.3046 19.2046 20.2 18.1 20.2H6.5C5.39543 20.2 4.5 19.3046 4.5 18.2V13C4.5 11.8954 5.39543 11 6.5 11Z" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M12.3 15V17.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                  </svg>
                </span>

                <input id="password"
                       type="password"
                       class="field__input"
                       name="password"
                       placeholder="••••••••"
                       required
                       autocomplete="current-password">
                <span class="field__shine" aria-hidden="true"></span>
              </div>
            </div>

            <div class="field field--split">
              <label class="check" for="remember">
                <input class="check__input"
                       type="checkbox"
                       name="remember"
                       id="remember"
                       {{ old('remember') ? 'checked' : '' }}>
                <span class="check__label">Ghi nhớ đăng nhập</span>
              </label>

              @if (Route::has('password.request'))
                <a class="link link--quiet" href="{{ route('password.request') }}">Quên mật khẩu?</a>
              @endif
            </div>
          </div>

          <div class="auth-actions">
            <button type="submit" class="btn btn--primary btn--full">
              <span class="btn__txt">Đăng nhập</span>
              <span class="btn__glow" aria-hidden="true"></span>
            </button>

            <div class="auth-foot">
              <span class="auth-foot__text">Chưa có tài khoản?</span>
              @if (Route::has('register'))
                <a class="link link--strong" href="{{ route('register') }}">Đăng ký</a>
              @endif
            </div>
          </div>
        </form>
      </div>
    </div>

    <p class="auth-meta" aria-hidden="true">
      <span class="auth-meta__gradient">Weekly To-Do</span>
      <span class="auth-meta__dot">•</span>
      Secure sign-in
    </p>
  </div>
</section>
@endsection
