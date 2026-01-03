<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Weekly To-Do')</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">


  {{-- CSS global --}}
  <link rel="stylesheet"
        href="{{ asset('css/todo.css') }}?v={{ file_exists(public_path('css/todo.css')) ? filemtime(public_path('css/todo.css')) : time() }}">

  {{-- CSS riêng từng trang --}}
  @stack('styles')
</head>

<body class="bg-light">
  @include('layouts.partials.sidebar')

  <div class="app-shell">
    <main class="app-main">
      <div class="container-fluid py-3">
        <div class="container">
          @include('layouts.partials.toast')
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

  {{-- Mobile offcanvas --}}
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

  {{-- Nếu page có @section('scripts') --}}
  @yield('scripts')

  {{-- Nếu partial/page có @push('scripts') --}}
  @stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

@stack('page_scripts')

</body>
</html>
