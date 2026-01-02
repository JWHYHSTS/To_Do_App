@php
  $links = [
    ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => '🏠'],
    ['label' => 'Tasks',     'route' => 'tasks.index', 'icon' => '📝'],
    ['label' => 'Tạo Task',  'route' => 'tasks.create', 'icon' => '➕'],
    ['label' => 'Quản lý tiến độ', 'route' => 'kanban', 'icon' => '📊'],
  ];
@endphp

{{-- Mobile top mini bar (only on small screens) --}}
<div class="mobile-top d-lg-none">
  <button class="btn btn-dark btn-sm"
          type="button"
          data-bs-toggle="offcanvas"
          data-bs-target="#mobileSidebar"
          aria-controls="mobileSidebar">
    ☰ Menu
  </button>

  <div class="d-flex align-items-center gap-2">
    <span class="text-muted small">{{ Auth::user()->name ?? 'User' }}</span>
    <form method="POST" action="{{ route('logout') }}" class="m-0">
      @csrf
      <button class="btn btn-outline-danger btn-sm" type="submit">Đăng xuất</button>
    </form>
  </div>
</div>

{{-- Desktop dock sidebar --}}
<aside class="sidebar-dock d-none d-lg-flex">
  <div class="sidebar-inner">
    <div class="sidebar-brand">
      <div class="brand-title sidebar-text">Weekly To-Do</div>

      <button type="button"
              class="sidebar-toggle"
              onclick="toggleSidebarDock()"
              aria-label="Thu gọn / Mở rộng"
              title="Thu gọn / Mở rộng">
        ☰
      </button>
    </div>

    <nav class="sidebar-nav" aria-label="Main navigation">
      @foreach($links as $l)
        <a class="sidebar-link {{ request()->routeIs($l['route']) ? 'active' : '' }}"
           href="{{ route($l['route']) }}">
          <span class="sidebar-ic" aria-hidden="true">{{ $l['icon'] }}</span>
          <span class="sidebar-text">{{ $l['label'] }}</span>
        </a>
      @endforeach
    </nav>

    <div class="sidebar-footer">
      <div class="user-line sidebar-text">User: {{ Auth::user()->name ?? 'User' }}</div>

      <form method="POST" action="{{ route('logout') }}" class="m-0">
        @csrf
        <button class="btn btn-sm btn-outline-light w-100" type="submit">Đăng xuất</button>
      </form>
    </div>
  </div>
</aside>

{{-- Mobile offcanvas sidebar --}}
<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="mobileSidebarLabel">Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body">
    <div class="d-grid gap-2">
      @foreach($links as $l)
        <a class="btn btn-outline-dark text-start mobile-nav-link {{ request()->routeIs($l['route']) ? 'active' : '' }}"
           href="{{ route($l['route']) }}">
          {{ $l['label'] }}
        </a>
      @endforeach
    </div>

    <hr class="my-3">

    <div class="small text-muted">User: {{ Auth::user()->name ?? 'User' }}</div>
    <form method="POST" action="{{ route('logout') }}" class="mt-2">
      @csrf
      <button class="btn btn-outline-danger w-100" type="submit">Đăng xuất</button>
    </form>
  </div>
</div>
