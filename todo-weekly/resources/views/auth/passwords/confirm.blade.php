{{-- resources/views/auth/passwords/confirm.blade.php --}}
@extends('layouts.app')

@section('title', 'Xác nhận mật khẩu')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/auth-confirm.css') }}">
@endpush

@section('content')
<section class="confirm-page" aria-label="Trang xác nhận mật khẩu">
  <div class="confirm-shell">
    <div class="confirm-card" role="region" aria-labelledby="confirmTitle">
      <div class="confirm-glow" aria-hidden="true"></div>

      <header class="confirm-header">
        <div class="confirm-badge" aria-hidden="true">
          <span class="confirm-badge__dot"></span>
        </div>

        <div class="confirm-head">
          <h1 id="confirmTitle" class="confirm-title">
            <span class="confirm-title__gradient">Xác nhận mật khẩu</span>
          </h1>
          <p class="confirm-subtitle">
            Vui lòng nhập lại mật khẩu để tiếp tục thao tác bảo mật.
          </p>
        </div>
      </header>

      <div class="confirm-body">
        <form method="POST" action="{{ route('password.confirm') }}" class="confirm-form" novalidate>
          @csrf

          <div class="field">
            <label for="password" class="field__label">Mật khẩu</label>

            <div class="field__control @error('password') has-error @enderror">
              <input id="password"
                     type="password"
                     name="password"
                     class="field__input @error('password') is-invalid @enderror"
                     required
                     autocomplete="current-password"
                     placeholder="••••••••">

              {{-- ✅ Lỗi hiển thị trong ô --}}
              @error('password')
                <span class="field__errorInline" role="alert">{{ $message }}</span>
              @enderror
            </div>

            <p class="field__hint" aria-hidden="true">
              Đây là bước bảo mật để xác nhận bạn là chủ tài khoản.
            </p>
          </div>

          <div class="confirm-actions">
            <button type="submit" class="cbtn cbtn--primary cbtn--full">
              <span class="cbtn__txt">Xác nhận</span>
              <span class="cbtn__glow" aria-hidden="true"></span>
            </button>

            @if (Route::has('password.request'))
              <a class="cbtn cbtn--ghost cbtn--full" href="{{ route('password.request') }}">
                Quên mật khẩu?
              </a>
            @endif
          </div>
        </form>
      </div>
    </div>

    <p class="confirm-meta" aria-hidden="true">
      <span class="confirm-meta__brand">Weekly To-Do</span>
      <span class="confirm-meta__dot">•</span>
      Security checkpoint
    </p>
  </div>
</section>
@endsection
