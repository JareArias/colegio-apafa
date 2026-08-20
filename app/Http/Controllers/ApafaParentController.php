<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ApafaParentController extends Controller
{
    public function showCarnet(Request $request)
    {
        $user = $request->user();

        // Si tienes una relación 'students' o 'children' en tu modelo User, la cargamos
        // $user->load('students'); 

        return Inertia::render('Apafa/Profile/Carnet', [
            'parent' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'dni'   => $user->dni ?? 'Sin DNI registrado',
            ],
        ]);
    }
}
