<?php

use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('inicio');
})->name('home');

// Rutas de servicios
Route::resource('services', ServiceController::class)->except('show');

Route::view('panel', 'panel')->name('panel');
