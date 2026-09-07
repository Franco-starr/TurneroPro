<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::withCount('appointments')->latest()->get();

        return view('clients.index', compact('clients'));
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function show(Client $client): View
    {
        $appointments = $client->appointments()
            ->with('service')
            ->latest('fecha_hora')
            ->get();

        return view('clients.show', compact('client', 'appointments'));
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $this->validated($request, $client);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        if ($client->appointments()->exists()) {
            return back()->with('success', 'No se puede eliminar: el cliente tiene turnos registrados.');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }

    /**
     * @return array<string, string>
     */
    private function validated(Request $request, ?Client $client = null): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:30',
            'email' => ['required', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($client?->id)],
        ]);
    }
}
