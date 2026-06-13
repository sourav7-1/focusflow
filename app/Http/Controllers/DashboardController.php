<?php

namespace App\Http\Controllers;

use App\Models\StudySession;
use App\Models\Goal; // Added: Import Goal model
use Illuminate\Support\Facades\Auth; // Added: Import Auth facade to resolve 'Undefined method id' warning
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Get all sessions
        $sessions = StudySession::where('user_id', $userId)
            ->latest()
            ->get();

        // 2. Check for active session
        $activeSession = StudySession::where('user_id', $userId)
            ->whereNull('end_time')
            ->first();

        // 3. Today's study minutes
        $todayMinutes = StudySession::where('user_id', $userId)
            ->whereDate('date', today())
            ->sum('duration');

        // 4. Weekly study minutes
        $weeklyMinutes = StudySession::where('user_id', $userId)
            ->whereBetween('date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->sum('duration');

        // 5. Daily Goal and Goal Percentage
        $dailyGoal = Goal::where('user_id', $userId)->first()?->daily_goal ?? 240; // Default 240 mins
        $goalPercentage = $dailyGoal > 0 ? min(100, round(($todayMinutes / $dailyGoal) * 100)) : 0;

        // 6. Streak calculation
        $dates = StudySession::where('user_id', $userId)
            ->whereNotNull('duration')
            ->where('duration', '>', 0)
            ->pluck('date')
            ->unique()
            ->sortDesc()
            ->values();

        $streak = 0;
        if ($dates->isNotEmpty()) {
            if ($dates->contains(today()->toDateString())) {
                $streak = 1;
                $checkDate = today()->subDay();
                while ($dates->contains($checkDate->toDateString())) {
                    $streak++;
                    $checkDate->subDay();
                }
            } elseif ($dates->contains(today()->subDay()->toDateString())) {
                $streak = 1;
                $checkDate = today()->subDays(2);
                while ($dates->contains($checkDate->toDateString())) {
                    $streak++;
                    $checkDate->subDay();
                }
            }
        }

        // 7. Last 7 days chart data
        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i)->toDateString();
            $chartLabels[] = today()->subDays($i)->format('D'); // Mon, Tue...
            $chartValues[] = StudySession::where('user_id', $userId)
                ->whereDate('date', $date)
                ->sum('duration');
        }

        // 8. Total study minutes for Achievements
        $totalMinutes = StudySession::where('user_id', $userId)->sum('duration');

        return view('dashboard', compact(
            'sessions',
            'activeSession',
            'todayMinutes',
            'weeklyMinutes',
            'dailyGoal',
            'goalPercentage',
            'streak',
            'chartLabels',
            'chartValues',
            'totalMinutes'
        ));
    }
}