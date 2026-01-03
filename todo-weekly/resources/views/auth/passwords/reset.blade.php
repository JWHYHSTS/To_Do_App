@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/auth-reset.css') }}">
@endpush

@section('content')
<section class="reset-page" aria-label="Trang đặt lại mật khẩu">
  <div class="reset-shell">
    <div class="reset-card" role="region" aria-labelledby="resetTitle">
      <div class="reset-glow" aria-hidden="true"></div>

      <header class="reset-header">
        <div class="reset-badge" aria-hidden="true">
          <span class="reset-badge__dot"></span>
        </div>

        <div class="reset-head">
          <h1 id="resetTitle" class="reset-title">
            <span class="reset-title__gradient">Đặt lại mật khẩu</span>
          </h1>
          <p class="reset-subtitle">
            Tạo mật khẩu mới để bảo mật tài khoản của bạn.
          </p>
        </div>
      </header>

      <div class="reset-body">
        <form method="POST" action="{{ route('password.update') }}" class="reset-form" novalidate>
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">

          {{-- Email --}}
          <div class="field">
            <label for="email" class="field__label">Email</label>
            <div class="field__control @error('email') has-error @enderror">
              <input id="email"
                     type="email"
                     name="email"
                     class="field__input @error('email') is-invalid @enderror"
                     value="{{ $email ?? old('email') }}"
                     required
                     autocomplete="email"
                     autofocus
                     placeholder="name@example.com">

              @error('email')
                <span class="field__errorInline">{{ $message }}</span>
              @enderror
            </div>
          </div>

          {{-- Password --}}
          <div class="field">
            <label for="password" class="field__label">Mật khẩu mới</label>
            <div class="field__control @error('password') has-error @enderror">
              <input id="password"
                     type="password"
                     name="password"
                     class="field__input @error('password') is-invalid @enderror"
                     required
                     autocomplete="new-password"
                     placeholder="••••••••">

              @error('password')
                <span class="field__errorInline">{{ $message }}</span>
              @enderror
            </div>
          </div>

          {{-- Confirm --}}
          <div class="field">
            <label for="password-confirm" class="field__label">Xác nhận mật khẩu</label>
            <div class="field__control">
              <input id="password-confirm"
                     type="password"
                     name="password_confirmation"
                     class="field__input"
                     required
                     autocomplete="new-password"
                     placeholder="••••••••">
            </div>
          </div>

          <div class="reset-actions">
            <button type="submit" class="rbtn rbtn--primary rbtn--full">
              <span class="rbtn__txt">Đặt lại mật khẩu</span>
              <span class="rbtn__glow" aria-hidden="true"></span>
            </button>

            <a href="{{ route('login') }}" class="rbtn rbtn--ghost rbtn--full">
              Quay lại đăng nhập
            </a>
          </div>
        </form>
      </div>
    </div>

    <p class="reset-meta" aria-hidden="true">
      <span class="reset-meta__brand">Weekly To-Do</span>
      <span class="reset-meta__dot">•</span>
      Secure password reset
    </p>
  </div>
</section>
@endsection
