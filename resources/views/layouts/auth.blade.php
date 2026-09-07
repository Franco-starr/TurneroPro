<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', 'Ingresar - TurneroPro')</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FDFDFC] text-[#1b1b18] antialiased dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <div class="flex min-h-screen flex-col">
            <header class="border-b border-[#19140035] dark:border-[#3E3E3A]">
                <nav class="mx-auto flex max-w-4xl items-center justify-between px-6 py-4">
                    <a href="{{ route('home') }}" class="text-lg font-semibold">
                        {{ config('app.name', 'TurneroPro') }}
                    </a>
                    <a href="{{ route('home') }}" class="text-sm hover:underline">Volver al inicio</a>
                </nav>
            </header>

            <main class="mx-auto flex w-full max-w-4xl flex-1 items-center justify-center px-6 py-10">
                @yield('content')
            </main>
        </div>
    </body>
</html>