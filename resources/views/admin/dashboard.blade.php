@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-full">
                <span class="text-2xl">👥</span>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Users</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-full">
                <span class="text-2xl">📝</span>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Kuis</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_quizzes'] }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-full">
                <span class="text-2xl">❓</span>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Total Soal</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_questions'] }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-full">
                <span class="text-2xl">✅</span>
            </div>
            <div class="ml-4">
                <p class="text-gray-500 text-sm">Pengerjaan Selesai</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['completed_attempts'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Attempts -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Aktivitas Terbaru</h3>
        </div>
        <div class="p-6">
            @forelse($recentAttempts as $attempt)
                <div class="flex items-center justify-between py-3 border-b last:border-0">
                    <div>
                        <p class="font-medium">{{ $attempt->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $attempt->quiz->title }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold {{ $attempt->status === 'completed' ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $attempt->status === 'completed' ? $attempt->total_score . ' pts' : 'In Progress' }}
                        </p>
                        <p class="text-xs text-gray-400">{{ $attempt->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">Belum ada aktivitas</p>
            @endforelse
        </div>
    </div>

    <!-- Active Users -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">User Paling Aktif</h3>
        </div>
        <div class="p-6">
            @forelse($activeUsers as $index => $user)
                <div class="flex items-center justify-between py-3 border-b last:border-0">
                    <div class="flex items-center">
                        <span class="w-8 h-8 flex items-center justify-center bg-indigo-100 text-indigo-600 font-bold rounded-full">
                            {{ $index + 1 }}
                        </span>
                        <div class="ml-3">
                            <p class="font-medium">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">Level {{ $user->level }} • {{ $user->xp }} XP</p>
                        </div>
                    </div>
                    <p class="text-indigo-600 font-semibold">{{ $user->quiz_attempts_count }} kuis</p>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">Belum ada user aktif</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
