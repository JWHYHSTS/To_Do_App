{{-- resources/views/auth/verify.blade.php --}}
@extends('layouts.app')

@section('title', 'Xác minh email')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/auth-verify.css') }}">
@endpush

@section('content')
{{-- CHỈNH SỬA: layout auth riêng (không đổi logic form/route/session) --}}
<section class="verify-page" aria-label="Trang xác minh email">
  <div class="verify-shell">
    <div class="verify-card" role="region" aria-labelledby="verifyTitle">
      <div class="verify-glow" aria-hidden="true"></div>

      <header class="verify-header">
        <div class="verify-badge" aria-hidden="true">
          <span class="verify-badge__dot"></span>
        </div>

        <div class="verify-headline">
          <h1 id="verifyTitle" class="verify-title">
            <span class="verify-title__gradient">Xác minh email</span>
          </h1>
          <p class="verify-subtitle">
            Vui lòng kiểm tra hộp thư đến để hoàn tất đăng ký và kích hoạt tài khoản.
          </p>
        </div>
      </header>

      <div class="verify-body">
        @if (session('resent'))
          <div class="verify-alert verify-alert--success" role="alert">
            <span class="verify-alert__icon" aria-hidden="true">✓</span>
            <div class="verify-alert__content">
              <div class="verify-alert__title">Đã gửi lại liên kết</div>
              <div class="verify-alert__desc">Liên kết xác minh mới đã được gửi đến email của bạn.</div>
            </div>
          </div>
        @endif

        <div class="verify-note">
          <p class="verify-note__p">
            Trước khi tiếp tục, vui lòng kiểm tra email để lấy liên kết xác minh.
          </p>
          <p class="verify-note__p verify-note__muted">
            Nếu bạn chưa nhận được email, bạn có thể yêu cầu gửi lại.
          </p>
        </div>

        <div class="verify-actions">
          <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="vbtn vbtn--primary vbtn--full">
              <span class="vbtn__txt">Gửi lại email xác minh</span>
              <span class="vbtn__glow" aria-hidden="true"></span>
            </button>
          </form>

          <div class="verify-help">
            <span class="verify-help__text">Mẹo:</span>
            <span class="verify-help__text">Kiểm tra cả mục Spam / Promotions.</span>
          </div>
        </div>
      </div>
    </div>

    <p class="verify-meta" aria-hidden="true">
      <span class="verify-meta__brand">Weekly To-Do</span>
      <span class="verify-meta__dot">•</span>
      Secure verification
    </p>
  </div>
</section>
@endsection
