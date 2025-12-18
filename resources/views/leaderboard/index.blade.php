<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🏆 Leaderboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Banner -->
            <div class="bg-gradient-to-r from-yellow-500 via-orange-500 to-red-500 rounded-xl shadow-lg p-6 mb-8 text-white">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                    <div>
                        <p class="text-4xl font-bold">{{ number_format($stats['total_users']) }}</p>
                        <p class="text-yellow-100">Total Pemain</p>
                    </div>
                    <div>
                        <p class="text-4xl font-bold">{{ number_format($stats['total_quizzes_completed']) }}</p>
                        <p class="text-yellow-100">Quiz Selesai</p>
                    </div>
                    <div>
                        <p class="text-4xl font-bold">{{ number_format($stats['total_xp_earned']) }}</p>
                        <p class="text-yellow-100">Total XP</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Leaderboard -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                            <h3 class="text-xl font-bold text-white">🌟 Top Players</h3>
                        </div>
                        <div class="p-6">
                            <!-- Current User Rank -->
                            @auth
                                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <span class="text-2xl mr-3">📍</span>
                                            <div>
                                                <p class="font-medium">Posisi Anda</p>
                                                <p class="text-sm text-gray-500">{{ auth()->user()->name }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-3xl font-bold text-indigo-600">#{{ $currentUserRank }}</p>
                                            <p class="text-sm text-gray-500">{{ auth()->user()->xp }} XP</p>
                                        </div>
                                    </div>
                                </div>
                            @endauth

                            <!-- Top 3 Podium -->
                            @if($topUsers->count() >= 3)
                                <div class="flex justify-center items-end gap-4 mb-8">
                                    <!-- 2nd Place -->
                                    <div class="text-center">
                                        <div class="w-16 h-16 mx-auto bg-gray-200 rounded-full flex items-center justify-center text-2xl mb-2">
                                            {{ strtoupper(substr($topUsers[1]->name, 0, 1)) }}
                                        </div>
                                        <div class="bg-gray-300 rounded-t-lg p-3 w-24">
                                            <span class="text-2xl">🥈</span>
                                            <p class="font-medium text-sm truncate">{{ $topUsers[1]->name }}</p>
                                            <p class="text-xs text-gray-600">{{ $topUsers[1]->xp }} XP</p>
                                        </div>
                                    </div>
                                    <!-- 1st Place -->
                                    <div class="text-center">
                                        <div class="w-20 h-20 mx-auto bg-yellow-200 rounded-full flex items-center justify-center text-3xl mb-2 ring-4 ring-yellow-400">
                                            {{ strtoupper(substr($topUsers[0]->name, 0, 1)) }}
                                        </div>
                                        <div class="bg-yellow-400 rounded-t-lg p-4 w-28">
                                            <span class="text-3xl">🥇</span>
                                            <p class="font-bold truncate">{{ $topUsers[0]->name }}</p>
                                            <p class="text-sm">{{ $topUsers[0]->xp }} XP</p>
                                        </div>
                                    </div>
                                    <!-- 3rd Place -->
                                    <div class="text-center">
                                        <div class="w-16 h-16 mx-auto bg-orange-100 rounded-full flex items-center justify-center text-2xl mb-2">
                                            {{ strtoupper(substr($topUsers[2]->name, 0, 1)) }}
                                        </div>
                                        <div class="bg-orange-300 rounded-t-lg p-3 w-24">
                                            <span class="text-2xl">🥉</span>
                                            <p class="font-medium text-sm truncate">{{ $topUsers[2]->name }}</p>
                                            <p class="text-xs text-gray-600">{{ $topUsers[2]->xp }} XP</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Full Ranking List -->
                            <div class="space-y-2">
                                @foreach($topUsers as $index => $user)
                                    @if($index >= 3)
                                        <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 {{ auth()->id() === $user->id ? 'bg-indigo-50 border border-indigo-200' : '' }}">
                                            <div class="flex items-center">
                                                <span class="w-8 text-center font-bold text-gray-500">{{ $index + 1 }}</span>
                                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center ml-3">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div class="ml-3">
                                                    <p class="font-medium">{{ $user->name }}</p>
                                                    <p class="text-sm text-gray-500">Level {{ $user->level }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-indigo-600">{{ number_format($user->xp) }} XP</p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Weekly Top -->
                <div>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-teal-500 px-6 py-4">
                            <h3 class="text-lg font-bold text-white">🔥 Top Minggu Ini</h3>
                        </div>
                        <div class="p-4">
                            @forelse($weeklyTop as $index => $user)
                                <div class="flex items-center justify-between py-3 border-b last:border-0">
                                    <div class="flex items-center">
                                        <span class="w-6 h-6 flex items-center justify-center bg-green-100 text-green-600 text-sm font-bold rounded-full">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="ml-3 font-medium">{{ Str::limit($user->name, 15) }}</span>
                                    </div>
                                    <span class="text-green-600 font-bold">
                                        {{ number_format($user->quiz_attempts_sum_total_score ?? 0) }} pts
                                    </span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">Belum ada aktivitas minggu ini</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="mt-6 space-y-3">
                        <a href="{{ route('quiz.lobby') }}" 
                           class="block w-full text-center bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-medium">
                            🎮 Main Quiz Sekarang
                        </a>
                        <a href="{{ route('result.history') }}" 
                           class="block w-full text-center border border-indigo-600 text-indigo-600 py-3 rounded-lg hover:bg-indigo-50 font-medium">
                            📜 Riwayat Saya
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
