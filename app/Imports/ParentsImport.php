<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParentsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection): void
    {
        foreach ($collection as $row) {
            if (empty($row['dni_padre']) || empty($row['nombre_padre'])) {
                continue;
            }

            // 1. Crear o buscar al Padre / Apoderado
            $parent = User::firstOrCreate(
                ['dni' => trim($row['dni_padre'])],
                [
                    'name'     => trim($row['nombre_padre']),
                    'email'    => trim($row['email_padre'] ?? $row['dni_padre'] . '@apafa.com'),
                    'password' => Hash::make(trim($row['dni_padre'])),
                ]
            );

            // 2. Crear o buscar al Estudiante
            if (!empty($row['dni_alumno']) && !empty($row['nombre_alumno'])) {
                $student = Student::firstOrCreate(
                    ['dni' => trim($row['dni_alumno'])],
                    [
                        'name'    => trim($row['nombre_alumno']),
                        'grade'   => trim($row['grado'] ?? '1ro'),
                        'section' => trim($row['seccion'] ?? 'A'),
                        'level'   => trim($row['nivel'] ?? 'Secundaria'),
                    ]
                );

                // 3. Vincular Padre con Alumno en la tabla pivote
                $parent->students()->syncWithoutDetaching([$student->id]);
            }
        }
    }
}