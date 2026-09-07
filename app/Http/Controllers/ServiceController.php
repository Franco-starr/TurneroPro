<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador para la gestión del CRUD de Servicios.
 * Coordina las peticiones de navegación y persistencia para la entidad Service.
 */
class ServiceController extends Controller
{
    /**
     * Muestra el listado principal de servicios.
     * Recupera los registros ordenados desde el más reciente.
     */
    public function index(): View
    {
        // Obtiene todos los servicios ordenados por fecha de creación descendente (created_at)
        $services = Service::withCount('appointments')->latest()->get();

        // Renderiza la vista 'services.index' pasando la colección de servicios
        return view('services.index', compact('services'));
    }

    /**
     * Muestra el formulario para la creación de un nuevo servicio.
     */
    public function create(): View
    {
        return view('services.create');
    }

    /**
     * Valida y almacena un nuevo servicio en la base de datos.
     *
     * @param  Request  $request  Contiene los datos enviados desde el formulario.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validación de entradas según las restricciones del negocio
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1', // Duración en minutos
            'price' => 'required|numeric|min:0', // Permite valores decimales positivos
        ]);

        // 2. Persistencia utilizando asignación masiva ($fillable en el modelo Service)
        Service::create($validated);

        // 3. Redirección al listado con un mensaje de estado en la sesión (Flash Data)
        return redirect()->route('services.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    /**
     * Muestra el formulario para editar un servicio existente.
     * Hace uso de Route Model Binding para inyectar automáticamente el modelo Service.
     */
    public function edit(Service $service): View
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Valida y actualiza los datos del servicio especificado.
     *
     * @ param Request $request Datos actualizados del formulario.
     *
     * @ param Service $service Instancia del servicio a modificar (vía Route Model Binding).
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        // 1. Validación de los datos recibidos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        // 2. Actualización de los datos en la base de datos
        $service->update($validated);

        // 3. Redirección con mensaje de éxito
        return redirect()->route('services.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    /**
     * Elimina el servicio especificado de la base de datos.
     *
     * @ param Service $service Instancia del servicio a eliminar.
     */
    public function destroy(Service $service): RedirectResponse
    {
        if ($service->appointments()->exists()) {
            return back()->with('success', 'No se puede eliminar: el servicio tiene turnos registrados.');
        }

        // Eliminación física del registro
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Servicio eliminado correctamente.');
    }
}
