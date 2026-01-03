{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard - Weekly Planner')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/dashboard-wow.css') }}?v={{ file_exists(public_path('css/dashboard-wow.css')) ? filemtime(public_path('css/dashboard-wow.css')) : time() }}">
@endpush

@php
  use App\Models\Task;

  $dayHeader = [
    1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5',
    5 => 'Thứ 6', 6 => 'Thứ 7', 7 => 'CN'
  ];

  $prevWeek = $startOfWeek->copy()->subWeek()->toDateString();
  $nextWeek = $startOfWeek->copy()->addWeek()->toDateString();
@endphp

@section('content')
<section class="dashx" aria-label="Weekly Planner Dashboard">
  <div class="dashx-wrap">

    {{-- =======================================================
       HERO / TOPBAR
       ======================================================= --}}
    <header class="dashx-hero">
      <div class="dashx-hero__bg" aria-hidden="true"></div>

      <div class="dashx-hero__row">
        <div class="dashx-hero__title">
          <div class="dashx-badge" aria-hidden="true">
            <span class="dashx-badge__dot"></span>
          </div>

          <div class="dashx-hero__titleText">
            <h1 class="dashx-h1">
              <span class="dashx-h1__grad">Weekly Planner</span>
            </h1>

            <div class="dashx-sub">
              <span class="dashx-sub__range">
                {{ $startOfWeek->format('d/m/Y') }} → {{ $endOfWeek->format('d/m/Y') }}
              </span>
              <span class="dashx-sub__dot">•</span>
              <span class="dashx-sub__tz">Timezone: {{ config('app.timezone') }}</span>
            </div>
          </div>
        </div>

        <div class="dashx-hero__tools">
          <div class="dashx-pillgroup" role="group" aria-label="Điều hướng tuần">
            <a class="dashx-btn dashx-btn--ghost" href="{{ route('dashboard', ['week' => $prevWeek]) }}">← Tuần trước</a>
            <a class="dashx-btn dashx-btn--ghost" href="{{ route('dashboard', ['week' => $nextWeek]) }}">Tuần sau →</a>
          </div>

          <form class="dashx-week" method="GET" action="{{ route('dashboard') }}">
            <label class="dashx-week__label" for="weekPick">Chọn tuần</label>
            <div class="dashx-week__control">
              <input id="weekPick" type="date" name="week" value="{{ $weekParam }}" class="dashx-input">
              <button class="dashx-btn dashx-btn--dark" type="submit">Chọn</button>
            </div>
          </form>

          <a class="dashx-btn dashx-btn--primary" href="{{ route('tasks.create') }}">
            <span class="dashx-btn__txt">+ Tạo Task</span>
            <span class="dashx-btn__glow" aria-hidden="true"></span>
          </a>
        </div>
      </div>

      <div class="dashx-hero__meta">
        <div class="dashx-chips" aria-hidden="true">
          <span class="dashx-chipTag">Weekly</span>
          <span class="dashx-chipTag">Planner</span>
          <span class="dashx-chipTag">Focus</span>
          <span class="dashx-chipTag">Flow</span>
        </div>

        <div class="dashx-hint">
          Mẹo: bấm dấu <span class="dashx-hint__kbd">+</span> trong từng ô để tạo task nhanh theo giờ.
        </div>
      </div>
    </header>

    {{-- =======================================================
       MOBILE: DAY TABS + LIST (dễ dùng trên điện thoại)
       ======================================================= --}}
    <div class="dashx-mobile d-block d-lg-none">
      <div class="dashx-card">
        <div class="dashx-card__head">
          <div class="dashx-card__title">Lịch theo ngày</div>
          <div class="dashx-card__desc">Chọn tab để xem task theo từng ngày.</div>
        </div>

        <div class="dashx-card__body">
          <ul class="nav nav-pills dashx-tabs" id="dayTabs" role="tablist">
            @foreach($days as $idx => $d)
              @php $tabId = 'day-' . $d['key']; @endphp
              <li class="nav-item" role="presentation">
                <button class="nav-link @if($idx===0) active @endif"
                        id="{{ $tabId }}-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#{{ $tabId }}"
                        type="button" role="tab"
                        aria-controls="{{ $tabId }}"
                        aria-selected="{{ $idx===0 ? 'true' : 'false' }}">
                  <span class="dashx-tabs__top">{{ $dayHeader[$d['date']->dayOfWeekIso] }}</span>
                  <span class="dashx-tabs__sub">{{ $d['date']->format('d/m') }}</span>
                </button>
              </li>
            @endforeach
          </ul>

          <div class="tab-content dashx-tabpanes">
            @foreach($days as $idx => $d)
              @php
                $tabId = 'day-' . $d['key'];
                $dayTasks = $tasksByDate[$d['key']] ?? collect();
              @endphp

              <div class="tab-pane fade @if($idx===0) show active @endif"
                   id="{{ $tabId }}" role="tabpanel" aria-labelledby="{{ $tabId }}-tab">
                <div class="dashx-day">
                  <div class="dashx-day__head">
                    <div class="dashx-day__left">
                      <div class="dashx-day__title">{{ $dayHeader[$d['date']->dayOfWeekIso] }}</div>
                      <div class="dashx-day__date">{{ $d['date']->format('d/m/Y') }}</div>
                    </div>

                    <a class="dashx-btn dashx-btn--soft"
                       href="{{ route('tasks.create', ['date' => $d['key'], 'time' => '09:00']) }}">
                      + Nhanh
                    </a>
                  </div>

                  <div class="dashx-day__body">
                    @if($dayTasks->isEmpty())
                      <div class="dashx-empty">
                        <div class="dashx-empty__icon" aria-hidden="true">+</div>
                        <div>
                          <div class="dashx-empty__title">Chưa có task</div>
                          <div class="dashx-empty__desc">Tạo task mới để bắt đầu kế hoạch ngày hôm nay.</div>
                        </div>
                      </div>
                    @else
                      <div class="dashx-list">
                        @foreach($dayTasks->sortBy('scheduled_time') as $t)
                          <article class="dashx-task">
                            <div class="dashx-task__top">
                              <div class="dashx-task__title" title="{{ $t->title }}">{{ $t->title }}</div>
                              <span class="badge status-badge {{ $t->status_badge_class }}">{{ $t->status }}</span>
                            </div>

                            <div class="dashx-task__meta">
                              <span class="dashx-meta__pill">{{ substr($t->scheduled_time,0,5) }}</span>
                              <span class="dashx-meta__sep">•</span>
                              <span class="dashx-meta__pill">{{ $t->duration_minutes }}'</span>
                              @if($t->priority)
                                <span class="dashx-meta__sep">•</span>
                                <span class="dashx-meta__pill">Priority: {{ $t->priority }}</span>
                              @endif
                            </div>

                            <div class="dashx-task__actions">
                              <a class="dashx-btn dashx-btn--ghost" href="{{ route('tasks.edit', $t) }}">Sửa</a>

                              <select class="dashx-select"
                                      data-action="quick-status"
                                      data-task-id="{{ $t->id }}">
                                @foreach(Task::STATUSES as $st)
                                  <option value="{{ $st }}" @selected($st === $t->status)>{{ $st }}</option>
                                @endforeach
                              </select>

                              <form method="POST" action="{{ route('tasks.destroy', $t) }}"
                                    onsubmit="return confirm('Xóa task này?')">
                                @csrf @method('DELETE')
                                <button class="dashx-btn dashx-btn--danger" type="submit">Xóa</button>
                              </form>
                            </div>
                          </article>
                        @endforeach
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>

        </div>
      </div>
    </div>

    {{-- =======================================================
       DESKTOP: STICKY GRID (Giờ sticky + Thứ sticky)
       ======================================================= --}}
    <div class="dashx-desktop d-none d-lg-block">
      <div class="dashx-surface">
        <div class="dashx-surface__head">
          <div class="dashx-surface__title">
            <span class="dashx-surface__grad">Lưới theo giờ</span>
            <span class="dashx-surface__note">7 ngày × {{ count($hours) }} khung giờ</span>
          </div>

          <div class="dashx-legend" aria-hidden="true">
            <span class="dashx-legend__dot dashx-legend__dot--a"></span> Focus
            <span class="dashx-legend__dot dashx-legend__dot--b"></span> Progress
            <span class="dashx-legend__dot dashx-legend__dot--c"></span> Review
            <span class="dashx-legend__dot dashx-legend__dot--d"></span> Done
          </div>
        </div>

        {{-- IMPORTANT: set số ngày để template luôn khớp, không lệch CN --}}
        <div class="dashx-grid" style="--dashx-days: {{ count($days) }};">
          {{-- HEAD (sticky top) --}}
          <div class="dashx-grid__head">
            <div class="dashx-grid__corner">
              <div class="dashx-corner__label">Giờ</div>
            </div>

            @foreach($days as $d)
              <div class="dashx-grid__dayhead">
                <div class="dashx-dayhead__top">{{ $dayHeader[$d['date']->dayOfWeekIso] }}</div>
                <div class="dashx-dayhead__sub">{{ $d['date']->format('d/m') }}</div>
              </div>
            @endforeach
          </div>

          {{-- ROWS --}}
          @foreach($hours as $h)
            <div class="dashx-grid__row">
              <div class="dashx-grid__hour">
                <span class="dashx-hour__txt">{{ sprintf('%02d:00', $h) }}</span>
              </div>

              @foreach($days as $d)
                @php
                  $dayTasks = $tasksByDate[$d['key']] ?? collect();
                  $slotTasks = $dayTasks->filter(function($t) use ($h) {
                    $hour = (int) substr($t->scheduled_time, 0, 2);
                    return $hour === $h;
                  });
                @endphp

                <div class="dashx-grid__cell">
                  <a class="dashx-cell__add"
                     title="Tạo task lúc {{ sprintf('%02d:00', $h) }}"
                     href="{{ route('tasks.create', ['date' => $d['key'], 'time' => sprintf('%02d:00', $h)]) }}">
                    +
                  </a>

                  @foreach($slotTasks as $t)
                    <div class="dashx-chip" data-task-id="{{ $t->id }}">
                      <div class="dashx-chip__top">
                        <div class="dashx-chip__title" title="{{ $t->title }}">{{ $t->title }}</div>
                        <span class="badge status-badge {{ $t->status_badge_class }}">{{ $t->status }}</span>
                      </div>

                      <div class="dashx-chip__meta">
                        <span class="dashx-mini">{{ substr($t->scheduled_time,0,5) }}</span>
                        <span class="dashx-mini__dot">•</span>
                        <span class="dashx-mini">{{ $t->duration_minutes }}'</span>
                        @if($t->priority)
                          <span class="dashx-mini__dot">•</span>
                          <span class="dashx-mini">{{ $t->priority }}</span>
                        @endif
                      </div>

                      <div class="dashx-chip__actions">
                        <a class="dashx-btn dashx-btn--ghost dashx-btn--xs" href="{{ route('tasks.edit', $t) }}">Sửa</a>

                        <button class="dashx-btn dashx-btn--soft dashx-btn--xs"
                                data-action="set-status"
                                data-task-id="{{ $t->id }}"
                                data-status="{{ Task::STATUS_DONE }}">
                          Done
                        </button>

                        <button class="dashx-btn dashx-btn--soft dashx-btn--xs"
                                data-action="set-status"
                                data-task-id="{{ $t->id }}"
                                data-status="{{ Task::STATUS_REVIEW }}">
                          Review
                        </button>

                        <button class="dashx-btn dashx-btn--soft dashx-btn--xs"
                                data-action="set-status"
                                data-task-id="{{ $t->id }}"
                                data-status="{{ Task::STATUS_PROGRESS }}">
                          Progress
                        </button>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endforeach
            </div>
          @endforeach
        </div>

      </div>
    </div>

  </div>
</section>
@endsection

@section('scripts')
<script>
(function () {
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  async function patchStatus(taskId, payload) {
    const res = await fetch(`/tasks/${taskId}/status`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload)
    });

    if (!res.ok) {
      const txt = await res.text();
      console.error(txt);
      alert('Không cập nhật được. Vui lòng thử lại.');
      return null;
    }
    return res.json();
  }

  // Desktop buttons quick set status
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-action="set-status"]');
    if (!btn) return;

    const taskId = btn.getAttribute('data-task-id');
    const status = btn.getAttribute('data-status');

    btn.disabled = true;
    const out = await patchStatus(taskId, { status });
    btn.disabled = false;

    if (out?.ok) window.location.reload();
  });

  // Mobile dropdown quick status
  document.addEventListener('change', async (e) => {
    const sel = e.target.closest('select[data-action="quick-status"]');
    if (!sel) return;

    const taskId = sel.getAttribute('data-task-id');
    const status = sel.value;

    sel.disabled = true;
    const out = await patchStatus(taskId, { status });
    sel.disabled = false;

    if (out?.ok) window.location.reload();
  });
})();
</script>
@endsection
