<header class="fixed top-0 right-0 w-full lg:w-[calc(100%-260px)] h-16 bg-[#0A1128] shadow-md flex justify-between items-center px-4 sm:px-6 z-40">
    <!-- Left section: Hamburger + Tab Title -->
    <div class="flex items-center gap-4">
        <!-- Hamburger button (mobile only) -->
        <button onclick="toggleSidebar()" class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center text-white hover:bg-white/20 transition-colors lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
        </button>

        <h2 class="text-lg sm:text-xl font-bold text-white tracking-tight leading-none" style="font-family: 'Hanken Grotesk', sans-serif;">
            @yield('header')
        </h2>
    </div>

    <!-- Right section: profile info -->
    <div class="flex items-center gap-5">
        <!-- User Profile -->
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity group cursor-pointer" title="Pengaturan Profil">
            <div class="text-right hidden sm:block">
                <p class="text-white font-semibold text-sm leading-tight group-hover:text-blue-200 transition-colors">{{ Auth::user()->name ?? 'Guest' }}</p>
                <p class="text-blue-200 text-xs mt-0.5 leading-none font-medium">{{ Auth::user() ? (Auth::user()->role === 'intern' ? 'Internship Program' : (Auth::user()->role === 'pembimbing' ? 'Pembimbing / Mentor' : 'Administrator')) : 'Visitor' }}</p>
            </div>
            
            <div class="w-10 h-10 rounded-full border-2 border-blue-300 shadow-md bg-white/10 group-hover:border-white transition-colors flex items-center justify-center text-blue-200 group-hover:text-white overflow-hidden">
                @if(Auth::user() && Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                @endif
            </div>
        </a>
    </div>
</header>
<!-- Spacer for fixed header -->
<div class="h-16 w-full"></div>
