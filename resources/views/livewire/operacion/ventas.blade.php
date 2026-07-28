<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-cart-shopping text-brand-500"></i>
                    <span>Punto de Venta (POS)</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Emisión de ventas mostrador, facturación y cobro directo
                </p>
            </div>
            
            <nav class="flex text-xs text-slate-400 font-medium">
                <a href="#" class="hover:text-brand-500 transition">Operaciones</a>
                <span class="mx-2">&sol;</span>
                <span class="text-slate-700 dark:text-slate-200">Ventas</span>
            </nav>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Active Box Check Guard Banner -->
        @if (!$activeCaja)
            <div class="bg-rose-500/10 border-2 border-rose-500/30 rounded-2xl p-5 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-3 text-rose-600 dark:text-rose-400">
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/20 flex items-center justify-center shrink-0 text-xl font-bold">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold uppercase tracking-wide">Caja no aperturada</h3>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                            Debes aperturar una caja activa para la sucursal <strong class="text-slate-900 dark:text-white">{{ auth()->user()->sucursal?->nombre }}</strong> antes de poder realizar ventas.
                        </p>
                    </div>
                </div>

                <button 
                    wire:click="openQuickAperturaModal" 
                    class="px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-500 rounded-xl shadow-lg shadow-rose-600/30 active:scale-[0.98] transition flex items-center gap-2 shrink-0">
                    <i class="fa-solid fa-key"></i>
                    <span>Aperturar Caja Ahora</span>
                </button>
            </div>
        @else
            <!-- Active Box Info Bar -->
            <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl px-4 py-2.5 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="font-bold text-slate-900 dark:text-white">Caja Activa:</span>
                    <span class="text-slate-600 dark:text-slate-300">Apertura Bs {{ number_format($activeCaja->monto_apertura, 2) }}</span>
                </div>
                <div class="text-slate-400 font-medium text-[11px]">
                    Cajero: <strong class="text-brand-500">{{ auth()->user()->nombre_completo }}</strong>
                </div>
            </div>
        @endif

        <!-- Main POS Grid (Disabled if no active caja) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 {{ !$activeCaja ? 'opacity-50 pointer-events-none' : '' }}">
            
            <!-- LEFT COLUMN: Search & Cart (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- SECTION 2: Buscador de Productos -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-magnifying-glass text-brand-500"></i>
                            <span>Buscador de Productos (Nombre o SKU)</span>
                        </label>
                        <span class="text-[11px] text-slate-400">Escribe para ver coincidencias con stock</span>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-barcode text-sm"></i>
                        </div>
                        <input 
                            type="text" 
                            wire:model.live.debounce.250ms="searchProducto" 
                            placeholder="Ej: Teclado Logitech, SKU-1002..." 
                            class="w-full pl-10 pr-4 py-3 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition shadow-inner font-medium"
                        >
                    </div>

                    <!-- Live Match Results Dropdown -->
                    @if (trim($searchProducto) !== '')
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl overflow-hidden divide-y divide-slate-100 dark:divide-slate-800 max-h-72 overflow-y-auto">
                            @forelse ($searchResults as $item)
                                <div class="p-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition flex items-center justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $item->nombre }}</div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[10px] font-mono text-slate-400">SKU: {{ $item->sku }}</span>
                                            @if ($item->stock_actual > 0)
                                                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">
                                                    Stock: {{ number_format($item->stock_actual, 0) }}
                                                </span>
                                            @else
                                                <span class="text-[10px] font-bold text-rose-500 bg-rose-500/10 px-2 py-0.5 rounded-full border border-rose-500/20">
                                                    Sin Stock (0)
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <div class="text-right">
                                            <div class="text-xs font-extrabold text-brand-600 dark:text-brand-400 font-mono">
                                                Bs {{ number_format($item->precio_venta, 2) }}
                                            </div>
                                        </div>

                                        @if ($item->stock_actual > 0)
                                            <button 
                                                wire:click="addToCart({{ $item->id }}, 1)" 
                                                class="px-3 py-1.5 text-xs font-bold text-white bg-brand-600 hover:bg-brand-500 rounded-xl shadow-md transition active:scale-[0.95] flex items-center gap-1">
                                                <i class="fa-solid fa-plus text-[10px]"></i>
                                                <span>Agregar</span>
                                            </button>
                                        @else
                                            <button disabled class="px-3 py-1.5 text-xs font-semibold text-slate-400 bg-slate-100 dark:bg-slate-800 rounded-xl cursor-not-allowed">
                                                Agotado
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-xs text-slate-400">
                                    No se encontraron productos coincidentes en esta sucursal.
                                </div>
                            @endforelse
                        </div>
                    @endif

                </div>

                <!-- SECTION 3: Listado Final de Productos (Sales Cart Table) -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    
                    <div class="px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/50">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-cart-flatbed text-brand-500 text-sm"></i>
                            <h2 class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                                Detalle de Productos a Vender ({{ count($cart) }})
                            </h2>
                        </div>

                        @if (!empty($cart))
                            <button 
                                wire:click="clearCart" 
                                class="text-[11px] font-semibold text-rose-500 hover:text-rose-700 transition flex items-center gap-1">
                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                <span>Vaciar Carrito</span>
                            </button>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100/70 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-[10px] font-bold uppercase tracking-wider">
                                    <th class="py-2.5 px-4 w-10 text-center">#</th>
                                    <th class="py-2.5 px-4">Producto</th>
                                    <th class="py-2.5 px-4 text-right">Precio</th>
                                    <th class="py-2.5 px-4 text-center w-32">Cantidad</th>
                                    <th class="py-2.5 px-4 text-right w-28">Desc. Item (Bs)</th>
                                    <th class="py-2.5 px-4 text-right">Subtotal</th>
                                    <th class="py-2.5 px-4 text-center w-12">Quitar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-xs">
                                @forelse ($cart as $index => $item)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition">
                                        <td class="py-3 px-4 text-center text-slate-400 font-mono">{{ $index + 1 }}</td>
                                        <td class="py-3 px-4">
                                            <div class="font-bold text-slate-900 dark:text-white">{{ $item['nombre'] }}</div>
                                            <div class="text-[10px] font-mono text-slate-400">SKU: {{ $item['sku'] }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono text-slate-700 dark:text-slate-300">
                                            Bs {{ number_format($item['precio_unitario'], 2) }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex items-center justify-center gap-1">
                                                <button 
                                                    wire:click="updateCartItemQuantity({{ $index }}, {{ $item['cantidad'] - 1 }})" 
                                                    class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs flex items-center justify-center transition">
                                                    -
                                                </button>
                                                <input 
                                                    type="number" 
                                                    step="1"
                                                    value="{{ $item['cantidad'] }}"
                                                    wire:change="updateCartItemQuantity({{ $index }}, $event.target.value)"
                                                    class="w-20 text-center py-1 text-xs font-mono font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white focus:outline-none"
                                                >
                                                <button 
                                                    wire:click="updateCartItemQuantity({{ $index }}, {{ $item['cantidad'] + 1 }})" 
                                                    class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs flex items-center justify-center transition">
                                                    +
                                                </button>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <input 
                                                type="number" 
                                                step="0.50"
                                                value="{{ $item['descuento_unitario'] }}"
                                                wire:change="updateCartItemDiscount({{ $index }}, $event.target.value)"
                                                class="w-20 text-right py-1 px-2 text-xs font-mono bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-rose-600 dark:text-rose-400 font-semibold focus:outline-none"
                                            >
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                                            Bs {{ number_format($item['subtotal'], 2) }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <button 
                                                wire:click="removeFromCart({{ $index }})" 
                                                class="p-1 text-slate-400 hover:text-rose-500 transition">
                                                <i class="fa-solid fa-xmark text-sm"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-slate-400">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <i class="fa-solid fa-cart-arrow-down text-4xl text-slate-300 dark:text-slate-700"></i>
                                                <p class="text-xs font-medium">El carrito de compras está vacío</p>
                                                <p class="text-[11px] text-slate-400">Busca productos en la barra superior para agregarlos</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Client, Totals & Payment (4 cols) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- SECTION 1: Cliente (Client Selector) -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-3">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-user-tag text-brand-500"></i>
                        <span>Cliente (Razón Social / NIT)</span>
                    </label>

                    <select 
                        wire:model.live="cliente_id" 
                        class="w-full px-3.5 py-2.5 text-xs bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                        <option value="">Cliente S/N (Ventas Mostrador)</option>
                        @foreach ($clientes as $cli)
                            @if ($cli->id !== 1)
                                <option value="{{ $cli->id }}">{{ $cli->nombre_razon_social }} (CI/NIT: {{ $cli->cedula_nit_ruc ?? 'S/N' }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- SECTION 4: Descuentos y Resumen de Totales -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
                    <div class="text-xs font-extrabold uppercase tracking-wider text-slate-800 dark:text-white flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                        <i class="fa-solid fa-calculator text-brand-500"></i>
                        <span>Resumen de Cobro</span>
                    </div>

                    <!-- Descuento General -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Descuento General Adicional (Bs)
                        </label>
                        <input 
                            type="number" 
                            step="0.50"
                            wire:model.live="descuentoGeneral" 
                            placeholder="0.00" 
                            class="w-full px-3.5 py-2 text-xs font-mono font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-rose-600 dark:text-rose-400 focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                        >
                    </div>

                    <!-- Totals Breakdown -->
                    <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Subtotal Bruto:</span>
                            <span class="font-mono font-semibold text-slate-900 dark:text-white">Bs {{ number_format($cartSubtotalBruto, 2) }}</span>
                        </div>

                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Descuentos en Items:</span>
                            <span class="font-mono text-rose-500">-Bs {{ number_format($cartDescuentosItemsSum, 2) }}</span>
                        </div>

                        <div class="flex justify-between text-slate-600 dark:text-slate-400">
                            <span>Descuento General:</span>
                            <span class="font-mono text-rose-500">-Bs {{ number_format(max(0, $descuentoGeneral), 2) }}</span>
                        </div>

                        <!-- Suma Total de Descuentos Realizados -->
                        <div class="p-2.5 bg-amber-500/10 border border-amber-500/20 rounded-xl flex justify-between items-center text-xs">
                            <span class="font-bold text-amber-700 dark:text-amber-300">Total Descuentos:</span>
                            <span class="font-mono font-extrabold text-amber-700 dark:text-amber-300">
                                -Bs {{ number_format($totalDescuentosAcumulado, 2) }}
                            </span>
                        </div>

                        <!-- TOTAL FINAL A PAGAR -->
                        <div class="p-3 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl flex justify-between items-center mt-3">
                            <div>
                                <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Total A Pagar</div>
                                <div class="text-[10px] text-slate-400">Impuestos incl.</div>
                            </div>
                            <div class="text-xl font-black font-mono text-emerald-600 dark:text-emerald-400">
                                Bs {{ number_format($totalFinalAPagar, 2) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 5: Método de Pago & Procesar Venta -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm space-y-4">
                    <div class="text-xs font-extrabold uppercase tracking-wider text-slate-800 dark:text-white flex items-center gap-2 pb-2 border-b border-slate-100 dark:border-slate-800">
                        <i class="fa-solid fa-credit-card text-brand-500"></i>
                        <span>Forma de Pago</span>
                    </div>

                    <!-- Método Selector -->
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">Método Principal</label>
                        <select 
                            wire:model.live="metodoPago" 
                            class="w-full px-3.5 py-2.5 text-xs font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition">
                            <option value="EFECTIVO">💵 EFECTIVO</option>
                            <option value="QR">📱 PAGO QR</option>
                            <option value="TRANSFERENCIA">🏦 TRANSFERENCIA BANCARIA</option>
                        </select>
                    </div>

                    @if ($metodoPago === 'EFECTIVO')
                        <!-- Monto Recibido -->
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                Monto Recibido (Bs)
                            </label>
                            <input 
                                type="number" 
                                step="0.50"
                                wire:model.live="montoPagado" 
                                placeholder="Bs {{ number_format($totalFinalAPagar, 2) }}" 
                                class="w-full px-3.5 py-2.5 text-sm font-mono font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            >
                        </div>

                        <!-- Cambio -->
                        <div class="p-3 bg-blue-500/10 border border-blue-500/20 rounded-xl flex justify-between items-center text-xs">
                            <span class="font-bold text-blue-700 dark:text-blue-300">Cambio a Entregar:</span>
                            <span class="font-mono font-black text-sm text-blue-700 dark:text-blue-300">
                                Bs {{ number_format($cambioEfectivo, 2) }}
                            </span>
                        </div>
                    @else
                        <!-- Referencia Transacción -->
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-slate-400 mb-1">
                                N° Comprobante / Referencia
                            </label>
                            <input 
                                type="text" 
                                wire:model="referenciaTransaccion" 
                                placeholder="Ej: QR-987654 o TR-12345" 
                                class="w-full px-3.5 py-2 text-xs font-mono bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            >
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <button 
                        wire:click="saveVenta" 
                        {{ empty($cart) ? 'disabled' : '' }}
                        class="w-full py-3.5 px-4 text-xs font-extrabold uppercase tracking-wider text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 disabled:from-slate-400 disabled:to-slate-500 disabled:cursor-not-allowed rounded-xl shadow-lg shadow-emerald-500/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-receipt text-sm"></i>
                        <span>Procesar y Guardar Venta</span>
                    </button>
                </div>

            </div>

        </div>

    </div>

    <!-- Quick Apertura Modal (if accessed without open box) -->
    @if ($isQuickAperturaModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ open: true }">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeQuickAperturaModal"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="w-full max-w-md transform overflow-hidden rounded-3xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl border border-slate-200 dark:border-slate-800 transition-all space-y-4">
                    
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-brand-500/10 text-brand-500 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Apertura Rápida de Caja</h3>
                        </div>
                        <button wire:click="closeQuickAperturaModal" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <form wire:submit.prevent="saveQuickApertura" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                Monto Inicial de Apertura (Bs) <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                step="0.01"
                                wire:model="montoAperturaRapida" 
                                placeholder="0.00" 
                                class="w-full px-3.5 py-2.5 text-xs font-mono font-bold bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:outline-none transition"
                            >
                            @error('montoAperturaRapida') <span class="text-rose-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button 
                                type="button" 
                                wire:click="closeQuickAperturaModal" 
                                class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                                Cancelar
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 text-xs font-semibold text-white bg-brand-600 hover:bg-brand-500 rounded-xl shadow-md transition">
                                Abrir Caja y Continuar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif

    <!-- SweetAlert2 Listener -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal:modal', data => {
                const payload = Array.isArray(data) ? data[0] : data;
                Swal.fire({
                    icon: payload.type || 'info',
                    title: payload.title || '',
                    text: payload.text || '',
                    confirmButtonColor: '#0275c5',
                    timer: payload.type === 'success' ? 2500 : null,
                    timerProgressBar: payload.type === 'success'
                });
            });

            Livewire.on('swal:venta-success', data => {
                const payload = Array.isArray(data) ? data[0] : data;
                
                // Automatically open thermal ticket print popup window
                if (payload.pdf_url) {
                    const printWindow = window.open(
                        payload.pdf_url, 
                        'ReciboTicket_' + Date.now(), 
                        'width=450,height=650,top=100,left=100,scrollbars=yes,resizable=yes'
                    );
                    if (printWindow) {
                        printWindow.focus();
                    }
                }

                Swal.fire({
                    icon: 'success',
                    title: payload.title || '¡Venta Registrada!',
                    text: payload.text || 'La venta ha sido procesada y se ha generado el recibo.',
                    confirmButtonColor: '#0275c5',
                    confirmButtonText: 'Aceptar',
                    timer: 3500,
                    timerProgressBar: true
                });
            });
        });
    </script>

</div>

