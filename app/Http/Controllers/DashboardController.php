<?php

namespace App\Http\Controllers;

use App\Models\StudySession;
use Illuminate\Support\Facades\Auth; // Added: Import Auth facade to resolve 'Undefined method id' warning

class DashboardController extends Controller
{
    public function index()
    {
        // Changed: Replaced auth()->id() with Auth::id() to avoid IDE/static analysis warnings
        $sessions = StudySession::where('user_id', Auth::id())
            ->latest()
            ->get();

        // Changed: Replaced auth()->id() with Auth::id() to avoid IDE/static analysis warnings
        $todayMinutes = StudySession::where('user_id', Auth::id())
            ->whereDate('date', today())
            ->sum('duration');

        // Added: Calculated weekly study minutes for the dashboard
        $weeklyMinutes = StudySession::where('user_id', Auth::id())
            ->whereBetween('date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->sum('duration');

        // Changed: Passed the newly calculated $weeklyMinutes to the view
        return view('dashboard', compact(
            'sessions',
            'todayMinutes',
            'weeklyMinutes'
        ));

        
    }
}