@extends('layouts.admin')

@section('title', 'Edit Soal')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Edit Soal</h2>
        
        <form action="{{ route('admin.quizzes.questions.update', [$quiz, $question]) }}" method="POST" 
              enctype="multipart/form-data" x-data="questionForm()">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Tipe Soal *</label>
                <select name="type" x-model="type" required
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="single_choice">Pilihan Ganda (Single)</option>
                    <option value="multiple_choice">Pilihan Ganda (Multiple)</option>
                    {{-- Hidden for now - focus on tested question types --}}
                    {{-- <option value="ordering">Urutan (Ordering)</option> --}}
                    {{-- <option value="matching">Menjodohkan (Matching)</option> --}}
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Pertanyaan *</label>
                <textarea name="content" rows="4" required
                          class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">{{ old('content', $question->content) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Poin *</label>
                <input type="number" name="points" value="{{ old('points', $question->points) }}" 
                       min="1" max="100" required
                       class="w-32 border rounded-lg px-4 py-2">
            </div>

            <!-- Options Section -->
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Pilihan Jawaban *</label>
                
                <template x-for="(option, index) in options" :key="index">
                    <div class="flex gap-2 mb-2 items-center">
                        <input type="text" :name="'options['+index+'][text]'" 
                               x-model="option.text" required placeholder="Teks jawaban"
                               class="flex-1 border rounded-lg px-4 py-2">
                        
                        <template x-if="type === 'matching'">
                            <input type="text" :name="'options['+index+'][pair_text]'" 
                                   x-model="option.pair_text" placeholder="Pasangan"
                                   class="flex-1 border rounded-lg px-4 py-2">
                        </template>
                        
                        <template x-if="type === 'ordering'">
                            <input type="number" :name="'options['+index+'][order_sequence]'" 
                                   x-model="option.order_sequence" placeholder="Urutan" min="1"
                                   class="w-20 border rounded-lg px-4 py-2">
                        </template>
                        
                        <template x-if="type !== 'ordering'">
                            <label class="flex items-center whitespace-nowrap">
                                <input type="checkbox" :name="'options['+index+'][is_correct]'" 
                                       x-model="option.is_correct" value="1"
                                       class="mr-1 rounded">
                                <span class="text-sm">Benar</span>
                            </label>
                        </template>
                        
                        <button type="button" @click="removeOption(index)" 
                                class="text-red-500 hover:text-red-700 px-2">✕</button>
                    </div>
                </template>
                
                <button type="button" @click="addOption()" 
                        class="text-indigo-600 hover:text-indigo-800 text-sm mt-2">
                    + Tambah Pilihan
                </button>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.quizzes.questions.index', $quiz) }}" 
                   class="px-6 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Update Soal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function questionForm() {
    return {
        type: '{{ $question->type }}',
        options: @json($question->options->map(fn($o) => [
            'text' => $o->option_text,
            'is_correct' => $o->is_correct,
            'pair_text' => $o->pair_text ?? '',
            'order_sequence' => $o->order_sequence ?? 0
        ])),
        addOption() {
            this.options.push({ 
                text: '', 
                is_correct: false, 
                pair_text: '', 
                order_sequence: this.options.length + 1 
            });
        },
        removeOption(index) {
            if (this.options.length > 2) {
                this.options.splice(index, 1);
            }
        }
    }
}
</script>
@endsection
