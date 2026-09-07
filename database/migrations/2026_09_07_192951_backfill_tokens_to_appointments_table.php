<?php

use App\Models\Appointment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Appointment::query()
            ->whereNull('token')
            ->get()
            ->each(fn (Appointment $appointment) => $appointment->update([
                'token' => (string) Str::uuid(),
            ]));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
