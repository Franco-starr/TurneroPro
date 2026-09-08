<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\View\View;

class PanelController extends Controller
{
    public function index(): View
    {
        $hoy = Appointment::query()
            ->where('status', '!=', AppointmentStatus::Cancelled)
            ->whereDate('fecha_hora', Carbon::today())
            ->count();

        $pendientes = Appointment::query()
            ->where('status', AppointmentStatus::Pending)
            ->count();

        return view('panel', [
            'turnosHoy' => $hoy,
            'turnosPendientes' => $pendientes,
            'clientes' => Client::query()->count(),
            'servicios' => Service::query()->count(),
        ]);
    }
}
