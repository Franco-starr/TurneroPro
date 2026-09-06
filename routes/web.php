<?php

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

// Rutas de configuración del local
Route::get('store-settings/edit', [StoreSettingController::class, 'edit'])->name('store-settings.edit');
Route::put('store-settings', [StoreSettingController::class, 'update'])->name('store-settings.update');

Route::view('panel', 'panel')->name('panel');
