@extends('layouts.auth')

@section('title', 'Ingresar - TurneroPro')

@section('meta_description', 'Ingresá a TurneroPro para administrar tus servicios, clientes, turnos y la agenda de tu negocio.')

@section('content')
    <div class="w-full max-w-sm rounded-sm border border-[#19140035] p-6 dark:border-[#3E3E3A]">
        <h1 class="text-2xl font-bold">Ingresar</h1>
        <p class="mt-1 text-sm text-[#706f6c] dark:text-[#A1A09A]">Iniciar sesión para administrar TurneroPro.</p>

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="mb-1 block text-sm font-medium">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                >
                @error('email')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium">Contraseña</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-sm border border-[#19140035] px-3 py-2 text-sm dark:border-[#3E3E3A] dark:bg-[#111110]"
                >
            </div>

            <button type="submit" class="w-full rounded-sm bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700 dark:bg-green-600 dark:hover:bg-green-700 dark:text-white">
                Ingresar
            </button>
        </form>
    </div>
@endsection