@extends('layouts.app')

@section('title', 'Quản lý tiến độ')

@php
  use App\Models\Task;
@endphp

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
  <h1 class="h4 mb-0">Quản lý tiến độ</h1>
  <a class="btn btn-primary btn-sm" href="{{ route('tasks.create') }}">+ Tạo Task</a>
</div>

<div class="row g-3">
  @foreach($statuses as $st)
    @php $col = $grouped[$st] ?? collect(); @endphp
    <div class="col-12 col-lg-3">
      <div class="card h-100">
        <div class="card-header fw-semibold">
          {{ $st }} <span class="text-muted">({{ $col->count() }})</span>
        </div>

        <div class="card-body vstack gap-2">
          @forelse($col as $t)
            <div class="p-2 bg-white border rounded">
              <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="fw-semibold text-truncate" title="{{ $t->title }}">
                  {{ $t->title }}
                </div>
                <span class="badge status-badge {{ $t->status_badge_class }}">
                  {{ $t->status }}
                </span>
              </div>

              <div class="small text-muted">
                {{ $t->scheduled_date->format('d/m') }}
                • {{ substr($t->scheduled_time, 0, 5) }}
              </div>

              <div class="mt-2 d-flex gap-2">
                <a class="btn btn-outline-secondary btn-sm"
                   href="{{ route('tasks.edit', $t) }}">
                  Sửa
                </a>
              </div>
            </div>
          @empty
            <div class="text-muted">Trống.</div>
          @endforelse
        </div>
      </div>
    </div>
  @endforeach
</div>
@endsection
