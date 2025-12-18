@extends('layouts.admin')

@section('title', 'Bulk Edit Soal - ' . $quiz->title)

@section('content')
<div x-data="bulkEditor()" class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold">Bulk Edit: {{ $quiz->title }}</h2>
            <p class="text-gray-500">{{ $questions->total() }} soal total</p>
        </div>
        <a href="{{ route('admin.quizzes.questions.index', $quiz) }}" 
           class="border px-4 py-2 rounded-lg hover:bg-gray-50">← Kembali</a>
    </div>

    <!-- Bulk Actions Bar -->
    <div class="bg-white rounded-lg shadow p-4 sticky top-0 z-10">
        <div class="flex flex-wrap items-center gap-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" @change="toggleAll($event)" class="rounded">
                <span class="text-gray-700">Pilih Semua</span>
            </label>
            
            <span class="text-gray-500">|</span>
            
            <span class="text-sm text-gray-600">
                <span x-text="selectedIds.length">0</span> soal dipilih
            </span>
            
            <span class="text-gray-500">|</span>
            
            <!-- Bulk Delete -->
            <form action="{{ route('admin.questions.bulk-delete', $quiz) }}" method="POST" 
                  @submit="beforeSubmit($event)" class="inline">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="question_ids[]" :value="id">
                </template>
                <button type="submit" 
                        :disabled="selectedIds.length === 0"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    🗑️ Hapus
                </button>
            </form>

            <!-- Bulk Move -->
            <form action="{{ route('admin.questions.bulk-move', $quiz) }}" method="POST" 
                  @submit="beforeSubmit($event)" class="inline flex items-center gap-2">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="question_ids[]" :value="id">
                </template>
                <select name="target_quiz_id" required 
                        class="border rounded px-3 py-2">
                    <option value="">Pindah ke...</option>
                    @foreach($allQuizzes as $q)
                        <option value="{{ $q->id }}">{{ $q->title }}</option>
                    @endforeach
                </select>
                <button type="submit" 
                        :disabled="selectedIds.length === 0"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    📦 Pindah
                </button>
            </form>

            <!-- Bulk Update Points -->
            <form action="{{ route('admin.questions.bulk-points', $quiz) }}" method="POST" 
                  @submit="beforeSubmit($event)" class="inline flex items-center gap-2">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="question_ids[]" :value="id">
                </template>
                <input type="number" name="points" required min="1" max="100" placeholder="Poin"
                       class="border rounded px-3 py-2 w-20">
                <button type="submit" 
                        :disabled="selectedIds.length === 0"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    💰 Set Poin
                </button>
            </form>
        </div>
    </div>

    <!-- Questions List -->
    <div class="bg-white rounded-lg shadow">
        <div class="divide-y">
            @forelse($questions as $index => $question)
                <div class="p-4 hover:bg-gray-50 flex items-start gap-4">
                    <input type="checkbox" 
                           :value="{{ $question->id }}"
                           @change="toggleSelection({{ $question->id }})"
                           :checked="selectedIds.includes({{ $question->id }})"
                           class="mt-1 rounded">
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs">
                                #{{ $questions->firstItem() + $index }}
                            </span>
                            <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded text-xs">
                                {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                            </span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                {{ $question->points }} pts
                            </span>
                        </div>
                        <div class="text-sm text-gray-800 line-clamp-2">
                            {!! Str::limit(strip_tags($question->content), 150) !!}
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('admin.quizzes.questions.edit', [$quiz, $question]) }}" 
                           class="text-yellow-600 hover:text-yellow-800 p-2">✏️</a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    Tidak ada soal
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-4">
        {{ $questions->links() }}
    </div>
</div>

<script>
function bulkEditor() {
    return {
        selectedIds: [],
        
        toggleAll(event) {
            if (event.target.checked) {
                this.selectedIds = @json($questions->pluck('id'));
            } else {
                this.selectedIds = [];
            }
        },
        
        toggleSelection(id) {
            const idx = this.selectedIds.indexOf(id);
            if (idx > -1) {
                this.selectedIds.splice(idx, 1);
            } else {
                this.selectedIds.push(id);
            }
        },
        
        beforeSubmit(event) {
            if (this.selectedIds.length === 0) {
                event.preventDefault();
                alert('Pilih minimal 1 soal!');
                return false;
            }
            if (event.submitter.innerText.includes('Hapus')) {
                if (!confirm('Yakin hapus ' + this.selectedIds.length + ' soal?')) {
                    event.preventDefault();
                    return false;
                }
            }
            return true;
        }
    }
}
</script>
@endsection
