<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\StoreSetting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $services = Service::orderBy('name')->get();

        $settings = StoreSetting::first();
        $horario = [
            'opening_time' => $settings->opening_time ?? config('store.opening_time'),
            'closing_time' => $settings->closing_time ?? config('store.closing_time'),
            'days' => $settings?->days ?: config('store.days'),
        ];

        return view('inicio', compact('services', 'horario'));
    }
}
