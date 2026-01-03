{{-- resources/views/tasks/_form.blade.php --}}
@php
  use App\Models\Task;

  $recurrenceVal = old('recurrence', $task->recurrence ?? 'none');

  $scheduledDateVal = old(
    'scheduled_date',
    optional($task->scheduled_date)->format('Y-m-d') ?? $task->scheduled_date
  );

  $scheduledTimeVal = old(
    'scheduled_time',
    is_string($task->scheduled_time)
      ? substr($task->scheduled_time, 0, 5)
      : ($task->scheduled_time?->format('H:i') ?? '09:00')
  );

  $recurrenceUntilVal = old(
    'recurrence_until',
    !empty($task->recurrence_until)
      ? (is_string($task->recurrence_until) ? $task->recurrence_until : $task->recurrence_until->format('Y-m-d'))
      : ''
  );

  // Badge màu theo status (UI only)
  $statusTone = match(old('status', $task->status)) {
    Task::STATUS_TODO => 'todo',
    Task::STATUS_PROGRESS => 'progress',
    Task::STATUS_REVIEW => 'review',
    Task::STATUS_DONE => 'done',
    default => 'neutral'
  };
@endphp

<div class="tfx" data-status-tone="{{ $statusTone }}">
  <div class="tfx-shell">

    {{-- HERO --}}
    <div class="tfx-hero">
      <div class="tfx-hero__bg" aria-hidden="true"></div>

      <div class="tfx-hero__row">
        <div class="tfx-hero__left">
          <div class="tfx-mark" aria-hidden="true">
            <span class="tfx-mark__dot"></span>
          </div>
          <div class="tfx-hero__text">
            <div class="tfx-kicker">Task Editor</div>
            <h2 class="tfx-title">
              {{ $task->exists ? 'Cập nhật công việc' : 'Tạo công việc mới' }}
            </h2>
            <div class="tfx-sub">
              Điền thông tin chính xác để lập kế hoạch theo tuần. Hỗ trợ lặp lại theo lịch.
            </div>
          </div>
        </div>

        <div class="tfx-hero__right">
          <span class="tfx-badge">
            <span class="tfx-badge__dot" aria-hidden="true"></span>
            <span class="tfx-badge__text">
              {{ old('status', $task->status) ?: 'Draft' }}
            </span>
          </span>
        </div>
      </div>
    </div>

    {{-- FORM BODY --}}
    <div class="tfx-card">
      <div class="row g-3">

        {{-- Title --}}
        <div class="col-12">
          <label class="form-label tfx-label">
            Tiêu đề <span class="text-danger">*</span>
          </label>
          <div class="tfx-field">
            <input
              class="form-control tfx-control"
              name="title"
              value="{{ old('title', $task->title) }}"
              required
              maxlength="255"
              placeholder="Ví dụ: Hoàn thành báo cáo, Fix bug UI, Ôn tập Laravel..."
              autocomplete="off"
            >
          </div>
          <div class="tfx-help">Ngắn gọn, rõ ràng, tối đa 255 ký tự.</div>
        </div>

        {{-- Description --}}
        <div class="col-12">
          <label class="form-label tfx-label">Mô tả</label>
          <div class="tfx-field">
            <textarea
              class="form-control tfx-control tfx-textarea"
              name="description"
              rows="4"
              placeholder="Mô tả chi tiết: mục tiêu, checklist, link tài liệu..."
            >{{ old('description', $task->description) }}</textarea>
          </div>
          <div class="tfx-help">Gợi ý: dùng mô tả để lưu checklist, tài liệu tham chiếu, ghi chú tiến độ.</div>
        </div>

        {{-- Status --}}
        <div class="col-12 col-md-4">
          <label class="form-label tfx-label">Trạng thái</label>
          <div class="tfx-field">
            <select class="form-select tfx-control" name="status" required id="tfxStatus">
              @foreach($statuses as $st)
                <option value="{{ $st }}" @selected(old('status', $task->status) === $st)>{{ $st }}</option>
              @endforeach
            </select>
          </div>
          <div class="tfx-help">Trạng thái giúp hệ thống phân loại và theo dõi tiến độ.</div>
        </div>

        {{-- Priority --}}
        <div class="col-12 col-md-4">
          <label class="form-label tfx-label">Priority</label>
          <div class="tfx-field">
            <select class="form-select tfx-control" name="priority" id="tfxPriority">
              <option value="">-- none --</option>
              @foreach($priorities as $p)
                <option value="{{ $p }}" @selected(old('priority', $task->priority) === $p)>{{ $p }}</option>
              @endforeach
            </select>
          </div>
          <div class="tfx-help">Ưu tiên để sắp xếp: High/Medium/Low (tuỳ hệ thống).</div>
        </div>

        {{-- Duration --}}
        <div class="col-12 col-md-4">
          <label class="form-label tfx-label">Thời lượng (phút)</label>
          <div class="tfx-field tfx-field--inline">
            <input
              class="form-control tfx-control"
              type="number"
              name="duration_minutes"
              value="{{ old('duration_minutes', $task->duration_minutes ?? 60) }}"
              min="15"
              max="480"
              required
            >
            <span class="tfx-suffix">min</span>
          </div>
          <div class="tfx-help">Giới hạn: 15..480. Khuyến nghị 30/60/90.</div>
        </div>

        {{-- Date --}}
        <div class="col-12 col-md-6">
          <label class="form-label tfx-label">
            Ngày dự kiến <span class="text-danger">*</span>
          </label>
          <div class="tfx-field">
            <input
              class="form-control tfx-control"
              type="date"
              name="scheduled_date"
              value="{{ $scheduledDateVal }}"
              required
            >
          </div>
          <div class="tfx-help">Ngày hiển thị trên Weekly Planner.</div>
        </div>

        {{-- Time --}}
        <div class="col-12 col-md-6">
          <label class="form-label tfx-label">
            Giờ dự kiến <span class="text-danger">*</span>
          </label>
          <div class="tfx-field">
            <input
              class="form-control tfx-control"
              type="time"
              name="scheduled_time"
              value="{{ $scheduledTimeVal }}"
              required
            >
          </div>
          <div class="tfx-help">Chọn giờ để task rơi đúng khung trong grid.</div>
        </div>

        {{-- Recurrence --}}
        <div class="col-12">
          <div class="tfx-divider"></div>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label tfx-label">Lặp lại</label>
          <div class="tfx-field">
            <select class="form-select tfx-control" name="recurrence" id="tfxRecurrence">
              <option value="none"  @selected($recurrenceVal === 'none')>Không lặp</option>
              <option value="daily" @selected($recurrenceVal === 'daily')>Hằng ngày</option>
              <option value="weekly" @selected($recurrenceVal === 'weekly')>Hằng tuần</option>
            </select>
          </div>
          <div class="tfx-help">
            Nếu chọn lặp, hệ thống tự sinh task tương lai theo scheduler.
          </div>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label tfx-label">Lặp đến ngày (tuỳ chọn)</label>
          <div class="tfx-field">
            <input
              class="form-control tfx-control"
              type="date"
              name="recurrence_until"
              value="{{ $recurrenceUntilVal }}"
              id="tfxUntil"
            >
          </div>
          <div class="tfx-help">
            Để trống = lặp mãi. Khuyến nghị đặt giới hạn để tránh sinh quá nhiều task.
          </div>
        </div>

        {{-- Actions --}}
        <div class="col-12">
          <div class="tfx-actions">
            <button class="tfx-btn tfx-btn--primary" type="submit">
              <span class="tfx-btn__txt">{{ $submitLabel }}</span>
              <span class="tfx-btn__glow" aria-hidden="true"></span>
            </button>

            <a class="tfx-btn tfx-btn--ghost" href="{{ route('tasks.index') }}">Quay lại</a>
            <a class="tfx-btn tfx-btn--soft" href="{{ route('dashboard') }}">Dashboard</a>
          </div>

          <div class="tfx-footnote">
            Mẹo: dùng Priority + Duration để sắp lịch tuần hợp lý (tối ưu năng suất).
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

