@extends('layouts.admin')

@section('title', 'Kelola User')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold">Kelola User</h2>
</div>

<!-- Search -->
<div class="mb-4">
    <form action="{{ route('admin.users.index') }}" method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Cari nama atau email..."
               class="flex-1 max-w-md border rounded-lg px-4 py-2">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            Cari
        </button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level/XP</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kuis Dikerjakan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bergabung</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($users as $user)
                <tr class="hover:bg-gray-50 {{ $user->role === 'banned' ? 'bg-red-50' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <p class="font-medium">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-medium">Lv. {{ $user->level }}</span>
                        <span class="text-gray-500 text-sm">({{ $user->xp }} XP)</span>
                    </td>
                    <td class="px-6 py-4">{{ $user->quiz_attempts_count }} kuis</td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.users.show', $user) }}" 
                               class="text-blue-600 hover:text-blue-800">👁️</a>
                            <form action="{{ route('admin.users.toggle-ban', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="{{ $user->role === 'banned' ? 'text-green-600' : 'text-red-600' }}"
                                        title="{{ $user->role === 'banned' ? 'Aktifkan' : 'Blokir' }}">
                                    {{ $user->role === 'banned' ? '✓' : '🚫' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        Tidak ada user ditemukan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
