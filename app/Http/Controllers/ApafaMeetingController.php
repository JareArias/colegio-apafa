<?php

namespace App\Http\Controllers;

use App\Models\ApafaMeeting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApafaMeetingController extends Controller
{
    // Mostrar listado de reuniones
    public function index()
    {
        $meetings = ApafaMeeting::withCount('attendances')
            ->orderBy('meeting_date', 'desc')
            ->get();

        return Inertia::render('Apafa/Meetings/Index', [
            'meetings' => $meetings
        ]);
    }

    // Guardar una nueva reunión
    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'meeting_date' => 'required|date',
            'is_active'    => 'boolean',
        ]);

        // Si se marca como activa, desactivamos las demás
        if ($request->is_active) {
            ApafaMeeting::query()->update(['is_active' => false]);
        }

        ApafaMeeting::create([
            'title'        => $request->title,
            'description'  => $request->description,
            'meeting_date' => $request->meeting_date,
            'is_active'    => $request->is_active ?? true,
        ]);

        return back()->with('success', 'Reunión creada correctamente.');
    }

    // Cambiar estado activo/inactivo
    public function toggleStatus(ApafaMeeting $meeting)
    {
        if (!$meeting->is_active) {
            // Desactivar todas las reuniones antes de activar esta
            ApafaMeeting::query()->update(['is_active' => false]);
            $meeting->update(['is_active' => true]);
        } else {
            $meeting->update(['is_active' => false]);
        }

        return back()->with('success', 'Estado de la reunión actualizado.');
    }
}
