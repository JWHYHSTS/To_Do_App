<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\CarbonImmutable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $tz = config('app.timezone', 'Asia/Ho_Chi_Minh');

        $weekParam = $request->string('week')->toString();
        $anchorDate = $weekParam
            ? CarbonImmutable::parse($weekParam, $tz)
            : CarbonImmutable::now($tz);

        $startOfWeek = $anchorDate->startOfWeek(CarbonImmutable::MONDAY);
        $endOfWeek   = $startOfWeek->addDays(6);

        // ✅ Auto-generate: để bấm "Tuần sau" là có dữ liệu
        $generateFrom = Carbon::parse($startOfWeek->toDateString(), $tz)->subDay()->startOfDay();
        $generateTo   = Carbon::parse($endOfWeek->toDateString(), $tz)->addDays(21)->endOfDay(); // 3 tuần tới

        $this->generateRecurringForUser(Auth::id(), $tz, $generateFrom, $generateTo);

        $tasks = Task::query()
            ->where('user_id', Auth::id())
            ->where('is_template', false)
            ->whereBetween('scheduled_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->get();

        $tasksByDate = $tasks->groupBy(fn (Task $t) => $t->scheduled_date->format('Y-m-d'));

        $hours = range(0, 23);

        $days = collect(range(0, 6))->map(function ($i) use ($startOfWeek, $tz) {
            $d = $startOfWeek->addDays($i);
            return [
                'date'  => $d,
                'key'   => $d->format('Y-m-d'),
                'label' => $d->setTimezone($tz)->isoFormat('dddd'),
            ];
        });

        return view('dashboard', [
            'startOfWeek' => $startOfWeek,
            'endOfWeek'   => $endOfWeek,
            'days'        => $days,
            'hours'       => $hours,
            'tasksByDate' => $tasksByDate,
            'weekParam'   => $anchorDate->toDateString(),
        ]);
    }

    private function generateRecurringForUser(int $userId, string $tz, Carbon $from, Carbon $to): void
    {
        $templates = Task::query()
            ->where('user_id', $userId)
            ->where('is_template', true)
            ->whereIn('recurrence', ['daily', 'weekly'])
            ->get();

        foreach ($templates as $tpl) {
            $start = Carbon::parse($tpl->scheduled_date, $tz)->startOfDay();
            $until = $tpl->recurrence_until
                ? Carbon::parse($tpl->recurrence_until, $tz)->endOfDay()
                : null;

            $tplTime = Task::normalizeTime((string) $tpl->scheduled_time);

            $last = $tpl->last_generated_for
                ? Carbon::parse($tpl->last_generated_for, $tz)->startOfDay()
                : $start->copy();

            $cursor = $last->copy()->addDay();

            if ($cursor->lt($from)) {
                $cursor = $from->copy();
            }

            while ($cursor->lte($to)) {
                if ($until && $cursor->gt($until)) break;

                $shouldCreate = false;

                if ($tpl->recurrence === 'daily') {
                    $shouldCreate = true;
                } elseif ($tpl->recurrence === 'weekly') {
                    $shouldCreate = ($cursor->dayOfWeekIso === $start->dayOfWeekIso);
                }

                if ($shouldCreate) {
                    // ✅ chống trùng chuẩn bằng whereTime
                    $exists = Task::query()
                        ->where('user_id', $tpl->user_id)
                        ->where('is_template', false)
                        ->where('series_id', $tpl->series_id)
                        ->whereDate('scheduled_date', $cursor->toDateString())
                        ->whereTime('scheduled_time', $tplTime)
                        ->exists();

                    if (!$exists) {
                        DB::transaction(function () use ($tpl, $cursor, $tplTime) {
                            Task::create([
                                'user_id'          => $tpl->user_id,
                                'title'            => $tpl->title,
                                'description'      => $tpl->description,
                                'status'           => $tpl->status,
                                'priority'         => $tpl->priority,
                                'duration_minutes' => $tpl->duration_minutes,
                                'scheduled_date'   => $cursor->toDateString(),
                                'scheduled_time'   => $tplTime,

                                'is_template'       => false,
                                'series_id'         => $tpl->series_id,
                                'recurrence'        => 'none',
                                'recurrence_until'  => null,
                                'last_generated_for'=> null,
                            ]);
                        });
                    }
                }

                // ✅ cập nhật tiến độ + normalize luôn time của template
                $tpl->last_generated_for = $cursor->toDateString();
                $tpl->scheduled_time = $tplTime;
                $tpl->save();

                $cursor->addDay();
            }
        }
    }
}
