@extends('layouts.app')

@section('title', 'Nuevo Servicio - TurneroPro')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Nuevo Servicio</h1>
    </div>

    <form method="POST" action="{{ route('services.store') }}" class="max-w-xl space-y-5">
        @csrf

        <div>
            <label for="name" class="mb-1 block text-sm font-medium">Nombre</label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name') }}"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="duration" class="mb-1 block text-sm font-medium">Duración (minutos)</label>
            <input
                type="number"
                name="duration"
                id="duration"
                value="{{ old('duration') }}"
                min="1"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
            @error('duration')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="price" class="mb-1 block text-sm font-medium">Precio</label>
            <input
                type="number"
                name="price"
                id="price"
                value="{{ old('price') }}"
                min="0"
                step="0.01"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
            @error('price')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-sm bg-green-600 px-5 py-2 text-sm text-white hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700 dark:text-white">
                Guardar Servicio
            </button>
            <a href="{{ route('services.index') }}" class="text-sm hover:underline">Cancelar</a>
        </div>
    </form>
@endsection
