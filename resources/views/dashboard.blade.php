<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🎯 FocusFlow Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-bold mb-4">
                        Welcome, {{ auth()->user()->name }} 👋
                        
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    <div class="bg-blue-500 text-white p-4 rounded">
        <h3 class="text-lg font-bold">Today's Study</h3>
        <p class="text-2xl">{{ $todayMinutes }} Minutes</p>
    </div>

    <div class="bg-green-500 text-white p-4 rounded">
        <h3 class="text-lg font-bold">Weekly Study</h3>
        <p class="text-2xl">{{ $weeklyMinutes }} Minutes</p>
    </div>

</div>
                    

                    <p class="mb-6">
                        Track your study sessions and stay focused.
                    </p>

                    <form method="POST" action="{{ route('study.start') }}">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                            ▶ Start Study
                        </button>
                    </form>

                    <div class="mt-8">
                        <h3 class="text-xl font-bold mb-4">Today's Sessions</h3>

                        @forelse($sessions as $session)
                            <div class="border rounded p-4 mb-3">
                                <p><strong>Start:</strong> {{ $session->start_time }}</p>
                                <p><strong>Duration:</strong> {{ $session->duration ?? 'Running...' }} minutes</p>

                                @if(!$session->end_time)
                                    <form method="POST" action="{{ route('study.stop', $session->id) }}" class="mt-2">
                                        @csrf
                                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded">
                                            ⏹ Stop
                                        </button>
                                    </form>
                                @else
                                    <p class="mt-2"><strong>Ended:</strong> {{ $session->end_time }}</p>
                                @endif
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