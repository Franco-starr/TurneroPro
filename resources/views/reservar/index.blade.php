@extends('layouts.app')

@section('title', 'Reservar turno - TurneroPro')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Reservar un turno</h1>
        <p class="mt-1 text-[#706f6c] dark:text-[#A1A09A]">Elegí el servicio, la fecha y el horario, y dejanos tus datos.</p>
    </div>

    <form method="GET" action="{{ route('reservar') }}" class="mb-8 max-w-xl space-y-4 rounded-sm border border-[#19140035] p-5 dark:border-[#3E3E3A]">
        <div>
            <label for="service_id" class="mb-1 block text-sm font-medium">Servicio</label>
            <select
                name="service_id"
                id="service_id"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
                <option value="" disabled {{ ! old('service_id', request('service_id')) ? 'selected' : '' }}>Seleccionar servicio…</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}" {{ (string) old('service_id', request('service_id')) === (string) $service->id ? 'selected' : '' }}>
                        {{ $service->name }} · {{ $service->duration }} min · ${{ number_format($service->price, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="fecha" class="mb-1 block text-sm font-medium">Fecha</label>
            <input
                type="date"
                name="fecha"
                id="fecha"
                min="{{ now()->toDateString() }}"
                value="{{ old('fecha', request('fecha')) }}"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                required
            >
        </div>

        <button type="submit" class="rounded-sm bg-[#1b1b18] px-5 py-2 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
            Ver horarios
        </button>
    </form>

    @if ($fecha !== null && $serviceId !== null)
        <form method="POST" action="{{ route('reserva.store') }}" class="max-w-xl space-y-5">
            @csrf
            <input type="hidden" name="service_id" value="{{ $serviceId }}">
            <input type="hidden" name="fecha" value="{{ $fecha }}">

            <div>
                <h2 class="mb-2 text-lg font-semibold">Horarios disponibles</h2>

                @if ($slots)
                    <div class="flex flex-wrap gap-2">
                        @foreach ($slots as $slot)
                            <label class="cursor-pointer rounded-sm border border-[#19140035] px-3 py-2 text-sm has-[:checked]:bg-[#1b1b18] has-[:checked]:text-white dark:border-[#3E3E3A] dark:has-[:checked]:bg-[#eeeeec] dark:has-[:checked]:text-[#1C1C1A]">
                                <input type="radio" name="hora" value="{{ $slot }}" class="sr-only" {{ old('hora') === $slot ? 'checked' : '' }}>
                                {{ $slot }}
                            </label>
                        @endforeach
                    </div>
                @else
                    <p class="rounded-sm border border-[#19140035] px-4 py-3 text-sm text-[#706f6c] dark:border-[#3E3E3A] dark:text-[#A1A09A]">
                        No hay horarios disponibles para la fecha seleccionada.
                    </p>
                @endif

                @error('hora')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="nombre" class="mb-1 block text-sm font-medium">Nombre</label>
                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        value="{{ old('nombre') }}"
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
                        value="{{ old('apellido') }}"
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
                        value="{{ old('telefono') }}"
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
                        value="{{ old('email') }}"
                        class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                        required
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" class="rounded-sm bg-[#1b1b18] px-5 py-2 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
                Confirmar reserva
            </button>
        </form>
    @endif
@endsection