<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\StoreSetting;
use App\Services\AvailabilityService;
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

    public function semanal(Request $request): View
    {
        $semana = Carbon::today();

        if ($request->filled('mes')) {
            $mes = $request->input('mes');
            if (
                is_string($mes)
                && preg_match('/^(?P<anio>\d{4})-(?P<mes>\d{2})$/', $mes, $partes)
                && checkdate((int) $partes['mes'], 1, (int) $partes['anio'])
            ) {
                $semana = Carbon::createFromDate((int) $partes['anio'], (int) $partes['mes'], 1);
            }
        } elseif ($request->filled('semana')) {
            $dia = $request->input('semana');
            if (
                is_string($dia)
                && preg_match('/^(?P<anio>\d{4})-(?P<mes>\d{2})-(?P<dia>\d{2})$/', $dia, $partes)
                && checkdate((int) $partes['mes'], (int) $partes['dia'], (int) $partes['anio'])
            ) {
                $semana = Carbon::parse($dia);
            }
        }

        $inicioSemana = $semana->copy()->startOfWeek(Carbon::MONDAY);
        $finSemana = $inicioSemana->copy()->addDays(6)->endOfDay();

        $turnosSemana = Appointment::with(['client', 'service'])
            ->whereBetween('fecha_hora', [$inicioSemana, $finSemana])
            ->orderBy('fecha_hora')
            ->get()
            ->groupBy(fn (Appointment $turno) => $turno->fecha_hora->toDateString());

        $dias = collect(range(0, 6))->map(function (int $offset) use ($inicioSemana, $turnosSemana): array {
            $fecha = $inicioSemana->copy()->addDays($offset);

            return [
                'fecha' => $fecha,
                'turnos' => $turnosSemana->get($fecha->toDateString(), collect()),
                'esHoy' => $fecha->isSameDay(Carbon::today()),
            ];
        })->all();

        $storeSetting = StoreSetting::query()->latest()->first();
        $availability = app(AvailabilityService::class);

        $mejoras = array_map(
            fn (array $dia) => array_merge($dia, ['esCerrado' => ! $availability->isOpenOn($dia['fecha'])]),
            $dias,
        );

        return view('agenda.semanal', [
            'dias' => $mejoras,
            'inicioSemana' => $inicioSemana,
            'finSemana' => $inicioSemana->copy()->addDays(6),
            'semanaAnterior' => $inicioSemana->copy()->subWeek()->toDateString(),
            'semanaSiguiente' => $inicioSemana->copy()->addWeek()->toDateString(),
            'mesActual' => $semana->format('Y-m'),
            'mesAnterior' => $semana->copy()->startOfMonth()->subMonth()->format('Y-m'),
            'mesSiguiente' => $semana->copy()->startOfMonth()->addMonth()->format('Y-m'),
            'franjaApertura' => $storeSetting?->opening_time,
            'franjaCierre' => $storeSetting?->closing_time,
        ]);
    }
}
