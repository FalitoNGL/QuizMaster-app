<x-app-layout>
    <div class="min-h-screen bg-gray-100 py-12">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Header with celebration -->
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 p-8 text-center text-white">
                    <div class="text-6xl mb-4">
                        @if($percentage >= 80)
                            🏆
                        @elseif($percentage >= 60)
                            🎉
                        @elseif($percentage >= 40)
                            💪
                        @else
                            📚
                        @endif
                    </div>
                    <h1 class="text-3xl font-bold mb-2">Kuis Selesai!</h1>
                    <p class="opacity-90">{{ $quiz->title }}</p>
                </div>

                <!-- Score Section -->
                <div class="p-8 text-center">
                    <!-- Big Score Circle -->
                    <div class="inline-flex items-center justify-center w-40 h-40 rounded-full border-8 
                         {{ $percentage >= 60 ? 'border-emerald-500 text-emerald-600' : 'border-red-400 text-red-500' }} mb-6">
                        <div>
                            <div class="text-5xl font-bold">{{ $percentage }}%</div>
                            <div class="text-sm text-gray-500">Skor</div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-green-50 rounded-xl p-4">
                            <div class="text-3xl font-bold text-green-600">{{ $correctCount }}</div>
                            <div class="text-sm text-green-600">Jawaban Benar</div>
                        </div>
                        <div class="bg-red-50 rounded-xl p-4">
                            <div class="text-3xl font-bold text-red-600">{{ $totalQuestions - $correctCount }}</div>
                            <div class="text-sm text-red-600">Jawaban Salah</div>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="bg-gray-50 rounded-xl p-4 mb-8">
                        @if($percentage >= 80)
                            <p class="text-gray-700">🌟 Luar biasa! Kamu menguasai materi ini dengan sangat baik!</p>
                        @elseif($percentage >= 60)
                            <p class="text-gray-700">👍 Bagus! Terus tingkatkan pemahamanmu.</p>
                        @elseif($percentage >= 40)
                            <p class="text-gray-700">💡 Lumayan! Coba pelajari lagi materi yang belum dikuasai.</p>
                        @else
                            <p class="text-gray-700">📖 Jangan menyerah! Pelajari lagi dan coba kembali.</p>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="space-y-3">
                        <a href="{{ route('quiz.show', $quiz) }}" 
                           class="block w-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-3 rounded-xl font-bold hover:from-emerald-600 hover:to-teal-700 transition-all">
                            🔄 Main Lagi
                        </a>
                        <a href="{{ route('quiz.lobby') }}" 
                           class="block w-full border-2 border-gray-300 text-gray-700 py-3 rounded-xl font-bold hover:bg-gray-50 transition-all">
                            ← Kembali ke Lobby
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
