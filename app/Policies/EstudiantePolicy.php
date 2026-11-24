<?php

namespace App\Policies;

use App\Models\Estudiante;
use App\Models\User;

class EstudiantePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('estudiantes.ver');
    }

    public function view(User $user, Estudiante $estudiante): bool
    {
        // El administrador puede consultar cualquier registro.
        if ($user->hasRole('administrador')) {
            return true;
        }

        // El estudiante solo puede consultar su propio registro (activo o inactivo).
        if ($user->hasRole('estudiante')) {
            return $estudiante->user_id === $user->id;
        }

        // El profesor: misma carrera, y no puede ver inactivos de otros estudiantes.
        if ($user->hasRole('profesor')) {
            if ($user->carrera_id !== $estudiante->carrera_id) {
                return false;
            }

            return $estudiante->activo === true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('estudiantes.crear');
    }

    public function update(User $user, Estudiante $estudiante): bool
    {

        return $user->can('estudiantes.editar');
    }

    public function delete(User $user, Estudiante $estudiante): bool
    {
        return $user->can('estudiantes.eliminar');
    }
}