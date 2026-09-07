@extends('layouts.app')

@section('title', 'TurneroPro - Inicio')

@section('content')
    <div class="flex flex-col items-start gap-6">
        <div>
            <h1 class="text-3xl font-bold">TurneroPro</h1>
            <p class="mt-2 text-[#706f6c] dark:text-[#A1A09A]">
                Sistema de gestión de turnos para que tu negocio administre citas, clientes y disponibilidad desde un solo lugar.
            </p>
        </div>

        @auth
            <a href="{{ route('panel') }}" class="rounded-sm bg-[#1b1b18] px-5 py-2 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
                Ir al Panel
            </a>
        @else
            <a href="{{ route('reservar') }}" class="rounded-sm bg-[#1b1b18] px-5 py-2 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
                Reservar un turno
            </a>
        @endauth
    </div>
@endsection
