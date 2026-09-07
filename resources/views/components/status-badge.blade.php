@props(['status'])

<span
    class="inline-block rounded-sm border px-2 py-1 text-xs font-medium
        @switch($status)
            @case(\App\Enums\AppointmentStatus::Completed)
                border-green-600 text-green-700 dark:text-green-400
                @break
            @case(\App\Enums\AppointmentStatus::Cancelled)
                border-red-600 text-red-700 dark:text-red-400 line-through
                @break
            @default
                border-amber-500 text-amber-700 dark:text-amber-400
        @endswitch
    "
>
    @switch($status)
        @case(\App\Enums\AppointmentStatus::Completed)
            Completado
            @break
        @case(\App\Enums\AppointmentStatus::Cancelled)
            Cancelado
            @break
        @default
            Pendiente
    @endswitch
</span>