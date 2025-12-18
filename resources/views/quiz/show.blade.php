<x-app-layout>
    <div class="min-h-screen bg-gray-100 py-12" x-data="quizSetup()">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Quiz Header Card -->
            <div class="relative overflow-hidden bg-white rounded-3xl shadow-xl mb-8">
                <!-- Background Pattern (dark mode only) -->
                <div class="absolute inset-0 opacity-0">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                </div>
                
                <div class="relative p-8 md:p-12">
                    <div class="flex flex-col md:flex-row gap-8 items-center">
                        <!-- Thumbnail -->
                        <div class="w-32 h-32 md:w-40 md:h-40 rounded-2xl overflow-hidden shadow-xl ring-4 ring-indigo-100 flex-shrink-0">
                            @if($quiz->thumbnail)
                                <img src="{{ Storage::url($quiz->thumbnail) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-6xl">📚</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Info -->
                        <div class="flex-1 text-center md:text-left">
                            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-3">{{ $quiz->title }}</h1>
                            <p class="text-gray-600 mb-6">{{ $quiz->description ?: 'Uji pengetahuanmu sekarang!' }}</p>
                            
                            <!-- Stats -->
                            <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                                <div class="flex items-center gap-2 bg-blue-50 rounded-full px-4 py-2">
                                    <span class="text-xl">📝</span>
                                    <span class="text-blue-700 font-semibold">{{ $quiz->questions_count }} Soal</span>
                                </div>
                                <div class="flex items-center gap-2 bg-green-50 rounded-full px-4 py-2">
                                    <span class="text-xl">⏱️</span>
                                    <span class="text-green-700 font-semibold">{{ $quiz->time_limit }} Menit</span>
                                </div>
                                <div class="flex items-center gap-2 bg-purple-50 rounded-full px-4 py-2">
                                    <span class="text-xl">⭐</span>
                                    <span class="text-purple-700 font-semibold">{{ $quiz->total_points ?? ($quiz->questions_count * 10) }} Poin</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mode Selection -->
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Pilih Mode Bermain</h2>
            
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                <!-- Mode Kuis Card -->
                <div @click="showQuizSetup = true" 
                     class="group cursor-pointer relative overflow-hidden rounded-3xl p-8 shadow-xl hover:shadow-emerald-500/25 hover:scale-[1.02] transition-all duration-300"
                     style="background: linear-gradient(to bottom right, #10b981, #0d9488);">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                    
                    <div class="relative">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <span class="text-3xl">📖</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-2">Mode Kuis</h3>
                        <p class="text-white text-sm mb-4">Belajar sambil bermain dengan feedback langsung setiap soal</p>
                        
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-white text-sm">
                                <svg class="w-5 h-5 text-emerald-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Pilih jumlah soal sendiri
                            </div>
                            <div class="flex items-center gap-2 text-white text-sm">
                                <svg class="w-5 h-5 text-emerald-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Lihat pembahasan & referensi
                            </div>
                            <div class="flex items-center gap-2 text-white text-sm">
                                <svg class="w-5 h-5 text-emerald-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Tanpa tekanan waktu (opsional)
                            </div>
                        </div>
                        
                        <div class="mt-6 flex items-center text-white font-semibold group-hover:translate-x-2 transition-transform">
                            Mulai Belajar
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Mode Ujian Card -->
                <form action="{{ route('quiz.start', $quiz) }}" method="POST" class="h-full">
                    @csrf
                    <button type="submit" 
                            class="group w-full h-full text-left cursor-pointer relative overflow-hidden bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl p-8 shadow-xl hover:shadow-purple-500/25 hover:scale-[1.02] transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
                        
                        <div class="relative">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <span class="text-3xl">📝</span>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-2">Mode Ujian</h3>
                            <p class="text-white text-sm mb-4">Simulasi ujian seperti aslinya dengan hasil di akhir</p>
                            
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-white text-sm">
                                    <svg class="w-5 h-5 text-indigo-200" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Timer ketat {{ $quiz->time_limit }} menit
                                </div>
                                <div class="flex items-center gap-2 text-white text-sm">
                                    <svg class="w-5 h-5 text-indigo-200" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Anti-cheat aktif
                                </div>
                                <div class="flex items-center gap-2 text-white text-sm">
                                    <svg class="w-5 h-5 text-indigo-200" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Skor & ranking leaderboard
                                </div>
                            </div>
                            
                            <div class="mt-6 flex items-center text-white font-semibold group-hover:translate-x-2 transition-transform">
                                Mulai Ujian
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </div>
                        </div>
                    </button>
                </form>
            </div>

            <!-- Back Link -->
            <div class="text-center">
                <a href="{{ route('quiz.lobby') }}" class="inline-flex items-center text-gray-500 hover:text-indigo-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Lobby
                </a>
            </div>
        </div>

        <!-- Quiz Mode Setup Modal -->
        <div x-show="showQuizSetup" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
             @click.self="showQuizSetup = false">
            <div x-show="showQuizSetup"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-6 text-white">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <span class="text-2xl">📖</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Setup Mode Kuis</h3>
                            <p class="text-white/80 text-sm">Atur preferensi belajarmu</p>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Body -->
                <div class="p-6 space-y-6">
                    <!-- Question Count Slider -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-gray-700 font-medium">Jumlah Soal</label>
                            <span class="text-2xl font-bold text-emerald-600" x-text="questionCount"></span>
                        </div>
                        <input type="range" 
                               x-model="questionCount" 
                               min="5" 
                               max="{{ $quiz->questions_count }}" 
                               step="5"
                               class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-emerald-500">
                        <div class="flex justify-between text-xs text-gray-500 mt-2">
                            <span>5 soal</span>
                            <span>{{ $quiz->questions_count }} soal</span>
                        </div>
                    </div>

                    <!-- Time Slider -->
                    <div>
                        <div class="flex justify-between items-center mb-3">
                            <label class="text-gray-700 font-medium">Batas Waktu</label>
                            <span class="text-2xl font-bold text-emerald-600" x-text="timeLimit == 0 ? '∞' : timeLimit + ' menit'"></span>
                        </div>
                        <input type="range" 
                               x-model="timeLimit" 
                               min="0" 
                               max="60" 
                               step="5"
                               class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-emerald-500">
                        <div class="flex justify-between text-xs text-gray-500 mt-2">
                            <span>Tanpa batas</span>
                            <span>60 menit</span>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-6 bg-gray-50 flex gap-3">
                    <button @click="showQuizSetup = false" 
                            class="flex-1 py-3 px-4 border-2 border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-100 transition-colors">
                        Batal
                    </button>
                    <form action="{{ route('quiz.start-quiz-mode', $quiz) }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="question_count" :value="questionCount">
                        <input type="hidden" name="time_limit" :value="timeLimit">
                        <button type="submit" 
                                class="w-full py-3 px-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-bold hover:from-emerald-600 hover:to-teal-700 transition-all shadow-lg shadow-emerald-500/25">
                            🚀 Mulai Kuis
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function quizSetup() {
        return {
            showQuizSetup: false,
            questionCount: Math.min(20, {{ $quiz->questions_count }}),
            timeLimit: 0
        }
    }
    </script>
</x-app-layout>
