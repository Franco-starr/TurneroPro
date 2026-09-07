@extends('layouts.app')

@section('title', 'Editar Cliente - TurneroPro')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Editar Cliente</h1>
    </div>

    <form method="POST" action="{{ route('clients.update', $client) }}" class="max-w-xl space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="nombre" class="mb-1 block text-sm font-medium">Nombre</label>
            <input
                type="text"
                name="nombre"
                id="nombre"
                value="{{ old('nombre', $client->nombre) }}"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
            @error('nombre')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="apellido" class="mb-1 block text-sm font-medium">Apellido</label>
            <input
                type="text"
                name="apellido"
                id="apellido"
                value="{{ old('apellido', $client->apellido) }}"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
            @error('apellido')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="telefono" class="mb-1 block text-sm font-medium">Teléfono</label>
            <input
                type="text"
                name="telefono"
                id="telefono"
                value="{{ old('telefono', $client->telefono) }}"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
            @error('telefono')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="mb-1 block text-sm font-medium">Email</label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email', $client->email) }}"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
            @error('email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-sm bg-[#1b1b18] px-5 py-2 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
                Guardar Cambios
            </button>
            <a href="{{ route('clients.show', $client) }}" class="text-sm hover:underline">Cancelar</a>
        </div>
    </form>
@endsection