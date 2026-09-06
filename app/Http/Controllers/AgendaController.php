<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function index(Request $request): View
    {
        $fecha = Carbon::today()->format('Y-m-d');

        if ($request->filled('fecha')) {
            $dia = $request->input('fecha');
            if (
                is_string($dia)
                && preg_match('/^(?P<anio>\d{4})-(?P<mes>\d{2})-(?P<dia>\d{2})$/', $dia, $partes)
                && checkdate((int) $partes['mes'], (int) $partes['dia'], (int) $partes['anio'])
            ) {
                $fecha = $dia;
            }
        }

        $fechaLegible = Carbon::parse($fecha)->locale('es')->translatedFormat('l, j \d\e F \d\e Y');

        $turnos = Appointment::with(['client', 'service'])
            ->whereDate('fecha_hora', $fecha)
            ->orderBy('fecha_hora')
            ->get();

        return view('agenda.index', compact('fecha', 'fechaLegible', 'turnos'));
    }
}
