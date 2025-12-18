<x-app-layout>
    <div class="min-h-screen bg-gray-100 py-8" x-data="quizMode()">
        <div class="max-w-3xl mx-auto px-4">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">{{ $quiz->title }}</h1>
                        <p class="text-sm text-gray-500">Mode Kuis</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <!-- Timer -->
                        @if($remainingTime !== null)
                        <div class="text-center">
                            <div class="text-2xl font-mono font-bold" 
                                 :class="remainingTime < 60 ? 'text-red-500' : 'text-indigo-600'"
                                 x-text="formatTime(remainingTime)"></div>
                            <p class="text-xs text-gray-500">Sisa Waktu</p>
                        </div>
                        @endif
                        
                        <!-- Progress -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-emerald-600">{{ $currentIndex + 1 }}/{{ $totalQuestions }}</div>
                            <p class="text-xs text-gray-500">Soal</p>
                        </div>
                        
                        <!-- Score -->
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $correctCount }}</div>
                            <p class="text-xs text-gray-500">Benar</p>
                        </div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-4 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 transition-all duration-300"
                         style="width: {{ (($currentIndex) / $totalQuestions) * 100 }}%"></div>
                </div>
            </div>

            <!-- Question Card -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="mb-6">
                    <span class="inline-block bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full text-sm font-medium mb-4">
                        Soal {{ $currentIndex + 1 }}
                    </span>
                    <h2 class="text-xl font-semibold text-gray-800 leading-relaxed">
                        {!! nl2br(e($question->content)) !!}
                    </h2>
                </div>

                <!-- Options -->
                <div class="space-y-3 mb-6">
                    @foreach($question->options as $option)
                    <div @click="!answered && selectAnswer({{ $option->id }})"
                         :class="{
                             'border-gray-200 hover:border-indigo-400 cursor-pointer': !answered,
                             'border-green-500 bg-green-50': answered && {{ $option->is_correct ? 'true' : 'false' }},
                             'border-red-500 bg-red-50': answered && selectedAnswer == {{ $option->id }} && !{{ $option->is_correct ? 'true' : 'false' }},
                             'opacity-50 cursor-not-allowed': answered
                         }"
                         class="border-2 rounded-xl p-4 transition-all">
                        <div class="flex items-center gap-3">
                            <div :class="{
                                    'bg-gray-200': !answered,
                                    'bg-green-500 text-white': answered && {{ $option->is_correct ? 'true' : 'false' }},
                                    'bg-red-500 text-white': answered && selectedAnswer == {{ $option->id }} && !{{ $option->is_correct ? 'true' : 'false' }}
                                 }"
                                 class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm">
                                <span x-show="!answered || (selectedAnswer != {{ $option->id }} && !{{ $option->is_correct ? 'true' : 'false' }})">
                                    {{ chr(65 + $loop->index) }}
                                </span>
                                <span x-show="answered && {{ $option->is_correct ? 'true' : 'false' }}">✓</span>
                                <span x-show="answered && selectedAnswer == {{ $option->id }} && !{{ $option->is_correct ? 'true' : 'false' }}">✗</span>
                            </div>
                            <span class="text-gray-700 flex-1">{{ $option->option_text }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Feedback Section (shown after answering) -->
                <div x-show="answered" x-cloak class="border-t pt-6 mt-6">
                    <!-- Result Badge -->
                    <div x-show="isCorrect" class="bg-green-100 border border-green-300 rounded-xl p-4 mb-4">
                        <div class="flex items-center gap-2 text-green-700">
                            <span class="text-2xl">🎉</span>
                            <span class="font-bold text-lg">Jawaban Benar!</span>
                        </div>
                    </div>
                    
                    <div x-show="!isCorrect" class="bg-red-100 border border-red-300 rounded-xl p-4 mb-4">
                        <div class="flex items-center gap-2 text-red-700">
                            <span class="text-2xl">😔</span>
                            <span class="font-bold text-lg">Jawaban Salah</span>
                        </div>
                    </div>

                    <!-- Explanation -->
                    <div x-show="explanation" class="bg-blue-50 rounded-xl p-4 mb-4">
                        <h4 class="font-bold text-blue-700 mb-2">💡 Pembahasan:</h4>
                        <p class="text-gray-700" x-text="explanation"></p>
                    </div>

                    <!-- Reference -->
                    <div x-show="reference" class="bg-gray-50 rounded-xl p-4 mb-4">
                        <h4 class="font-bold text-gray-700 mb-2">📚 Referensi:</h4>
                        <p class="text-gray-600 text-sm" x-text="reference"></p>
                    </div>

                    <!-- Next Button -->
                    <form action="{{ route('quiz.quiz-mode-next', $quiz) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-4 rounded-xl font-bold text-lg hover:from-emerald-600 hover:to-teal-700 transition-all">
                            {{ $currentIndex + 1 < $totalQuestions ? '➡️ Soal Berikutnya' : '🏁 Lihat Hasil' }}
                        </button>
                    </form>
                </div>

                <!-- Loading indicator while submitting -->
                <div x-show="isLoading" class="text-center py-4">
                    <div class="inline-block animate-spin w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full"></div>
                </div>
            </div>

            <!-- Quit Button -->
            <div class="text-center mt-6">
                <a href="{{ route('quiz.quiz-mode-finish', $quiz) }}" 
                   class="text-gray-500 hover:text-red-500 text-sm"
                   onclick="return confirm('Yakin ingin keluar? Progress akan hilang.')">
                    Keluar dari Kuis
                </a>
            </div>
        </div>
    </div>

    <script>
    function quizMode() {
        return {
            answered: {{ $answered ? 'true' : 'false' }},
            selectedAnswer: null,
            isCorrect: false,
            explanation: null,
            reference: null,
            isLoading: false,
            remainingTime: {{ $remainingTime ?? 'null' }},

            init() {
                // Start countdown if time limit exists
                if (this.remainingTime !== null) {
                    setInterval(() => {
                        if (this.remainingTime > 0) {
                            this.remainingTime--;
                        } else {
                            window.location.href = '{{ route("quiz.quiz-mode-finish", $quiz) }}';
                        }
                    }, 1000);
                }
            },

            formatTime(seconds) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            },

            async selectAnswer(optionId) {
                if (this.answered || this.isLoading) return;
                
                this.isLoading = true;
                this.selectedAnswer = optionId;

                try {
                    const response = await fetch('{{ route("quiz.quiz-mode-answer", $quiz) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ answer: optionId })
                    });

                    const data = await response.json();
                    
                    this.isCorrect = data.is_correct;
                    this.explanation = data.explanation;
                    this.reference = data.reference;
                    this.answered = true;
                } catch (error) {
                    console.error('Error submitting answer:', error);
                    alert('Gagal menyimpan jawaban. Coba lagi.');
                } finally {
                    this.isLoading = false;
                }
            }
        }
    }
    </script>
</x-app-layout>
