@extends('layouts.app')

@section('title', 'Página no encontrada - TurneroPro')

@section('meta_description', 'No encontramos la página que buscabas en TurneroPro. Volvé al inicio para reservar un turno o consultar tus citas.')

@section('content')
    <div class="mx-auto max-w-xl text-center">
        <p class="text-7xl font-bold text-green-600 dark:text-green-400">404</p>
        <h1 class="mt-4 text-2xl font-bold">Página no encontrada</h1>
        <p class="mt-2 text-[#706f6c] dark:text-[#A1A09A]">
            La página que buscás no existe o fue movida. Volvé al inicio para seguir usando TurneroPro.
        </p>
        <a href="{{ route('home') }}"
            class="mt-6 inline-block rounded-sm bg-green-600 px-6 py-2.5 text-sm text-white hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700 dark:text-white">
            Volver al inicio
        </a>
    </div>
@endsection