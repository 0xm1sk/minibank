<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Mini Bank') }}</title>

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
            
            /* Fix yellow colors in light mode */
            .light .text-yellow-400 {
                color: #ea580c !important; /* Changed to orange */
            }
            
            .light .shadow-md {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
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
    <body class="font-sans text-gray-100 antialiased bg-gray-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-900">
            <!-- Theme Toggle -->
            <div class="absolute top-4 right-4">
                <div id="theme-toggle" class="theme-toggle">
                    <div class="theme-toggle-slider">
                        <svg class="w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>

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
    </body>
</html>

