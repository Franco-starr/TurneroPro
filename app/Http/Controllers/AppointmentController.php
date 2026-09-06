<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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

        $service = Service::findOrFail($validated['service_id']);
        $inicio = Carbon::parse($validated['fecha'].' '.$validated['hora']);
        $fin = $inicio->copy()->addMinutes($service->duration);

        $apertura = Carbon::parse($validated['fecha'].' '.config('store.opening_time'));
        $cierre = Carbon::parse($validated['fecha'].' '.config('store.closing_time'));

        if ($inicio->lt($apertura) || $fin->gt($cierre)) {
            throw ValidationException::withMessages([
                'hora' => 'El turno debe estar dentro del horario de atención ('.config('store.opening_time').' a '.config('store.closing_time').').',
            ]);
        }

        $solapado = Appointment::with('service')
            ->where('fecha_hora', '<', $fin)
            ->get()
            ->contains(fn (Appointment $appointment) => $appointment->fecha_hora->copy()
                ->addMinutes($appointment->service->duration)
                ->isAfter($inicio));

        if ($solapado) {
            throw ValidationException::withMessages([
                'fecha' => 'Ese horario no está disponible.',
            ]);
        }

        Appointment::create([
            'client_id' => $validated['client_id'],
            'service_id' => $validated['service_id'],
            'fecha_hora' => $validated['fecha'].' '.$validated['hora'],
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Turno creado correctamente.');
    }
}
