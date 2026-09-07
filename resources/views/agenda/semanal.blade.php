@extends('layouts.app')

@section('title', 'Agenda Semanal - TurneroPro')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold">Agenda Semanal</h1>
            <p class="mt-1 text-[#706f6c] dark:text-[#A1A09A]">
                {{ $inicioSemana->locale('es')->translatedFormat('j \d\e F') }} al {{ $finSemana->locale('es')->translatedFormat('j \d\e F \d\e Y') }}
            </p>
        </div>
        <div class="flex items-center gap-3 text-sm">
            <a href="{{ route('agenda.semanal', ['semana' => $semanaAnterior]) }}" class="rounded-sm border border-[#19140035] px-4 py-2 hover:bg-[#F1F1EF] dark:border-[#3E3E3A] dark:hover:bg-[#111110]">
                ← Semana anterior
            </a>
            <a href="{{ route('agenda.semanal') }}" class="rounded-sm border border-[#19140035] px-4 py-2 hover:bg-[#F1F1EF] dark:border-[#3E3E3A] dark:hover:bg-[#111110]">
                Esta semana
            </a>
            <a href="{{ route('agenda.semanal', ['semana' => $semanaSiguiente]) }}" class="rounded-sm border border-[#19140035] px-4 py-2 hover:bg-[#F1F1EF] dark:border-[#3E3E3A] dark:hover:bg-[#111110]">
                Semana siguiente →
            </a>
            <a href="{{ route('agenda') }}" class="rounded-sm bg-[#1b1b18] px-4 py-2 text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
                Agenda del día
            </a>
        </div>
    </div>

    <div class="overflow-x-auto rounded-sm border border-[#19140035] dark:border-[#3E3E3A]">
        <div class="flex min-w-max divide-x divide-[#19140035] dark:divide-[#3E3E3A]">
            @foreach ($dias as $dia)
                <div class="w-64 bg-[#FDFDFC] dark:bg-[#0a0a0a]">
                    <div class="border-b border-[#19140035] px-4 py-3 dark:border-[#3E3E3A]">
                        <h2 class="text-sm font-semibold capitalize">
                            {{ $dia['fecha']->locale('es')->translatedFormat('l') }}
                            @if ($dia['esHoy'])
                                <span class="ml-1 rounded-sm bg-[#1b1b18] px-1.5 py-0.5 text-xs font-medium text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">Hoy</span>
                            @endif
                        </h2>
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $dia['fecha']->format('j/n/y') }}</p>
                        @if ($franjaApertura)
                            <p class="mt-1 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                {{ \Carbon\Carbon::parse($franjaApertura)->format('H:i') }}–{{ \Carbon\Carbon::parse($franjaCierre)->format('H:i') }}
                            </p>
                        @endif
                    </div>

                    <div class="space-y-3 px-4 py-4">
                        @forelse ($dia['turnos'] as $turno)
                            <div class="rounded-sm border border-[#19140035] p-3 dark:border-[#3E3E3A]">
                                <p class="text-sm font-semibold">
                                    {{ $turno->fecha_hora->format('H:i') }}–{{ $turno->fecha_hora->copy()->addMinutes($turno->service->duration)->format('H:i') }}
                                </p>
                                <p class="mt-1 truncate text-sm">{{ $turno->client->nombre }} {{ $turno->client->apellido }}</p>
                                <p class="mt-0.5 flex items-center gap-2 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                    <span class="truncate">{{ $turno->service->name }}</span>
                                    <x-status-badge :status="$turno->status" />
                                </p>
                            </div>
                        @empty
                            <p class="py-6 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">Sin turnos</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection