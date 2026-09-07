<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Mail\ReservaConfirmada;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(private readonly AvailabilityService $availability) {}

    public function index(Request $request): View
    {
        $services = Service::orderBy('name')->get();
        $slots = [];

        $fecha = $request->filled('fecha') ? $request->input('fecha') : old('fecha');
        $serviceId = $request->filled('service_id') ? $request->input('service_id') : old('service_id');

        if ($fecha && $serviceId && $this->isValidFutureDate($fecha)) {
            $service = Service::find($serviceId);

            if ($service) {
                $slots = $this->availability->availableSlotsFor($service, Carbon::parse($fecha));
            }
        }

        return view('reservar.index', compact('services', 'slots', 'fecha', 'serviceId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:255',
            'email' => 'required|email',
            'service_id' => 'required|integer|exists:services,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => 'required|date_format:H:i',
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $inicio = Carbon::parse($validated['fecha'].' '.$validated['hora']);

        if ($inicio->lt(Carbon::now())) {
            throw ValidationException::withMessages([
                'fecha' => 'No se puede reservar un horario en el pasado.',
            ]);
        }

        if (! in_array($validated['hora'], $this->availability->availableSlotsFor($service, $inicio->copy()->startOfDay())) || ! $this->availability->isSlotAvailable($inicio, $service)) {
            throw ValidationException::withMessages([
                'hora' => 'Ese horario ya no está disponible.',
            ]);
        }

        $client = Client::firstOrCreate(
            ['email' => $validated['email']],
            [
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'telefono' => $validated['telefono'],
            ],
        );

        $appointment = Appointment::create([
            'client_id' => $client->id,
            'service_id' => $service->id,
            'fecha_hora' => $inicio,
            'status' => AppointmentStatus::Pending,
        ]);

        try {
            Mail::to($client->email)->send(new ReservaConfirmada($appointment));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('reserva.confirmada')->with('reserva', [
            'servicio' => $service->name,
            'duracion' => $service->duration,
            'fecha' => $inicio->format('d/m/Y'),
            'hora' => $inicio->format('H:i'),
            'cliente' => $client->nombre.' '.$client->apellido,
            'token' => $appointment->token,
        ]);
    }

    public function confirmada(): View
    {
        return view('reservar.confirmada');
    }

    public function lookup(): View
    {
        return view('mi-turno');
    }

    public function search(Request $request): View
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $client = Client::where('email', $validated['email'])->first();

        $appointments = $client
            ? $client->appointments()
                ->with('service')
                ->where('fecha_hora', '>', Carbon::now())
                ->where('status', AppointmentStatus::Pending)
                ->orderBy('fecha_hora')
                ->get()
            : collect();

        return view('mi-turno', [
            'email' => $validated['email'],
            'appointments' => $appointments,
        ]);
    }

    public function cancelar(Appointment $appointment): View|RedirectResponse
    {
        if (! $this->canBeCancelled($appointment)) {
            return redirect()->route('home')
                ->with('info', 'Este turno ya no se puede cancelar.');
        }

        return view('reservar.cancelar', compact('appointment'));
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        if (! $this->canBeCancelled($appointment)) {
            return redirect()->route('home')
                ->with('info', 'Este turno ya no se puede cancelar.');
        }

        $appointment->update(['status' => AppointmentStatus::Cancelled]);

        return redirect()->route('home')
            ->with('success', 'Tu turno fue cancelado correctamente.');
    }

    private function canBeCancelled(Appointment $appointment): bool
    {
        return $appointment->status === AppointmentStatus::Pending
            && $appointment->fecha_hora->isFuture();
    }

    private function isValidFutureDate(string $fecha): bool
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return false;
        }

        [$anio, $mes, $dia] = array_map('intval', explode('-', $fecha));

        if (! checkdate($mes, $dia, $anio)) {
            return false;
        }

        return Carbon::parse($fecha)->gte(Carbon::today());
    }
}
