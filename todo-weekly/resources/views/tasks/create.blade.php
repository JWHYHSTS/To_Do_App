@extends('layouts.app')

@section('title', 'Tạo Task')

@push('styles')
<link rel="stylesheet"
      href="{{ asset('css/task-form-wow.css') }}?v={{ file_exists(public_path('css/task-form-wow.css')) ? filemtime(public_path('css/task-form-wow.css')) : time() }}">
@endpush

@section('content')
  <div class="task-page">

    <form method="POST" action="{{ route('tasks.store') }}">
      @csrf
      @include('tasks._form', [
        'task' => $task,
        'statuses' => $statuses,
        'priorities' => $priorities,
        'submitLabel' => 'Tạo mới'
      ])
    </form>
  </div>
@endsection
