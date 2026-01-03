<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateRecurringTasks extends Command
{
    protected $signature = 'tasks:generate-recurring {--days=14 : Number of days ahead to generate}';
    protected $description = 'Generate recurring task occurrences from templates (daily/weekly).';

    public function handle(): int
    {
        $daysAhead = max(1, (int) $this->option('days'));

        // Nên dùng timezone app để khớp dashboard
        $tz = config('app.timezone', 'Asia/Ho_Chi_Minh');

        $today  = Carbon::now($tz)->startOfDay();
        $toDate = $today->copy()->addDays($daysAhead)->endOfDay();

        $templates = Task::query()
            ->where('is_template', true)
            ->whereIn('recurrence', ['daily', 'weekly'])
            ->get();

        $created = 0;

        foreach ($templates as $tpl) {
            $start = Carbon::parse($tpl->scheduled_date, $tz)->startOfDay();
            $until = $tpl->recurrence_until
                ? Carbon::parse($tpl->recurrence_until, $tz)->endOfDay()
                : null;

            $last = $tpl->last_generated_for
                ? Carbon::parse($tpl->last_generated_for, $tz)->startOfDay()
                : $start->copy();

            // bắt đầu từ ngày sau last_generated_for
            $cursor = $last->copy()->addDay();

            while ($cursor->lte($toDate)) {
                if ($until && $cursor->gt($until)) break;

                $shouldCreate = false;

                if ($tpl->recurrence === 'daily') {
                    $shouldCreate = true;
                } elseif ($tpl->recurrence === 'weekly') {
                    $shouldCreate = ($cursor->dayOfWeekIso === $start->dayOfWeekIso);
                }

                if ($shouldCreate) {
                    $exists = Task::query()
                        ->where('user_id', $tpl->user_id)          // ✅ thêm user_id để chống trùng đúng user
                        ->where('is_template', false)
                        ->where('series_id', $tpl->series_id)
                        ->whereDate('scheduled_date', $cursor->toDateString())
                        ->where('scheduled_time', $tpl->scheduled_time)
                        ->exists();

                    if (!$exists) {
                        DB::transaction(function () use ($tpl, $cursor, &$created) {
                            Task::create([
                                // ✅ BẮT BUỘC: user_id
                                'user_id' => $tpl->user_id,

                                'title' => $tpl->title,
                                'description' => $tpl->description,
                                'status' => $tpl->status,
                                'priority' => $tpl->priority,
                                'duration_minutes' => $tpl->duration_minutes,
                                'scheduled_date' => $cursor->toDateString(),
                                'scheduled_time' => $tpl->scheduled_time,

                                'is_template' => false,
                                'series_id' => $tpl->series_id,
                                'recurrence' => 'none',
                                'recurrence_until' => null,
                                'last_generated_for' => null,
                            ]);

                            $created++;
                        });
                    }

                    // Cập nhật tiến độ generate để lần sau không quét lại
                    $tpl->last_generated_for = $cursor->toDateString();
                    $tpl->save();
                }

                $cursor->addDay();
            }
        }

        $this->info("Generated {$created} task(s).");
        return self::SUCCESS;
    }
}
