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

        <!-- Theme Toggle Styles -->
        <style>
            /* Light mode styles */
            .light {
                color-scheme: light;
            }
            
            .light body {
                background-color: #f8fafc;
                color: #1e293b;
            }
            
            .light .bg-gray-900 {
                background-color: #ffffff !important;
            }
            
            .light .bg-gray-800 {
                background-color: #ffffff !important;
            }
            
            .light .bg-gray-700 {
                background-color: #f1f5f9 !important;
            }
            
            .light .text-gray-200 {
                color: #334155 !important;
            }
            
            .light .text-gray-300 {
                color: #475569 !important;
            }
            
            .light .text-gray-400 {
                color: #64748b !important;
            }
            
            .light .text-gray-500 {
                color: #64748b !important;
            }
            
            .light .border-gray-600 {
                border-color: #e2e8f0 !important;
            }
            
            .light .border-gray-700 {
                border-color: #e2e8f0 !important;
            }
            
            .light .bg-indigo-600 {
                background-color: #3b82f6 !important;
            }
            
            .light .bg-green-600 {
                background-color: #10b981 !important;
            }
            
            .light .bg-red-600 {
                background-color: #ef4444 !important;
            }
            
            .light .bg-purple-600 {
                background-color: #8b5cf6 !important;
            }
            
            .light .bg-yellow-900 {
                background-color: #f59e0b !important;
            }
            
            .light .text-yellow-300 {
                color: #d97706 !important;
            }
            
            .light .text-green-400 {
                color: #10b981 !important;
            }
            
            .light .text-red-400 {
                color: #ef4444 !important;
            }
            
            .light .text-indigo-400 {
                color: #3b82f6 !important;
            }
            
            .light .shadow-md {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
            }
            
            .light .shadow-lg {
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            }
            
            /* Light mode table hover */
            .light .hover\\:bg-gray-700:hover {
                background-color: #f1f5f9 !important;
            }
            
            /* Light mode notification styles */
            .light .bg-green-900 {
                background-color: #dcfce7 !important;
            }
            
            .light .text-green-200 {
                color: #166534 !important;
            }
            
            .light .border-green-700 {
                border-color: #bbf7d0 !important;
            }
            
            .light .bg-red-900 {
                background-color: #fef2f2 !important;
            }
            
            .light .text-red-200 {
                color: #991b1b !important;
            }
            
            .light .border-red-700 {
                border-color: #fecaca !important;
            }
            
            .light .bg-blue-900 {
                background-color: #eff6ff !important;
            }
            
            .light .text-blue-200 {
                color: #1e40af !important;
            }
            
            .light .border-blue-700 {
                border-color: #bfdbfe !important;
            }
            
            /* Light mode form inputs */
            .light .border-gray-600 {
                border-color: #d1d5db !important;
            }
            
            .light .bg-gray-700 {
                background-color: #f9fafb !important;
            }
            
            .light .focus\\:border-indigo-500:focus {
                border-color: #3b82f6 !important;
            }
            
            .light .focus\\:ring-indigo-500:focus {
                --tw-ring-color: #3b82f6 !important;
            }
            
            /* Light mode status badges - FIX YELLOW COLORS */
            .light .bg-green-900 {
                background-color: #dcfce7 !important;
            }
            
            .light .text-green-300 {
                color: #166534 !important;
            }
            
            .light .bg-yellow-900 {
                background-color: #dbeafe !important; /* Changed to light blue */
            }
            
            .light .text-yellow-300 {
                color: #1e40af !important; /* Changed to dark blue */
            }
            
            .light .bg-red-900 {
                background-color: #fef2f2 !important;
            }
            
            .light .text-red-300 {
                color: #991b1b !important;
            }
            
            /* Fix yellow warning text in forms */
            .light .text-yellow-400 {
                color: #ea580c !important; /* Changed to orange */
            }
            
            /* Theme toggle button */
            .theme-toggle {
                position: relative;
                width: 60px;
                height: 30px;
                background: #4b5563;
                border-radius: 15px;
                cursor: pointer;
                transition: background 0.3s ease;
            }
            
            .theme-toggle:hover {
                background: #6b7280;
            }
            
            .light .theme-toggle {
                background: #cbd5e1;
            }
            
            .light .theme-toggle:hover {
                background: #94a3b8;
            }
            
            .theme-toggle-slider {
                position: absolute;
                top: 3px;
                left: 3px;
                width: 24px;
                height: 24px;
                background: white;
                border-radius: 50%;
                transition: transform 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .light .theme-toggle-slider {
                transform: translateX(30px);
            }
            
            /* Smooth transitions */
            * {
                transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-900">
        <div class="min-h-screen bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-gray-800 shadow">
                    <div class="py-6 sm:py-8 lg:py-12">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
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

            <!-- Theme Toggle Script -->
            <script>
                // Theme management
                const html = document.documentElement;
                const themeToggle = document.getElementById('theme-toggle');
                
                // Load saved theme or default to dark
                const savedTheme = localStorage.getItem('theme') || 'dark';
                html.classList.toggle('light', savedTheme === 'light');
                
                // Update toggle button
                function updateToggleIcon() {
                    if (themeToggle) {
                        const isLight = html.classList.contains('light');
                        themeToggle.innerHTML = isLight ? 
                            '<div class="theme-toggle-slider"><svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/></svg></div>' :
                            '<div class="theme-toggle-slider"><svg class="w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg></div>';
                    }
                }
                
                // Initialize toggle
                updateToggleIcon();
                
                // Toggle theme
                if (themeToggle) {
                    themeToggle.addEventListener('click', function() {
                        const isLight = html.classList.contains('light');
                        html.classList.toggle('light', !isLight);
                        localStorage.setItem('theme', isLight ? 'dark' : 'light');
                        updateToggleIcon();
                    });
                }
                
                // Update toggle when theme changes
                const observer = new MutationObserver(updateToggleIcon);
                observer.observe(html, { attributes: true, attributeFilter: ['class'] });
            </script>
        </div>
    </body>
</html>
