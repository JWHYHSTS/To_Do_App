<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Weekly To-Do')</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- Cache-bust CSS để chắc chắn không dùng bản cũ --}}
  <link rel="stylesheet" href="{{ asset('css/todo.css') }}?v={{ filemtime(public_path('css/todo.css')) }}">
</head>

<body class="bg-light">
  {{-- Sidebar (desktop + mobile offcanvas) --}}
  @include('layouts.partials.sidebar')

  {{-- Main content --}}
  <div class="app-shell">
    <main class="app-main">
      <div class="container-fluid py-3">
        <div class="container">
          @include('layouts.partials.toast')

          @if($errors->any())
            <div class="alert alert-danger">
              <div class="fw-semibold mb-1">Có lỗi dữ liệu:</div>
              <ul class="mb-0">
                @foreach($errors->all() as $e)
                  <li>{{ $e }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @yield('content')
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Toast auto show --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const el = document.getElementById('appToast');
      if (el && window.bootstrap) new bootstrap.Toast(el).show();
    });
  </script>

  {{-- Desktop sidebar collapse state --}}
  <script>
    (function () {
      const KEY = 'sidebar_collapsed';
      const body = document.body;

      function apply() {
        const collapsed = localStorage.getItem(KEY) === '1';
        body.classList.toggle('sidebar-collapsed', collapsed);
      }

      window.toggleSidebarDock = function () {
        const next = !body.classList.contains('sidebar-collapsed');
        localStorage.setItem(KEY, next ? '1' : '0');
        apply();
      }

      apply();
    })();
  </script>

  {{-- Mobile offcanvas: click link -> close offcanvas cleanly --}}
  <script>
    document.addEventListener('click', (e) => {
      const link = e.target.closest('a.mobile-nav-link');
      if (!link) return;

      const offEl = document.getElementById('mobileSidebar');
      if (!offEl || !window.bootstrap) return;

      const inst = bootstrap.Offcanvas.getInstance(offEl);
      if (inst) inst.hide();
    });

    window.addEventListener('pageshow', () => {
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
    });
  </script>

  @yield('scripts')
</body>
</html>
