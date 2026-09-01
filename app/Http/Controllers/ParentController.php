<?php

namespace App\Http\Controllers;

use App\Imports\ParentsImport;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $parents = User::with('students')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('dni', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Apafa/Parents/Index', [
            'parents' => $parents,
            'filters' => $request->only(['search']),
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        Excel::import(new ParentsImport, $request->file('file'));

        return redirect()->back()->with('message', 'Padrón de padres y alumnos importado con éxito.');
    }
}