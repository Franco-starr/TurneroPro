@extends('layouts.app')

@section('title', $client->nombre.' '.$client->apellido.' - TurneroPro')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">{{ $client->nombre }} {{ $client->apellido }}</h1>
        <a href="{{ route('clients.edit', $client) }}" class="rounded-sm bg-[#1b1b18] px-5 py-2 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
            Editar Cliente
        </a>
    </div>

    <div class="mb-8 max-w-xl space-y-2 rounded-sm border border-[#19140035] p-5 text-sm dark:border-[#3E3E3A]">
        <p><span class="text-[#706f6c] dark:text-[#A1A09A]">Nombre:</span> <span class="font-medium">{{ $client->nombre }}</span></p>
        <p><span class="text-[#706f6c] dark:text-[#A1A09A]">Apellido:</span> <span class="font-medium">{{ $client->apellido }}</span></p>
        <p><span class="text-[#706f6c] dark:text-[#A1A09A]">Teléfono:</span> <span class="font-medium">{{ $client->telefono }}</span></p>
        <p><span class="text-[#706f6c] dark:text-[#A1A09A]">Email:</span> <span class="font-medium">{{ $client->email }}</span></p>
    </div>

    <h2 class="mb-4 text-2xl font-bold">Historial de turnos</h2>

    <div class="overflow-x-auto rounded-sm border border-[#19140035] dark:border-[#3E3E3A]">
        <table class="min-w-full divide-y divide-[#19140035] dark:divide-[#3E3E3A]">
            <thead class="bg-[#F1F1EF] dark:bg-[#111110]">
                <tr class="text-left text-xs uppercase tracking-wide text-[#706f6c] dark:text-[#A1A09A]">
                    <th class="px-4 py-3">Fecha y hora</th>
                    <th class="px-4 py-3">Servicio</th>
                    <th class="px-4 py-3">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#19140035] dark:divide-[#3E3E3A]">
                @forelse ($appointments as $appointment)
                    <tr>
                        <td class="px-4 py-3">{{ $appointment->fecha_hora->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $appointment->service->name }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$appointment->status" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                            Este cliente todavía no tiene turnos.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection