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
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Changed: Replaced simple welcome heading with a styled welcome banner --}}
                    <div class="bg-indigo-600 text-white p-6 rounded-xl mb-8 shadow-lg">
                        <h2 class="text-2xl font-bold">
                            Welcome back, {{ auth()->user()->name }} 👋
                        </h2>

                        <p class="mt-2 text-indigo-100">
                            Stay focused. Track your study sessions and achieve your goals.
                        </p>
                    </div>

                    {{-- Changed: Replaced the old card layout with the new styled gradient cards grid (Today, Weekly, Streak) --}}
                    {{-- Changed: Added hover scale, translateY, shadow, and transition animations --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white p-6 rounded-xl shadow-lg hover:-translate-y-1 hover:shadow-2xl hover:brightness-110 transform transition-all duration-300">
        <h3 class="text-lg font-semibold">Today's Study</h3>
        <p class="text-3xl font-bold mt-2">
            {{ $todayMinutes }}
        </p>
        <p class="text-sm">Minutes</p>
    </div>

    <div class="bg-gradient-to-r from-green-500 to-green-700 text-white p-6 rounded-xl shadow-lg hover:-translate-y-1 hover:shadow-2xl hover:brightness-110 transform transition-all duration-300">
        <h3 class="text-lg font-semibold">Weekly Study</h3>
        <p class="text-3xl font-bold mt-2">
            {{ $weeklyMinutes }}
        </p>
        <p class="text-sm">Minutes</p>
    </div>

    <div class="bg-gradient-to-r from-purple-500 to-purple-700 text-white p-6 rounded-xl shadow-lg hover:-translate-y-1 hover:shadow-2xl hover:brightness-110 transform transition-all duration-300">
        <h3 class="text-lg font-semibold">Current Streak</h3>
        <p class="text-3xl font-bold mt-2">
            0
        </p>
        <p class="text-sm">Days</p>
    </div>

</div>
                    

                    {{-- Changed: Removed redundant helper paragraph --}}

                    <form method="POST" action="{{ route('study.start') }}">
                        @csrf
                        {{-- Changed: Styled Start Study button with rounded-xl, shadow, hover and transition classes --}}
                        {{-- Changed: Added scale, cursor pointer, and hover effects --}}
                        <button
                            type="submit"
                            class="bg-green-600 hover:bg-green-700 hover:scale-105 hover:shadow-xl text-white px-6 py-3 rounded-xl shadow-lg transform transition-all duration-300 cursor-pointer">
                            ▶ Start Study
                        </button>
                    </form>

                    <div class="mt-8">
                        <h3 class="text-xl font-bold mb-4">Today's Sessions</h3>

                        @forelse($sessions as $session)
                            {{-- Changed: Upgraded session card style with dynamic border and active pulse if running --}}
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl shadow-md p-5 mb-4 border-l-4 transition-all duration-300 hover:shadow-lg {{ !$session->end_time ? 'border-red-500 animate-pulse-subtle' : 'border-blue-500' }}">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400"><strong>Start:</strong> {{ $session->start_time }}</p>
                                        <p class="text-lg font-semibold mt-1">
                                            <strong>Duration:</strong> 
                                            @if(!$session->end_time)
                                                <span class="text-red-500 font-bold inline-flex items-center gap-1.5">
                                                    <span class="relative flex h-2.5 w-2.5">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                                                    </span>
                                                    Running...
                                                </span>
                                            @else
                                                {{ $session->duration }} minutes
                                            @endif
                                        </p>
                                        @if($session->end_time)
                                            <p class="text-xs text-gray-400 mt-1"><strong>Ended:</strong> {{ $session->end_time }}</p>
                                        @endif
                                    </div>

                                    @if(!$session->end_time)
                                        <form method="POST" action="{{ route('study.stop', $session->id) }}" class="m-0">
                                            @csrf
                                            {{-- Changed: Styled Stop button with hover scale and transition --}}
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 hover:scale-105 text-white font-semibold px-4 py-2 rounded-lg shadow hover:shadow-md transform transition-all duration-300 flex items-center gap-1.5 cursor-pointer">
                                                ⏹ Stop Study
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p>No study sessions yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>