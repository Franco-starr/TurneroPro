<x-mail::message>
# ¡Turno reservado!

Hola {{ $appointment->client->nombre }}, tu turno quedó confirmado:

**Servicio:** {{ $appointment->service->name }}

**Fecha:** {{ $appointment->fecha_hora->format('d/m/Y') }} a las {{ $appointment->fecha_hora->format('H:i') }} hs

Si no podés asistir, podés cancelarlo desde el siguiente enlace:

<x-mail::button :url="route('reserva.cancelar', $appointment->token)">
Cancelar turno
</x-mail::button>

¡Te esperamos!

{{ config('app.name') }}
</x-mail::message>