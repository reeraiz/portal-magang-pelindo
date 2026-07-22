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
        <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />

        <!-- PWA Support -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0A1128">
        <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

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
    <body class="font-sans antialiased text-gray-900 bg-gray-50">
        <!-- Mobile Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content Wrapper -->
        <div class="lg:ml-[260px] flex flex-col min-h-screen">
            <!-- Header -->
            @include('layouts.header')

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 overflow-auto">
                <!-- Toast Notification -->
                <!-- Toast Notifications -->
                <div class="fixed top-20 right-4 z-50 flex flex-col gap-3 w-full max-w-sm">
                    @if(session('status'))
                    <div id="toast-notification" class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 shadow-lg animate-fade-in">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-sm font-semibold flex-1">{{ session('status') }}</p>
                        <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 shadow-lg animate-fade-in relative group">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold mb-1">Terjadi kesalahan:</p>
                            <ul class="text-xs space-y-0.5 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button onclick="this.parentElement.remove()" class="absolute top-4 right-4 text-red-400 hover:text-red-600 transition-colors opacity-0 group-hover:opacity-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    @endif
                </div>

                @yield('content')
            </main>
        </div>

        <style>
            @keyframes fade-in {
                from { opacity: 0; transform: translateY(-8px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in {
                animation: fade-in 0.3s ease-out;
            }
        </style>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('main-sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            // Auto-dismiss toast after 5 seconds
            const toast = document.getElementById('toast-notification');
            if (toast) {
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-8px)';
                    toast.style.transition = 'all 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
            }

            // Global SweetAlert2 for confirm-form class
            document.addEventListener('DOMContentLoaded', function() {
                const confirmForms = document.querySelectorAll('.confirm-form');
                confirmForms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const msg = this.getAttribute('data-confirm-msg') || 'Apakah Anda yakin ingin melanjutkan?';
                        Swal.fire({
                            title: 'Konfirmasi',
                            text: msg,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#2563eb',
                            cancelButtonColor: '#f87171',
                            confirmButtonText: 'Ya, Lanjutkan',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'rounded-2xl',
                                confirmButton: 'rounded-xl',
                                cancelButton: 'rounded-xl'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                    });
                });
            });

            // PWA Installation Handler
            let deferredPrompt;
            const pwaInstallBtn = document.getElementById('pwa-install-btn');

            window.addEventListener('beforeinstallprompt', (e) => {
                // Prevent the mini-infobar from appearing on mobile
                e.preventDefault();
                // Stash the event so it can be triggered later.
                deferredPrompt = e;
                // Update UI notify the user they can install the PWA
                if (pwaInstallBtn) {
                    pwaInstallBtn.classList.remove('hidden');
                }
            });

            if (pwaInstallBtn) {
                pwaInstallBtn.addEventListener('click', async () => {
                    if (deferredPrompt) {
                        // Show the install prompt
                        deferredPrompt.prompt();
                        // Wait for the user to respond to the prompt
                        const { outcome } = await deferredPrompt.userChoice;
                        if (outcome === 'accepted') {
                            pwaInstallBtn.classList.add('hidden');
                        }
                        deferredPrompt = null;
                    } else {
                        // Fallback for iOS or unsupported browsers
                        Swal.fire({
                            title: 'Install Aplikasi',
                            html: 'Untuk pengguna iOS: Buka menu Share (bagikan) di Safari lalu pilih <b>"Add to Home Screen"</b>.<br><br>Untuk Android: Buka menu browser (titik tiga) lalu pilih <b>"Install App"</b> atau <b>"Add to Home Screen"</b>.',
                            icon: 'info',
                            confirmButtonColor: '#2563eb',
                            confirmButtonText: 'Mengerti',
                            customClass: {
                                popup: 'rounded-2xl',
                                confirmButton: 'rounded-xl'
                            }
                        });
                    }
                });

                // Tampilkan tombol untuk iOS karena iOS Safari tidak memicu beforeinstallprompt
                const isIos = () => {
                    const userAgent = window.navigator.userAgent.toLowerCase();
                    return /iphone|ipad|ipod/.test(userAgent);
                };
                // Deteksi jika sudah dalam mode PWA (standalone) di iOS
                const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone);

                if (isIos() && !isInStandaloneMode()) {
                    pwaInstallBtn.classList.remove('hidden');
                }
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </body>
</html>
