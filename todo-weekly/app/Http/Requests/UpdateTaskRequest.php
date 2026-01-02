<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\Task $task */
        $task = $this->route('task');
        return $task && $this->user()->can('update', $task);
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'status'           => ['required', Rule::in(Task::STATUSES)],
            'priority'         => ['nullable', Rule::in(Task::PRIORITIES)],
            'scheduled_date'   => ['required', 'date'],
            'scheduled_time'   => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
        ];
    }
}
