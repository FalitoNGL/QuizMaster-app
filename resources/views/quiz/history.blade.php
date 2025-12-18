<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Riwayat Pengerjaan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('quiz.lobby') }}" class="text-indigo-600 hover:text-indigo-800">
                    ← Kembali ke Lobby
                </a>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kuis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Skor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($attempts as $attempt)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="font-medium">{{ $attempt->quiz->title }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-indigo-600">{{ $attempt->total_score }}</span>
                                    <span class="text-gray-500 text-sm">pts</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $attempt->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('result.show', $attempt) }}" 
                                       class="text-indigo-600 hover:text-indigo-800">
                                        Lihat Detail →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    Belum ada riwayat pengerjaan.
                                    <a href="{{ route('quiz.lobby') }}" class="text-indigo-600 block mt-2">
                                        Mulai kuis pertamamu! →
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $attempts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
