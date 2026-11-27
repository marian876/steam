<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AulaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::table('grupos')->insert(['id' => 1, 'curso' => '3', 'seccion' => 'A', 'especialidad' => 'BATAN', 'numero' => 1, 'anio' => 2026, 'activo' => 1]);
        Storage::fake('local');
    }

    private function usuario(string $rol): User
    {
        return User::factory()->create(['rol' => $rol, 'email_verified_at' => now()]);
    }

    public function test_acceso_exige_asignacion_de_grupo(): void
    {
        $alumno = $this->usuario(User::ROL_ALUMNO);
        $docente = $this->usuario(User::ROL_DOCENTE);
        $tarea = DB::table('aula_tareas')->insertGetId(['grupo_id' => 1, 'docente_id' => $docente->id, 'titulo' => 'Informe', 'materia' => 'Business', 'consigna' => 'Preparar informe']);
        $this->get('/aula')->assertRedirect('/ingresar');
        DB::table('grupo_user')->insert(['grupo_id' => 1, 'user_id' => $alumno->id]);
        $this->actingAs($alumno)->get('/aula/tareas/'.$tarea)->assertNotFound();
        DB::table('aula_integrantes_autorizados')->insert(['grupo_id' => 1, 'user_id' => $alumno->id, 'autorizado_por' => $docente->id]);
        $this->actingAs($alumno)->get('/aula/tareas/'.$tarea)->assertOk();
        DB::table('grupo_user')->where('user_id', $alumno->id)->delete();
        $this->actingAs($alumno)->get('/aula/tareas/'.$tarea)->assertNotFound();
    }

    public function test_entrega_grupal_privada_y_devolucion_docente(): void
    {
        $docente = $this->usuario(User::ROL_DOCENTE);
        $alumno = $this->usuario(User::ROL_ALUMNO);
        $otro = $this->usuario(User::ROL_ALUMNO);
        DB::table('grupo_user')->insert(['grupo_id' => 1, 'user_id' => $alumno->id]);
        DB::table('aula_integrantes_autorizados')->insert(['grupo_id' => 1, 'user_id' => $alumno->id, 'autorizado_por' => $docente->id]);
        $this->actingAs($docente)->post('/aula/tareas', ['grupo_id' => 1, 'titulo' => 'Macro', 'materia' => 'Business', 'consigna' => 'Enviar', 'vence_en' => '2026-12-01T12:00'])->assertRedirect('/aula');
        $this->actingAs($alumno)->get('/aula?mes=2026-12')->assertOk()->assertSee('Calendario de entregas')->assertSee('Macro');
        $tarea = DB::table('aula_tareas')->value('id');
        $this->actingAs($alumno)->post('/aula/tareas/'.$tarea.'/entregas', ['archivo' => UploadedFile::fake()->create('trabajo.pdf', 10, 'application/pdf')])->assertRedirect();
        $entrega = DB::table('aula_entregas')->first();
        Storage::disk('local')->assertExists($entrega->archivo);
        $this->actingAs($otro)->get('/aula/entregas/'.$entrega->id.'/archivo')->assertNotFound();
        $this->actingAs($alumno)->get('/aula/entregas/'.$entrega->id.'/archivo')->assertOk();
        $this->actingAs($alumno)->post('/aula/entregas/'.$entrega->id.'/devoluciones', ['comentario' => 'No autorizado'])->assertForbidden();
        $this->actingAs($docente)->post('/aula/entregas/'.$entrega->id.'/devoluciones', ['comentario' => 'Revisen la conclusión'])->assertRedirect();
        $this->actingAs($alumno)->get('/aula/tareas/'.$tarea)->assertSee('Revisen la conclusión');
    }

    public function test_alumno_no_puede_crear_tareas_ni_autorizarse(): void
    {
        $alumno = $this->usuario(User::ROL_ALUMNO);
        $this->actingAs($alumno)->post('/aula/tareas', ['grupo_id' => 1])->assertForbidden();
        $this->actingAs($alumno)->post('/aula/autorizaciones', ['grupo_id' => 1, 'user_id' => $alumno->id])->assertForbidden();
        $this->actingAs($alumno)->post('/mis-grupos', ['grupo' => 1])->assertForbidden();
    }

    public function test_profesor_asigna_grupo_del_curso_y_rechaza_otro(): void
    {
        $admin = $this->usuario(User::ROL_DOCENTE);
        $alumno = $this->usuario(User::ROL_ALUMNO);
        $alumno->curso = '2do BATAN A';
        $alumno->save();
        $this->actingAs($admin)->post('/aula/autorizaciones', ['grupo_id' => 1, 'user_id' => $alumno->id])->assertStatus(422);
        $alumno->curso = '3ro BATAN A';
        $alumno->save();
        $this->actingAs($admin)->post('/aula/autorizaciones', ['grupo_id' => 1, 'user_id' => $alumno->id])->assertRedirect();
        $this->assertDatabaseHas('aula_integrantes_autorizados', ['user_id' => $alumno->id, 'grupo_id' => 1]);
    }

    public function test_registro_acepta_curso_y_no_otorga_rol_docente(): void
    {
        $this->post('/registro', ['name' => 'Ana', 'apellido' => 'Prueba', 'email' => 'ana@sil.edu.py', 'password' => 'secreto123', 'password_confirmation' => 'secreto123', 'curso' => '3ro BATAN C'])->assertRedirect('/aula');
        $usuario = User::where('email', 'ana@sil.edu.py')->firstOrFail();
        $this->assertSame(User::ROL_ALUMNO, $usuario->rol);
        $this->assertSame('3ro BATAN C', $usuario->curso);
        $this->assertCount(0, $usuario->grupos);
    }

    public function test_registro_rechaza_curso_o_dominio_ajeno(): void
    {
        $this->post('/registro', ['name' => 'Ana', 'apellido' => 'Prueba', 'email' => 'ana@example.com', 'password' => 'secreto123', 'password_confirmation' => 'secreto123', 'curso' => '3ro BATAN C'])->assertSessionHasErrors('email');
        $this->post('/registro', ['name' => 'Ana', 'apellido' => 'Prueba', 'email' => 'ana@alumnos.sil.edu.py', 'password' => 'secreto123', 'password_confirmation' => 'secreto123', 'curso' => '4to BATAN A'])->assertSessionHasErrors('curso');
    }

    public function test_coordinador_puede_asignar_grupos(): void
    {
        $coordinador = $this->usuario(User::ROL_COORDINADOR);
        $alumno = $this->usuario(User::ROL_ALUMNO);
        $alumno->curso = '3ro BATAN A';
        $alumno->save();
        $this->actingAs($coordinador)->post('/aula/autorizaciones', ['grupo_id' => 1, 'user_id' => $alumno->id])->assertRedirect();
        $this->assertDatabaseHas('grupo_user', ['grupo_id' => 1, 'user_id' => $alumno->id]);
    }
}
