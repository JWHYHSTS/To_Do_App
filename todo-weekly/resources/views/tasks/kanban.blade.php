@extends('layouts.app')

@section('title', 'Quản lý tiến độ')

@push('styles')
<link rel="stylesheet"
      href="{{ asset('css/kanban-tech.css') }}?v={{ file_exists(public_path('css/kanban-tech.css')) ? filemtime(public_path('css/kanban-tech.css')) : time() }}">
@endpush

@php
  use App\Models\Task;

  // Map tên status => key để style (tương thích cả khi bạn đổi text label)
  $toneOf = function(string $st){
    $s = mb_strtolower($st);
    if (str_contains($s, 'todo') || str_contains($s, 'to do')) return 'todo';
    if (str_contains($s, 'progress') || str_contains($s, 'in progress')) return 'progress';
    if (str_contains($s, 'review')) return 'review';
    if (str_contains($s, 'done')) return 'done';
    return 'neutral';
  };
@endphp

@section('content')
<section class="kbx" aria-label="Quản lý tiến độ (Kanban)">

  {{-- ===== Header ===== --}}
  <header class="kbx-hero">
    <div class="kbx-hero__bg" aria-hidden="true"></div>

    <div class="kbx-hero__row">
      <div class="kbx-hero__left">
        <div class="kbx-mark" aria-hidden="true"><span class="kbx-mark__dot"></span></div>

        <div class="kbx-hero__text">
          <div class="kbx-kicker">Kanban Board</div>
          <h1 class="kbx-title">Quản lý tiến độ</h1>
          <div class="kbx-sub">
            Theo dõi task theo trạng thái. Kéo thả có thể bổ sung sau; hiện tại tập trung UI + thao tác nhanh.
          </div>
        </div>
      </div>

      <div class="kbx-hero__right">
        <a class="kbx-btn kbx-btn--create" href="{{ route('tasks.create') }}">
          <span class="kbx-btn__ico" aria-hidden="true">＋</span>
          <span class="kbx-btn__txt">Tạo Task</span>
          <span class="kbx-btn__glow" aria-hidden="true"></span>
        </a>

        <a class="kbx-btn kbx-btn--ghost" href="{{ route('dashboard') }}">
          <span class="kbx-btn__txt">Dashboard</span>
        </a>
      </div>
    </div>

    <div class="kbx-hero__meta">
      <div class="kbx-pills" aria-hidden="true">
        <span class="kbx-pill kbx-pill--a">Tech</span>
        <span class="kbx-pill kbx-pill--b">Dark</span>
        <span class="kbx-pill kbx-pill--c">Focus</span>
      </div>

      <div class="kbx-hint">
        Mẹo: bấm “Sửa” để cập nhật trạng thái/giờ và quay lại board để theo dõi.
      </div>
    </div>
  </header>

  {{-- ===== Board ===== --}}
  <div class="kbx-board" role="region" aria-label="Kanban columns">
    <div class="kbx-board__grid">
      @foreach($statuses as $st)
        @php
          $col = $grouped[$st] ?? collect();
          $tone = $toneOf($st);
        @endphp

        <section class="kbx-col" data-tone="{{ $tone }}" aria-label="Cột {{ $st }}">
          <header class="kbx-col__head">
            <div class="kbx-col__title">
              <span class="kbx-col__dot" aria-hidden="true"></span>
              <span class="kbx-col__name">{{ $st }}</span>
            </div>
            <div class="kbx-col__count">{{ $col->count() }}</div>
          </header>

          <div class="kbx-col__body">
            @forelse($col as $t)
              <article class="kbx-card" data-task-id="{{ $t->id }}">
                <div class="kbx-card__top">
                  <div class="kbx-card__title" title="{{ $t->title }}">
                    {{ $t->title }}
                  </div>

                  {{-- Badge giữ nguyên logic cũ của bạn --}}
                  <span class="badge status-badge {{ $t->status_badge_class }}">
                    {{ $t->status }}
                  </span>
                </div>

                <div class="kbx-card__meta">
                  <span class="kbx-meta__pill">
                    {{ $t->scheduled_date?->format('d/m') ?? '' }}
                  </span>
                  <span class="kbx-meta__sep">•</span>
                  <span class="kbx-meta__pill">
                    {{ $t->scheduled_time ? substr($t->scheduled_time,0,5) : '' }}
                  </span>

                  @if(!empty($t->duration_minutes))
                    <span class="kbx-meta__sep">•</span>
                    <span class="kbx-meta__pill">{{ $t->duration_minutes }}'</span>
                  @endif

                  @if(!empty($t->priority))
                    <span class="kbx-meta__sep">•</span>
                    <span class="kbx-meta__pill">P: {{ $t->priority }}</span>
                  @endif
                </div>

                <div class="kbx-card__actions">
                  <a class="kbx-btn kbx-btn--edit" href="{{ route('tasks.edit', $t) }}">
                    <span class="kbx-btn__txt">Sửa</span>
                  </a>

                  <a class="kbx-btn kbx-btn--view" href="{{ route('tasks.index', ['q' => $t->title]) }}">
                    <span class="kbx-btn__txt">Tìm</span>
                  </a>
                </div>
              </article>
            @empty
              <div class="kbx-empty">
                <div class="kbx-empty__icon" aria-hidden="true">＋</div>
                <div>
                  <div class="kbx-empty__title">Trống</div>
                  <div class="kbx-empty__desc">Chưa có task ở trạng thái này.</div>
                </div>
              </div>
            @endforelse
          </div>
        </section>
      @endforeach
    </div>
  </div>

</section>
@endsection
