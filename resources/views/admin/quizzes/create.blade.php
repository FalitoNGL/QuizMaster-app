@extends('layouts.admin')

@section('title', 'Tambah Kuis Baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Tambah Kuis Baru</h2>
        
        <form action="{{ route('admin.quizzes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Judul Kuis *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="Contoh: Keamanan Jaringan Dasar">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Kategori</label>
                <select name="category_id" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->icon }} {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Jelaskan tentang kuis ini...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Thumbnail</label>
                <input type="file" name="thumbnail" accept="image/*"
                       class="w-full border rounded-lg px-4 py-2">
                @error('thumbnail')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Waktu Pengerjaan (menit) *</label>
                <input type="number" name="time_limit" value="{{ old('time_limit', 30) }}" 
                       min="1" max="180" required
                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('time_limit')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="ml-2 text-gray-700">Aktifkan kuis (dapat diakses user)</span>
                </label>
            </div>

            <!-- Question Bank Settings -->
            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-700 mb-3">🎲 Pengaturan Question Bank</h4>
                
                <div class="mb-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="randomize_questions" value="1" {{ old('randomize_questions') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-gray-700">Acak urutan soal setiap attempt</span>
                    </label>
                </div>
                
                <div>
                    <label class="block text-gray-700 text-sm mb-1">Batasi jumlah soal per attempt (kosongkan = semua soal)</label>
                    <input type="number" name="question_limit" value="{{ old('question_limit') }}" 
                           min="1" placeholder="Contoh: 20"
                           class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Soal akan diambil secara acak dari pool jika dikombinasikan dengan opsi acak</p>
                </div>
            </div>

            <!-- Schedule Settings -->
            <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                <h4 class="font-medium text-gray-700 mb-3">📅 Jadwal Quiz (Opsional)</h4>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm mb-1">Waktu Mulai</label>
                        <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" 
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan = langsung tersedia</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm mb-1">Waktu Berakhir</label>
                        <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" 
                               class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan = tidak ada batas waktu</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.quizzes.index') }}" 
                   class="px-6 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Simpan Kuis
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
