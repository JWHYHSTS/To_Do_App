@extends('layouts.app')

@section('title', 'Tasks')

@push('styles')
<link rel="stylesheet"
      href="{{ asset('css/tasks-index-neo.css') }}?v={{ file_exists(public_path('css/tasks-index-neo.css')) ? filemtime(public_path('css/tasks-index-neo.css')) : time() }}">
@endpush
@push('styles')
<link rel="stylesheet" href="{{ asset('css/tasks-dark.css') }}?v={{ time() }}">
@endpush
@section('content')
<section class="nx">

  {{-- Topbar / Title --}}
  <header class="nx-top">
    <div class="nx-top__left">
      <div class="nx-brand">
        <span class="nx-brand__dot" aria-hidden="true"></span>
      </div>

      <div class="nx-head">
        <div class="nx-kicker">Weekly To-Do • Task Manager</div>
        <h1 class="nx-title">Danh sách Tasks</h1>
        <p class="nx-sub">Tìm kiếm, lọc, thao tác hàng loạt. UI tối ưu cho bảng dữ liệu và thao tác nhanh.</p>
      </div>
    </div>

    <div class="nx-top__right">
      <a class="nx-btn nx-btn--create" href="{{ route('tasks.create') }}">
        <span class="nx-ico" aria-hidden="true">＋</span>
        <span>Tạo Task</span>
      </a>

      {{-- Nếu route kanban khác tên, đổi lại đúng route --}}
      <a class="nx-btn nx-btn--kanban" href="{{ route('kanban') ?? '#' }}">
        <span class="nx-ico" aria-hidden="true">▦</span>
        <span>Kanban</span>
      </a>
    </div>
  </header>

  {{-- Filter + Actions --}}
  <div class="nx-shell">
    <div class="nx-filter">
      <form class="nx-filter__grid" method="GET" action="{{ route('tasks.index') }}">
        <div class="nx-field">
          <label class="nx-label">Tìm theo tiêu đề</label>
          <div class="nx-inputwrap">
            <span class="nx-prefix" aria-hidden="true">⌕</span>
            <input class="nx-input" name="q" value="{{ $q }}" placeholder="Nhập từ khóa...">
          </div>
          <div class="nx-help">Gợi ý: nhập 1–2 từ khoá ngắn để lọc nhanh.</div>
        </div>

        <div class="nx-field">
          <label class="nx-label">Lọc trạng thái</label>
          <div class="nx-inputwrap">
            <span class="nx-prefix" aria-hidden="true">⛭</span>
            <select class="form-select" name="status" id="statusSelect">
              <option value="">-- Tất cả --</option>
              @foreach($statuses as $st)
                <option value="{{ $st }}" @selected($st === $status)>{{ $st }}</option>
              @endforeach
            </select>
          </div>
          <div class="nx-help">Chọn trạng thái để xem theo tiến độ.</div>
        </div>

        <div class="nx-filter__buttons">
          <button class="nx-btn nx-btn--apply" type="submit">
            <span class="nx-ico" aria-hidden="true">✓</span>
            <span>Áp dụng</span>
          </button>

          <a class="nx-btn nx-btn--reset" href="{{ route('tasks.index') }}">
            <span class="nx-ico" aria-hidden="true">↺</span>
            <span>Reset</span>
          </a>
        </div>
      </form>

      <div class="nx-bulk">
        {{-- Bulk delete selected --}}
        <form id="bulkForm" method="POST" action="{{ route('tasks.bulkDeleteSelected') }}" class="nx-bulk__item">
          @csrf
          @method('DELETE')
          <div id="bulkHidden"></div>

          <button type="submit"
                  class="nx-btn nx-btn--danger"
                  id="btnDeleteSelected"
                  disabled
                  onclick="return confirm('Xóa các task đã chọn?')">
            <span class="nx-ico" aria-hidden="true">✕</span>
            <span>Xóa đã chọn</span>
          </button>
        </form>

        {{-- Delete all filtered --}}
        <form method="POST" action="{{ route('tasks.deleteAllFiltered') }}" class="nx-bulk__item"
              onsubmit="return confirm('Xóa TẤT CẢ task theo bộ lọc hiện tại? Hành động không thể hoàn tác.')">
          @csrf
          @method('DELETE')
          <input type="hidden" name="q" value="{{ $q }}">
          <input type="hidden" name="status" value="{{ $status }}">

          <button type="submit" class="nx-btn nx-btn--hot">
            <span class="nx-ico" aria-hidden="true">🔥</span>
            <span>Xóa tất cả (theo bộ lọc)</span>
          </button>
        </form>

        <div class="nx-bulk__note">
          Mẹo: tick checkbox để bật “Xóa đã chọn”.
        </div>
      </div>
    </div>

    {{-- Table --}}
    <div class="nx-table">
      <div class="nx-table__wrap">
        <table class="nx-table__tbl" role="table">
          <thead>
            <tr>
              <th class="nx-col-check">
                <input type="checkbox" id="checkAll" class="nx-check" aria-label="Chọn tất cả">
              </th>
              <th>Task</th>
              <th class="nx-col-date">Ngày</th>
              <th class="nx-col-time">Giờ</th>
              <th class="nx-col-dur">Thời lượng</th>
              <th class="nx-col-status">Status</th>
              <th class="nx-col-pri">Priority</th>
              <th class="nx-col-act">Hành động</th>
            </tr>
          </thead>

          <tbody>
            @forelse($tasks as $t)
              <tr>
                <td class="nx-col-check">
                  <input type="checkbox" value="{{ $t->id }}" class="row-check nx-check" aria-label="Chọn task {{ $t->id }}">
                </td>

                <td class="nx-taskcell">
                  <div class="nx-taskcell__main">
                    <div class="nx-taskcell__title" title="{{ $t->title }}">{{ $t->title }}</div>
                    <div class="nx-taskcell__meta">
                      <span class="nx-mini">#{{ $t->id }}</span>
                      <span class="nx-dot">•</span>
                      <span>{{ $t->scheduled_date->format('d/m/Y') }}</span>
                      <span class="nx-dot">•</span>
                      <span>{{ substr($t->scheduled_time,0,5) }}</span>
                    </div>
                  </div>
                </td>

                <td class="nx-col-date nx-hide-md">{{ $t->scheduled_date->format('d/m/Y') }}</td>
                <td class="nx-col-time nx-hide-md">{{ substr($t->scheduled_time,0,5) }}</td>

                <td class="nx-col-dur">
                  <span class="nx-pill nx-pill--dur">{{ $t->duration_minutes }}'</span>
                </td>

                <td class="nx-col-status">
                  {{-- giữ logic badge class của bạn, chỉ “neo” lại style --}}
                  <span class="badge status-badge {{ $t->status_badge_class }} nx-badge">
                    {{ $t->status }}
                  </span>
                </td>

                <td class="nx-col-pri">
                  <span class="nx-pill nx-pill--pri">{{ $t->priority ?? '-' }}</span>
                </td>

                <td class="nx-col-act">
                  <div class="nx-act">
                    <a class="nx-btn nx-btn--edit" href="{{ route('tasks.edit', $t) }}">
                      <span class="nx-ico" aria-hidden="true">✎</span>
                      <span>Sửa</span>
                    </a>

                    <form method="POST" action="{{ route('tasks.destroy', $t) }}"
                          onsubmit="return confirm('Xóa task này?')">
                      @csrf
                      @method('DELETE')
                      <button class="nx-btn nx-btn--del" type="submit">
                        <span class="nx-ico" aria-hidden="true">🗑</span>
                        <span>Xóa</span>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="nx-empty">
                  Không có dữ liệu. Hãy tạo task mới hoặc điều chỉnh bộ lọc.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="nx-pager">
        {{ $tasks->links() }}
      </div>
    </div>
  </div>

</section>
@endsection

@section('scripts')
<script>
(function () {
  const checkAll = document.getElementById('checkAll');
  const btn = document.getElementById('btnDeleteSelected');
  const bulkHidden = document.getElementById('bulkHidden');

  function rowChecks() {
    return Array.from(document.querySelectorAll('.row-check'));
  }

  function refreshBtn() {
    const anyChecked = rowChecks().some(c => c.checked);
    btn.disabled = !anyChecked;
  }

  function syncHiddenInputs() {
    bulkHidden.innerHTML = '';
    rowChecks().filter(c => c.checked).forEach(c => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'ids[]';
      input.value = c.value;
      bulkHidden.appendChild(input);
    });
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

  document.getElementById('bulkForm')?.addEventListener('submit', () => {
    syncHiddenInputs();
  });

  refreshBtn();
})();
</script>
@push('page_scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('statusSelect');
  if (!el) return;

  new TomSelect(el, {
    create: false,
    allowEmptyOption: true,
    placeholder: '-- Tất cả --',
    dropdownParent: 'body'
  });
});
</script>
@endpush
@endsection
