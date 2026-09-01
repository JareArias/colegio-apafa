<?php

namespace App\Http\Controllers;

use App\Models\ApafaFine;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FineController extends Controller
{
    /**
     * Muestra la lista de multas (si es admin ve todas, si es padre ve solo las suyas).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ApafaFine::with(['parent', 'meeting']);

        // Si no es admin, filtrar solo para el usuario logueado
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        } else {
            // Filtro de búsqueda por DNI o Nombre del padre para el admin
            if ($search = $request->input('search')) {
                $query->whereHas('parent', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('dni', 'like', "%{$search}%");
                });
            }
        }

        $fines = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Apafa/Fines/Index', [
            'fines'   => $fines,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Marcar una multa como pagada (exclusivo para Admin).
     */
    public function markAsPaid(ApafaFine $fine)
    {
        $fine->update(['status' => 'paid']);

        return redirect()->back()->with('message', 'Multa registrada como pagada.');
    }
}
