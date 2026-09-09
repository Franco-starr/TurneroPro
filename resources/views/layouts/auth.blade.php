<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $pageTitle = trim($__env->yieldContent('title')) ?: 'Ingresar - TurneroPro';
            $pageDescription = trim((string) $__env->yieldContent('meta_description')) ?: 'Ingresá a TurneroPro para administrar tus servicios, clientes, turnos y la agenda de tu negocio.';
            $ogImage = asset('og-image.png');
        @endphp

        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $pageDescription }}">
        <link rel="canonical" href="{{ url()->current() }}">

        <meta property="og:locale" content="es_AR">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('app.name', 'TurneroPro') }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $pageDescription }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ $ogImage }}">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $pageDescription }}">
        <meta name="twitter:image" content="{{ $ogImage }}">

        <link rel="icon" href="{{ asset('favicon.ico') }}">

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