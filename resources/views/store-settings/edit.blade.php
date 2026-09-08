@extends('layouts.app')

@section('title', 'Configuración del local - TurneroPro')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Configuración del local</h1>
        <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
            Horario de atención que se valida y se muestra al crear un turno.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-sm border border-green-600 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-400 dark:bg-green-900/30 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('store-settings.update') }}" class="max-w-xl space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="mb-1 block text-sm font-medium">Días de atención</label>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                @foreach ([1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'] as $valor => $etiqueta)
                    <label class="flex cursor-pointer items-center gap-2 rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A]">
                        <input type="checkbox" name="days[]" value="{{ $valor }}" class="h-4 w-4" @checked(in_array($valor, old('days', $settings->days ?? config('store.days')), true))>
                        {{ $etiqueta }}
                    </label>
                @endforeach
            </div>
            @error('days')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="opening_time" class="mb-1 block text-sm font-medium">Apertura</label>
            <input
                type="time"
                name="opening_time"
                id="opening_time"
                value="{{ old('opening_time', $settings->opening_time ?? config('store.opening_time')) }}"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
            @error('opening_time')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="closing_time" class="mb-1 block text-sm font-medium">Cierre</label>
            <input
                type="time"
                name="closing_time"
                id="closing_time"
                value="{{ old('closing_time', $settings->closing_time ?? config('store.closing_time')) }}"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
            @error('closing_time')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-sm bg-green-600 px-5 py-2 text-sm text-white hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700 dark:text-white">
                Guardar Horario
            </button>
            <a href="{{ route('panel') }}" class="text-sm hover:underline">Cancelar</a>
        </div>
    </form>
@endsection