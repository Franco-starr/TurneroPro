@extends('layouts.app')

@section('title', 'Nuevo Turno - TurneroPro')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Nuevo Turno</h1>
    </div>

    <form method="POST" action="{{ route('appointments.store') }}" class="max-w-xl space-y-5">
        @csrf

        <div>
            <label for="cliente" class="mb-1 block text-sm font-medium">Cliente</label>
            <select
                name="client_id"
                id="cliente"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
                <option value="" disabled {{ old('client_id') ? '' : 'selected' }}>Seleccionar cliente…</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->nombre }} {{ $client->apellido }}
                    </option>
                @endforeach
            </select>
            @error('client_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="servicio" class="mb-1 block text-sm font-medium">Servicio</label>
            <select
                name="service_id"
                id="servicio"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
                <option value="" disabled {{ old('service_id') ? '' : 'selected' }}>Seleccionar servicio…</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                        {{ $service->name }}
                    </option>
                @endforeach
            </select>
            @error('service_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="fecha" class="mb-1 block text-sm font-medium">Fecha</label>
            <input
                type="date"
                name="fecha"
                id="fecha"
                value="{{ old('fecha') }}"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
            @error('fecha')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="hora" class="mb-1 block text-sm font-medium">Hora</label>
            <input
                type="time"
                name="hora"
                id="hora"
                value="{{ old('hora') }}"
                min="{{ $settings->opening_time ?? config('store.opening_time') }}"
                max="{{ $settings->closing_time ?? config('store.closing_time') }}"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
            <p class="mt-1 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                Horario de atención: {{ $settings->opening_time ?? config('store.opening_time') }} a {{ $settings->closing_time ?? config('store.closing_time') }}
            </p>
            @error('hora')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="rounded-sm bg-green-600 px-5 py-2 text-sm text-white hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700 dark:text-white">
                Guardar Turno
            </button>
            <a href="{{ route('appointments.index') }}" class="text-sm hover:underline">Cancelar</a>
        </div>
    </form>
@endsection