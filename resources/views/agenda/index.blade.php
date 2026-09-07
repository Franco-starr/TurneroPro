@extends('layouts.app')

@section('title', 'Agenda - TurneroPro')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Agenda</h1>
        <p class="mt-1 text-[#706f6c] dark:text-[#A1A09A]">{{ ucfirst($fechaLegible) }}</p>
    </div>

    <form method="GET" action="{{ route('agenda') }}" class="mb-6 flex max-w-md items-end gap-3">
        <div class="flex-1">
            <label for="fecha" class="mb-1 block text-sm font-medium">Fecha</label>
            <input
                type="date"
                name="fecha"
                id="fecha"
                value="{{ $fecha }}"
                class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
            >
        </div>
        <button type="submit" class="rounded-sm bg-[#1b1b18] px-5 py-2 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
            Ver día
        </button>
    </form>

    @if (session('success'))
        <div class="mb-6 rounded-sm border border-green-600 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-400 dark:bg-green-900/30 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-sm border border-[#19140035] dark:border-[#3E3E3A]">
        <table class="min-w-full divide-y divide-[#19140035] dark:divide-[#3E3E3A]">
            <thead class="bg-[#F1F1EF] dark:bg-[#111110]">
                <tr class="text-left text-xs uppercase tracking-wide text-[#706f6c] dark:text-[#A1A09A]">
                    <th class="px-4 py-3">Horario</th>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3">Servicio</th>
                    <th class="px-4 py-3">Duración</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#19140035] dark:divide-[#3E3E3A]">
                @forelse ($turnos as $turno)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3 font-medium">
                            {{ $turno->fecha_hora->format('H:i') }}–{{ $turno->fecha_hora->copy()->addMinutes($turno->service->duration)->format('H:i') }}
                        </td>
                        <td class="px-4 py-3">{{ $turno->client->nombre }} {{ $turno->client->apellido }}</td>
                        <td class="px-4 py-3">{{ $turno->service->name }}</td>
                        <td class="px-4 py-3">{{ $turno->service->duration }} min</td>
                        <td class="px-4 py-3">
                            <span class="inline-block rounded-sm border px-2 py-1 text-xs font-medium
                                @switch($turno->status)
                                    @case(\App\Enums\AppointmentStatus::Completed)
                                        border-green-600 text-green-700 dark:text-green-400
                                        @break
                                    @case(\App\Enums\AppointmentStatus::Cancelled)
                                        border-red-600 text-red-700 dark:text-red-400 line-through
                                        @break
                                    @default
                                        border-amber-500 text-amber-700 dark:text-amber-400
                                @endswitch
                            ">
                                @switch($turno->status)
                                    @case(\App\Enums\AppointmentStatus::Completed)
                                        Completado
                                        @break
                                    @case(\App\Enums\AppointmentStatus::Cancelled)
                                        Cancelado
                                        @break
                                    @default
                                        Pendiente
                                @endswitch
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('appointments.complete', $turno) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-sm border border-[#19140035] px-2 py-1 text-xs hover:bg-[#F1F1EF] dark:border-[#3E3E3A] dark:hover:bg-[#111110]">
                                        Completar
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('appointments.cancel', $turno) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-sm border border-[#19140035] px-2 py-1 text-xs hover:bg-[#F1F1EF] dark:border-[#3E3E3A] dark:hover:bg-[#111110]">
                                        Cancelar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                            No hay turnos programados para este día.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection