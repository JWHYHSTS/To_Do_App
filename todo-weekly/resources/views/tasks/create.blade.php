@extends('layouts.app')

@section('title', 'Tạo Task')

@section('content')
<h1 class="h4 mb-3">Tạo Task</h1>

<div class="card task-form-card">
  <div class="card-body">
    <form method="POST" action="{{ route('tasks.store') }}">
      @csrf
      @include('tasks._form', ['task' => $task, 'submitLabel' => 'Tạo mới'])
    </form>
  </div>
</div>
@endsection
