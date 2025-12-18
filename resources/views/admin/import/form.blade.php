@extends('layouts.admin')

@section('title', 'Import Soal')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.quizzes.questions.index', $quiz) }}" class="text-indigo-600 hover:text-indigo-800">
            ← Kembali ke Daftar Soal
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Import Soal dari Excel</h2>
        <p class="text-gray-600 mb-6">Quiz: <strong>{{ $quiz->title }}</strong></p>
        
        <!-- Instructions -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <h4 class="font-bold text-blue-800 mb-2">📋 Format Excel yang Dibutuhkan:</h4>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• <strong>content</strong>: Teks pertanyaan</li>
                <li>• <strong>type</strong>: single_choice, multiple_choice, ordering, atau matching</li>
                <li>• <strong>points</strong>: Nilai poin (default: 10)</li>
                <li>• <strong>option_1 - option_4</strong>: Pilihan jawaban</li>
                <li>• <strong>correct_options</strong>: Nomor jawaban benar (contoh: "1" atau "1,3")</li>
                <li>• <strong>pair_1 - pair_4</strong>: Pasangan untuk tipe matching</li>
            </ul>
        </div>

        <!-- Download Template -->
        <div class="mb-6">
            <a href="{{ route('admin.import.template') }}" 
               class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                📥 Download Template Excel
            </a>
        </div>
        
        <form action="{{ route('admin.import.process', $quiz) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Pilih File Excel *</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                       class="w-full border rounded-lg px-4 py-2">
                <p class="text-sm text-gray-500 mt-1">Format: .xlsx, .xls, atau .csv (max 10MB)</p>
                @error('file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.quizzes.questions.index', $quiz) }}" 
                   class="px-6 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    📤 Import Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
