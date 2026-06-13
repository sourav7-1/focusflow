<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🎯 FocusFlow Dashboard
        </h2>
    </x-slot>

    <style>
        @keyframes pulse-subtle {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1), 0 2px 4px -1px rgba(239, 68, 68, 0.06);
            }
            50% {
                transform: scale(1.01);
                box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.2), 0 4px 6px -2px rgba(239, 68, 68, 0.1);
            }
        }
        .animate-pulse-subtle {
            animation: pulse-subtle 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Pomodoro Timer */
        @keyframes timer-ring-spin {
            from { stroke-dashoffset: 440; }
        }
        .timer-ring-track {
            fill: none;
            stroke: #e5e7eb;
            stroke-width: 10;
        }
        .dark .timer-ring-track {
            stroke: #374151;
        }
        .timer-ring-progress {
            fill: none;
            stroke-width: 10;
            stroke-linecap: round;
            stroke: url(#timerGradient);
            transform-origin: center;
            transform: rotate(-90deg);
            transition: stroke-dashoffset 1s linear;
        }
        .timer-ring-progress.paused {
            animation: none !important;
        }
        @keyframes timer-glow {
            0%, 100% { filter: drop-shadow(0 0 6px rgba(99,102,241,0.4)); }
            50% { filter: drop-shadow(0 0 14px rgba(168,85,247,0.6)); }
        }
        .timer-running .timer-ring-progress {
            animation: timer-glow 2s ease-in-out infinite;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Welcome Back Banner --}}
            <div class="bg-indigo-600 text-white p-6 rounded-xl mb-8 shadow-lg">
                <h2 class="text-2xl font-bold">
                    Welcome back, {{ auth()->user()->name }} 👋
                </h2>
                <p class="mt-2 text-indigo-100">
                    Stay focused and achieve goals. Track your study sessions and stay consistent.
                </p>
            </div>

            {{-- Stats Grid (4 columns) --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <!-- Today -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white p-6 rounded-xl shadow-lg hover:-translate-y-1 hover:shadow-2xl hover:brightness-110 transform transition-all duration-300">
                    <h3 class="text-lg font-semibold opacity-90">Today</h3>
                    <p class="text-3xl font-bold mt-2">{{ $todayMinutes }}</p>
                    <p class="text-xs opacity-75 mt-1">Minutes</p>
                </div>

                <!-- Weekly -->
                <div class="bg-gradient-to-r from-green-500 to-green-700 text-white p-6 rounded-xl shadow-lg hover:-translate-y-1 hover:shadow-2xl hover:brightness-110 transform transition-all duration-300">
                    <h3 class="text-lg font-semibold opacity-90">Weekly</h3>
                    <p class="text-3xl font-bold mt-2">{{ $weeklyMinutes }}</p>
                    <p class="text-xs opacity-75 mt-1">Minutes</p>
                </div>

                <!-- Goal % -->
                <div class="bg-gradient-to-r from-orange-500 to-red-600 text-white p-6 rounded-xl shadow-lg hover:-translate-y-1 hover:shadow-2xl hover:brightness-110 transform transition-all duration-300">
                    <h3 class="text-lg font-semibold opacity-90">Goal %</h3>
                    <p class="text-3xl font-bold mt-2">{{ $goalPercentage }}%</p>
                    <p class="text-xs opacity-75 mt-1">of {{ $dailyGoal }}m goal</p>
                </div>

                <!-- Streak -->
                <div class="bg-gradient-to-r from-purple-500 to-purple-700 text-white p-6 rounded-xl shadow-lg hover:-translate-y-1 hover:shadow-2xl hover:brightness-110 transform transition-all duration-300">
                    <h3 class="text-lg font-semibold opacity-90">Streak</h3>
                    <p class="text-3xl font-bold mt-2">{{ $streak }}</p>
                    <p class="text-xs opacity-75 mt-1">Days</p>
                </div>
            </div>

            {{-- Daily Goal Progress Bar --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-8 border border-gray-100 dark:border-gray-700">
                <div class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-3">
                    Goal: {{ $dailyGoal }} min
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-6 overflow-hidden shadow-inner mb-3">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-6 rounded-full transition-all duration-500 ease-out" style="width: {{ $goalPercentage }}%"></div>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $goalPercentage }}%</span>
                </div>
            </div>

            {{-- Pomodoro Timer --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-8 border border-gray-100 dark:border-gray-700" id="pomodoro-section">
                <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-100">⏱ Focus Timer</h3>

                <div class="flex flex-col sm:flex-row items-center gap-8">

                    {{-- SVG Ring Timer --}}
                    <div class="relative flex-shrink-0" id="timer-wrapper">
                        <svg width="160" height="160" viewBox="0 0 160 160">
                            <defs>
                                <linearGradient id="timerGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#6366f1"/>
                                    <stop offset="100%" style="stop-color:#a855f7"/>
                                </linearGradient>
                            </defs>
                            <!-- Track -->
                            <circle class="timer-ring-track" cx="80" cy="80" r="70"/>
                            <!-- Progress -->
                            <circle id="timerRing" class="timer-ring-progress" cx="80" cy="80" r="70"
                                stroke-dasharray="440" stroke-dashoffset="0"/>
                        </svg>
                        <!-- Display -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span id="timerDisplay" class="text-4xl font-black text-gray-800 dark:text-gray-100 tabular-nums tracking-tight">25:00</span>
                            <span id="timerLabel" class="text-xs font-semibold text-indigo-500 mt-1 uppercase tracking-widest">Focus</span>
                        </div>
                    </div>

                    {{-- Controls --}}
                    <div class="flex-1 space-y-5">
                        {{-- Mode Selector --}}
                        <div class="flex gap-2 flex-wrap">
                            <button onclick="setMode(25, 'Focus')"
                                id="btn-focus"
                                class="px-3 py-1.5 rounded-lg text-sm font-semibold bg-indigo-600 text-white shadow transition-all duration-200 hover:bg-indigo-700">
                                🎯 25 min
                            </button>
                            <button onclick="setMode(10, 'Short Break')"
                                id="btn-short"
                                class="px-3 py-1.5 rounded-lg text-sm font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 shadow transition-all duration-200 hover:bg-green-100 dark:hover:bg-green-900">
                                ☕ 10 min
                            </button>
                            <button onclick="setMode(15, 'Long Break')"
                                id="btn-long"
                                class="px-3 py-1.5 rounded-lg text-sm font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 shadow transition-all duration-200 hover:bg-blue-100 dark:hover:bg-blue-900">
                                🌙 15 min
                            </button>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-3 flex-wrap">
                            <button id="btn-start" onclick="startTimer()"
                                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-lg hover:scale-105 transform transition-all duration-200">
                                ▶ Start
                            </button>
                            <button id="btn-pause" onclick="pauseTimer()" disabled
                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-5 py-2.5 rounded-xl shadow-lg hover:scale-105 transform transition-all duration-200 opacity-50 cursor-not-allowed">
                                ⏸ Pause
                            </button>
                            <button onclick="resetTimer()"
                                class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold px-5 py-2.5 rounded-xl shadow hover:scale-105 transform transition-all duration-200">
                                ↺ Reset
                            </button>
                        </div>

                        {{-- Session Count --}}
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Completed sessions: <span id="sessionCount" class="font-bold text-indigo-600 dark:text-indigo-400">0</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Action Controls (Start / Stop) --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-8 border border-gray-100 dark:border-gray-700 flex flex-wrap gap-4 items-center justify-center sm:justify-start">
                @if(!$activeSession)
                    <form method="POST" action="{{ route('study.start') }}" class="m-0">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 hover:scale-105 hover:shadow-xl text-white font-semibold px-6 py-3 rounded-xl shadow-lg transform transition-all duration-300 cursor-pointer flex items-center gap-2">
                            ▶ Start Study
                        </button>
                    </form>
                    <button class="bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-semibold px-6 py-3 rounded-xl cursor-not-allowed opacity-50 flex items-center gap-2" disabled>
                        ⏹ Stop Study
                    </button>
                @else
                    <button class="bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-semibold px-6 py-3 rounded-xl cursor-not-allowed opacity-50 flex items-center gap-2" disabled>
                        ▶ Start Study
                    </button>
                    <form method="POST" action="{{ route('study.stop', $activeSession->id) }}" class="m-0">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 hover:scale-105 hover:shadow-xl text-white font-semibold px-6 py-3 rounded-xl shadow-lg transform transition-all duration-300 cursor-pointer flex items-center gap-2">
                            ⏹ Stop Study
                        </button>
                    </form>
                    <span class="text-red-500 font-bold inline-flex items-center gap-1.5 animate-pulse pl-2">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                        Session in progress...
                    </span>
                @endif
            </div>

            {{-- Today's Sessions --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-8 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">Today's Sessions</h3>

                @forelse($sessions->where('date', today()->toDateString()) as $session)
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl shadow-sm p-4 mb-4 border-l-4 transition-all duration-300 hover:shadow-md {{ !$session->end_time ? 'border-red-500 animate-pulse-subtle' : 'border-blue-500' }}">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400"><strong>Start:</strong> {{ $session->start_time }}</p>
                                <p class="text-base font-semibold mt-1">
                                    <strong>Duration:</strong> 
                                    @if(!$session->end_time)
                                        <span class="text-red-500 font-bold inline-flex items-center gap-1.5">
                                            <span class="relative flex h-2 w-2">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                            </span>
                                            Running...
                                        </span>
                                    @else
                                        {{ $session->duration }} minutes
                                    @endif
                                </p>
                            </div>

                            @if(!$session->end_time)
                                <form method="POST" action="{{ route('study.stop', $session->id) }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 hover:scale-105 text-white font-semibold px-4 py-2 rounded-lg shadow hover:shadow-md transform transition-all duration-300 flex items-center gap-1.5 cursor-pointer">
                                        ⏹ Stop Session
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No study sessions recorded today yet.</p>
                @endforelse
            </div>

            {{-- Study Analytics & Achievements Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Study Analytics --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-100">Study Analytics (Last 7 Days)</h3>
                    
                    <div class="space-y-4">
                        @php
                            $maxVal = max($chartValues) ?: 60; // fallback to 60 to prevent division by zero
                        @endphp
                        @foreach($chartLabels as $index => $label)
                            @php
                                $minutes = $chartValues[$index] ?? 0;
                                $percent = min(100, round(($minutes / $maxVal) * 100));
                            @endphp
                            <div class="flex items-center gap-4">
                                <!-- Day Label -->
                                <span class="w-12 text-sm font-semibold text-gray-600 dark:text-gray-400">{{ $label }}</span>
                                
                                <!-- Bar Container -->
                                <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-lg h-6 overflow-hidden shadow-inner">
                                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-6 rounded-lg transition-all duration-500" style="width: {{ $percent }}%"></div>
                                </div>
                                
                                <!-- Value -->
                                <span class="w-16 text-right text-sm font-bold text-gray-700 dark:text-gray-300">{{ $minutes }} min</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Achievements --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-100 dark:border-gray-700">
                    <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-gray-100">Achievements</h3>
                    
                    <div class="space-y-4">
                        <!-- Bronze Badge (5 Hours) -->
                        @php
                            $bronzeUnlocked = $totalMinutes >= 300;
                            $bronzeProgress = min(100, round(($totalMinutes / 300) * 100));
                        @endphp
                        <div class="p-4 rounded-xl border {{ $bronzeUnlocked ? 'bg-amber-50/50 border-amber-200 dark:bg-amber-950/20 dark:border-amber-900' : 'bg-gray-50 border-gray-200 dark:bg-gray-900 dark:border-gray-800 opacity-60' }} flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl">🥉</span>
                                <div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-100 {{ $bronzeUnlocked ? 'text-amber-800 dark:text-amber-300' : '' }}">5 Hours Study</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Accumulate 5 hours of total study time.</p>
                                    @if(!$bronzeUnlocked)
                                        <div class="w-32 bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full mt-2 overflow-hidden shadow-inner">
                                            <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $bronzeProgress }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($bronzeUnlocked)
                                    <span class="bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 text-xs font-bold px-2.5 py-1 rounded-full">Unlocked</span>
                                @else
                                    <span class="text-xs text-gray-400 font-semibold">{{ min(300, $totalMinutes) }}m / 300m</span>
                                @endif
                            </div>
                        </div>

                        <!-- Silver Badge (25 Hours) -->
                        @php
                            $silverUnlocked = $totalMinutes >= 1500;
                            $silverProgress = min(100, round(($totalMinutes / 1500) * 100));
                        @endphp
                        <div class="p-4 rounded-xl border {{ $silverUnlocked ? 'bg-slate-50/50 border-slate-200 dark:bg-slate-900/20 dark:border-slate-800' : 'bg-gray-50 border-gray-200 dark:bg-gray-900 dark:border-gray-800 opacity-60' }} flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl">🥈</span>
                                <div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-100 {{ $silverUnlocked ? 'text-slate-700 dark:text-slate-300' : '' }}">25 Hours Study</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Accumulate 25 hours of total study time.</p>
                                    @if(!$silverUnlocked)
                                        <div class="w-32 bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full mt-2 overflow-hidden shadow-inner">
                                            <div class="bg-slate-400 h-1.5 rounded-full" style="width: {{ $silverProgress }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($silverUnlocked)
                                    <span class="bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 text-xs font-bold px-2.5 py-1 rounded-full">Unlocked</span>
                                @else
                                    <span class="text-xs text-gray-400 font-semibold">{{ min(1500, $totalMinutes) }}m / 1500m</span>
                                @endif
                            </div>
                        </div>

                        <!-- Gold Badge (100 Hours) -->
                        @php
                            $goldUnlocked = $totalMinutes >= 6000;
                            $goldProgress = min(100, round(($totalMinutes / 6000) * 100));
                        @endphp
                        <div class="p-4 rounded-xl border {{ $goldUnlocked ? 'bg-yellow-50/50 border-yellow-200 dark:bg-yellow-950/20 dark:border-yellow-900' : 'bg-gray-50 border-gray-200 dark:bg-gray-900 dark:border-gray-800 opacity-60' }} flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl">🥇</span>
                                <div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-100 {{ $goldUnlocked ? 'text-yellow-700 dark:text-yellow-300' : '' }}">100 Hours Study</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Accumulate 100 hours of total study time.</p>
                                    @if(!$goldUnlocked)
                                        <div class="w-32 bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full mt-2 overflow-hidden shadow-inner">
                                            <div class="bg-yellow-500 h-1.5 rounded-full" style="width: {{ $goldProgress }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($goldUnlocked)
                                    <span class="bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 text-xs font-bold px-2.5 py-1 rounded-full">Unlocked</span>
                                @else
                                    <span class="text-xs text-gray-400 font-semibold">{{ min(6000, $totalMinutes) }}m / 6000m</span>
                                @endif
                            </div>
                        </div>

                        <!-- 7 Day Streak (7 Days) -->
                        @php
                            $streakUnlocked = $streak >= 7;
                            $streakProgress = min(100, round(($streak / 7) * 100));
                        @endphp
                        <div class="p-4 rounded-xl border {{ $streakUnlocked ? 'bg-orange-50/50 border-orange-200 dark:bg-orange-950/20 dark:border-orange-900' : 'bg-gray-50 border-gray-200 dark:bg-gray-900 dark:border-gray-800 opacity-60' }} flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-3xl">🔥</span>
                                <div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-100 {{ $streakUnlocked ? 'text-orange-700 dark:text-orange-300' : '' }}">7 Day Streak</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Maintain a study streak for 7 consecutive days.</p>
                                    @if(!$streakUnlocked)
                                        <div class="w-32 bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full mt-2 overflow-hidden shadow-inner">
                                            <div class="bg-orange-500 h-1.5 rounded-full" style="width: {{ $streakProgress }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if($streakUnlocked)
                                    <span class="bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300 text-xs font-bold px-2.5 py-1 rounded-full">Unlocked</span>
                                @else
                                    <span class="text-xs text-gray-400 font-semibold">{{ $streak }}d / 7d</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Pomodoro Timer JavaScript --}}
    <script>
        // --- State ---
        let totalSeconds   = 25 * 60;  // default 25 min
        let remainingSeconds = totalSeconds;
        let timerInterval  = null;
        let isRunning      = false;
        let completedSessions = 0;
        const CIRCUMFERENCE = 440; // 2 * π * 70

        const display  = document.getElementById('timerDisplay');
        const label    = document.getElementById('timerLabel');
        const ring     = document.getElementById('timerRing');
        const wrapper  = document.getElementById('timer-wrapper');
        const btnStart = document.getElementById('btn-start');
        const btnPause = document.getElementById('btn-pause');
        const countEl  = document.getElementById('sessionCount');

        function formatTime(s) {
            const m = Math.floor(s / 60).toString().padStart(2, '0');
            const sec = (s % 60).toString().padStart(2, '0');
            return `${m}:${sec}`;
        }

        function updateRing() {
            const progress = remainingSeconds / totalSeconds;
            const offset   = CIRCUMFERENCE * (1 - progress);
            ring.style.strokeDashoffset = offset;
        }

        function setMode(minutes, modeName) {
            resetTimer();
            totalSeconds     = minutes * 60;
            remainingSeconds = totalSeconds;
            display.textContent = formatTime(remainingSeconds);
            label.textContent   = modeName;
            ring.style.strokeDashoffset = 0;

            // Highlight active mode button
            ['btn-focus','btn-short','btn-long'].forEach(id => {
                const b = document.getElementById(id);
                b.classList.remove('bg-indigo-600','text-white');
                b.classList.add('bg-gray-100','dark:bg-gray-700','text-gray-700','dark:text-gray-200');
            });
            const modeMap = { 'Focus': 'btn-focus', 'Short Break': 'btn-short', 'Long Break': 'btn-long' };
            const activeBtn = document.getElementById(modeMap[modeName] || 'btn-focus');
            activeBtn.classList.add('bg-indigo-600','text-white');
            activeBtn.classList.remove('bg-gray-100','dark:bg-gray-700','text-gray-700','dark:text-gray-200');
        }

        function startTimer() {
            if (isRunning) return;
            isRunning = true;

            // Button states
            btnStart.disabled = true;
            btnStart.classList.add('opacity-50','cursor-not-allowed');
            btnPause.disabled = false;
            btnPause.classList.remove('opacity-50','cursor-not-allowed');

            // Glow animation
            wrapper.classList.add('timer-running');

            timerInterval = setInterval(() => {
                if (remainingSeconds <= 0) {
                    clearInterval(timerInterval);
                    isRunning = false;
                    completedSessions++;
                    countEl.textContent = completedSessions;
                    wrapper.classList.remove('timer-running');
                    display.textContent = '00:00';

                    // Browser notification
                    if (Notification.permission === 'granted') {
                        new Notification('⏱ FocusFlow', { body: `${label.textContent} session complete!`, icon: '/favicon.ico' });
                    }

                    // Auto-reset buttons
                    btnStart.disabled = false;
                    btnStart.classList.remove('opacity-50','cursor-not-allowed');
                    btnPause.disabled = true;
                    btnPause.classList.add('opacity-50','cursor-not-allowed');
                    return;
                }
                remainingSeconds--;
                display.textContent = formatTime(remainingSeconds);
                updateRing();
            }, 1000);
        }

        function pauseTimer() {
            if (!isRunning) return;
            clearInterval(timerInterval);
            isRunning = false;
            wrapper.classList.remove('timer-running');
            ring.classList.add('paused');

            btnStart.disabled = false;
            btnStart.classList.remove('opacity-50','cursor-not-allowed');
            btnPause.disabled = true;
            btnPause.classList.add('opacity-50','cursor-not-allowed');
        }

        function resetTimer() {
            clearInterval(timerInterval);
            isRunning = false;
            remainingSeconds = totalSeconds;
            display.textContent = formatTime(remainingSeconds);
            ring.style.strokeDashoffset = 0;
            ring.classList.remove('paused');
            wrapper.classList.remove('timer-running');

            btnStart.disabled = false;
            btnStart.classList.remove('opacity-50','cursor-not-allowed');
            btnPause.disabled = true;
            btnPause.classList.add('opacity-50','cursor-not-allowed');
        }

        // Request notification permission on load
        document.addEventListener('DOMContentLoaded', () => {
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        });
    </script>
</x-app-layout>