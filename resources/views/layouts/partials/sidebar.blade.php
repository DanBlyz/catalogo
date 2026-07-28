<aside 
    x-cloak
    :class="{ 
        'translate-x-0': sidebarOpen, 
        '-translate-x-full md:translate-x-0': !sidebarOpen,
        'w-64': !sidebarCollapsed,
        'w-20': sidebarCollapsed
    }"
    class="fixed inset-y-0 left-0 z-40 bg-slate-900 text-slate-100 flex flex-col transition-all duration-300 ease-in-out border-r border-slate-800 shadow-xl">

    <!-- Sidebar Header (Logo & Title) -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 text-white flex items-center justify-center font-black text-lg shadow-lg shadow-brand-600/30 shrink-0">
                C
            </div>
            <div x-show="!sidebarCollapsed" class="flex flex-col transition-opacity duration-200">
                <span class="font-extrabold text-sm tracking-tight text-white leading-none">CATÁLOGO POS</span>
                <span class="text-[10px] text-slate-400 font-semibold mt-1">Gestión & Inventario</span>
            </div>
        </a>

        <!-- Mobile Close Button -->
        <button 
            @click="sidebarOpen = false" 
            class="md:hidden text-slate-400 hover:text-white p-1 rounded-lg focus:outline-none">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Sidebar Navigation Links -->
    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6 custom-scrollbar">
        
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

        <!-- Grupo Administración & Sistema -->
        @if (auth()->user()->tieneAlgunPermiso(['roles.gestionar', 'sucursales.gestionar', 'permisos.gestionar', 'usuarios.gestionar', 'reportes.ver']))
            <div>
                <div x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Sistema
                </div>
                <ul class="space-y-1">
                    @if (auth()->user()->tienePermiso('roles.gestionar'))
                        <li>
                            <a href="{{ route('administracion.roles') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('administracion.roles') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-user-shield text-base w-5 text-center shrink-0 text-amber-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Roles</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->tienePermiso('sucursales.gestionar'))
                        <li>
                            <a href="{{ route('administracion.sucursales') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('administracion.sucursales') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-store text-base w-5 text-center shrink-0 text-cyan-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Sucursales</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->tienePermiso('permisos.gestionar'))
                        <li>
                            <a href="{{ route('administracion.permisos') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('administracion.permisos') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-key text-base w-5 text-center shrink-0 text-emerald-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Permisos</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->tienePermiso('usuarios.gestionar'))
                        <li>
                            <a href="{{ route('administracion.usuarios') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('administracion.usuarios') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-user-gear text-base w-5 text-center shrink-0 text-blue-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Usuarios</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->tienePermiso('reportes.ver'))
                        <li>
                            <a href="{{ route('administracion.reportes') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('administracion.reportes') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-chart-pie text-base w-5 text-center shrink-0 text-rose-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Reportes</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @endif

        <!-- Grupo Personas -->
        @if (auth()->user()->tieneAlgunPermiso(['clientes.gestionar', 'proveedores.gestionar']))
            <div>
                <div x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Personas
                </div>
                <ul class="space-y-1">
                    @if (auth()->user()->tienePermiso('clientes.gestionar'))
                        <li>
                            <a href="{{ route('persona.clientes') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('persona.clientes') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-users text-base w-5 text-center shrink-0 text-emerald-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Clientes</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->tienePermiso('proveedores.gestionar'))
                        <li>
                            <a href="{{ route('persona.proveedores') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('persona.proveedores') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-truck-field text-base w-5 text-center shrink-0 text-indigo-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Proveedores</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @endif

        <!-- Grupo Catálogo -->
        @if (auth()->user()->tieneAlgunPermiso(['categorias.gestionar', 'marcas.gestionar', 'productos.ver']))
            <div>
                <div x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Catálogo
                </div>
                <ul class="space-y-1">
                    @if (auth()->user()->tienePermiso('categorias.gestionar'))
                        <li>
                            <a href="{{ route('catalogo.categorias') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('catalogo.categorias') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-layer-group text-base w-5 text-center shrink-0 text-amber-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Categorías</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->tienePermiso('marcas.gestionar'))
                        <li>
                            <a href="{{ route('catalogo.marcas') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('catalogo.marcas') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-copyright text-base w-5 text-center shrink-0 text-purple-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Marcas</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->tienePermiso('productos.ver'))
                        <li>
                            <a href="{{ route('catalogo.productos') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('catalogo.productos') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-box text-base w-5 text-center shrink-0 text-brand-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Productos</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @endif

        <!-- Grupo Operaciones -->
        @if (auth()->user()->tieneAlgunPermiso(['cajas.ver', 'ventas.crear', 'ventas.ver', 'movimientos.ver', 'pagos.ver']))
            <div>
                <div x-show="!sidebarCollapsed" class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Operaciones
                </div>
                <ul class="space-y-1">
                    @if (auth()->user()->tienePermiso('cajas.ver'))
                        <li>
                            <a href="{{ route('operacion.cajas') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('operacion.cajas') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-cash-register text-base w-5 text-center shrink-0 text-emerald-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Cajas</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->tienePermiso('ventas.crear'))
                        <li>
                            <a href="{{ route('operacion.ventas') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('operacion.ventas') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-cart-shopping text-base w-5 text-center shrink-0 text-teal-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Ventas</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->tienePermiso('ventas.ver'))
                        <li>
                            <a href="{{ route('operacion.ventas-dia') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('operacion.ventas-dia') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-calendar-day text-base w-5 text-center shrink-0 text-amber-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Ventas del Día</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->tienePermiso('movimientos.ver'))
                        <li>
                            <a href="{{ route('operacion.movimientos') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('operacion.movimientos') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-arrow-right-arrow-left text-base w-5 text-center shrink-0 text-cyan-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Movimientos (Kardex)</span>
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->tienePermiso('pagos.ver'))
                        <li>
                            <a href="{{ route('operacion.pagos') }}" 
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-150 {{ request()->routeIs('operacion.pagos') ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/30 font-semibold' : 'hover:bg-slate-800/80 hover:text-white text-slate-300' }}">
                                <i class="fa-solid fa-money-bill-transfer text-base w-5 text-center shrink-0 text-emerald-400"></i>
                                <span x-show="!sidebarCollapsed" class="truncate">Pagos</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @endif

    </div>

    <!-- Sidebar Footer / Current User Profile Card -->
    <div class="p-3 border-t border-slate-800 bg-slate-950/60">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-600 text-white font-extrabold flex items-center justify-center text-sm shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div x-show="!sidebarCollapsed" class="flex flex-col truncate">
                    <span class="font-bold text-xs text-slate-200 truncate">{{ auth()->user()->name }}</span>
                    <span class="text-[10px] text-slate-400 font-medium truncate">{{ auth()->user()->esAdmin() ? 'Administrador' : 'Cajero' }} &bull; {{ auth()->user()->sucursal?->nombre ?? 'Sucursal' }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" x-show="!sidebarCollapsed">
                @csrf
                <button type="submit" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition" title="Cerrar Sesión">
                    <i class="fa-solid fa-right-from-bracket text-sm"></i>
                </button>
            </form>
        </div>
    </div>

</aside>