{{-- UX: disable "recurrence_until" khi recurrence = none (chỉ UI, không thay đổi backend) --}}
@push('scripts')
<script>
(function(){
  const rec = document.getElementById('tfxRecurrence');
  const until = document.getElementById('tfxUntil');
  const status = document.getElementById('tfxStatus');

  function syncUntil(){
    if(!rec || !until) return;
    const off = (rec.value === 'none');
    until.disabled = off;
    until.closest('.tfx-field')?.classList.toggle('is-disabled', off);
  }

  function syncTone(){
    // optional: set data attr để badge đổi tông (CSS)
    const root = document.querySelector('.tfx');
    if(!root || !status) return;
    const v = (status.value || '').toLowerCase();
    // map đơn giản theo text hiển thị (nếu bạn dùng enum khác, vẫn ok)
    if(v.includes('done')) root.dataset.statusTone = 'done';
    else if(v.includes('review')) root.dataset.statusTone = 'review';
    else if(v.includes('progress')) root.dataset.statusTone = 'progress';
    else if(v.includes('todo')) root.dataset.statusTone = 'todo';
    else root.dataset.statusTone = 'neutral';
  }

  rec?.addEventListener('change', syncUntil);
  status?.addEventListener('change', syncTone);

  syncUntil();
  syncTone();
})();
</script>
@endpush
