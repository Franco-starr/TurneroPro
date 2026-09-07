@extends('layouts.app')

@section('title', 'Cancelar turno - TurneroPro')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-3xl font-bold">Cancelar turno</h1>
        <p class="mt-2 text-[#706f6c] dark:text-[#A1A09A]">Estás por cancelar el siguiente turno:</p>

        <div class="mt-6 space-y-2 rounded-sm border border-[#19140035] p-5 text-sm dark:border-[#3E3E3A]">
            <p><span class="text-[#706f6c] dark:text-[#A1A09A]">Servicio:</span> <span class="font-medium">{{ $appointment->service->name }}</span></p>
            <p><span class="text-[#706f6c] dark:text-[#A1A09A]">Fecha:</span> <span class="font-medium">{{ $appointment->fecha_hora->format('d/m/Y') }}</span></p>
            <p><span class="text-[#706f6c] dark:text-[#A1A09A]">Hora:</span> <span class="font-medium">{{ $appointment->fecha_hora->format('H:i') }}</span></p>
            <p><span class="text-[#706f6c] dark:text-[#A1A09A]">Cliente:</span> <span class="font-medium">{{ $appointment->client->nombre }} {{ $appointment->client->apellido }}</span></p>
        </div>

        <div class="mt-6 flex items-center gap-4">
            <form method="POST" action="{{ route('reserva.cancel', $appointment->token) }}">
                @csrf
                @method('DELETE')

                <button type="submit" class="rounded-sm border border-red-600 px-5 py-2 text-sm font-medium text-red-600 hover:bg-red-600 hover:text-white dark:border-red-400 dark:text-red-400 dark:hover:bg-red-400 dark:hover:text-[#1C1C1A]">
                    Sí, cancelar mi turno
                </button>
            </form>

            <a href="{{ route('home') }}" class="text-sm hover:underline">No, volver</a>
        </div>
    </div>
@endsection