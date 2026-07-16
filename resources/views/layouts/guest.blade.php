<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Portal Magang Pelindo') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <!-- PWA Support -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0A1128">
        <link rel="apple-touch-icon" href="/build/icons/icon-192x192.png">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js').then(function(registration) {
                        console.log('ServiceWorker registration successful');
                    }, function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
                });
            }
        </script>
    </head>
    <body class="font-sans antialiased" style="background-image: url('{{ asset('bg-login.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden" style="background-color: rgba(0, 0, 0, 0.85); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);">
            <!-- Background Decoration -->
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-[128px] opacity-10"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-indigo-600 rounded-full mix-blend-multiply filter blur-[128px] opacity-10"></div>

            <div class="relative z-10 flex flex-col items-center">
                <h2 class="text-3xl font-extrabold text-white tracking-wide mb-8 font-['Hanken_Grotesk']">Portal Magang</h2>

                <div class="w-full sm:max-w-md px-8 py-8 bg-white rounded-3xl relative z-10" style="box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.7), 0 0 50px rgba(0, 0, 0, 0.45);">
                    {{ $slot }}
                </div>
                
                <p class="mt-8 text-sm text-gray-500 font-medium tracking-wide">PORTAL MAGANG PELINDO</p>
            </div>
        </div>
    </body>
</html>
