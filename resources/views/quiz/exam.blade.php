<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ujian - {{ $quiz->title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Dark Mode Script -->
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);
            updateDarkModeIcon();
        }
        function updateDarkModeIcon() {
            const isDark = document.documentElement.classList.contains('dark');
            const moonIcon = document.getElementById('moonIcon');
            const sunIcon = document.getElementById('sunIcon');
            if (moonIcon && sunIcon) {
                moonIcon.style.display = isDark ? 'none' : 'inline';
                sunIcon.style.display = isDark ? 'inline' : 'none';
            }
        }
        document.addEventListener('DOMContentLoaded', updateDarkModeIcon);
    </script>

    <!-- Universal Dark Mode CSS -->
    <style>
        html.dark { color-scheme: dark; }
        html.dark body { background-color: #111827 !important; color: #f3f4f6 !important; }
        html.dark h1, html.dark h2, html.dark h3, html.dark h4, html.dark h5, html.dark h6 { color: #ffffff !important; }
        html.dark p, html.dark span, html.dark label, html.dark li { color: #e5e7eb !important; }
        html.dark .bg-white { background-color: #1f2937 !important; }
        html.dark .bg-gray-50, html.dark .bg-gray-100 { background-color: #1f2937 !important; }
        html.dark .bg-gray-200 { background-color: #374151 !important; }
        html.dark .border, html.dark .border-gray-200 { border-color: #4b5563 !important; }
        html.dark input, html.dark textarea, html.dark select { background-color: #374151 !important; color: #f3f4f6 !important; border-color: #4b5563 !important; }
        html.dark .text-gray-500, html.dark .text-gray-600, html.dark .text-gray-700, html.dark .text-gray-800 { color: #d1d5db !important; }
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100" x-data="examApp()" x-init="initVisibilityDetection()">
    <!-- Anti-cheat: Disable right click -->
    <script>
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('copy', e => e.preventDefault());
    </script>

    <!-- Warning Modal -->
    <div x-show="showWarning" x-cloak 
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md mx-4 text-center">
            <span class="text-6xl">⚠️</span>
            <h3 class="text-xl font-bold mt-4 text-red-600">Peringatan!</h3>
            <p class="text-gray-600 mt-2">Anda meninggalkan halaman ujian. Ini tercatat oleh sistem.</p>
            <p class="text-sm text-gray-500 mt-2">Pelanggaran: <span x-text="violations"></span>x</p>
            <button @click="showWarning = false" 
                    class="mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                Kembali ke Ujian
            </button>
        </div>
    </div>

    <div class="min-h-screen flex">
        <!-- Main Question Area -->
        <div class="flex-1 p-6">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow p-4 mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold">{{ $quiz->title }}</h1>
                    <p class="text-sm text-gray-500">Soal <span x-text="currentIndex + 1"></span> dari {{ count($questions) }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Timer -->
                    <div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg font-mono text-xl font-bold"
                         :class="{ 'animate-pulse': remainingTime < 60 }">
                        ⏱️ <span x-text="formatTime(remainingTime)"></span>
                    </div>
                </div>
            </div>

            <!-- Question Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <template x-for="(question, index) in questions" :key="question.id">
                    <div x-show="currentIndex === index">
                        <!-- Question Type Badge -->
                        <div class="flex items-center mb-4">
                            <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm">
                                <span x-text="getTypeLabel(question.type)"></span>
                            </span>
                            <span class="ml-2 text-gray-500 text-sm" x-text="question.points + ' poin'"></span>
                        </div>

                        <!-- Question Content -->
                        <div class="prose max-w-none mb-6 text-lg" x-html="question.content"></div>

                        <!-- Options - Single Choice -->
                        <template x-if="question.type === 'single_choice'">
                            <div class="space-y-3">
                                <template x-for="option in question.options" :key="option.id">
                                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all"
                                           :class="{ 
                                               'border-indigo-600 bg-indigo-50': answers[question.id] == option.id,
                                               'border-gray-200 hover:border-indigo-300': answers[question.id] != option.id
                                           }">
                                        <input type="radio" 
                                               :name="'question_' + question.id" 
                                               :value="option.id"
                                               x-model="answers[question.id]"
                                               @change="saveAnswer(question.id)"
                                               class="hidden">
                                        <span class="w-6 h-6 border-2 rounded-full mr-4 flex items-center justify-center"
                                              :class="answers[question.id] == option.id ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300'">
                                            <span x-show="answers[question.id] == option.id" class="w-2 h-2 bg-white rounded-full"></span>
                                        </span>
                                        <span x-text="option.option_text"></span>
                                    </label>
                                </template>
                            </div>
                        </template>

                        <!-- Options - Multiple Choice -->
                        <template x-if="question.type === 'multiple_choice'">
                            <div class="space-y-3">
                                <p class="text-sm text-gray-500 mb-2">Pilih semua yang benar:</p>
                                <template x-for="option in question.options" :key="option.id">
                                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all"
                                           :class="{ 
                                               'border-indigo-600 bg-indigo-50': isSelected(question.id, option.id),
                                               'border-gray-200 hover:border-indigo-300': !isSelected(question.id, option.id)
                                           }">
                                        <input type="checkbox" 
                                               :value="option.id"
                                               @change="toggleMultiple(question.id, option.id)"
                                               :checked="isSelected(question.id, option.id)"
                                               class="hidden">
                                        <span class="w-6 h-6 border-2 rounded mr-4 flex items-center justify-center"
                                              :class="isSelected(question.id, option.id) ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300'">
                                            <span x-show="isSelected(question.id, option.id)" class="text-white text-sm">✓</span>
                                        </span>
                                        <span x-text="option.option_text"></span>
                                    </label>
                                </template>
                            </div>
                        </template>

                        <!-- Ordering (Interactive) -->
                        <template x-if="question.type === 'ordering'">
                            <div class="space-y-2">
                                <p class="text-sm text-gray-500 mb-4">📋 Seret atau gunakan tombol untuk mengurutkan:</p>
                                <div class="space-y-2" x-data="{ draggedIdx: null }">
                                    <template x-for="(optId, idx) in getOrderingList(question.id, question.options)" :key="optId">
                                        <div class="flex items-center gap-3 p-4 bg-white border-2 rounded-lg shadow-sm transition-all hover:shadow-md cursor-move"
                                             :class="{ 'border-indigo-500 bg-indigo-50': draggedIdx === idx, 'border-gray-200': draggedIdx !== idx }"
                                             draggable="true"
                                             @dragstart="draggedIdx = idx"
                                             @dragend="draggedIdx = null"
                                             @dragover.prevent
                                             @drop="reorderItem(question.id, question.options, draggedIdx, idx); draggedIdx = null">
                                            
                                            <!-- Order Number -->
                                            <span class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold text-sm"
                                                  x-text="idx + 1"></span>
                                            
                                            <!-- Option Text -->
                                            <span class="flex-1 font-medium" x-text="getOptionText(question.options, optId)"></span>
                                            
                                            <!-- Up/Down Buttons -->
                                            <div class="flex flex-col gap-1">
                                                <button type="button"
                                                        @click="moveItemUp(question.id, question.options, idx)"
                                                        :disabled="idx === 0"
                                                        class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded flex items-center justify-center disabled:opacity-30 disabled:cursor-not-allowed">
                                                    ⬆️
                                                </button>
                                                <button type="button"
                                                        @click="moveItemDown(question.id, question.options, idx)"
                                                        :disabled="idx === getOrderingList(question.id, question.options).length - 1"
                                                        class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded flex items-center justify-center disabled:opacity-30 disabled:cursor-not-allowed">
                                                    ⬇️
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <p class="text-xs text-gray-400 mt-2">💡 Tip: Seret item atau klik tombol ⬆️⬇️ untuk mengubah urutan</p>
                            </div>
                        </template>

                        <!-- Matching (Simplified) -->
                        <template x-if="question.type === 'matching'">
                            <div class="space-y-3">
                                <p class="text-sm text-gray-500 mb-2">Pasangkan dengan benar:</p>
                                <template x-for="option in question.options" :key="option.id">
                                    <div class="flex items-center gap-4 p-4 border rounded-lg">
                                        <span class="flex-1 font-medium" x-text="option.option_text"></span>
                                        <span class="text-gray-400">→</span>
                                        <select @change="setMatching(question.id, option.id, $event.target.value)"
                                                class="flex-1 border rounded px-3 py-2">
                                            <option value="">Pilih pasangan...</option>
                                            <template x-for="opt in question.options" :key="'pair_' + opt.id">
                                                <option :value="opt.pair_text" x-text="opt.pair_text"></option>
                                            </template>
                                        </select>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Navigation -->
                <div class="flex justify-between mt-8 pt-6 border-t">
                    <button @click="prevQuestion()" 
                            :disabled="currentIndex === 0"
                            class="px-6 py-3 border rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        ← Sebelumnya
                    </button>
                    <button x-show="currentIndex < questions.length - 1" 
                            @click="nextQuestion()"
                            class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Selanjutnya →
                    </button>
                    <button x-show="currentIndex === questions.length - 1" 
                            @click="submitExam()"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        ✓ Selesai & Submit
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar - Question Navigation -->
        <div class="w-80 bg-white shadow-lg p-6">
            <h3 class="font-bold mb-4">Navigasi Soal</h3>
            <div class="grid grid-cols-5 gap-2 mb-6">
                @foreach($questions as $index => $q)
                    <button @click="goToQuestion({{ $index }})"
                            class="w-10 h-10 rounded-lg text-sm font-medium transition-all"
                            :class="{
                                'bg-indigo-600 text-white': currentIndex === {{ $index }},
                                'bg-green-500 text-white': currentIndex !== {{ $index }} && answers[{{ $q->id }}],
                                'bg-gray-200': currentIndex !== {{ $index }} && !answers[{{ $q->id }}]
                            }">
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>

            <div class="border-t pt-4">
                <p class="text-sm text-gray-500 mb-2">Status:</p>
                <div class="flex items-center text-sm mb-1">
                    <span class="w-4 h-4 bg-green-500 rounded mr-2"></span>
                    <span>Terjawab: <span x-text="answeredCount"></span></span>
                </div>
                <div class="flex items-center text-sm">
                    <span class="w-4 h-4 bg-gray-200 rounded mr-2"></span>
                    <span>Belum: <span x-text="{{ count($questions) }} - answeredCount"></span></span>
                </div>
            </div>

            <form action="{{ route('exam.submit', $attempt) }}" method="POST" class="mt-6"
                  @submit="isSubmitting = true">
                @csrf
                <button type="submit" 
                        @click="confirmSubmit($event)"
                        class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 font-medium">
                    🏁 Kumpulkan Sekarang
                </button>
            </form>
        </div>
    </div>

    <script>
    function examApp() {
        return {
            currentIndex: 0,
            remainingTime: {{ $remainingTime }},
            showWarning: false,
            violations: 0,
            isSubmitting: false,
            answers: {},
            questions: @json($questions),
            storageKey: 'quizmaster_attempt_{{ $attempt->id }}',

            init() {
                // Load existing answers from database
                @foreach($attempt->answers as $ans)
                    this.answers[{{ $ans->question_id }}] = @json($ans->user_answer);
                @endforeach

                // Restore from localStorage if available (offline backup)
                this.loadFromLocalStorage();

                // Start timer
                this.startTimer();

                // Watch for answer changes to save to localStorage
                this.$watch('answers', () => {
                    this.saveToLocalStorage();
                }, { deep: true });
            },

            // Save answers to localStorage as backup
            saveToLocalStorage() {
                try {
                    localStorage.setItem(this.storageKey, JSON.stringify({
                        answers: this.answers,
                        currentIndex: this.currentIndex,
                        timestamp: Date.now()
                    }));
                } catch (e) {
                    console.warn('localStorage not available:', e);
                }
            },

            // Load answers from localStorage
            loadFromLocalStorage() {
                try {
                    const saved = localStorage.getItem(this.storageKey);
                    if (saved) {
                        const data = JSON.parse(saved);
                        // Only restore if data is less than 3 hours old
                        if (data.timestamp && (Date.now() - data.timestamp) < 3 * 60 * 60 * 1000) {
                            // Merge localStorage answers with database answers (localStorage takes priority for unsaved)
                            Object.keys(data.answers || {}).forEach(qId => {
                                if (!this.answers[qId] && data.answers[qId]) {
                                    this.answers[qId] = data.answers[qId];
                                    // Sync to server
                                    this.saveAnswer(parseInt(qId));
                                }
                            });
                            this.currentIndex = data.currentIndex || 0;
                        }
                    }
                } catch (e) {
                    console.warn('Failed to load from localStorage:', e);
                }
            },

            // Clear localStorage after submit
            clearLocalStorage() {
                try {
                    localStorage.removeItem(this.storageKey);
                } catch (e) {}
            },

            startTimer() {
                setInterval(() => {
                    if (this.remainingTime > 0) {
                        this.remainingTime--;
                    } else {
                        this.autoSubmit();
                    }
                }, 1000);

                // Sync with server every 30 seconds
                setInterval(() => this.syncTime(), 30000);
            },

            formatTime(seconds) {
                const m = Math.floor(seconds / 60);
                const s = seconds % 60;
                return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            },

            getTypeLabel(type) {
                const labels = {
                    'single_choice': 'Pilihan Ganda',
                    'multiple_choice': 'Pilihan Ganda (Multiple)',
                    'ordering': 'Urutan',
                    'matching': 'Menjodohkan'
                };
                return labels[type] || type;
            },

            get answeredCount() {
                return Object.keys(this.answers).filter(k => this.answers[k]).length;
            },

            isSelected(qId, optId) {
                return Array.isArray(this.answers[qId]) && this.answers[qId].includes(optId);
            },

            toggleMultiple(qId, optId) {
                if (!Array.isArray(this.answers[qId])) {
                    this.answers[qId] = [];
                }
                const idx = this.answers[qId].indexOf(optId);
                if (idx > -1) {
                    this.answers[qId].splice(idx, 1);
                } else {
                    this.answers[qId].push(optId);
                }
                this.saveAnswer(qId);
            },

            setOrdering(qId, optId, value) {
                if (!this.answers[qId]) this.answers[qId] = {};
                this.answers[qId][optId] = parseInt(value);
                this.saveAnswer(qId);
            },

            // Get ordered list of option IDs for ordering question
            getOrderingList(qId, options) {
                if (!this.orderingLists) this.orderingLists = {};
                if (!this.orderingLists[qId]) {
                    // Initialize with original order
                    this.orderingLists[qId] = options.map(o => o.id);
                }
                return this.orderingLists[qId];
            },

            // Get option text by ID
            getOptionText(options, optId) {
                const opt = options.find(o => o.id === optId);
                return opt ? opt.option_text : '';
            },

            // Move item up in ordering
            moveItemUp(qId, options, idx) {
                if (idx <= 0) return;
                const list = this.getOrderingList(qId, options);
                [list[idx-1], list[idx]] = [list[idx], list[idx-1]];
                this.updateOrderingAnswer(qId, options);
            },

            // Move item down in ordering
            moveItemDown(qId, options, idx) {
                const list = this.getOrderingList(qId, options);
                if (idx >= list.length - 1) return;
                [list[idx], list[idx+1]] = [list[idx+1], list[idx]];
                this.updateOrderingAnswer(qId, options);
            },

            // Reorder via drag and drop
            reorderItem(qId, options, fromIdx, toIdx) {
                if (fromIdx === null || fromIdx === toIdx) return;
                const list = this.getOrderingList(qId, options);
                const [item] = list.splice(fromIdx, 1);
                list.splice(toIdx, 0, item);
                this.updateOrderingAnswer(qId, options);
            },

            // Update answer after reordering
            updateOrderingAnswer(qId, options) {
                const list = this.getOrderingList(qId, options);
                this.answers[qId] = {};
                list.forEach((optId, idx) => {
                    this.answers[qId][optId] = idx + 1;
                });
                this.saveAnswer(qId);
            },

            setMatching(qId, optId, value) {
                if (!this.answers[qId]) this.answers[qId] = {};
                this.answers[qId][optId] = value;
                this.saveAnswer(qId);
            },

            async saveAnswer(questionId) {
                try {
                    await fetch('{{ route("exam.answer", $attempt) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            question_id: questionId,
                            answer: this.answers[questionId]
                        })
                    });
                } catch (e) {
                    console.error('Failed to save answer:', e);
                }
            },

            async syncTime() {
                try {
                    const res = await fetch('{{ route("exam.time", $attempt) }}');
                    const data = await res.json();
                    this.remainingTime = data.remaining;
                    if (data.expired) {
                        this.autoSubmit();
                    }
                } catch (e) {
                    console.error('Failed to sync time:', e);
                }
            },

            initVisibilityDetection() {
                // Use visibilitychange API - doesn't trigger on confirm() dialogs
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden && !this.isSubmitting) {
                        this.handleVisibilityChange();
                    }
                });
            },

            handleVisibilityChange() {
                this.violations++;
                this.showWarning = true;
                
                // Record violation on server
                fetch('{{ route("exam.violation", $attempt) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).catch(e => console.error('Failed to record violation:', e));
            },

            confirmSubmit(event) {
                event.preventDefault();
                
                if (confirm('Yakin ingin mengumpulkan? Jawaban Anda tidak dapat diubah.')) {
                    this.isSubmitting = true;
                    this.clearLocalStorage();
                    event.target.closest('form').submit();
                }
            },

            nextQuestion() {
                if (this.currentIndex < this.questions.length - 1) {
                    this.currentIndex++;
                }
            },

            prevQuestion() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                }
            },

            goToQuestion(index) {
                this.currentIndex = index;
            },

            submitExam() {
                if (confirm('Yakin ingin mengumpulkan? Jawaban Anda tidak dapat diubah.')) {
                    document.querySelector('form').submit();
                }
            },

            autoSubmit() {
                alert('Waktu habis! Ujian akan dikumpulkan otomatis.');
                document.querySelector('form').submit();
            }
        }
    }
    </script>

    <!-- Dark Mode Toggle Button -->
    <button onclick="toggleDarkMode()"
            class="fixed bottom-6 right-6 w-14 h-14 rounded-full shadow-lg flex items-center justify-center text-2xl hover:scale-110 transition-transform z-50"
            style="background-color: #1f2937;"
            title="Toggle Dark Mode">
        <span id="moonIcon" style="color: white;">🌙</span>
        <span id="sunIcon" style="display: none; color: #fbbf24;">☀️</span>
    </button>
</body>
</html>
