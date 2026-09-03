<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::latest()->get();

        return view('services.index', compact('services'));
    }

    public function create(): View
    {
        return view('services.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        Service::create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Service $service): View
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Servicio eliminado correctamente.');
    }
}
