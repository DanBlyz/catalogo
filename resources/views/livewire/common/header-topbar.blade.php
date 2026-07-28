<header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 md:px-6 flex items-center justify-between sticky top-0 z-30 transition-colors duration-200 shadow-sm">
    
    <!-- Left Section: Sidebar Toggle & Global Search -->
    <div class="flex items-center gap-3 md:gap-4 flex-1 max-w-xl">
        <!-- Desktop Sidebar Collapse Toggle -->
        <button 
            type="button"
            @click="sidebarCollapsed = !sidebarCollapsed" 
            class="hidden md:flex p-2 rounded-xl text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <!-- Mobile Sidebar Open Toggle -->
        <button 
            type="button"
            @click="sidebarOpen = true" 
            class="flex md:hidden p-2 rounded-xl text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <!-- Quick Search Container -->
        <div class="relative w-full max-w-md" wire:click.outside="resetSearch">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>

                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="searchQuery"
                    placeholder="Buscar productos, clientes, SKU, recibos..." 
                    class="w-full pl-9 pr-8 py-1.5 text-xs bg-slate-100 dark:bg-slate-800/90 border border-transparent focus:border-brand-500 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500/20 focus:outline-none transition shadow-inner font-medium"
                    @keydown.escape.window="$wire.resetSearch()"
                >

                @if (!empty($searchQuery))
                    <button 
                        type="button" 
                        wire:click="resetSearch" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-white transition">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                @endif
            </div>

            <!-- Instant Search Results Dropdown -->
            @if ($showSearchDropdown)
                <div class="absolute left-0 right-0 mt-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden z-50 divide-y divide-slate-100 dark:divide-slate-800 max-h-96 overflow-y-auto">
                    
                    <!-- 📦 Productos Section -->
                    @if (!empty($searchResultsProductos))
                        <div class="p-2">
                            <div class="px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-brand-600 dark:text-brand-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-box"></i> Productos
                            </div>
                            <div class="space-y-1">
                                @foreach ($searchResultsProductos as $p)
                                    <a href="{{ route('catalogo.productos') }}" class="flex items-center justify-between p-2 hover:bg-slate-50 dark:hover:bg-slate-800/60 rounded-xl transition group">
                                        <div class="truncate">
                                            <div class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-brand-500 transition">
                                                {{ $p['nombre'] }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-mono">
                                                SKU: {{ $p['sku'] }} &bull; {{ $p['categoria'] }}
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0 ml-2">
                                            <div class="text-xs font-mono font-bold text-slate-900 dark:text-white">Bs {{ number_format($p['precio_venta'], 2) }}</div>
                                            <div class="text-[10px] font-mono font-bold {{ $p['stock'] <= 5 ? 'text-rose-500' : 'text-emerald-600 dark:text-emerald-400' }}">
                                                Stock: {{ number_format($p['stock'], 0) }} u.
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- 👥 Clientes Section -->
                    @if (!empty($searchResultsClientes))
                        <div class="p-2">
                            <div class="px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-users"></i> Clientes
                            </div>
                            <div class="space-y-1">
                                @foreach ($searchResultsClientes as $c)
                                    <a href="{{ route('persona.clientes') }}" class="flex items-center justify-between p-2 hover:bg-slate-50 dark:hover:bg-slate-800/60 rounded-xl transition group">
                                        <div class="truncate">
                                            <div class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-brand-500 transition">
                                                {{ $c['nombre_razon_social'] }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-mono">
                                                CI/NIT: {{ $c['ci_nit_documento'] ?? 'S/N' }}
                                            </div>
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                                            {{ $c['telefono'] ?? '-' }}
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- 🧾 Ventas / Recibos Section -->
                    @if (!empty($searchResultsVentas))
                        <div class="p-2">
                            <div class="px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-receipt"></i> Recibos de Venta
                            </div>
                            <div class="space-y-1">
                                @foreach ($searchResultsVentas as $v)
                                    <a href="{{ route('operacion.ventas-dia') }}" class="flex items-center justify-between p-2 hover:bg-slate-50 dark:hover:bg-slate-800/60 rounded-xl transition group">
                                        <div>
                                            <div class="text-xs font-mono font-extrabold text-slate-900 dark:text-white group-hover:text-brand-500 transition">
                                                {{ $v['numero_recibo'] }}
                                            </div>
                                            <div class="text-[10px] text-slate-400">
                                                {{ $v['cliente'] }} &bull; {{ $v['fecha'] }}
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs font-mono font-bold text-slate-900 dark:text-white">
                                                Bs {{ number_format($v['total'], 2) }}
                                            </div>
                                            <span class="inline-flex text-[9px] font-extrabold px-1.5 py-0.5 rounded-full {{ $v['estado'] === 'COMPLETADA' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-rose-500/10 text-rose-600' }}">
                                                {{ $v['estado'] }}
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- No Results -->
                    @if (empty($searchResultsProductos) && empty($searchResultsClientes) && empty($searchResultsVentas))
                        <div class="p-6 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-magnifying-glass text-xl mb-1 block opacity-50"></i>
                            No se encontraron resultados para "<span class="font-semibold text-slate-600 dark:text-slate-300">{{ $searchQuery }}</span>".
                        </div>
                    @endif

                </div>
            @endif
        </div>
    </div>

    <!-- Right Section: Theme Switcher, Notifications, User Dropdown -->
    <div class="flex items-center gap-2 md:gap-3">
        
        <!-- Dark / Light Mode Switcher -->
        <button 
            type="button" 
            @click="
                darkMode = !darkMode; 
                localStorage.setItem('theme', darkMode ? 'dark' : 'light'); 
                if(darkMode) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark'); }
            "
            class="p-2 rounded-xl text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition"
            title="Alternar Modo Oscuro/Claro">
            <i x-show="!darkMode" class="fa-solid fa-moon text-lg text-amber-500"></i>
            <i x-show="darkMode" class="fa-solid fa-sun text-lg text-amber-400"></i>
        </button>

        <!-- Dynamic Notifications Dropdown -->
        <div class="relative" x-data="{ openNotif: false }">
            <button 
                type="button" 
                @click="openNotif = !openNotif" 
                @click.outside="openNotif = false"
                class="p-2 rounded-xl text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition relative"
                title="Notificaciones del Sistema">
                <i class="fa-solid fa-bell text-lg"></i>

                @if ($unreadCount > 0)
                    <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-rose-500 text-white rounded-full text-[9px] font-extrabold flex items-center justify-center animate-pulse border-2 border-white dark:border-slate-900">
                        {{ $unreadCount }}
                    </span>
                @endif
            </button>

            <!-- Notifications Dropdown Panel -->
            <div 
                x-show="openNotif" 
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-80 sm:w-96 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl overflow-hidden z-50">
                
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/50">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-bell text-brand-500 text-xs"></i>
                        <h3 class="text-xs font-bold text-slate-900 dark:text-white">Notificaciones del Sistema</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-brand-500/10 text-brand-500">
                        {{ $unreadCount }} pendientes
                    </span>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-80 overflow-y-auto">
                    @forelse ($notificaciones as $n)
                        <a href="{{ $n['link'] }}" class="p-3.5 flex items-start gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition group block">
                            <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center text-sm font-bold
                                @if($n['tipo'] === 'danger') bg-rose-500/10 text-rose-500
                                @elseif($n['tipo'] === 'warning') bg-amber-500/10 text-amber-500
                                @elseif($n['tipo'] === 'success') bg-emerald-500/10 text-emerald-500
                                @else bg-blue-500/10 text-blue-500 @endif">
                                <i class="{{ $n['icono'] }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-brand-500 transition">
                                    {{ $n['titulo'] }}
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-snug">
                                    {{ $n['mensaje'] }}
                                </p>
                                <span class="text-[9px] font-semibold text-slate-400 mt-1 block">
                                    {{ $n['fecha'] }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-bell-slash text-xl mb-1 block opacity-40"></i>
                            No tienes notificaciones pendientes.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Profile User Menu Dropdown -->
        <div class="relative" x-data="{ userMenuOpen: false }">
            <button 
                type="button" 
                @click="userMenuOpen = !userMenuOpen" 
                @click.outside="userMenuOpen = false"
                class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-600 text-white font-black text-xs flex items-center justify-center shadow-md">
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                </div>
                
                <div class="hidden sm:block text-left">
                    <div class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
                        {{ $user->name }}
                    </div>
                    <div class="text-[10px] text-slate-400 font-semibold">
                        {{ $user->esAdmin() ? 'Administrador' : 'Cajero' }} &bull; {{ $user->sucursal?->nombre ?? 'Sucursal' }}
                    </div>
                </div>

                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 hidden sm:block"></i>
            </button>

            <!-- User Menu Panel -->
            <div 
                x-show="userMenuOpen" 
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl p-2 z-50 space-y-1">
                
                <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800 mb-1">
                    <div class="text-xs font-bold text-slate-900 dark:text-white truncate">
                        {{ $user->nombre_completo ?? $user->name }}
                    </div>
                    <div class="text-[10px] text-slate-400 truncate">
                        {{ $user->email }}
                    </div>
                </div>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <i class="fa-solid fa-user-pen text-slate-400 w-4 text-center"></i>
                    <span>Editar Perfil</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition text-left">
                        <i class="fa-solid fa-right-from-bracket text-rose-500 w-4 text-center"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>

    </div>
</header>
