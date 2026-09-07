@extends('layouts.app')

@section('title', 'TurneroPro - Inicio')

@section('content')
    <div class="mx-auto max-w-4xl space-y-16">
        <section class="flex flex-col items-start gap-6 pt-4">
            <div>
                <h1 class="text-4xl font-bold">TurneroPro</h1>
                <p class="mt-3 max-w-xl text-[#706f6c] dark:text-[#A1A09A]">
                    Sistema de gestión de turnos para que tu negocio administre citas, clientes y disponibilidad desde un solo lugar.
                </p>
            </div>

            @auth
                <a href="{{ route('panel') }}" class="rounded-sm bg-[#1b1b18] px-6 py-2.5 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
                    Ir al Panel
                </a>
            @else
                <div class="flex flex-col items-start gap-2">
                    <a href="{{ route('reservar') }}" class="rounded-sm bg-[#1b1b18] px-6 py-2.5 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
                        Reservar un turno
                    </a>
                    <a href="{{ route('mi-turno') }}" class="text-sm text-[#706f6c] hover:underline dark:text-[#A1A09A]">
                        ¿Ya tenés un turno?
                    </a>
                </div>
            @endauth
        </section>

        <section>
            <h2 class="mb-4 text-2xl font-bold">Nuestros servicios</h2>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($services as $service)
                    <div class="flex flex-col rounded-sm border border-[#19140035] p-5 dark:border-[#3E3E3A]">
                        <h3 class="text-lg font-semibold">{{ $service->name }}</h3>
                        <p class="mt-1 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $service->duration }} min · ${{ number_format($service->price, 0, ',', '.') }}
                        </p>
                        <a href="{{ route('reservar', ['service_id' => $service->id]) }}"
                            class="mt-4 rounded-sm bg-[#1b1b18] px-4 py-2 text-center text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
                            Reservar
                        </a>
                    </div>
                @empty
                    <p class="rounded-sm border border-[#19140035] px-4 py-3 text-sm text-[#706f6c] dark:border-[#3E3E3A] dark:text-[#A1A09A] sm:col-span-2 lg:col-span-3">
                        Todavía no hay servicios cargados.
                    </p>
                @endforelse
            </div>
        </section>

        <section>
            <h2 class="mb-4 text-2xl font-bold">Cómo funciona</h2>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-sm border border-[#19140035] p-5 dark:border-[#3E3E3A]">
                    <p class="text-xl font-bold text-[#706f6c] dark:text-[#A1A09A]">1</p>
                    <p class="mt-2 text-sm font-medium">Elegí un servicio.</p>
                </div>
                <div class="rounded-sm border border-[#19140035] p-5 dark:border-[#3E3E3A]">
                    <p class="text-xl font-bold text-[#706f6c] dark:text-[#A1A09A]">2</p>
                    <p class="mt-2 text-sm font-medium">Elegí una fecha y un horario disponible.</p>
                </div>
                <div class="rounded-sm border border-[#19140035] p-5 dark:border-[#3E3E3A]">
                    <p class="text-xl font-bold text-[#706f6c] dark:text-[#A1A09A]">3</p>
                    <p class="mt-2 text-sm font-medium">Completá tus datos.</p>
                </div>
                <div class="rounded-sm border border-[#19140035] p-5 dark:border-[#3E3E3A]">
                    <p class="text-xl font-bold text-[#706f6c] dark:text-[#A1A09A]">4</p>
                    <p class="mt-2 text-sm font-medium">Confirmá tu turno.</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="mb-4 text-2xl font-bold">Beneficios</h2>

            <div class="grid gap-4 sm:grid-cols-2">
                <p class="rounded-sm border border-[#19140035] px-5 py-4 text-sm dark:border-[#3E3E3A]">Sin llamadas.</p>
                <p class="rounded-sm border border-[#19140035] px-5 py-4 text-sm dark:border-[#3E3E3A]">Horarios siempre actualizados.</p>
                <p class="rounded-sm border border-[#19140035] px-5 py-4 text-sm dark:border-[#3E3E3A]">Reservá en menos de un minuto.</p>
                <p class="rounded-sm border border-[#19140035] px-5 py-4 text-sm dark:border-[#3E3E3A]">Confirmación inmediata.</p>
            </div>
        </section>

        <section>
            <h2 class="mb-4 text-2xl font-bold">Horario de atención</h2>

            <div class="rounded-sm border border-[#19140035] p-5 dark:border-[#3E3E3A]">
                <p class="text-sm">Lunes a Domingo</p>
                <p class="mt-1 text-lg font-medium">{{ $horario['opening_time'] }} - {{ $horario['closing_time'] }}</p>
            </div>
        </section>

        <section>
            <h2 class="mb-4 text-2xl font-bold">Preguntas frecuentes</h2>

            <div class="space-y-3">
                <details class="rounded-sm border border-[#19140035] p-5 dark:border-[#3E3E3A]">
                    <summary class="cursor-pointer text-sm font-medium">¿Necesito una cuenta para reservar?</summary>
                    <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        No. Podés reservar un turno con solo dejar tus datos, sin registrarte.
                    </p>
                </details>

                <details class="rounded-sm border border-[#19140035] p-5 dark:border-[#3E3E3A]">
                    <summary class="cursor-pointer text-sm font-medium">¿Cómo elijo el horario?</summary>
                    <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        Elegís el servicio y la fecha, y te mostramos los horarios disponibles al momento.
                    </p>
                </details>
            </div>
        </section>

        <section class="flex flex-col items-center gap-4 pb-4 text-center">
            @auth
                <p class="text-lg font-semibold">¿Listo para administrar tus turnos?</p>
                <a href="{{ route('panel') }}" class="rounded-sm bg-[#1b1b18] px-6 py-2.5 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
                    Ir al Panel
                </a>
            @else
                <p class="text-lg font-semibold">¿Listo para reservar?</p>
                <a href="{{ route('reservar') }}" class="rounded-sm bg-[#1b1b18] px-6 py-2.5 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
                    Reservar un turno
                </a>
            @endauth
        </section>
    </div>
@endsection