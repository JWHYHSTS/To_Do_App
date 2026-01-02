<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $tz = config('app.timezone', 'Asia/Ho_Chi_Minh');

        // Create demo user if none exists
        $user = User::query()->first() ?? User::query()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('12345678'),
        ]);

        $now = CarbonImmutable::now($tz);
        $startOfWeek = $now->startOfWeek(CarbonImmutable::MONDAY);

        $titles = [
            'Ôn bài Laravel', 'Thiết kế UI Weekly Planner', 'Viết báo cáo', 'Code Task Policy',
            'Review pull request', 'Refactor controller', 'Viết test case', 'Chuẩn hóa migration',
            'Tối ưu query', 'Chuẩn bị demo', 'Đọc tài liệu', 'Sửa bug UI mobile',
        ];

        $statuses = Task::STATUSES;
        $priorities = Task::PRIORITIES;
        $hours = [7, 8, 9, 10, 13, 14, 15, 16, 19, 20, 21];

        Task::query()->where('user_id', $user->id)->delete();

        for ($i = 0; $i < 20; $i++) {
            $dayOffset = $i % 7;
            $date = $startOfWeek->addDays($dayOffset)->toDateString();

            $hour = $hours[array_rand($hours)];
            $minute = (rand(0, 1) === 1) ? '00' : '30';

            $status = $statuses[array_rand($statuses)];
            $duration = [30, 60, 90, 120][array_rand([0,1,2,3])];

            $task = Task::create([
                'user_id' => $user->id,
                'title' => $titles[array_rand($titles)] . ' #' . ($i + 1),
                'description' => (rand(0, 1) ? 'Ghi chú ngắn cho task demo.' : null),
                'status' => Task::STATUS_TODO, // set then apply transition below
                'priority' => $priorities[array_rand($priorities)],
                'scheduled_date' => $date,
                'scheduled_time' => sprintf('%02d:%s', $hour, $minute),
                'duration_minutes' => $duration,
            ]);

            // Apply status transition rules and save
            $task->applyStatusTransition($status);
            $task->save();
        }
    }
}
