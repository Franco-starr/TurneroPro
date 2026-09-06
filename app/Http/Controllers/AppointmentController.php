<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(): View
    {
        $appointments = Appointment::with(['client', 'service'])->latest()->get();

        return view('appointments.index', compact('appointments'));
    }

    public function create(): View
    {
        $clients = Client::orderBy('nombre')->get();
        $services = Service::orderBy('name')->get();

        return view('appointments.create', compact('clients', 'services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|integer|exists:clients,id',
            'service_id' => 'required|integer|exists:services,id',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
        ]);

        Appointment::create([
            'client_id' => $validated['client_id'],
            'service_id' => $validated['service_id'],
            'fecha_hora' => $validated['fecha'].' '.$validated['hora'],
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Turno creado correctamente.');
    }
}
