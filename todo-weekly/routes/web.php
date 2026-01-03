<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes();

// Laravel UI mặc định redirect sau login/register → /home
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->middleware('auth')->name('home');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // ✅ ĐẶT CÁC ROUTE ĐẶC BIỆT TRƯỚC resource (tránh bị /tasks/{task} nuốt)
    Route::delete('/tasks/bulk-delete', [TaskController::class, 'bulkDeleteSelected'])
        ->name('tasks.bulkDeleteSelected');

    Route::delete('/tasks/delete-all', [TaskController::class, 'deleteAllFiltered'])
        ->name('tasks.deleteAllFiltered');

    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
        ->whereNumber('task')
        ->name('tasks.status');

    // ✅ Resource để sau + khóa {task} chỉ nhận số
    Route::resource('tasks', TaskController::class)
        ->whereNumber('task')
        ->except(['show']);

    Route::get('/kanban', [TaskController::class, 'kanban'])
        ->name('kanban');
});
