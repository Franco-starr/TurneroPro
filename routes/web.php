<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('inicio');
})->name('home');

// Rutas de servicios
Route::resource('services', ServiceController::class)->except('show');

// Rutas de clientes
Route::resource('clients', ClientController::class)->only(['index', 'create', 'store']);

Route::view('panel', 'panel')->name('panel');
