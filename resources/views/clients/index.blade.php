@extends('layouts.app')

@section('title', 'Clientes - TurneroPro')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Clientes</h1>
        <a href="{{ route('clients.create') }}" class="rounded-sm bg-[#1b1b18] px-5 py-2 text-sm text-white dark:bg-[#eeeeec] dark:text-[#1C1C1A]">
            Nuevo Cliente
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
                    <th class="px-4 py-3">Apellido</th>
                    <th class="px-4 py-3">Teléfono</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#19140035] dark:divide-[#3E3E3A]">
                @forelse ($clients as $client)
                    <tr>
                        <td class="px-4 py-3">{{ $client->nombre }}</td>
                        <td class="px-4 py-3">{{ $client->apellido }}</td>
                        <td class="px-4 py-3">{{ $client->telefono }}</td>
                        <td class="px-4 py-3">{{ $client->email }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('clients.show', $client) }}" class="text-sm hover:underline">Ver</a>
                                <a href="{{ route('clients.edit', $client) }}" class="text-sm hover:underline">Editar</a>

                                @if ($client->appointments()->exists())
                                    <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Eliminar</span>
                                @else
                                    <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('¿Eliminar este cliente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:underline dark:text-red-400">Eliminar</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-[#706f6c] dark:text-[#A1A09A]">
                            No hay clientes registrados aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection