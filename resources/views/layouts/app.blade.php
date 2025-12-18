<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'QuizMaster') }}</title>

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
            html.dark a:hover { color: #a5b4fc !important; }
            html.dark .bg-white { background-color: #1f2937 !important; }
            html.dark .bg-gray-50, html.dark .bg-gray-100 { background-color: #1f2937 !important; }
            html.dark .bg-gray-200 { background-color: #374151 !important; }
            html.dark .border, html.dark .border-gray-200, html.dark .border-gray-300 { border-color: #4b5563 !important; }
            html.dark input, html.dark textarea, html.dark select { background-color: #374151 !important; color: #f3f4f6 !important; border-color: #4b5563 !important; }
            html.dark input::placeholder, html.dark textarea::placeholder { color: #9ca3af !important; }
            html.dark table, html.dark thead { background-color: #1f2937 !important; }
            html.dark th, html.dark td { color: #e5e7eb !important; border-color: #374151 !important; }
            html.dark tbody tr:hover { background-color: #374151 !important; }
            html.dark .text-gray-500 { color: #9ca3af !important; }
            html.dark .text-gray-600 { color: #d1d5db !important; }
            html.dark .text-gray-700 { color: #e5e7eb !important; }
            html.dark .text-gray-800, html.dark .text-gray-900 { color: #f3f4f6 !important; }
            html.dark .shadow { box-shadow: 0 1px 3px 0 rgba(0,0,0,0.3) !important; }
            html.dark nav { background-color: #1f2937 !important; }
            html.dark .ring-white { --tw-ring-color: #374151 !important; }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <div class="text-gray-800">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
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
