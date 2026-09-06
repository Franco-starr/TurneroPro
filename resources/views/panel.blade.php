@extends('layouts.app')

@section('title', 'TurneroPro - Panel')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Panel</h1>
        <p class="mt-2 text-[#706f6c] dark:text-[#A1A09A]">
            Administrá los servicios, clientes, turnos y la configuración del local.
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <a href="{{ route('appointments.index') }}" class="rounded-sm border border-[#19140035] p-5 hover:bg-[#F1F1EF] dark:border-[#3E3E3A] dark:hover:bg-[#111110]">
            <h2 class="text-lg font-semibold">Turnos</h2>
            <p class="mt-1 text-sm text-[#706f6c] dark:text-[#A1A09A]">Ver y crear turnos.</p>
        </a>
        <a href="{{ route('services.index') }}" class="rounded-sm border border-[#19140035] p-5 hover:bg-[#F1F1EF] dark:border-[#3E3E3A] dark:hover:bg-[#111110]">
            <h2 class="text-lg font-semibold">Servicios</h2>
            <p class="mt-1 text-sm text-[#706f6c] dark:text-[#A1A09A]">Administrar los servicios del local.</p>
        </a>
        <a href="{{ route('clients.index') }}" class="rounded-sm border border-[#19140035] p-5 hover:bg-[#F1F1EF] dark:border-[#3E3E3A] dark:hover:bg-[#111110]">
            <h2 class="text-lg font-semibold">Clientes</h2>
            <p class="mt-1 text-sm text-[#706f6c] dark:text-[#A1A09A]">Registrar clientes del negocio.</p>
        </a>
        <a href="{{ route('store-settings.edit') }}" class="rounded-sm border border-[#19140035] p-5 hover:bg-[#F1F1EF] dark:border-[#3E3E3A] dark:hover:bg-[#111110]">
            <h2 class="text-lg font-semibold">Configuración del local</h2>
            <p class="mt-1 text-sm text-[#706f6c] dark:text-[#A1A09A]">Definir horario de apertura y cierre.</p>
        </a>
    </div>
@endsection