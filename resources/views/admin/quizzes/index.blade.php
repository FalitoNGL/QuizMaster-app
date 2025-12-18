@extends('layouts.admin')

@section('title', 'Kelola Kuis')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Daftar Kuis</h2>
    <a href="{{ route('admin.quizzes.create') }}" 
       class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
        + Tambah Kuis
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Soal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($quizzes as $quiz)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($quiz->thumbnail)
                                <img src="{{ Storage::url($quiz->thumbnail) }}" class="w-10 h-10 rounded object-cover mr-3">
                            @else
                                <div class="w-10 h-10 bg-indigo-100 rounded flex items-center justify-center mr-3">
                                    <span class="text-indigo-600">📝</span>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium">{{ $quiz->title }}</p>
                                <p class="text-sm text-gray-500">{{ Str::limit($quiz->description, 50) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">
                            {{ $quiz->questions_count }} soal
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $quiz->time_limit }} menit</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-sm {{ $quiz->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $quiz->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.quizzes.analytics', $quiz) }}" 
                               class="text-purple-600 hover:text-purple-800" title="Analytics">📊</a>
                            <a href="{{ route('admin.quizzes.questions.index', $quiz) }}" 
                               class="text-blue-600 hover:text-blue-800" title="Kelola Soal">📄</a>
                            <a href="{{ route('admin.quizzes.edit', $quiz) }}" 
                               class="text-yellow-600 hover:text-yellow-800" title="Edit">✏️</a>
                            <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" 
                                  onsubmit="return confirm('Yakin hapus kuis ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        Belum ada kuis. <a href="{{ route('admin.quizzes.create') }}" class="text-indigo-600">Buat sekarang</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $quizzes->links() }}
</div>
@endsection
