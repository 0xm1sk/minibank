<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Mini-Bank</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-900">
        <div class="min-h-screen bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Unified Notification System -->
            @if(session('success') || session('error') || session('info'))
                <div id="notification" class="fixed top-4 right-4 z-50 transform translate-x-full transition-all duration-500 ease-in-out">
                    @if(session('success'))
                        <div class="bg-green-900 border border-green-700 text-green-200 px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-900 border border-red-700 text-red-200 px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="bg-blue-900 border border-blue-700 text-blue-200 px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('info') }}</span>
                        </div>
                    @endif
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const notification = document.getElementById('notification');
                        if (notification) {
                            // Slide in notification
                            setTimeout(() => {
                                notification.classList.remove('translate-x-full');
                                notification.classList.add('translate-x-0');
                            }, 100);

                            // Auto-hide after 5 seconds
                            setTimeout(() => {
                                notification.classList.remove('translate-x-0');
                                notification.classList.add('translate-x-full');
                                
                                // Remove from DOM after animation
                                setTimeout(() => {
                                    notification.remove();
                                }, 500);
                            }, 5000);
                        }
                    });
                </script>
            @endif

            </div>
    </body>
</html>
