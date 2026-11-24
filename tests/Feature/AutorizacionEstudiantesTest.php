<?php

namespace Tests\Feature;

use App\Models\Carrera;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AutorizacionEstudiantesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $ver = Permission::create([
            'name' => 'estudiantes.ver',
            'guard_name' => 'web',
        ]);

        $editar = Permission::create([
            'name' => 'estudiantes.editar',
            'guard_name' => 'web',
        ]);

        $adminRole = Role::create([
            'name' => 'administrador',
            'guard_name' => 'web',
        ]);

        $profesorRole = Role::create([
            'name' => 'profesor',
            'guard_name' => 'web',
        ]);

        $estudianteRole = Role::create([
            'name' => 'estudiante',
            'guard_name' => 'web',
        ]);

        $adminRole->givePermissionTo([$ver, $editar]);
        $profesorRole->givePermissionTo([$ver, $editar]);
        $estudianteRole->givePermissionTo([$ver]);
    }

    public function test_student_can_view_own_record(): void
    {
        $carrera = Carrera::create([
            'nombre' => 'Ingeniería de Software',
            'codigo' => 'ISW',
        ]);

        $user = User::factory()->create([
            'carrera_id' => $carrera->id,
        ]);
        $user->assignRole('estudiante');

        $estudiante = Estudiante::create([
            'user_id' => $user->id,
            'carrera_id' => $carrera->id,
            'cedula' => '1-1111-1111',
            'nombre' => 'Ana',
            'apellido' => 'Prueba',
            'correo_institucional' => 'ana@test.test',
            'activo' => true,
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/estudiantes/{$estudiante->id}")
            ->assertOk();
    }

    public function test_student_cannot_view_another_student_record(): void
    {
        $carrera = Carrera::create([
            'nombre' => 'Ingeniería de Software',
            'codigo' => 'ISW',
        ]);

        $user1 = User::factory()->create([
            'carrera_id' => $carrera->id,
        ]);
        $user1->assignRole('estudiante');

        $user2 = User::factory()->create([
            'carrera_id' => $carrera->id,
        ]);

        $estudiante = Estudiante::create([
            'user_id' => $user2->id,
            'carrera_id' => $carrera->id,
            'cedula' => '2-2222-2222',
            'nombre' => 'Luis',
            'apellido' => 'Prueba',
            'correo_institucional' => 'luis@test.test',
            'activo' => true,
        ]);

        $this->actingAs($user1, 'sanctum')
            ->getJson("/api/estudiantes/{$estudiante->id}")
            ->assertForbidden();
    }

    public function test_professor_cannot_update_student_from_another_career(): void
    {
        $carreraITI = Carrera::create([
            'nombre' => 'Tecnologías de Información',
            'codigo' => 'ITI',
        ]);

        $carreraISW = Carrera::create([
            'nombre' => 'Ingeniería de Software',
            'codigo' => 'ISW',
        ]);

        $profesor = User::factory()->create([
            'carrera_id' => $carreraITI->id,
        ]);
        $profesor->assignRole('profesor');

        $propietario = User::factory()->create([
            'carrera_id' => $carreraISW->id,
        ]);

        $estudiante = Estudiante::create([
            'user_id' => $propietario->id,
            'carrera_id' => $carreraISW->id,
            'cedula' => '3-3333-3333',
            'nombre' => 'Carlos',
            'apellido' => 'Prueba',
            'correo_institucional' => 'carlos@test.test',
            'activo' => true,
        ]);

        $this->actingAs($profesor, 'sanctum')
            ->putJson("/api/estudiantes/{$estudiante->id}", [
                'nombre' => 'Nombre modificado',
            ])
            ->assertForbidden();
    }
}
