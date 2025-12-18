<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Kuis
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Result Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <!-- Header with Grade -->
                <div class="bg-gradient-to-r {{ $stats['percentage'] >= 70 ? 'from-green-500 to-emerald-600' : 'from-red-500 to-orange-600' }} p-8 text-white text-center">
                    <div class="w-24 h-24 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-4">
                        <span class="text-5xl font-bold">{{ $stats['grade'] }}</span>
                    </div>
                    <h2 class="text-2xl font-bold">{{ $stats['percentage'] >= 70 ? 'Selamat! 🎉' : 'Tetap Semangat! 💪' }}</h2>
                    <p class="text-white/80 mt-2">{{ $quiz->title }}</p>
                </div>

                <div class="p-8">
                    <!-- XP Earned -->
                    @if($xp_earned > 0)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-center">
                            <span class="text-3xl">⭐</span>
                            <p class="text-yellow-800 font-bold text-lg mt-2">+{{ $xp_earned }} XP Diperoleh!</p>
                        </div>
                    @endif

                    <!-- New Achievements -->
                    @if(session('new_achievements') && count(session('new_achievements')) > 0)
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg p-6 mb-6 text-white">
                            <h3 class="text-xl font-bold mb-4 text-center">🎉 Achievement Unlocked!</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach(session('new_achievements') as $achievement)
                                    <div class="bg-white/20 backdrop-blur rounded-lg p-4 flex items-center gap-3">
                                        <span class="text-4xl">{{ $achievement->icon }}</span>
                                        <div>
                                            <p class="font-bold">{{ $achievement->name }}</p>
                                            <p class="text-sm text-white/80">{{ $achievement->description }}</p>
                                            @if($achievement->xp_reward > 0)
                                                <p class="text-xs text-yellow-300">+{{ $achievement->xp_reward }} XP</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-blue-600">{{ $stats['score'] }}</p>
                            <p class="text-sm text-blue-600">Skor Anda</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-gray-600">{{ $stats['max_score'] }}</p>
                            <p class="text-sm text-gray-600">Skor Maksimal</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-green-600">{{ $stats['correct'] }}</p>
                            <p class="text-sm text-green-600">Benar</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-red-600">{{ $stats['wrong'] }}</p>
                            <p class="text-sm text-red-600">Salah</p>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-4 text-center">
                            <p class="text-3xl font-bold text-orange-600">{{ $attempt->violations ?? 0 }}</p>
                            <p class="text-sm text-orange-600">Pelanggaran</p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-8">
                        <div class="flex justify-between text-sm mb-2">
                            <span class=\"text-gray-600\">Persentase Benar</span>
                            <span class="font-bold">{{ $stats['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="h-4 rounded-full transition-all {{ $stats['percentage'] >= 70 ? 'bg-green-500' : 'bg-red-500' }}" 
                                 style="width: {{ $stats['percentage'] }}%"></div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('result.review', $attempt) }}" 
                           class="flex-1 text-center bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-medium">
                            📖 Lihat Pembahasan
                        </a>
                        <form action="{{ route('quiz.start', $quiz) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 font-medium">
                                🔄 Coba Lagi
                            </button>
                        </form>
                        <a href="{{ route('quiz.lobby') }}" 
                           class="flex-1 text-center border py-3 rounded-lg hover:bg-gray-50 font-medium">
                            🏠 Ke Lobby
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
