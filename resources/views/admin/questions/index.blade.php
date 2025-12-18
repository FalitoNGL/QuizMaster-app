@extends('layouts.admin')

@section('title', 'Kelola Soal - ' . $quiz->title)

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold">Soal: {{ $quiz->title }}</h2>
        <p class="text-gray-500">Total {{ $questions->total() }} soal</p>
    </div>
    <div class="flex space-x-2">
        <a href="{{ route('admin.quizzes.index') }}" 
           class="border px-4 py-2 rounded-lg hover:bg-gray-50">← Kembali</a>
        <a href="{{ route('admin.questions.bulk', $quiz) }}" 
           class="border border-purple-600 text-purple-600 px-4 py-2 rounded-lg hover:bg-purple-50">
            ✏️ Bulk Edit
        </a>
        <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" 
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            + Tambah Soal
        </a>
    </div>
</div>

<div class="space-y-4">
    @forelse($questions as $index => $question)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-sm mr-2">
                            #{{ $questions->firstItem() + $index }}
                        </span>
                        <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded text-sm mr-2">
                            {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                        </span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">
                            {{ $question->points }} pts
                        </span>
                    </div>
                    <div class="prose max-w-none">
                        {!! $question->content !!}
                    </div>
                    
                    <!-- Show Options -->
                    <div class="mt-4 space-y-2">
                        @foreach($question->options as $opt)
                            <div class="flex items-center text-sm {{ $opt->is_correct ? 'text-green-700 font-medium' : 'text-gray-600' }}">
                                <span class="w-5">{{ $opt->is_correct ? '✓' : '○' }}</span>
                                <span>{{ $opt->option_text }}</span>
                                @if($question->type === 'matching' && $opt->pair_text)
                                    <span class="mx-2">→</span>
                                    <span>{{ $opt->pair_text }}</span>
                                @endif
                                @if($question->type === 'ordering' && $opt->order_sequence)
                                    <span class="ml-2 text-gray-400">(urutan: {{ $opt->order_sequence }})</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex space-x-2 ml-4">
                    <a href="{{ route('admin.quizzes.questions.edit', [$quiz, $question]) }}" 
                       class="text-yellow-600 hover:text-yellow-800 p-2" title="Edit">✏️</a>
                    <form action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}" 
                          method="POST" onsubmit="return confirm('Yakin hapus soal ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 p-2" title="Hapus">🗑️</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 mb-4">Belum ada soal untuk kuis ini.</p>
            <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" 
               class="text-indigo-600 hover:text-indigo-800">Tambah soal pertama →</a>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $questions->links() }}
</div>
@endsection
