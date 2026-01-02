<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Trang gốc → luôn đưa về dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Auth routes (Login / Register / Logout / Forgot password)
Auth::routes();

/*
| FIX QUAN TRỌNG:
| Laravel UI mặc định redirect Login/Register → /home
| Nhưng project của bạn dùng /dashboard
| => Tạo route /home để redirect về dashboard
*/
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->middleware('auth')->name('home');

// Các route yêu cầu đăng nhập
Route::middleware('auth')->group(function () {

    // Dashboard (Weekly Planner)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Tasks CRUD
    Route::resource('tasks', TaskController::class)
        ->except(['show']);

    // Bulk delete
    Route::delete('/tasks/bulk-delete',
        [TaskController::class, 'bulkDeleteSelected']
    )->name('tasks.bulkDeleteSelected');

    Route::delete('/tasks/delete-all',
        [TaskController::class, 'deleteAllFiltered']
    )->name('tasks.deleteAllFiltered');

    // AJAX quick status update
    Route::patch('/tasks/{task}/status',
        [TaskController::class, 'updateStatus']
    )->name('tasks.status');

    // Kanban / Quản lý tiến độ
    Route::get('/kanban',
        [TaskController::class, 'kanban']
    )->name('kanban');
});
