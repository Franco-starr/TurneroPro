@extends('layouts.app')

@section('title', 'Mi turno - TurneroPro')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-3xl font-bold">¿Ya tenés un turno?</h1>
        <p class="mt-1 text-[#706f6c] dark:text-[#A1A09A]">Ingresá el email que usaste al reservar para ver o cancelar tus turnos.</p>

        <form method="POST" action="{{ route('mi-turno.buscar') }}" class="mt-6 flex flex-col gap-3 sm:flex-row">
            @csrf

            <input
                type="email"
                name="email"
                id="email"
                placeholder="tu@email.com"
                value="{{ old('email', $email ?? '') }}"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110] sm:flex-1"
                required
            >

            <button type="submit" class="rounded-sm bg-[#1b1b18] px-5 py-2 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
                Buscar
            </button>
        </form>

        @error('email')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror

        @isset($appointments)
            <div class="mt-8">
                @if ($appointments->isEmpty())
                    <p class="rounded-sm border border-[#19140035] px-4 py-3 text-sm text-[#706f6c] dark:border-[#3E3E3A] dark:text-[#A1A09A]">
                        No encontramos turnos futuros para <span class="font-medium">{{ $email }}</span>.
                    </p>
                @else
                    <h2 class="mb-3 text-lg font-semibold">Tus turnos</h2>

                    <div class="space-y-3">
                        @foreach ($appointments as $appointment)
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-sm border border-[#19140035] p-4 dark:border-[#3E3E3A]">
                                <div class="text-sm">
                                    <p class="font-medium">{{ $appointment->service->name }}</p>
                                    <p class="mt-0.5 text-[#706f6c] dark:text-[#A1A09A]">
                                        {{ $appointment->fecha_hora->format('d/m/Y') }} · {{ $appointment->fecha_hora->format('H:i') }}
                                    </p>
                                </div>
                                @if ($appointment->token)
                                    <a href="{{ route('reserva.cancelar', $appointment->token) }}" class="text-sm text-red-600 hover:underline dark:text-red-400">
                                        Cancelar turno
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endisset
    </div>
@endsection