@php
  $toastType  = session('toast_type', 'success'); // success|danger|warning|info
  $toastMsg   = session('toast') ?? session('success') ?? session('error');
  $toastTitle = session('toast_title', 'Thông báo');
@endphp

@if($toastMsg)
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
                data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>
@endif
