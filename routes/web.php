<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StoreSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Health check para el deploy (Render)
Route::get('/up', function () {
    return response()->json(['status' => 'ok']);
});

// Rutas públicas de reserva
Route::get('reservar', [BookingController::class, 'index'])->name('reservar');
Route::post('reservar', [BookingController::class, 'store'])->name('reserva.store')->middleware('throttle:reservas');
Route::get('reservar/confirmado', [BookingController::class, 'confirmada'])->name('reserva.confirmada');

// Búsqueda y cancelación pública de turnos
Route::get('mi-turno', [BookingController::class, 'lookup'])->name('mi-turno');
Route::post('mi-turno', [BookingController::class, 'search'])->name('mi-turno.buscar')->middleware('throttle:reservas');
Route::get('reservar/{appointment:token}/cancelar', [BookingController::class, 'cancelar'])->name('reserva.cancelar');
Route::delete('reservar/{appointment:token}', [BookingController::class, 'destroy'])->name('reserva.cancel');

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Zona administrativa protegida
Route::middleware('auth')->group(function () {
    // Rutas de servicios
    Route::resource('services', ServiceController::class)->except('show');

    // Rutas de clientes
    Route::resource('clients', ClientController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // Rutas de turnos
    Route::resource('appointments', AppointmentController::class)->only(['index', 'create', 'store']);

    // Acciones rápidas de estado de turnos
    Route::patch('appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::patch('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Agenda diaria
    Route::get('agenda', [AgendaController::class, 'index'])->name('agenda');

    // Agenda semanal
    Route::get('agenda-semanal', [AgendaController::class, 'semanal'])->name('agenda.semanal');

    // Rutas de configuración del local
    Route::get('store-settings/edit', [StoreSettingController::class, 'edit'])->name('store-settings.edit');
    Route::put('store-settings', [StoreSettingController::class, 'update'])->name('store-settings.update');

    Route::get('panel', [PanelController::class, 'index'])->name('panel');
});
