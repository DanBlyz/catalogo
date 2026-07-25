<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-gauge-high text-brand-500"></i>
                    <span>Panel de Control</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Bienvenido, {{ Auth::user()->name }}. Resumen general de tu tienda e inventario.
                </p>
            </div>
            
            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-2">
                <button class="px-3 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-cart-plus"></i>
                    <span>Nueva Venta</span>
                </button>
                <button class="px-3 py-2 text-xs font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-xl shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-plus"></i>
                    <span>Agregar Producto</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        
        <!-- KPI Metric Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card 1: Total Productos -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Productos en Stock</p>
                        <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">1,248</h3>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 mt-1">
                            <i class="fa-solid fa-arrow-trend-up"></i> +12 este mes
                        </span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-brand-500/10 text-brand-500 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-box"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2: Ventas del Día -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ventas del Día</p>
                        <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">$ 3,450.00</h3>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 mt-1">
                            <i class="fa-solid fa-circle-check"></i> 18 transacciones
                        </span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                </div>
            </div>

            <!-- Card 3: Stock Bajo Alerta -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stock Mínimo Alertas</p>
                        <h3 class="text-2xl font-extrabold text-amber-500 mt-1">5 Items</h3>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-600 dark:text-amber-400 mt-1">
                            <i class="fa-solid fa-triangle-exclamation"></i> Requiere reabastecer
                        </span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>
                </div>
            </div>

            <!-- Card 4: Clientes Registrados -->
            <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Clientes Activos</p>
                        <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">320</h3>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-brand-600 dark:text-brand-400 mt-1">
                            <i class="fa-solid fa-user-plus"></i> +4 esta semana
                        </span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-500 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>

        </div>

        <!-- System Information & Setup Banner -->
        <div class="bg-gradient-to-r from-brand-600 to-blue-700 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl">
                <i class="fa-solid fa-warehouse"></i>
            </div>
            <div class="relative z-10 max-w-2xl">
                <span class="px-2.5 py-1 rounded-full bg-white/20 text-xs font-bold uppercase tracking-wider">
                    Modo Producción Listo (Sin Vite)
                </span>
                <h2 class="text-2xl font-bold mt-3">¡Sistema de Inventario listo para operar!</h2>
                <p class="text-sm text-brand-100 mt-2 leading-relaxed">
                    Hemos configurado Tailwind CSS, Alpine.js y el tema AdminLTE con soporte claro/oscuro. Ahora podemos empezar a definir tus migraciones y controladores para Productos, Categorías, Clientes y Compras/Ventas.
                </p>
            </div>
        </div>

    </div>
</x-app-layout>

