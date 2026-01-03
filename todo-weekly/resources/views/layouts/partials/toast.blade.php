@php
  /**
   * CHUẨN TOAST KEY:
   * - toast        : nội dung
   * - toast_type   : success|danger|warning|info
   * - toast_title  : tiêu đề
   *
   * Dùng session()->pull() để đọc xong là xoá, tránh toast bị "dính" sang trang khác.
   */
  $toastMsg   = session()->pull('toast');
  $toastType  = session()->pull('toast_type', 'success');
  $toastTitle = session()->pull('toast_title', 'Thông báo');
@endphp

@if(!empty($toastMsg))
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
    <div id="appToast"
         class="toast align-items-center text-bg-{{ $toastType }} border-0"
         role="alert" aria-live="assertive" aria-atomic="true"
         data-bs-delay="2500">
      <div class="d-flex">
        <div class="toast-body">
          <div class="fw-semibold mb-1">{{ $toastTitle }}</div>
          <div>{{ $toastMsg }}</div>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto"
                data-bs-dismiss="toast" aria-label="Đóng"></button>
      </div>
    </div>
  </div>
@endif
