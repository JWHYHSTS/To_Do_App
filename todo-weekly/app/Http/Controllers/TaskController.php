<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    // (Không đúng chỗ - fillable nên nằm trong Model Task)
    // Giữ lại để bạn không bị đụng logic hiện tại.
    protected $fillable = [
        'title','description','status','priority','duration_minutes',
        'scheduled_date','scheduled_time',
        'is_template','series_id','recurrence','recurrence_until','last_generated_for',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    // GET /tasks
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $status = $request->input('status');

        $query = Task::query()
            ->where('user_id', Auth::id())
            ->where('is_template', false)
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%");
            })
            ->when($status && in_array($status, Task::STATUSES, true), function ($qq) use ($status) {
                $qq->where('status', $status);
            })
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time');

        $tasks = $query->paginate(10)->withQueryString();

        return view('tasks.index', [
            'tasks' => $tasks,
            'q' => $q,
            'status' => $status,
            'statuses' => Task::STATUSES,
        ]);
    }

    // GET /tasks/create
    public function create(Request $request)
    {
        $tz = config('app.timezone', 'Asia/Ho_Chi_Minh');
        $prefillDate = $request->input('date') ?: now($tz)->toDateString();
        $prefillTime = $request->input('time') ?: now($tz)->format('H:i');

        $task = new Task([
            'status' => Task::STATUS_TODO,
            'scheduled_date' => $prefillDate,
            'scheduled_time' => $prefillTime,
            'duration_minutes' => 60,
            'priority' => 'medium',
            'recurrence' => 'none',
        ]);

        return view('tasks.create', [
            'task' => $task,
            'statuses' => Task::STATUSES,
            'priorities' => Task::PRIORITIES,
        ]);
    }

    // POST /tasks
    public function store(Request $request)
{
    $data = $request->validate([
        'title'            => ['required','string','max:255'],
        'description'      => ['nullable','string'],
        'status'           => ['required','in:' . implode(',', Task::STATUSES)],
        'priority'         => ['nullable','string'],
        'duration_minutes' => ['required','integer','min:15','max:480'],
        'scheduled_date'   => ['required','date'],
        'scheduled_time'   => ['required'],
        'recurrence'       => ['nullable','in:none,daily,weekly'],
        'recurrence_until' => ['nullable','date'],
    ]);

    $userId = Auth::id();

    // ✅ chuẩn hóa time HH:MM để đồng bộ DB
    $data['scheduled_time'] = Task::normalizeTime((string) $data['scheduled_time']);

    $recurrence = $data['recurrence'] ?? 'none';
    $until = $data['recurrence_until'] ?? null;
    if (empty($until)) $until = null;

    if ($recurrence === 'none') {
        Task::create([
            ...$data,
            'user_id' => $userId,
            'is_template' => false,
            'series_id' => null,
            'recurrence' => 'none',
            'recurrence_until' => null,
            'last_generated_for' => null,
        ]);

        return redirect()->route('dashboard')
            ->with('toast', 'Đã tạo task.')
            ->with('toast_type', 'success')
            ->with('toast_title', 'Thành công');
    }

    $seriesId = (string) Str::uuid();

    // Template (KHÔNG HIỂN THỊ)
    Task::create([
        ...$data,
        'user_id' => $userId,
        'is_template' => true,
        'series_id' => $seriesId,
        'recurrence' => $recurrence,
        'recurrence_until' => $until,
        // ✅ đã có occurrence cho ngày gốc => last_generated_for = scheduled_date
        'last_generated_for' => $data['scheduled_date'],
    ]);

    // Occurrence cho ngày hiện tại (HIỂN THỊ)
    Task::create([
        ...$data,
        'user_id' => $userId,
        'is_template' => false,
        'series_id' => $seriesId,
        'recurrence' => 'none',
        'recurrence_until' => null,
        'last_generated_for' => null,
    ]);

    return redirect()->route('dashboard')
        ->with('toast', 'Đã tạo task (kèm cấu hình lặp).')
        ->with('toast_type', 'success')
        ->with('toast_title', 'Thành công');
}

    // GET /tasks/{task}/edit
    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        return view('tasks.edit', [
            'task' => $task,
            'statuses' => Task::STATUSES,
            'priorities' => Task::PRIORITIES,
        ]);
    }

    // PUT/PATCH /tasks/{task}
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $data = $request->validated();

        $task->fill($data);

        // apply transition rule AFTER fill but before save
        if (isset($data['status'])) {
            $task->applyStatusTransition($data['status']);
        }

        $task->save();

        return redirect()->route('tasks.index')
            ->with('toast', 'Đã cập nhật công việc.')
            ->with('toast_type', 'success')
            ->with('toast_title', 'Thành công');
    }

    // DELETE /tasks/{task}
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('toast', 'Đã xóa công việc.')
            ->with('toast_type', 'success')
            ->with('toast_title', 'Thành công');
    }

    /**
     * PATCH /tasks/{task}/status (AJAX quick update)
     */
    public function updateStatus(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'status' => ['required', 'in:' . implode(',', Task::STATUSES)],
            'scheduled_date' => ['nullable', 'date'],
            'scheduled_time' => ['nullable', 'date_format:H:i'],
        ]);

        if ($request->filled('scheduled_date')) {
            $task->scheduled_date = $request->input('scheduled_date');
        }
        if ($request->filled('scheduled_time')) {
            $task->scheduled_time = $request->input('scheduled_time');
        }

        $task->applyStatusTransition($request->input('status'));
        $task->save();

        return response()->json([
            'ok' => true,
            'task' => [
                'id' => $task->id,
                'status' => $task->status,
                'scheduled_date' => $task->scheduled_date->format('Y-m-d'),
                'scheduled_time' => substr((string)$task->scheduled_time, 0, 5),
            ],
        ]);
    }

    // GET /kanban
    public function kanban()
    {
        $tasks = Task::query()
            ->where('user_id', Auth::id())
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->get()
            ->groupBy('status');

        return view('tasks.kanban', [
            'grouped' => $tasks,
            'statuses' => Task::STATUSES,
        ]);
    }

    public function bulkDeleteSelected(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = $request->input('ids');

        $deleted = Task::query()
            ->where('user_id', Auth::id())
            ->whereIn('id', $ids)
            ->delete();

        return redirect()->route('tasks.index')
            ->with('toast', "Đã xóa {$deleted} task đã chọn.")
            ->with('toast_type', 'success')
            ->with('toast_title', 'Thành công');
    }

    public function deleteAllFiltered(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $status = $request->input('status');

        $query = Task::query()
            ->where('user_id', Auth::id())
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%");
            })
            ->when($status && in_array($status, Task::STATUSES, true), function ($qq) use ($status) {
                $qq->where('status', $status);
            });

        $count = (clone $query)->count();
        $deleted = $query->delete();

        return redirect()->route('tasks.index')
            ->with('toast', "Đã xóa {$deleted}/{$count} task theo bộ lọc hiện tại.")
            ->with('toast_type', 'warning')
            ->with('toast_title', 'Xóa hàng loạt');
    }
}
