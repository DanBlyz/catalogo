<!-- Mobile Backdrop Overlay -->
<div 
    x-cloak
    x-show="sidebarOpen" 
    @click="sidebarOpen = false" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 md:hidden">
</div>

<!-- AdminLTE Style Sidebar -->
<aside 
    x-cloak
    :class="{ 
        'translate-x-0': sidebarOpen, 
        '-translate-x-full md:translate-x-0': !sidebarOpen,
        'w-64': !sidebarCollapsed,
        'w-20': sidebarCollapsed
    }"
    class="fixed inset-y-0 left-0 z-40 bg-slate-900 text-slate-300 border-r border-slate-800 transition-all duration-300 flex flex-col shadow-2xl">

    
    <!-- Sidebar Header / Brand Logo -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800/80 bg-slate-950/40">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-blue-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-brand-500/20">
                <i class="fa-solid fa-boxes-stacked text-lg"></i>
            </div>
            <div x-show="!sidebarCollapsed" class="transition-opacity duration-200">
                <span class="font-extrabold text-white text-base tracking-wide block leading-tight">STOCK<span class="text-brand-400">HUB</span></span>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Inventario Web</span>
            </div>
        </a>

        <!-- Mobile close button -->
        <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white p-1">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Navigation Menu -->
    <div class="flex-1 overflow-y-auto py-4 px-3 space-y-6 scrollbar-thin scrollbar-thumb-slate-700">
        
        <!-- Grupo Principal -->
        <div>
            <div x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                Principal
            </div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                        <i class="fa-solid fa-gauge-high text-base w-5 text-center shrink-0"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Grupo Reportes & Ajustes -->
        <div>
            <div x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                Sistema
            </div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('administracion.roles') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('administracion.roles') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                        <i class="fa-solid fa-user-shield text-base w-5 text-center shrink-0 text-amber-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Roles</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('administracion.sucursales') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('administracion.sucursales') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                        <i class="fa-solid fa-store text-base w-5 text-center shrink-0 text-cyan-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Sucursales</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('administracion.permisos') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('administracion.permisos') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                        <i class="fa-solid fa-key text-base w-5 text-center shrink-0 text-emerald-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Permisos</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('administracion.usuarios') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('administracion.usuarios') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                        <i class="fa-solid fa-user-gear text-base w-5 text-center shrink-0 text-blue-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Usuarios</span>
                    </a>
                </li>

                <li>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 hover:bg-slate-800/80 hover:text-white text-slate-300">
                        <i class="fa-solid fa-chart-pie text-base w-5 text-center shrink-0 text-rose-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Reportes</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Grupo Personas -->
        <div>
            <div x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                Personas
            </div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('persona.clientes') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('persona.clientes') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                        <i class="fa-solid fa-users text-base w-5 text-center shrink-0 text-emerald-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Clientes</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('persona.proveedores') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('persona.proveedores') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                        <i class="fa-solid fa-truck-field text-base w-5 text-center shrink-0 text-indigo-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Proveedores</span>
                    </a>
                </li>

            </ul>
        </div>

        <!-- Grupo Catálogo -->
        <div>
            <div x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                Catálogo
            </div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('catalogo.categorias') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('catalogo.categorias') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                        <i class="fa-solid fa-layer-group text-base w-5 text-center shrink-0 text-amber-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Categorías</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('catalogo.marcas') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('catalogo.marcas') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                        <i class="fa-solid fa-copyright text-base w-5 text-center shrink-0 text-purple-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Marcas</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('catalogo.productos') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('catalogo.productos') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                        <i class="fa-solid fa-box text-base w-5 text-center shrink-0 text-brand-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Productos</span>
                    </a>
                </li>

            </ul>
        </div>

        <!-- Grupo Operaciones -->
        <div>
            <div x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                Operaciones
            </div>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('operacion.cajas') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('operacion.cajas') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                        <i class="fa-solid fa-cash-register text-base w-5 text-center shrink-0 text-emerald-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Cajas</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('operacion.ventas') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('operacion.ventas') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                        <i class="fa-solid fa-cart-shopping text-base w-5 text-center shrink-0 text-teal-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Ventas</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('operacion.movimientos') }}" 
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('operacion.movimientos') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                        <i class="fa-solid fa-arrow-right-arrow-left text-base w-5 text-center shrink-0 text-cyan-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Movimientos (Kardex)</span>
                    </a>
                </li>

                <li>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 hover:bg-slate-800/80 hover:text-white text-slate-300">
                        <i class="fa-solid fa-arrow-right-arrow-left text-base w-5 text-center shrink-0 text-cyan-400"></i>
                        <span x-show="!sidebarCollapsed" class="truncate">Pagos</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>

    <!-- Sidebar Footer / Current User Profile Card -->
    <div class="p-3 border-t border-slate-800 bg-slate-950/60">
        <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-800/40">
            <div class="w-9 h-9 rounded-lg bg-brand-500/20 text-brand-400 flex items-center justify-center font-bold text-sm shrink-0 border border-brand-500/30">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
            </div>
            <div x-show="!sidebarCollapsed" class="overflow-hidden">
                <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'Usuario' }}</p>
                <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
            </div>
        </div>
    </div>
</aside>
