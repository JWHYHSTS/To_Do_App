@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="h4 mb-0">Danh sách Tasks</h1>
  <a class="btn btn-primary btn-sm" href="{{ route('tasks.create') }}">+ Tạo Task</a>
</div>

<form class="row g-2 align-items-end mb-3" method="GET" action="{{ route('tasks.index') }}">
  <div class="col-12 col-md-5">
    <label class="form-label">Tìm theo tiêu đề</label>
    <input class="form-control" name="q" value="{{ $q }}" placeholder="Nhập từ khóa...">
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Lọc trạng thái</label>
    <select class="form-select" name="status">
      <option value="">-- Tất cả --</option>
      @foreach($statuses as $st)
        <option value="{{ $st }}" @selected($st === $status)>{{ $st }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-12 col-md-3 d-flex gap-2">
    <button class="btn btn-dark w-100" type="submit">Áp dụng</button>
    <a class="btn btn-outline-secondary w-100" href="{{ route('tasks.index') }}">Reset</a>
  </div>
</form>

{{-- Bulk actions --}}
<div class="d-flex flex-wrap gap-2 mb-2">
  <button type="submit"
          form="bulkForm"
          class="btn btn-outline-danger btn-sm"
          id="btnDeleteSelected"
          disabled
          onclick="return confirm('Xóa các task đã chọn?')">
    Xóa đã chọn
  </button>

  <form method="POST" action="{{ route('tasks.deleteAllFiltered') }}" class="d-inline"
        onsubmit="return confirm('Xóa TẤT CẢ task theo bộ lọc hiện tại? Hành động không thể hoàn tác.')">
    @csrf
    @method('DELETE')
    <input type="hidden" name="q" value="{{ $q }}">
    <input type="hidden" name="status" value="{{ $status }}">
    <button type="submit" class="btn btn-danger btn-sm">
      Xóa tất cả (theo bộ lọc)
    </button>
  </form>
</div>

<form id="bulkForm" method="POST" action="{{ route('tasks.bulkDeleteSelected') }}">
  @csrf
  @method('DELETE')

  <div class="card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:40px;">
              <input type="checkbox" id="checkAll">
            </th>
            <th>Tiêu đề</th>
            <th>Ngày</th>
            <th>Giờ</th>
            <th>Thời lượng</th>
            <th>Status</th>
            <th>Priority</th>
            <th class="text-end">Hành động</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tasks as $t)
            <tr>
              <td>
                <input type="checkbox" name="ids[]" value="{{ $t->id }}" class="row-check">
              </td>
              <td class="fw-semibold">{{ $t->title }}</td>
              <td>{{ $t->scheduled_date->format('d/m/Y') }}</td>
              <td>{{ substr($t->scheduled_time,0,5) }}</td>
              <td>{{ $t->duration_minutes }}'</td>
              <td><span class="badge status-badge {{ $t->status_badge_class }}">{{ $t->status }}</span></td>
              <td>{{ $t->priority ?? '-' }}</td>
              <td class="text-end">
                <div class="d-inline-flex gap-2">
                  <a class="btn btn-outline-secondary btn-sm" href="{{ route('tasks.edit', $t) }}">Sửa</a>

                  <form method="POST" action="{{ route('tasks.destroy', $t) }}"
                        onsubmit="return confirm('Xóa task này?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm" type="submit">Xóa</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-muted p-3">Không có dữ liệu.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-body">
      {{ $tasks->links() }}
    </div>
  </div>
</form>
@endsection

@section('scripts')
<script>
(function () {
  const checkAll = document.getElementById('checkAll');
  const btn = document.getElementById('btnDeleteSelected');

  function rowChecks() {
    return Array.from(document.querySelectorAll('.row-check'));
  }

  function refreshBtn() {
    const anyChecked = rowChecks().some(c => c.checked);
    btn.disabled = !anyChecked;
  }

  if (checkAll) {
    checkAll.addEventListener('change', () => {
      rowChecks().forEach(c => c.checked = checkAll.checked);
      refreshBtn();
    });
  }

  document.addEventListener('change', (e) => {
    if (e.target.classList.contains('row-check')) {
      const checks = rowChecks();
      const all = checks.length > 0 && checks.every(c => c.checked);
      if (checkAll) checkAll.checked = all;
      refreshBtn();
    }
  });

  refreshBtn();
})();
</script>
@endsection
