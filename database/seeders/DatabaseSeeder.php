<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\ApafaMeeting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear usuario Administrador
        $admin = User::create([
            'name' => 'Administrador APAFA',
            'email' => 'admin@colegio.edu.pe',
            'dni' => '00000000',
            'role' => 'admin',
            'password' => Hash::make('password123'),
        ]);

        // 2. Crear un Padre de prueba
        $padre = User::create([
            'name' => 'Juan Pérez',
            'email' => 'padre@colegio.edu.pe',
            'dni' => '12345678',
            'role' => 'padre',
            'password' => Hash::make('password123'),
        ]);

        // 3. Crear un Estudiante de prueba
        $estudiante = Student::create([
            'dni' => '87654321',
            'first_name' => 'Carlos',
            'last_name' => 'Pérez',
            'grade' => '1er Grado',
            'section' => 'A',
        ]);

        // 4. Relacionar al Padre con el Estudiante
        DB::table('parent_student')->insert([
            'user_id' => $padre->id,
            'student_id' => $estudiante->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Crear una Reunión de prueba
        ApafaMeeting::create([
            'title' => 'Asamblea General Ordinaria - APAFA',
            'description' => 'Reunión presencial para coordinaciones del año académico.',
            'meeting_date' => now()->addDays(2),
            'qr_token' => 'TOKEN-APAFA-2026',
            'is_active' => true,
        ]);
    }
}
