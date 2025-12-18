<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🎮 Quiz Lobby
            </h2>
            <a href="{{ route('leaderboard') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                🏆 Leaderboard
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 500)">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center justify-between">
                    <span>⚠️ {{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">&times;</button>
                </div>
            @endif
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center justify-between">
                    <span>✅ {{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">&times;</button>
                </div>
            @endif

            <!-- User Stats Bar -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 mb-8 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-2xl">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-bold">{{ auth()->user()->name }}</h3>
                            <p class="text-indigo-200">Level {{ auth()->user()->level }} • {{ auth()->user()->xp }} XP</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-indigo-200">Progress XP</p>
                        <div class="w-48 bg-white/20 rounded-full h-3 mt-1">
                            <div class="bg-yellow-400 h-3 rounded-full" 
                                 style="width: {{ auth()->user()->xp % 100 }}%"></div>
                        </div>
                        <p class="text-xs text-indigo-200 mt-1">{{ auth()->user()->xp % 100 }}/100 ke Level {{ auth()->user()->level + 1 }}</p>
                    </div>
                </div>
            </div>

            <!-- Quiz Cards -->
            <h3 class="text-2xl font-bold mb-6">Pilih Kuis</h3>
            
            <!-- Loading Skeleton -->
            <div x-show="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @for($i = 0; $i < 6; $i++)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden animate-pulse">
                    <div class="h-40 bg-gray-200"></div>
                    <div class="p-6">
                        <div class="h-6 bg-gray-200 rounded w-3/4 mb-3"></div>
                        <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                        <div class="h-4 bg-gray-200 rounded w-2/3 mb-4"></div>
                        <div class="flex justify-between mb-4">
                            <div class="h-4 bg-gray-200 rounded w-20"></div>
                            <div class="h-4 bg-gray-200 rounded w-20"></div>
                        </div>
                        <div class="h-12 bg-gray-200 rounded"></div>
                    </div>
                </div>
                @endfor
            </div>

            <!-- Actual Content -->
            <div x-show="!loading" x-cloak>
                @if($quizzes->isEmpty())
                    <div class="bg-white rounded-lg shadow p-8 text-center">
                        <p class="text-gray-500">Belum ada kuis tersedia.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($quizzes as $quiz)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                <!-- Thumbnail with Status Badge -->
                                <div class="h-44 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center relative px-8 py-4">
                                    @if($quiz->thumbnail)
                                        <img src="{{ Storage::url($quiz->thumbnail) }}" 
                                             class="max-w-full max-h-full object-contain rounded-lg shadow-lg">
                                    @else
                                        <span class="text-6xl">📝</span>
                                    @endif
                                    
                                    <!-- Schedule Badge -->
                                    @if($quiz->schedule_status === 'coming_soon')
                                        <div class="absolute top-2 right-2 bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                            ⏳ Coming Soon
                                        </div>
                                    @elseif($quiz->schedule_status === 'closed')
                                        <div class="absolute top-2 right-2 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                            🔒 Closed
                                        </div>
                                    @elseif($quiz->ends_at)
                                        <div class="absolute top-2 right-2 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                            ✅ Open
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="p-6">
                                    <h4 class="text-xl font-bold mb-2">{{ $quiz->title }}</h4>
                                    <p class="text-gray-500 text-sm mb-4 line-clamp-2">
                                        {{ $quiz->description ?: 'Tidak ada deskripsi' }}
                                    </p>
                                    
                                    <!-- Quiz Info -->
                                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                        <span>📊 {{ $quiz->questions_count }} soal</span>
                                        <span>⏱️ {{ $quiz->time_limit }} menit</span>
                                    </div>

                                    <!-- Schedule Info -->
                                    @if($quiz->starts_at && $quiz->schedule_status === 'coming_soon')
                                        <div class="bg-yellow-50 rounded-lg p-3 mb-4 text-center">
                                            <p class="text-xs text-yellow-600">Mulai pada</p>
                                            <p class="font-bold text-yellow-700">{{ $quiz->starts_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    @elseif($quiz->ends_at && $quiz->schedule_status === 'open')
                                        <div class="bg-blue-50 rounded-lg p-3 mb-4 text-center">
                                            <p class="text-xs text-blue-600">Berakhir pada</p>
                                            <p class="font-bold text-blue-700">{{ $quiz->ends_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    @endif



                                    <!-- Action Button -->
                                    @if($quiz->isAvailable())
                                        <a href="{{ route('quiz.show', $quiz) }}" 
                                           class="block w-full text-center bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-medium">
                                            {{ $quiz->has_completed ? 'Main Lagi' : 'Mulai Kuis' }}
                                        </a>
                                    @elseif($quiz->schedule_status === 'coming_soon')
                                        <button disabled class="block w-full text-center bg-gray-400 text-white py-3 rounded-lg cursor-not-allowed font-medium">
                                            ⏳ Belum Dibuka
                                        </button>
                                    @else
                                        <button disabled class="block w-full text-center bg-gray-400 text-white py-3 rounded-lg cursor-not-allowed font-medium">
                                            🔒 Sudah Ditutup
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Quick Links -->
            <div class="mt-8 flex justify-center gap-4">
                <a href="{{ route('result.history') }}" class="text-indigo-600 hover:text-indigo-800">
                    📜 Lihat Riwayat Pengerjaan →
                </a>
                <a href="{{ route('leaderboard') }}" class="text-indigo-600 hover:text-indigo-800">
                    🏆 Lihat Leaderboard →
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
