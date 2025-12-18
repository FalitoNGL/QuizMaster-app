@extends('layouts.admin')

@section('title', 'Detail User - ' . $user->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-800">
        ← Kembali ke Daftar User
    </a>
</div>

<!-- User Profile Card -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="flex items-start gap-6">
        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div class="flex-1">
            <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
            <p class="text-gray-500">{{ $user->email }}</p>
            <div class="flex items-center gap-4 mt-2">
                <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm">
                    Level {{ $user->level }}
                </span>
                <span class="text-yellow-600">⭐ {{ $user->xp }} XP</span>
                <span class="text-gray-500 text-sm">
                    Bergabung: {{ $user->created_at->format('d M Y') }}
                </span>
            </div>
        </div>
        <div>
            @if($user->role === 'banned')
                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">🚫 Diblokir</span>
            @else
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">✅ Aktif</span>
            @endif
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_attempts'] }}</p>
        <p class="text-sm text-gray-500">Total Attempt</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-green-600">{{ $stats['completed_attempts'] }}</p>
        <p class="text-sm text-gray-500">Selesai</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-purple-600">{{ $stats['total_score'] }}</p>
        <p class="text-sm text-gray-500">Total Skor</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-indigo-600">{{ $stats['avg_score'] }}</p>
        <p class="text-sm text-gray-500">Rata-rata</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-orange-600">{{ $stats['total_violations'] }}</p>
        <p class="text-sm text-gray-500">Pelanggaran</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['total_achievements'] }}</p>
        <p class="text-sm text-gray-500">Achievements</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Attempt History -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">📜 Riwayat Attempt</h3>
        @if($attempts->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 text-gray-600">Quiz</th>
                            <th class="text-left py-2 text-gray-600">Skor</th>
                            <th class="text-left py-2 text-gray-600">Violations</th>
                            <th class="text-left py-2 text-gray-600">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attempts as $attempt)
                            <tr class="border-b">
                                <td class="py-3">
                                    {{ $attempt->quiz->title ?? 'Quiz Dihapus' }}
                                </td>
                                <td class="py-3">
                                    <span class="font-bold text-indigo-600">{{ $attempt->total_score }}</span>
                                    <span class="text-gray-400">/{{ $attempt->max_score }}</span>
                                </td>
                                <td class="py-3">
                                    @if(($attempt->violations ?? 0) > 0)
                                        <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs">
                                            ⚠️ {{ $attempt->violations }}x
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 text-gray-500">
                                    {{ $attempt->created_at->format('d M Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $attempts->links() }}
            </div>
        @else
            <p class="text-gray-500 text-center py-4">Belum ada riwayat attempt</p>
        @endif
    </div>

    <!-- Achievements -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4">🏆 Achievements</h3>
        @if($user->achievements->count() > 0)
            <div class="space-y-3">
                @foreach($user->achievements as $achievement)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span class="text-3xl">{{ $achievement->icon }}</span>
                        <div>
                            <p class="font-medium">{{ $achievement->name }}</p>
                            <p class="text-xs text-gray-500">{{ $achievement->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">Belum ada achievement</p>
        @endif
    </div>
</div>
@endsection
