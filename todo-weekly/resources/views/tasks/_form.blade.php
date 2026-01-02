@php
  use App\Models\Task;

  // Giá trị mặc định cho recurrence (nếu Task cũ chưa có field)
  $recurrenceVal = old('recurrence', $task->recurrence ?? 'none');

  // Giá trị mặc định cho scheduled_date
  $scheduledDateVal = old(
    'scheduled_date',
    optional($task->scheduled_date)->format('Y-m-d') ?? $task->scheduled_date
  );

  // Giá trị mặc định cho scheduled_time (HH:mm)
  $scheduledTimeVal = old(
    'scheduled_time',
    is_string($task->scheduled_time)
      ? substr($task->scheduled_time, 0, 5)
      : ($task->scheduled_time?->format('H:i') ?? '09:00')
  );

  // Giá trị mặc định cho recurrence_until (Y-m-d)
  $recurrenceUntilVal = old(
    'recurrence_until',
    !empty($task->recurrence_until)
      ? (is_string($task->recurrence_until) ? $task->recurrence_until : $task->recurrence_until->format('Y-m-d'))
      : ''
  );
@endphp

<div class="row g-3">
  <div class="col-12">
    <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
    <input
      class="form-control"
      name="title"
      value="{{ old('title', $task->title) }}"
      required
      maxlength="255"
    >
  </div>

  <div class="col-12">
    <label class="form-label">Mô tả</label>
    <textarea class="form-control" name="description" rows="3">{{ old('description', $task->description) }}</textarea>
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Trạng thái</label>
    <select class="form-select" name="status" required>
      @foreach($statuses as $st)
        <option value="{{ $st }}" @selected(old('status', $task->status) === $st)>{{ $st }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Priority</label>
    <select class="form-select" name="priority">
      <option value="">-- none --</option>
      @foreach($priorities as $p)
        <option value="{{ $p }}" @selected(old('priority', $task->priority) === $p)>{{ $p }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Thời lượng (phút)</label>
    <input
      class="form-control"
      type="number"
      name="duration_minutes"
      value="{{ old('duration_minutes', $task->duration_minutes ?? 60) }}"
      min="15"
      max="480"
      required
    >
    <div class="form-text">Giới hạn: 15..480</div>
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label">Ngày dự kiến <span class="text-danger">*</span></label>
    <input
      class="form-control"
      type="date"
      name="scheduled_date"
      value="{{ $scheduledDateVal }}"
      required
    >
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label">Giờ dự kiến <span class="text-danger">*</span></label>
    <input
      class="form-control"
      type="time"
      name="scheduled_time"
      value="{{ $scheduledTimeVal }}"
      required
    >
  </div>

  {{-- =========================
      Recurrence (lặp lại)
      ========================= --}}
  <div class="col-12 col-md-6">
    <label class="form-label">Lặp lại</label>
    <select class="form-select" name="recurrence">
      <option value="none"  @selected($recurrenceVal === 'none')>Không lặp</option>
      <option value="daily" @selected($recurrenceVal === 'daily')>Hằng ngày</option>
      <option value="weekly" @selected($recurrenceVal === 'weekly')>Hằng tuần</option>
    </select>
    <div class="form-text">
      Nếu chọn lặp, hệ thống sẽ tự sinh task tương lai theo lịch (thông qua scheduler).
    </div>
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label">Lặp đến ngày (tuỳ chọn)</label>
    <input
      class="form-control"
      type="date"
      name="recurrence_until"
      value="{{ $recurrenceUntilVal }}"
    >
    <div class="form-text">
      Để trống = lặp mãi. Khuyến nghị đặt giới hạn để tránh sinh quá nhiều task.
    </div>
  </div>

  {{-- =========================
      Actions
      ========================= --}}
  <div class="col-12">
    <div class="task-form-actions d-flex flex-wrap gap-2">
      <button class="btn btn-dark" type="submit">{{ $submitLabel }}</button>
      <a class="btn btn-outline-secondary" href="{{ route('tasks.index') }}">Quay lại</a>
      <a class="btn btn-outline-primary" href="{{ route('dashboard') }}">Dashboard</a>
    </div>
  </div>
</div>
