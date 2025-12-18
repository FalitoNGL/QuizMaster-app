<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pembahasan - {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('result.show', $attempt) }}" class="text-indigo-600 hover:text-indigo-800">
                    ← Kembali ke Hasil
                </a>
            </div>

            <div class="space-y-6">
                @foreach($reviewData as $index => $data)
                    <div class="bg-white rounded-lg shadow p-6 {{ $data['is_correct'] ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500' }}">
                        <!-- Question Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded text-sm font-medium">
                                    Soal #{{ $index + 1 }}
                                </span>
                                <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded text-xs">
                                    {{ ucfirst(str_replace('_', ' ', $data['question']->type)) }}
                                </span>
                            </div>
                            <span class="px-3 py-1 rounded text-sm font-medium {{ $data['is_correct'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $data['is_correct'] ? '✓ Benar' : '✗ Salah' }}
                            </span>
                        </div>

                        <!-- Question Content -->
                        <div class="prose max-w-none mb-4">
                            {!! $data['question']->content !!}
                        </div>

                        @if($data['question']->type === 'single_choice' || $data['question']->type === 'multiple_choice')
                            <!-- Options Review for Single/Multiple Choice -->
                            <div class="space-y-2 mb-4">
                                @foreach($data['question']->options as $option)
                                    @php
                                        $isUserAnswer = false;
                                        $isCorrect = $option->is_correct;
                                        
                                        if ($data['question']->type === 'single_choice') {
                                            $isUserAnswer = $data['user_answer'] == $option->id;
                                        } elseif ($data['question']->type === 'multiple_choice') {
                                            $isUserAnswer = is_array($data['user_answer']) && in_array($option->id, $data['user_answer']);
                                        }
                                    @endphp
                                    <div class="flex items-center p-3 rounded-lg 
                                        {{ $isCorrect ? 'bg-green-50 border border-green-300' : '' }}
                                        {{ $isUserAnswer && !$isCorrect ? 'bg-red-50 border border-red-300' : '' }}
                                        {{ !$isUserAnswer && !$isCorrect ? 'bg-gray-50 border border-gray-200' : '' }}">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center mr-3
                                            {{ $isCorrect ? 'bg-green-500 text-white' : '' }}
                                            {{ $isUserAnswer && !$isCorrect ? 'bg-red-500 text-white' : '' }}
                                            {{ !$isUserAnswer && !$isCorrect ? 'bg-gray-300' : '' }}">
                                            @if($isCorrect)
                                                ✓
                                            @elseif($isUserAnswer)
                                                ✗
                                            @endif
                                        </span>
                                        <span class="{{ $isCorrect ? 'text-green-800 font-medium' : 'text-gray-600' }}">
                                            {{ $option->option_text }}
                                        </span>
                                        @if($isUserAnswer)
                                            <span class="ml-auto text-sm {{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                                                (Jawaban Anda)
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                        @elseif($data['question']->type === 'ordering')
                            <!-- Ordering Comparison -->
                            @if(!$data['is_correct'])
                                <div class="grid md:grid-cols-2 gap-4 mb-4">
                                    <!-- User's Answer -->
                                    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                                        <p class="text-sm font-medium text-red-800 mb-3">❌ Jawaban Anda:</p>
                                        <div class="space-y-2">
                                            @php
                                                $userOrdering = $data['user_answer'] ?? [];
                                                $optionsById = $data['question']->options->keyBy('id');
                                                // Sort by user's sequence
                                                if (is_array($userOrdering) && count($userOrdering) > 0) {
                                                    $sortedUserAnswer = collect($userOrdering)->sort();
                                                } else {
                                                    $sortedUserAnswer = collect([]);
                                                }
                                            @endphp
                                            @if($sortedUserAnswer->count() > 0)
                                                @foreach($sortedUserAnswer as $optId => $seq)
                                                    <div class="flex items-center gap-2 p-2 bg-white rounded border">
                                                        <span class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-sm font-bold">{{ $seq }}</span>
                                                        <span class="text-gray-600">{{ $optionsById[$optId]->option_text ?? '?' }}</span>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-red-600 italic">Tidak dijawab</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Correct Answer -->
                                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                        <p class="text-sm font-medium text-green-800 mb-3">✅ Jawaban Benar:</p>
                                        <div class="space-y-2">
                                            @foreach($data['question']->options->sortBy('order_sequence') as $option)
                                                <div class="flex items-center gap-2 p-2 bg-white rounded border">
                                                    <span class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">{{ $option->order_sequence }}</span>
                                                    <span class="text-gray-600">{{ $option->option_text }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-green-50 rounded-lg p-4 border border-green-200 mb-4">
                                    <p class="text-sm font-medium text-green-800 mb-3">✅ Urutan Anda Benar:</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($data['question']->options->sortBy('order_sequence') as $option)
                                            <div class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg border">
                                                <span class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">{{ $option->order_sequence }}</span>
                                                <span>{{ $option->option_text }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        @elseif($data['question']->type === 'matching')
                            <!-- Matching Comparison -->
                            @if(!$data['is_correct'])
                                <div class="grid md:grid-cols-2 gap-4 mb-4">
                                    <!-- User's Answer -->
                                    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                                        <p class="text-sm font-medium text-red-800 mb-3">❌ Jawaban Anda:</p>
                                        <div class="space-y-2">
                                            @php
                                                $userMatching = $data['user_answer'] ?? [];
                                                $optionsById = $data['question']->options->keyBy('id');
                                            @endphp
                                            @if(is_array($userMatching) && count($userMatching) > 0)
                                                @foreach($data['question']->options as $option)
                                                    @php
                                                        $userPair = $userMatching[$option->id] ?? null;
                                                        $isThisCorrect = $userPair === $option->pair_text;
                                                    @endphp
                                                    <div class="flex items-center gap-2 p-2 bg-white rounded border {{ $isThisCorrect ? 'border-green-400' : 'border-red-400' }}">
                                                        <span class="font-medium">{{ $option->option_text }}</span>
                                                        <span class="text-gray-400">→</span>
                                                        <span class="{{ $isThisCorrect ? 'text-green-600' : 'text-red-600' }}">
                                                            {{ $userPair ?: '(tidak dijawab)' }}
                                                        </span>
                                                        @if($isThisCorrect)
                                                            <span class="ml-auto text-green-500">✓</span>
                                                        @else
                                                            <span class="ml-auto text-red-500">✗</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-red-600 italic">Tidak dijawab</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Correct Answer -->
                                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                        <p class="text-sm font-medium text-green-800 mb-3">✅ Jawaban Benar:</p>
                                        <div class="space-y-2">
                                            @foreach($data['question']->options as $option)
                                                <div class="flex items-center gap-2 p-2 bg-white rounded border border-green-300">
                                                    <span class="font-medium">{{ $option->option_text }}</span>
                                                    <span class="text-gray-400">→</span>
                                                    <span class="text-green-700 font-medium">{{ $option->pair_text }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-green-50 rounded-lg p-4 border border-green-200 mb-4">
                                    <p class="text-sm font-medium text-green-800 mb-3">✅ Pasangan Anda Benar:</p>
                                    <div class="space-y-2">
                                        @foreach($data['question']->options as $option)
                                            <div class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg border">
                                                <span class="font-medium">{{ $option->option_text }}</span>
                                                <span class="text-gray-400">→</span>
                                                <span class="text-green-700">{{ $option->pair_text }}</span>
                                                <span class="ml-auto text-green-500">✓</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Points Info -->
                        <div class="text-sm text-gray-500 mt-4 pt-4 border-t">
                            <span>Poin: {{ $data['is_correct'] ? $data['question']->points : 0 }} / {{ $data['question']->points }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('quiz.lobby') }}" 
                   class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 font-medium">
                    🏠 Kembali ke Lobby
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
