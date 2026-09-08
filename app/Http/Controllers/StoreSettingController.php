<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StoreSettingController extends Controller
{
    public function edit(): View
    {
        $settings = StoreSetting::first();

        return view('store-settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i',
            'days' => 'required|array|min:1',
            'days.*' => 'integer|between:1,7',
        ]);

        $validated['days'] = array_map('intval', $validated['days']);

        $apertura = Carbon::parse($validated['opening_time']);
        $cierre = Carbon::parse($validated['closing_time']);

        if ($cierre->lte($apertura)) {
            throw ValidationException::withMessages([
                'closing_time' => 'El horario de cierre debe ser posterior al de apertura.',
            ]);
        }

        StoreSetting::firstOrCreate([], $validated)->update($validated);

        return redirect()->route('store-settings.edit')
            ->with('success', 'Horario de atención actualizado correctamente.');
    }
}
