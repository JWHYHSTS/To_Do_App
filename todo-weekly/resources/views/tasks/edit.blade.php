@extends('layouts.app')

@section('title', 'Sửa Task')

@section('content')
<h1 class="h4 mb-3">Sửa Task</h1>

<div class="card task-form-card">
  <div class="card-body">
    <form method="POST" action="{{ route('tasks.update', $task) }}">
      @csrf @method('PUT')
      @include('tasks._form', ['task' => $task, 'submitLabel' => 'Lưu thay đổi'])
    </form>
  </div>
</div>
@endsection
