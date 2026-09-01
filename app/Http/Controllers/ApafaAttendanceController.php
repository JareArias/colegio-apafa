<?php

namespace App\Http\Controllers;

use App\Models\ApafaMeeting;
use App\Models\ApafaAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ApafaAttendanceController extends Controller
{
    // Mostrar la pantalla de toma de asistencia y la lista en tiempo real
    public function index(Request $request)
    {
        $meetings = ApafaMeeting::where('is_active', true)->get();
        $selectedMeetingId = $request->input('meeting_id', $meetings->first()?->id);

        $attendances = [];
        if ($selectedMeetingId) {
            $attendances = ApafaAttendance::with(['user.students'])
                ->where('apafa_meeting_id', $selectedMeetingId)
                ->orderBy('scanned_at', 'desc')
                ->get();
        }

        return Inertia::render('Apafa/AttendanceIndex', [
            'meetings' => $meetings,
            'attendances' => $attendances,
            'selectedMeetingId' => (int) $selectedMeetingId,
        ]);
    }

    // Registrar la asistencia por DNI
    public function registerByDni(Request $request)
    {
        $request->validate([
            'meeting_id' => 'required|exists:apafa_meetings,id',
            'dni'        => 'required|string',
        ]);

        $padre = User::where('dni', $request->dni)->first();

        if (!$padre) {
            return back()->withErrors(['dni' => 'El DNI ingresado no corresponde a ningún padre registrado.']);
        }

        $meeting = ApafaMeeting::findOrFail($request->meeting_id);

        if (!$meeting->is_active) {
            return back()->withErrors(['dni' => 'La reunión no está activa para registrar asistencia.']);
        }

        // Verificar si ya registró asistencia
        $exists = ApafaAttendance::where('apafa_meeting_id', $meeting->id)
            ->where('user_id', $padre->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['dni' => 'El padre de familia ya registró asistencia previamente.']);
        }

        $now = Carbon::now();
        $status = $this->calculateStatus($meeting, $now);

        // Registrar asistencia
        ApafaAttendance::create([
            'apafa_meeting_id' => $meeting->id,
            'user_id'          => $padre->id,
            'status'           => $status, // 'present' o 'late'
            'registered_by'    => 'dni',
            'scanned_at'       => $now,
        ]);

        $estadoTxt = $status === 'late' ? '(Tardanza)' : '(Puntual)';
        return back()->with('success', "Asistencia registrada correctamente para: {$padre->name} {$estadoTxt}");
    }

    // Registrar asistencia mediante escáner de Código QR
    public function registerByQr(Request $request)
    {
        $request->validate([
            'meeting_id' => 'required|exists:apafa_meetings,id',
            'qr_code'    => 'required|string',
        ]);

        // Buscamos al padre por su DNI o por su token QR
        $padre = User::where('dni', $request->qr_code)->first();

        if (!$padre) {
            return back()->withErrors(['qr' => 'El código QR escaneado no pertenece a ningún apoderado válido.']);
        }

        $meeting = ApafaMeeting::findOrFail($request->meeting_id);

        if (!$meeting->is_active) {
            return back()->withErrors(['qr' => 'La reunión no está activa para registrar asistencia.']);
        }

        // Verificar si ya se registró
        $exists = ApafaAttendance::where('apafa_meeting_id', $meeting->id)
            ->where('user_id', $padre->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['qr' => '¡Atención! ' . $padre->name . ' ya registró su asistencia.']);
        }

        $now = Carbon::now();
        $status = $this->calculateStatus($meeting, $now);

        // Guardar asistencia
        ApafaAttendance::create([
            'apafa_meeting_id' => $meeting->id,
            'user_id'          => $padre->id,
            'status'           => $status, // 'present' o 'late'
            'registered_by'    => 'self_qr',
            'scanned_at'       => $now,
        ]);

        $estadoTxt = $status === 'late' ? '(Tardanza)' : '(Puntual)';
        return back()->with('success', "Asistencia registrada (QR) para: {$padre->name} {$estadoTxt}");
    }

    // Exportar la lista de asistencias a PDF
    public function exportPdf(Request $request, $meetingId)
    {
        $meeting = ApafaMeeting::findOrFail($meetingId);
        
        $attendances = ApafaAttendance::with(['user'])
            ->where('apafa_meeting_id', $meetingId)
            ->orderBy('scanned_at', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.attendance-report', [
            'meeting'     => $meeting,
            'attendances' => $attendances,
            'date'        => now()->format('d/m/Y H:i A'),
        ]);

        return $pdf->download('Reporte_Asistencia_Reunion_' . $meeting->id . '.pdf');
    }

    /**
     * Evalúa si el marcado actual corresponde a Puntual (present) o Tardanza (late).
     */
    private function calculateStatus(ApafaMeeting $meeting, Carbon $now): string
    {
        if (!$meeting->start_time) {
            return 'present';
        }

        $meetingDateStr = is_string($meeting->meeting_date) 
            ? Carbon::parse($meeting->meeting_date)->format('Y-m-d') 
            : $meeting->meeting_date->format('Y-m-d');

        $limitTime = Carbon::parse($meetingDateStr . ' ' . $meeting->start_time)
            ->addMinutes($meeting->tolerance_minutes ?? 0);

        return $now->greaterThan($limitTime) ? 'late' : 'present';
    }
}