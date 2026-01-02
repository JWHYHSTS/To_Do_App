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
        $daysAhead = (int) $this->option('days');
        if ($daysAhead < 1) $daysAhead = 14;

        $today = Carbon::today();
        $toDate = $today->copy()->addDays($daysAhead);

        $templates = Task::query()
            ->where('is_template', true)
            ->whereIn('recurrence', ['daily','weekly'])
            ->get();

        $created = 0;

        foreach ($templates as $tpl) {
            $start = Carbon::parse($tpl->scheduled_date)->startOfDay();
            $until = $tpl->recurrence_until ? Carbon::parse($tpl->recurrence_until)->endOfDay() : null;

            // cửa sổ generate: từ ngày mai hoặc từ last_generated_for+1
            $last = $tpl->last_generated_for ? Carbon::parse($tpl->last_generated_for)->startOfDay() : $start->copy();
            $cursor = $last->copy()->addDay();

            while ($cursor->lte($toDate)) {
                if ($until && $cursor->gt($until)) break;

                $shouldCreate = false;

                if ($tpl->recurrence === 'daily') {
                    $shouldCreate = true;
                }

                if ($tpl->recurrence === 'weekly') {
                    // weekly: trùng thứ với scheduled_date gốc
                    $shouldCreate = ($cursor->dayOfWeekIso === $start->dayOfWeekIso);
                }

                if ($shouldCreate) {
                    $exists = Task::query()
                        ->where('is_template', false)
                        ->where('series_id', $tpl->series_id)
                        ->whereDate('scheduled_date', $cursor->toDateString())
                        ->where('scheduled_time', $tpl->scheduled_time)
                        ->exists();

                    if (!$exists) {
                        DB::transaction(function () use ($tpl, $cursor, &$created) {
                            Task::create([
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

                            $tpl->last_generated_for = $cursor->toDateString();
                            $tpl->save();

                            $created++;
                        });
                    } else {
                        // đã có rồi thì vẫn cập nhật last_generated_for để không quét mãi cùng ngày
                        $tpl->last_generated_for = $cursor->toDateString();
                        $tpl->save();
                    }
                }

                $cursor->addDay();
            }
        }

        $this->info("Generated {$created} task(s).");
        return self::SUCCESS;
    }
}
