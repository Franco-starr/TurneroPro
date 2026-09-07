<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StoreSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('inicio');
})->name('home');

// Rutas de servicios
Route::resource('services', ServiceController::class)->except('show');

// Rutas de clientes
Route::resource('clients', ClientController::class)->only(['index', 'create', 'store']);

// Rutas de turnos
Route::resource('appointments', AppointmentController::class)->only(['index', 'create', 'store']);

// Acciones rápidas de estado de turnos
Route::patch('appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
Route::patch('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

// Agenda diaria
Route::get('agenda', [AgendaController::class, 'index'])->name('agenda');

// Rutas de configuración del local
Route::get('store-settings/edit', [StoreSettingController::class, 'edit'])->name('store-settings.edit');
Route::put('store-settings', [StoreSettingController::class, 'update'])->name('store-settings.update');

Route::view('panel', 'panel')->name('panel');
