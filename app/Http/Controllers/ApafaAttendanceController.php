<?php

namespace App\Http\Controllers;

use App\Models\ApafaMeeting;
use App\Models\ApafaAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApafaAttendanceController extends Controller
{
    // Mostrar la pantalla de toma de asistencia
    public function index()
    {
        $meetings = ApafaMeeting::where('is_active', true)->get();
        return Inertia::render('Apafa/AttendanceIndex', [
            'meetings' => $meetings
        ]);
    }

    // Registrar la asistencia por DNI
    public function registerByDni(Request $request)
    {
        $request->validate([
            'meeting_id' => 'required|exists:apafa_meetings,id',
            'dni' => 'required|string',
        ]);

        $padre = User::where('dni', $request->dni)->first();

        if (!$padre) {
            return back()->withErrors(['dni' => 'El DNI ingresado no corresponde a ningún padre registrado.']);
        }

        // Verificar si ya registró asistencia
        $exists = ApafaAttendance::where('apafa_meeting_id', $request->meeting_id)
            ->where('user_id', $padre->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['dni' => 'El padre de familia ya registró asistencia previamente.']);
        }

        // Registrar asistencia
        ApafaAttendance::create([
            'apafa_meeting_id' => $request->meeting_id,
            'user_id' => $padre->id,
            'status' => 'presente',
            'registered_by' => 'dni',
            'scanned_at' => now(),
        ]);

        return back()->with('success', 'Asistencia registrada correctamente para: ' . $padre->name);
    }
}
