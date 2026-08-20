<?php

namespace App\Http\Controllers;

use App\Models\ApafaAttendance;
use App\Models\ApafaMeeting;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
   public function index()
    {
        // Trae la reunión marcada como activa priorizando is_active = 1
        $activeMeeting = ApafaMeeting::orderBy('is_active', 'desc')
            ->where('is_active', '>', 0)
            ->first();

        $totalParents = User::count();
        $totalAttendees = 0;
        $quorumPercentage = 0;
        $recentAttendances = [];

        if ($activeMeeting) {
            $totalAttendees = ApafaAttendance::where('apafa_meeting_id', $activeMeeting->id)->count();

            if ($totalParents > 0) {
                $quorumPercentage = round(($totalAttendees / $totalParents) * 100, 1);
            }

            $recentAttendances = ApafaAttendance::with('user')
                ->where('apafa_meeting_id', $activeMeeting->id)
                ->orderBy('scanned_at', 'desc')
                ->take(5)
                ->get();
        }

        return Inertia::render('Dashboard', [
            'activeMeeting'    => $activeMeeting,
            'totalParents'     => $totalParents,
            'totalAttendees'   => $totalAttendees,
            'absentParents'    => max(0, $totalParents - $totalAttendees),
            'quorumPercentage' => $quorumPercentage,
            'recentAttendances'=> $recentAttendances ?? [],
        ]);
    }
}
