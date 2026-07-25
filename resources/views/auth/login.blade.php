<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-brand-950 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
        
        <!-- Decorative background elements -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Dark/Light Theme Switcher in top right -->
        <div class="absolute top-6 right-6">
            <button 
                type="button" 
                x-data 
                @click="
                    darkMode = !darkMode; 
                    localStorage.setItem('theme', darkMode ? 'dark' : 'light'); 
                    if(darkMode) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark'); }
                "
                class="p-2.5 rounded-xl bg-white/10 dark:bg-slate-800/60 backdrop-blur-md border border-white/20 dark:border-slate-700/50 text-slate-200 hover:text-white transition-all shadow-lg hover:scale-105"
                title="Cambiar tema">
                <i x-show="!darkMode" class="fa-solid fa-moon text-lg"></i>
                <i x-show="darkMode" class="fa-solid fa-sun text-lg text-amber-400"></i>
            </button>
        </div>

        <div class="w-full max-w-md space-y-8 relative">
            <!-- Brand Header -->
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 to-blue-500 shadow-xl shadow-brand-500/30 text-white text-2xl font-bold mb-4">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <h2 class="text-3xl font-extrabold text-white tracking-tight">
                    Sistema de Inventario
                </h2>
                <p class="mt-2 text-sm text-slate-300">
                    Control general de stock, compras y ventas
                </p>
            </div>

            <!-- Login Card -->
            <div class="bg-white/95 dark:bg-slate-900/90 backdrop-blur-xl border border-white/20 dark:border-slate-800 shadow-2xl rounded-3xl p-8 transition-all">
                
                <!-- Session Status -->
                <x-auth-session-status class="mb-6" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Correo Electrónico
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 

                                required 
                                autofocus 
                                autocomplete="username"
                                placeholder="usuario@tienda.com"
                                class="block w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/70 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all text-sm"
                            >
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
                    </div>

                    <!-- Password -->
                    <div x-data="{ showPassword: false }">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            Contraseña
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <input 
                                id="password" 
                                :type="showPassword ? 'text' : 'password'" 
                                name="password" 
                                required 
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="block w-full pl-10 pr-10 py-3 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/70 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all text-sm"
                            >
                            <button 
                                type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                                <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input 
                                id="remember_me" 
                                type="checkbox" 
                                name="remember" 
                                class="rounded border-slate-300 dark:border-slate-700 text-brand-600 shadow-sm focus:ring-brand-500 dark:bg-slate-800 dark:checked:bg-brand-600 transition"
                            >
                            <span class="ms-2 text-sm text-slate-600 dark:text-slate-400 font-medium">Recordarme</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm font-semibold text-brand-600 hover:text-brand-500 dark:text-brand-400 dark:hover:text-brand-300 transition" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button 
                            type="submit" 
                            class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl text-white bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 font-semibold shadow-lg shadow-brand-500/25 active:scale-[0.99] transition-all focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                            <i class="fa-solid fa-right-to-bracket text-base"></i>
                            <span>Ingresar al Sistema</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer note -->
            <p class="text-center text-xs text-slate-400 dark:text-slate-500">
                &copy; {{ date('Y') }} Sistema de Inventario General &bull; v1.0
            </p>
        </div>
    </div>
</x-guest-layout>

