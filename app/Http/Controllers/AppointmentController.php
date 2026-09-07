<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\StoreSetting;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private readonly AvailabilityService $availability) {}

    public function index(): View
    {
        $appointments = Appointment::with(['client', 'service'])->latest()->get();

        return view('appointments.index', compact('appointments'));
    }

    public function create(): View
    {
        $clients = Client::orderBy('nombre')->get();
        $services = Service::orderBy('name')->get();
        $settings = StoreSetting::first();

        return view('appointments.create', compact('clients', 'services', 'settings'));
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

        if (! $this->availability->isWithinBusinessHours($inicio, $service)) {
            $settings = StoreSetting::first();

            throw ValidationException::withMessages([
                'hora' => 'El turno debe estar dentro del horario de atención ('.($settings->opening_time ?? config('store.opening_time')).' a '.($settings->closing_time ?? config('store.closing_time')).').',
            ]);
        }

        if ($this->availability->isOverlapping($inicio, $service)) {
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

    public function complete(Appointment $appointment): RedirectResponse
    {
        $appointment->update(['status' => AppointmentStatus::Completed]);

        return redirect()->route('agenda', ['fecha' => $appointment->fecha_hora->format('Y-m-d')])
            ->with('success', 'Turno marcado como completado.');
    }

    public function cancel(Appointment $appointment): RedirectResponse
    {
        $appointment->update(['status' => AppointmentStatus::Cancelled]);

        return redirect()->route('agenda', ['fecha' => $appointment->fecha_hora->format('Y-m-d')])
            ->with('success', 'Turno cancelado.');
    }
}
