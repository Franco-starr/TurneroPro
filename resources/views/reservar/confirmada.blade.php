@extends('layouts.app')

@section('title', 'Reserva confirmada - TurneroPro')

@section('content')
    <div class="max-w-xl">
        <h1 class="text-3xl font-bold text-green-700 dark:text-green-400">¡Turno reservado!</h1>
        <p class="mt-2 text-[#706f6c] dark:text-[#A1A09A]">Tu reserva se registró correctamente. Te esperamos.</p>

        @if ($reserva = session('reserva'))
            <div class="mt-6 space-y-2 rounded-sm border border-[#19140035] p-5 text-sm dark:border-[#3E3E3A]">
                <p><span class="text-[#706f6c] dark:text-[#A1A09A]">Servicio:</span> <span class="font-medium">{{ $reserva['servicio'] }}</span></p>
                <p><span class="text-[#706f6c] dark:text-[#A1A09A]">Duración:</span> <span class="font-medium">{{ $reserva['duracion'] }} min</span></p>
                <p><span class="text-[#706f6c] dark:text-[#A1A09A]">Fecha:</span> <span class="font-medium">{{ $reserva['fecha'] }}</span></p>
                <p><span class="text-[#706f6c] dark:text-[#A1A09A]">Hora:</span> <span class="font-medium">{{ $reserva['hora'] }}</span></p>
                <p><span class="text-[#706f6c] dark:text-[#A1A09A]">Cliente:</span> <span class="font-medium">{{ $reserva['cliente'] }}</span></p>
            </div>
            <div class="mt-6 flex flex-wrap items-center gap-4">
                <a href="{{ route('reservar') }}" class="rounded-sm bg-[#1b1b18] px-5 py-2 text-sm text-white hover:bg-[#354735] dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
                    Reservar otro turno
                </a>
                <a href="{{ route('reserva.cancelar', $reserva['token']) }}" class="text-sm text-red-600 hover:underline dark:text-red-400">
                    Cancelar mi turno
                </a>
            </div>
        @else
            <p class="mt-6 text-sm text-[#706f6c] dark:text-[#A1A09A]">No hay una reserva reciente para mostrar.</p>
            <a href="{{ route('reservar') }}" class="mt-4 inline-block text-sm hover:underline">Volver a reservar</a>
        @endif
    </div>
@endsection