<?php

namespace Database\Seeders;

use App\Models\Carrera;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $sistemas = Carrera::firstOrCreate(
            ['codigo' => 'ITI'],
            ['nombre' => 'Ingeniería en Tecnologías de Información']
        );

        $software = Carrera::firstOrCreate(
            ['codigo' => 'ISW'],
            ['nombre' => 'Ingeniería de Software']
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@academica.test'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Password123!'),
                'carrera_id' => null,
            ]
        );
        $admin->syncRoles(['administrador']);

        $profesorIti = User::firstOrCreate(
            ['email' => 'profesor.iti@academica.test'],
            [
                'name' => 'Profesor ITI',
                'password' => Hash::make('Password123!'),
                'carrera_id' => $sistemas->id,
            ]
        );
        $profesorIti->syncRoles(['profesor']);

        $profesorIsw = User::firstOrCreate(
            ['email' => 'profesor.isw@academica.test'],
            [
                'name' => 'Profesor ISW',
                'password' => Hash::make('Password123!'),
                'carrera_id' => $software->id,
            ]
        );
        $profesorIsw->syncRoles(['profesor']);

        $ana = User::firstOrCreate(
            ['email' => 'ana@academica.test'],
            [
                'name' => 'Ana Rojas',
                'password' => Hash::make('Password123!'),
                'carrera_id' => $sistemas->id,
            ]
        );
        $ana->syncRoles(['estudiante']);

        $luis = User::firstOrCreate(
            ['email' => 'luis@academica.test'],
            [
                'name' => 'Luis Mora',
                'password' => Hash::make('Password123!'),
                'carrera_id' => $software->id,
            ]
        );
        $luis->syncRoles(['estudiante']);

        // Actividad 3: usuario coordinador
        $coordinador = User::firstOrCreate(
            ['email' => 'coordinador@academica.test'],
            [
                'name' => 'Coordinador Académico',
                'password' => Hash::make('Password123!'),
                'carrera_id' => $sistemas->id,
            ]
        );
        $coordinador->syncRoles(['coordinador']);

        // Registros de estudiante
        Estudiante::firstOrCreate(
            ['user_id' => $ana->id],
            [
                'carrera_id' => $sistemas->id,
                'cedula' => '1-1111-1111',
                'nombre' => 'Ana',
                'apellido' => 'Rojas',
                'correo_institucional' => 'ana@academica.test',
                'activo' => true,
            ]
        );

        Estudiante::firstOrCreate(
            ['user_id' => $luis->id],
            [
                'carrera_id' => $software->id,
                'cedula' => '2-2222-2222',
                'nombre' => 'Luis',
                'apellido' => 'Mora',
                'correo_institucional' => 'luis@academica.test',
                'activo' => true,
            ]
        );
    }
}
