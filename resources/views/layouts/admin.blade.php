<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin - {{ config('app.name', 'QuizMaster') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Dark Mode Script -->
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
        function toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('darkMode', isDark);
            updateDarkModeIcon();
        }
        function updateDarkModeIcon() {
            const isDark = document.documentElement.classList.contains('dark');
            const moonIcon = document.getElementById('moonIcon');
            const sunIcon = document.getElementById('sunIcon');
            if (moonIcon && sunIcon) {
                moonIcon.style.display = isDark ? 'none' : 'inline';
                sunIcon.style.display = isDark ? 'inline' : 'none';
            }
        }
        document.addEventListener('DOMContentLoaded', updateDarkModeIcon);
    </script>

    <!-- Universal Dark Mode CSS -->
    <style>
        html.dark { color-scheme: dark; }
        html.dark body { background-color: #111827 !important; color: #f3f4f6 !important; }
        html.dark h1, html.dark h2, html.dark h3, html.dark h4, html.dark h5, html.dark h6 { color: #ffffff !important; }
        html.dark p, html.dark span, html.dark label, html.dark li { color: #e5e7eb !important; }
        html.dark a { color: #818cf8 !important; }
        html.dark .bg-white { background-color: #1f2937 !important; }
        html.dark .bg-gray-50, html.dark .bg-gray-100 { background-color: #1f2937 !important; }
        html.dark .bg-gray-200 { background-color: #374151 !important; }
        html.dark .border, html.dark .border-gray-200, html.dark .border-gray-300 { border-color: #4b5563 !important; }
        html.dark input, html.dark textarea, html.dark select { background-color: #374151 !important; color: #f3f4f6 !important; border-color: #4b5563 !important; }
        html.dark table, html.dark thead { background-color: #1f2937 !important; }
        html.dark th, html.dark td { color: #e5e7eb !important; border-color: #374151 !important; }
        html.dark tbody tr:hover { background-color: #374151 !important; }
        html.dark .text-gray-500, html.dark .text-gray-600, html.dark .text-gray-700, html.dark .text-gray-800 { color: #d1d5db !important; }
        html.dark .shadow, html.dark .shadow-sm { box-shadow: 0 1px 3px 0 rgba(0,0,0,0.3) !important; }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-indigo-800 text-white min-h-screen">
            <div class="p-4">
                <h1 class="text-2xl font-bold" style="color: white !important;">🎮 QuizMaster</h1>
                <p class="text-indigo-200 text-sm" style="color: #a5b4fc !important;">Admin Panel</p>
            </div>
            <nav class="mt-6">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}"
                   style="color: white !important;">
                    <span class="mr-3">📊</span> Dashboard
                </a>
                <a href="{{ route('admin.quizzes.index') }}" 
                   class="flex items-center px-4 py-3 {{ request()->routeIs('admin.quizzes.*') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}"
                   style="color: white !important;">
                    <span class="mr-3">📝</span> Kelola Kuis
                </a>
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}"
                   style="color: white !important;">
                    <span class="mr-3">👥</span> Kelola User
                </a>
                <hr class="my-4 border-indigo-600">
                <a href="{{ route('quiz.lobby') }}" class="flex items-center px-4 py-3 hover:bg-indigo-700" style="color: white !important;">
                    <span class="mr-3">🎮</span> Ke Aplikasi
                </a>
                <form method="POST" action="{{ route('logout') }}" class="px-4 py-3">
                    @csrf
                    <button type="submit" class="flex items-center w-full text-left hover:text-indigo-200" style="color: white !important;">
                        <span class="mr-3">🚪</span> Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <h2 class="text-xl font-semibold text-gray-800">
                        @yield('title', 'Dashboard')
                    </h2>
                    <div class="flex items-center gap-4">
                        <span class="text-gray-600">{{ auth()->user()->name }}</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Dark Mode Toggle Button -->
    <button onclick="toggleDarkMode()"
            class="fixed bottom-6 right-6 w-14 h-14 rounded-full shadow-lg flex items-center justify-center text-2xl hover:scale-110 transition-transform z-50"
            style="background-color: #1f2937;"
            title="Toggle Dark Mode">
        <span id="moonIcon" style="color: white;">🌙</span>
        <span id="sunIcon" style="display: none; color: #fbbf24;">☀️</span>
    </button>
</body>
</html>
