<?php

namespace App\Http\Controllers;

use App\Models\StudySession;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $sessions = StudySession::where('user_id', Auth::id())
            ->latest()
            ->get();

        $todayMinutes = StudySession::where('user_id', Auth::id())
            ->whereDate('date', today())
            ->sum('duration');

                $weeklyMinutes = StudySession::where('user_id', Auth::id())
            ->whereBetween('date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->sum('duration');

        return view('dashboard', compact(
            'sessions',
            'todayMinutes',
            'weeklyMinutes'
        ));

        
    }
}