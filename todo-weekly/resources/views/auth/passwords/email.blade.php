{{-- resources/views/auth/passwords/email.blade.php --}}
@extends('layouts.app')

@section('title', 'Quên mật khẩu')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/auth-email.css') }}">
@endpush

@section('content')
<section class="email-page" aria-label="Trang quên mật khẩu">
  <div class="email-shell">
    <div class="email-card" role="region" aria-labelledby="emailTitle">
      <div class="email-glow" aria-hidden="true"></div>

      <header class="email-header">
        <div class="email-badge" aria-hidden="true">
          <span class="email-badge__dot"></span>
        </div>

        <div class="email-head">
          <h1 id="emailTitle" class="email-title">
            <span class="email-title__gradient">Quên mật khẩu</span>
          </h1>
          <p class="email-subtitle">
            Nhập email của bạn. Hệ thống sẽ gửi liên kết để đặt lại mật khẩu.
          </p>
        </div>
      </header>

      <div class="email-body">
        @if (session('status'))
          <div class="email-alert email-alert--success" role="alert">
            <span class="email-alert__icon" aria-hidden="true">✓</span>
            <div class="email-alert__content">
              <div class="email-alert__title">Đã gửi liên kết</div>
              <div class="email-alert__desc">{{ session('status') }}</div>
            </div>
          </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="email-form" novalidate>
          @csrf

          <div class="field">
            <label for="email" class="field__label">Email</label>

            <div class="field__control @error('email') has-error @enderror">
              <input id="email"
                     type="email"
                     name="email"
                     class="field__input @error('email') is-invalid @enderror"
                     value="{{ old('email') }}"
                     required
                     autocomplete="email"
                     autofocus
                     placeholder="name@example.com">

              {{-- ✅ Lỗi hiển thị trong ô --}}
              @error('email')
                <span class="field__errorInline" role="alert">{{ $message }}</span>
              @enderror
            </div>
          </div>

          <div class="email-actions">
            <button type="submit" class="ebtn ebtn--primary ebtn--full">
              <span class="ebtn__txt">Gửi link đặt lại mật khẩu</span>
              <span class="ebtn__glow" aria-hidden="true"></span>
            </button>

            <a class="ebtn ebtn--ghost ebtn--full" href="{{ route('login') }}">
              Quay lại đăng nhập
            </a>
          </div>
        </form>
      </div>
    </div>

    <p class="email-meta" aria-hidden="true">
      <span class="email-meta__brand">Weekly To-Do</span>
      <span class="email-meta__dot">•</span>
      Secure recovery
    </p>
  </div>
</section>
@endsection
