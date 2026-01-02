@extends('layouts.app')

@section('title', 'Dashboard - Weekly Planner')

@php
  use App\Models\Task;

  // Vietnamese day labels for header (Mon..Sun)
  $dayHeader = [
    1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5',
    5 => 'Thứ 6', 6 => 'Thứ 7', 7 => 'CN'
  ];

  // IMPORTANT: Carbon is mutable -> dùng copy() để không làm lệch $startOfWeek
  $prevWeek = $startOfWeek->copy()->subWeek()->toDateString();
  $nextWeek = $startOfWeek->copy()->addWeek()->toDateString();

  $statusQuick = [
    Task::STATUS_TODO => 'To Do',
    Task::STATUS_PROGRESS => 'Start Progress',
    Task::STATUS_REVIEW => 'Move to Review',
    Task::STATUS_DONE => 'Mark as Done',
  ];
@endphp

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
  <div>
    <h1 class="h4 mb-1">Weekly Planner</h1>
    <div class="text-muted">
      {{ $startOfWeek->format('d/m/Y') }} → {{ $endOfWeek->format('d/m/Y') }}
      (Timezone: {{ config('app.timezone') }})
    </div>
  </div>

  {{-- CHỖ THÊM NÚT LẶP TUẦN: đặt cùng hàng với Tuần trước / Tuần sau / Chọn / Tạo Task --}}
  <div class="d-flex flex-wrap align-items-center gap-2">
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('dashboard', ['week' => $prevWeek]) }}">← Tuần trước</a>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('dashboard', ['week' => $nextWeek]) }}">Tuần sau →</a>

    <form class="d-flex align-items-center gap-2" method="GET" action="{{ route('dashboard') }}">
      <input type="date" name="week" value="{{ $weekParam }}" class="form-control form-control-sm">
      <button class="btn btn-dark btn-sm" type="submit">Chọn</button>
    </form>

    <a class="btn btn-primary btn-sm" href="{{ route('tasks.create') }}">+ Tạo Task</a>
  </div>
</div>

<!-- Mobile-first: show tabs by day -->
<div class="d-block d-lg-none">
  <ul class="nav nav-pills mb-2" id="dayTabs" role="tablist">
    @foreach($days as $idx => $d)
      @php $tabId = 'day-' . $d['key']; @endphp
      <li class="nav-item" role="presentation">
        <button class="nav-link @if($idx===0) active @endif"
                id="{{ $tabId }}-tab"
                data-bs-toggle="tab"
                data-bs-target="#{{ $tabId }}"
                type="button" role="tab">
          {{ $dayHeader[$d['date']->dayOfWeekIso] }}
          <div class="small">{{ $d['date']->format('d/m') }}</div>
        </button>
      </li>
    @endforeach
  </ul>

  <div class="tab-content">
    @foreach($days as $idx => $d)
      @php
        $tabId = 'day-' . $d['key'];
        $dayTasks = $tasksByDate[$d['key']] ?? collect();
      @endphp
      <div class="tab-pane fade @if($idx===0) show active @endif" id="{{ $tabId }}" role="tabpanel">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div class="fw-semibold">
              {{ $dayHeader[$d['date']->dayOfWeekIso] }} - {{ $d['date']->format('d/m/Y') }}
            </div>
            <a class="btn btn-outline-primary btn-sm"
               href="{{ route('tasks.create', ['date' => $d['key'], 'time' => '09:00']) }}">
              + Nhanh
            </a>
          </div>

          <div class="card-body">
            @if($dayTasks->isEmpty())
              <div class="text-muted">Không có task.</div>
            @else
              <div class="vstack gap-2">
                @foreach($dayTasks as $t)
                  <div class="task-card p-2 border rounded bg-white">
                    <div class="d-flex justify-content-between gap-2">
                      <div>
                        <div class="fw-semibold">{{ $t->title }}</div>
                        <div class="small text-muted">
                          {{ substr($t->scheduled_time,0,5) }} • {{ $t->duration_minutes }}'
                          @if($t->priority) • Priority: {{ $t->priority }} @endif
                        </div>
                      </div>
                      <div class="text-end">
                        <span class="badge status-badge {{ $t->status_badge_class }}">{{ $t->status }}</span>
                      </div>
                    </div>

                    <div class="mt-2 d-flex flex-wrap gap-2 align-items-center">
                      <a class="btn btn-outline-secondary btn-sm" href="{{ route('tasks.edit', $t) }}">Sửa</a>

                      <select class="form-select form-select-sm w-auto"
                              data-action="quick-status"
                              data-task-id="{{ $t->id }}">
                        @foreach(Task::STATUSES as $st)
                          <option value="{{ $st }}" @selected($st === $t->status)>{{ $st }}</option>
                        @endforeach
                      </select>

                      <form method="POST" action="{{ route('tasks.destroy', $t) }}"
                            onsubmit="return confirm('Xóa task này?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm" type="submit">Xóa</button>
                      </form>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>

<!-- Desktop: grid 7 columns x hours -->
<div class="d-none d-lg-block">
  <div class="planner-grid">
    <div class="planner-head">
      <div class="planner-corner"></div>
      @foreach($days as $d)
        <div class="planner-dayhead">
          <div class="fw-semibold">{{ $dayHeader[$d['date']->dayOfWeekIso] }}</div>
          <div class="small text-muted">{{ $d['date']->format('d/m') }}</div>
        </div>
      @endforeach
    </div>

    @foreach($hours as $h)
      <div class="planner-row">
        <div class="planner-hour">
          {{ sprintf('%02d:00', $h) }}
        </div>

        @foreach($days as $d)
          @php
            $dayTasks = $tasksByDate[$d['key']] ?? collect();

            // Tasks in this slot: same hour (simple version)
            $slotTasks = $dayTasks->filter(function($t) use ($h) {
              $hour = (int) substr($t->scheduled_time, 0, 2);
              return $hour === $h;
            });
          @endphp

          <div class="planner-cell">
            <div class="cell-actions">
              <a class="btn btn-sm btn-light border"
                 href="{{ route('tasks.create', ['date' => $d['key'], 'time' => sprintf('%02d:00', $h)]) }}">
                 +</a>
            </div>

            @foreach($slotTasks as $t)
              <div class="task-chip" data-task-id="{{ $t->id }}">
                <div class="d-flex justify-content-between align-items-start gap-2">
                  <div class="fw-semibold text-truncate" title="{{ $t->title }}">{{ $t->title }}</div>
                  <span class="badge status-badge {{ $t->status_badge_class }}">{{ $t->status }}</span>
                </div>
                <div class="small text-muted">
                  {{ substr($t->scheduled_time,0,5) }} • {{ $t->duration_minutes }}'
                  @if($t->priority) • {{ $t->priority }} @endif
                </div>

                <div class="mt-2 d-flex flex-wrap gap-2 align-items-center">
                  <a class="btn btn-outline-secondary btn-sm" href="{{ route('tasks.edit', $t) }}">Sửa</a>

                  <button class="btn btn-outline-success btn-sm"
                          data-action="set-status"
                          data-task-id="{{ $t->id }}"
                          data-status="{{ Task::STATUS_DONE }}">
                    Mark Done
                  </button>

                  <button class="btn btn-outline-warning btn-sm"
                          data-action="set-status"
                          data-task-id="{{ $t->id }}"
                          data-status="{{ Task::STATUS_REVIEW }}">
                    Review
                  </button>

                  <button class="btn btn-outline-primary btn-sm"
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

  // Buttons quick set status (desktop)
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

  // Dropdown quick status (mobile)
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
