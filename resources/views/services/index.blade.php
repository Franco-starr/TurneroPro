@extends('layouts.app')

@section('title', 'Servicios - TurneroPro')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Servicios</h1>
        <a href="{{ route('services.create') }}" class="rounded-sm bg-[#1b1b18] px-5 py-2 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
            Nuevo Servicio
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-sm border border-green-600 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-400 dark:bg-green-900/30 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-sm border border-[#19140035] dark:border-[#3E3E3A]">
        <table class="min-w-full divide-y divide-[#19140035] dark:divide-[#3E3E3A]">
            <thead class="bg-[#F1F1EF] dark:bg-[#111110]">
                <tr class="text-left text-xs uppercase tracking-wide text-[#706f6c] dark:text-[#A1A09A]">
                    <th class="px-4 py-3">Nombre</th>
                    <th class="px-4 py-3">Duración</th>
                    <th class="px-4 py-3">Precio</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#19140035] dark:divide-[#3E3E3A]">
                @forelse ($services as $service)
                    <tr>
                        <td class="px-4 py-3">{{ $service->name }}</td>
                        <td class="px-4 py-3">{{ $service->duration }} min</td>
                        <td class="px-4 py-3">${{ number_format($service->price, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex items-center gap-3 text-sm">
                                <a href="{{ route('services.edit', $service) }}" class="hover:underline">Editar</a>
                                <form method="POST" action="{{ route('services.destroy', $service) }}" onsubmit="return confirm('¿Eliminar este servicio?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline dark:text-red-400">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                            No hay servicios registrados aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
