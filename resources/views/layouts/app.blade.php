<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'TurneroPro')</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FDFDFC] text-[#1b1b18] antialiased dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <div class="min-h-screen flex flex-col">
            <header class="border-b border-[#19140035] dark:border-[#3E3E3A]">
                <nav class="mx-auto flex max-w-4xl items-center justify-between px-6 py-4">
                    <a href="{{ route('home') }}" class="text-lg font-semibold">
                        {{ config('app.name', 'TurneroPro') }}
                    </a>
                    <div class="flex items-center gap-6 text-sm">
                        @auth
                            <a href="{{ route('home') }}" class="hover:underline">Inicio</a>
                            <a href="{{ route('panel') }}" class="hover:underline">Panel</a>
                            <a href="{{ route('agenda') }}" class="hover:underline">Agenda</a>
                            <a href="{{ route('agenda.semanal') }}" class="hover:underline">Semana</a>
                            <a href="{{ route('services.index') }}" class="hover:underline">Servicios</a>
                            <a href="{{ route('clients.index') }}" class="hover:underline">Clientes</a>
                            <a href="{{ route('appointments.index') }}" class="hover:underline">Turnos</a>
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="hover:underline">Cerrar sesión</button>
                            </form>
                        @else
                            <a href="{{ route('home') }}" class="hover:underline">Inicio</a>
                            <a href="{{ route('login') }}" class="hover:underline">Ingresar</a>
                        @endauth
                    </div>
                </nav>
            </header>

            <main class="mx-auto w-full max-w-4xl flex-1 px-6 py-10">
                @yield('content')
            </main>

            <footer class="border-t border-[#19140035] py-6 text-center text-sm text-[#706f6c] dark:border-[#3E3E3A] dark:text-[#A1A09A]">
                {{ config('app.name', 'TurneroPro') }} - Sistema de Gestión de Turnos
            </footer>
        </div>
    </body>
</html>
