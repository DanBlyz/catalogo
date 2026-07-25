<!-- Topbar Header -->
<header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 md:px-6 flex items-center justify-between sticky top-0 z-30 transition-colors duration-200 shadow-sm">
    
    <!-- Left Section: Sidebar Toggle & Search -->
    <div class="flex items-center gap-4">
        <!-- Desktop Sidebar Collapse Toggle -->
        <button 
            @click="sidebarCollapsed = !sidebarCollapsed" 
            class="hidden md:flex p-2 rounded-xl text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <!-- Mobile Sidebar Open Toggle -->
        <button 
            @click="sidebarOpen = true" 
            class="flex md:hidden p-2 rounded-xl text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <!-- Quick Search input -->
        <div class="relative hidden sm:block w-64 lg:w-80">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </div>
            <input 
                type="text" 
                placeholder="Buscar productos, clientes, SKU..." 
                class="w-full pl-9 pr-4 py-1.5 text-xs bg-slate-100 dark:bg-slate-800/80 border-0 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 transition"
            >
        </div>
    </div>

    <!-- Right Section: Theme Toggle, Notifications, Profile Dropdown -->
    <div class="flex items-center gap-3">
        
        <!-- Dark / Light Mode Switcher -->
        <button 
            type="button" 
            @click="
                darkMode = !darkMode; 
                localStorage.setItem('theme', darkMode ? 'dark' : 'light'); 
                if(darkMode) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark'); }
            "
            class="p-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
            title="Cambiar tema">
            <i x-show="!darkMode" class="fa-solid fa-moon text-base"></i>
            <i x-show="darkMode" class="fa-solid fa-sun text-base text-amber-400"></i>
        </button>

        <!-- Notification Bell placeholder -->
        <div class="relative" x-data="{ open: false }">
            <button 
                @click="open = !open" 
                class="p-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition relative">
                <i class="fa-solid fa-bell text-base"></i>
                <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-rose-500"></span>
            </button>
            <div 
                x-show="open" 
                @click.away="open = false" 
                x-transition 
                class="absolute right-0 mt-2 w-72 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl p-4 z-50">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-2 mb-3">
                    <span class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider">Notificaciones</span>
                    <span class="text-[10px] bg-brand-500/10 text-brand-500 dark:text-brand-400 px-2 py-0.5 rounded-full font-bold">2 nuevas</span>
                </div>
                <div class="space-y-3 text-xs">
                    <div class="flex gap-3 items-start">
                        <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-slate-200">Stock Bajo Alerta</p>
                            <p class="text-slate-400 text-[11px]">3 productos llegaron al límite mínimo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="h-6 w-[1px] bg-slate-200 dark:bg-slate-800 mx-1"></div>

        <!-- User Profile Dropdown -->
        <div class="relative" x-data="{ dropdownOpen: false }">
            <button 
                @click="dropdownOpen = !dropdownOpen" 
                class="flex items-center gap-2 p-1 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition focus:outline-none">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-brand-600 to-blue-500 text-white font-bold flex items-center justify-center text-xs shadow-md">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                </div>
                <span class="hidden sm:block text-xs font-semibold text-slate-700 dark:text-slate-200 max-w-[120px] truncate">
                    {{ Auth::user()->name ?? 'Usuario' }}
                </span>
                <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform" :class="{ 'rotate-180': dropdownOpen }"></i>
            </button>

            <!-- Dropdown Menu -->
            <div 
                x-show="dropdownOpen" 
                @click.away="dropdownOpen = false" 
                x-transition 
                class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl py-2 z-50">
                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800">
                    <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name ?? 'Usuario' }}</p>
                    <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                </div>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <i class="fa-solid fa-user-gear text-slate-400"></i>
                    <span>Mi Perfil</span>
                </a>

                <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition font-semibold">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</header>
