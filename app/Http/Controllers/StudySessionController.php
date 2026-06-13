<?php

namespace App\Http\Controllers;

use App\Models\StudySession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StudySessionController extends Controller
{
    // Start Study
    public function start()
    {
        $session = StudySession::create([
            'user_id' => Auth::id(),
            'date' => now()->toDateString(),
            'start_time' => now(),
        ]);

        return back()->with('success', 'Study Started!');
    }

    // Stop Study
    public function stop($id)
    {
        $session = StudySession::findOrFail($id);

        $session->end_time = now();

        $start = Carbon::parse($session->start_time);
        $end = Carbon::parse($session->end_time);

        $session->duration = $end->diffInMinutes($start);

        $session->save();

        return back()->with('success', 'Study Stopped!');
    }
}