<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sistema de Inventario') }}</title>

        <!-- Google Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Compiled Tailwind CSS (Local Build) -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="{{ asset('js/sweetalert2/sweetalert2.all.min.js') }}"></script>

        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
        @livewireStyles
    </head>
    <body 
        x-data="{ sidebarOpen: false, sidebarCollapsed: false }"
        class="font-sans bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased min-h-screen transition-colors duration-200">
        
        <div class="min-h-screen flex flex-col">
            <!-- Sidebar -->
            @include('layouts.partials.sidebar')

            <!-- Main Container -->
            <div 
                :class="{ 
                    'md:ml-64': !sidebarCollapsed, 
                    'md:ml-20': sidebarCollapsed 
                }"
                class="flex-1 flex flex-col transition-all duration-300 min-h-screen">
                
                <!-- Topbar Header -->
                @include('layouts.partials.header')

                <!-- Page Header (Optional) -->
                @isset($header)
                    <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 py-4 transition-colors">
                        {{ $header }}
                    </div>
                @endisset

                <!-- Main Content Slot -->
                <main class="flex-1 p-4 md:p-6 lg:p-8">
                    {{ $slot }}
                </main>

                <!-- Footer -->
                @include('layouts.partials.footer')
            </div>
        </div>

        @livewireScripts
    </body>
</html>

