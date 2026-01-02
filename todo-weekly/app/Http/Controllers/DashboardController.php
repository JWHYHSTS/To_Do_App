<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Weekly Planner: week start is Monday (Thứ 2).
     * Query param:
     * - week=YYYY-MM-DD (any date inside the desired week)
     */
    public function index(Request $request)
    {
        $tz = config('app.timezone', 'Asia/Ho_Chi_Minh');

        $weekParam = $request->string('week')->toString();
        $anchorDate = $weekParam
            ? CarbonImmutable::parse($weekParam, $tz)
            : CarbonImmutable::now($tz);

        // IMPORTANT: Start of week = Monday
        $startOfWeek = $anchorDate->startOfWeek(CarbonImmutable::MONDAY);
        $endOfWeek   = $startOfWeek->addDays(6);

        $tasks = Task::query()
            ->where('user_id', Auth::id())
            ->whereBetween('scheduled_date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->get();

        // Group tasks by date (Y-m-d)
        $tasksByDate = $tasks->groupBy(fn (Task $t) => $t->scheduled_date->format('Y-m-d'));

        // Planner hours (mobile + desktop). You can expand to 0..23 if you want.
        $hours = range(0, 23);

        // Build array of 7 days for rendering
        $days = collect(range(0, 6))->map(function ($i) use ($startOfWeek) {
            $d = $startOfWeek->addDays($i);
            return [
                'date' => $d,
                'key'  => $d->format('Y-m-d'),
                'label'=> $d->isoFormat('dddd'), // localized if you set locale
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
}
