@extends('layouts.admin')

@section('title', 'Analytics - ' . $quiz->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.quizzes.index') }}" class="text-indigo-600 hover:text-indigo-800">
        ← Kembali ke Daftar Kuis
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Total Attempts -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Attempts</p>
                <p class="text-3xl font-bold text-gray-800">{{ $attempts->count() }}</p>
            </div>
            <span class="text-4xl">📝</span>
        </div>
    </div>

    <!-- Average Score -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Rata-rata Skor</p>
                <p class="text-3xl font-bold text-blue-600">{{ $avgScore }}%</p>
            </div>
            <span class="text-4xl">📊</span>
        </div>
    </div>

    <!-- Average Time -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Rata-rata Waktu</p>
                <p class="text-3xl font-bold text-green-600">{{ $avgTime }} min</p>
            </div>
            <span class="text-4xl">⏱️</span>
        </div>
    </div>

    <!-- Completion Rate -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Completion Rate</p>
                <p class="text-3xl font-bold text-purple-600">{{ $completionRate }}%</p>
            </div>
            <span class="text-4xl">✅</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Score Distribution Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4 text-gray-800">📈 Distribusi Nilai</h3>
        <canvas id="scoreChart" height="200"></canvas>
    </div>

    <!-- Hardest Questions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-4 text-gray-800">🔥 Soal Tersulit</h3>
        @if(count($hardestQuestions) > 0)
            <div class="space-y-3">
                @foreach($hardestQuestions as $index => $stat)
                    <div class="border rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-600">#{{ $index + 1 }}</span>
                            <span class="text-sm px-2 py-1 rounded {{ $stat['correct_rate'] < 50 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $stat['correct_rate'] }}% benar
                            </span>
                        </div>
                        <p class="text-sm text-gray-800 line-clamp-2">
                            {!! Str::limit(strip_tags($stat['question']->content), 100) !!}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $stat['correct_answers'] }}/{{ $stat['total_answers'] }} jawaban benar
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">Belum ada data</p>
        @endif
    </div>
</div>

<!-- Recent Attempts -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-bold mb-4 text-gray-800">🕐 Attempt Terbaru</h3>
    @if($recentAttempts->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 text-gray-600">User</th>
                        <th class="text-left py-2 text-gray-600">Skor</th>
                        <th class="text-left py-2 text-gray-600">Persentase</th>
                        <th class="text-left py-2 text-gray-600">Waktu</th>
                        <th class="text-left py-2 text-gray-600">Pelanggaran</th>
                        <th class="text-left py-2 text-gray-600">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAttempts as $attempt)
                        <tr class="border-b">
                            <td class="py-2 text-gray-800">{{ $attempt->user->name ?? 'Unknown' }}</td>
                            <td class="py-2 text-gray-800">{{ $attempt->total_score }}/{{ $attempt->max_score }}</td>
                            <td class="py-2">
                                @php $pct = $attempt->max_score > 0 ? round(($attempt->total_score / $attempt->max_score) * 100) : 0; @endphp
                                <span class="px-2 py-1 rounded text-xs {{ $pct >= 70 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $pct }}%
                                </span>
                            </td>
                            <td class="py-2 text-gray-600">
                                {{ $attempt->start_time && $attempt->end_time ? $attempt->start_time->diffInMinutes($attempt->end_time) : 0 }} min
                            </td>
                            <td class="py-2">
                                @if(($attempt->violations ?? 0) > 0)
                                    <span class="px-2 py-1 rounded text-xs bg-orange-100 text-orange-700">
                                        ⚠️ {{ $attempt->violations }}x
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-2 text-gray-600">
                                {{ $attempt->created_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 text-center py-4">Belum ada attempt</p>
    @endif
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('scoreChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['0-20%', '21-40%', '41-60%', '61-80%', '81-100%'],
            datasets: [{
                label: 'Jumlah Peserta',
                data: [
                    {{ $scoreRanges['0-20'] }},
                    {{ $scoreRanges['21-40'] }},
                    {{ $scoreRanges['41-60'] }},
                    {{ $scoreRanges['61-80'] }},
                    {{ $scoreRanges['81-100'] }}
                ],
                backgroundColor: [
                    '#ef4444',
                    '#f97316',
                    '#eab308',
                    '#22c55e',
                    '#3b82f6'
                ],
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>
@endsection
