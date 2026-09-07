<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\StoreSetting;
use Carbon\Carbon;

class AvailabilityService
{
    public function isSlotAvailable(Carbon $inicio, Service $service): bool
    {
        return $this->isWithinBusinessHours($inicio, $service)
            && ! $this->isOverlapping($inicio, $service);
    }

    public function isWithinBusinessHours(Carbon $inicio, Service $service): bool
    {
        [$apertura, $cierre] = $this->businessHours($inicio);

        return $inicio->gte($apertura)
            && $inicio->copy()->addMinutes($service->duration)->lte($cierre);
    }

    public function isOverlapping(Carbon $inicio, Service $service): bool
    {
        $fin = $inicio->copy()->addMinutes($service->duration);

        return Appointment::query()
            ->with('service')
            ->where('status', '!=', AppointmentStatus::Cancelled)
            ->where('fecha_hora', '<', $fin)
            ->get()
            ->contains(fn (Appointment $appointment) => $appointment->fecha_hora->copy()
                ->addMinutes($appointment->service->duration)
                ->isAfter($inicio));
    }

    /**
     * @return array<int, string> String times in "H:i" format
     */
    public function availableSlotsFor(Service $service, Carbon $fecha, int $incrementMinutes = 30): array
    {
        [$apertura, $cierre] = $this->businessHours($fecha);

        $turnosDelDia = Appointment::with('service')
            ->whereDate('fecha_hora', $fecha)
            ->where('status', '!=', AppointmentStatus::Cancelled)
            ->get();

        $ultimoInicio = $cierre->copy()->subMinutes($service->duration);
        $slots = [];

        for ($candidato = $apertura->copy(); $candidato->lte($ultimoInicio); $candidato->addMinutes($incrementMinutes)) {
            if ($fecha->isToday() && $candidato->lt(Carbon::now())) {
                continue;
            }

            $finCandidato = $candidato->copy()->addMinutes($service->duration);

            $ocupado = $turnosDelDia->contains(
                fn (Appointment $turno) => $turno->fecha_hora->lt($finCandidato)
                    && $turno->fecha_hora->copy()->addMinutes($turno->service->duration)->isAfter($candidato),
            );

            if (! $ocupado) {
                $slots[] = $candidato->format('H:i');
            }
        }

        return $slots;
    }

    /**
     * @return array{Carbon, Carbon}
     */
    private function businessHours(Carbon $fecha): array
    {
        $settings = StoreSetting::first();

        return [
            Carbon::parse($fecha->toDateString().' '.($settings->opening_time ?? config('store.opening_time'))),
            Carbon::parse($fecha->toDateString().' '.($settings->closing_time ?? config('store.closing_time'))),
        ];
    }
}
