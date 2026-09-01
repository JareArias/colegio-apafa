<?php

namespace App\Http\Controllers;

use App\Models\ApafaFine;
use App\Models\ApafaMeeting;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApafaMeetingController extends Controller
{
    // Mostrar listado de reuniones
    public function index()
    {
        $meetings = ApafaMeeting::with(['attendances.user']) // Carga los asistentes y sus datos de usuario
            ->withCount('attendances')
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
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'meeting_date'      => 'required|date',
            'start_time'        => 'required|date_format:H:i',
            'tolerance_minutes' => 'required|integer|min:0|max:120',
            'is_active'         => 'boolean',
        ]);

        // Si se marca como activa, desactivamos las demás
        if ($request->is_active) {
            ApafaMeeting::query()->update(['is_active' => false]);
        }

        ApafaMeeting::create([
            'title'             => $request->title,
            'description'       => $request->description,
            'meeting_date'      => $request->meeting_date,
            'start_time'        => $request->start_time,
            'tolerance_minutes' => $request->tolerance_minutes,
            'is_active'         => $request->is_active ?? true,
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

    public function finish(ApafaMeeting $meeting)
    {
        // 1. Protección: Evitar re-ejecución si la reunión ya fue finalizada
        if ($meeting->status === 'finished') {
            return redirect()->back()->with('error', 'Esta reunión ya fue finalizada previamente.');
        }

        // 2. Cambiar el estado de la reunión a 'finished' y desactivarla
        $meeting->update([
            'status' => 'finished',
            'is_active' => false,
        ]);

        // 3. Obtener los IDs de los padres que SÍ asistieron
        $attendedParentIds = $meeting->attendances()->pluck('user_id')->toArray();

        // 4. Obtener todos los padres registrados (excluyendo administradores si los hay)
        $absentParents = User::whereNotIn('id', $attendedParentIds)
            ->whereHas('students') // Asegura aplicar solo a usuarios registrados como apoderados
            ->get();

        // 5. Generar la multa para cada padre faltante
        foreach ($absentParents as $parent) {
            ApafaFine::firstOrCreate(
                [
                    'user_id'          => $parent->id,
                    'apafa_meeting_id' => $meeting->id,
                ],
                [
                    'amount' => $meeting->fine_amount ?? 20.00, // Monto por defecto si es null
                    'status' => 'pending',
                ]
            );
        }

        return back()->with('success', 'La reunión ha sido cerrada y se generaron las multas correctamente.');
    }
}