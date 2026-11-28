<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\ConfiguracionAula;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

    public function test_plazos_visibles_por_curso_y_solo_autor_o_admin_los_edita(): void
    {
        $docente = $this->usuario(User::ROL_DOCENTE);
        $otroDocente = $this->usuario(User::ROL_DOCENTE);
        $alumno = $this->usuario(User::ROL_ALUMNO);
        $alumno->curso = '3ro BATAN A';
        $alumno->save();
        $plazo = ['curso' => '3ro BATAN A', 'materia' => 'Marketing', 'etapa' => 'Manual de marca', 'descripcion' => 'Primera versión', 'vence_en' => '2026-12-15T12:00'];
        $this->actingAs($alumno)->post('/aula/plazos', $plazo)->assertForbidden();
        $this->actingAs($docente)->post('/aula/plazos', $plazo)->assertRedirect('/panel/calendario');
        $id = DB::table('aula_plazos')->value('id');
        $this->actingAs($alumno)->get('/aula?mes=2026-12')->assertOk()->assertSee('Manual de marca');
        $alumno->curso = '3ro BATAN B';
        $alumno->save();
        $this->actingAs($alumno)->get('/aula?mes=2026-12')->assertDontSee('Manual de marca');
        $this->actingAs($otroDocente)->put('/aula/plazos/'.$id, $plazo)->assertForbidden();
        $this->actingAs($docente)->put('/aula/plazos/'.$id, array_merge($plazo, ['etapa' => 'Versión final']))->assertRedirect();
        $this->assertDatabaseHas('aula_plazos', ['id' => $id, 'etapa' => 'Versión final']);
    }

    public function test_datos_generales_separa_alumnos_y_usuarios(): void
    {
        $admin = $this->usuario(User::ROL_ADMIN);
        $this->usuario(User::ROL_ALUMNO)->update(['name' => 'AlumnoEjemplo']);
        $this->usuario(User::ROL_DOCENTE)->update(['name' => 'ProfesorEjemplo']);
        $this->actingAs($admin)->get('/panel/usuarios')->assertOk()->assertSee('ProfesorEjemplo')->assertDontSee('AlumnoEjemplo');
        $this->actingAs($admin)->get('/panel/alumnos')->assertOk()->assertSee('AlumnoEjemplo')->assertDontSee('ProfesorEjemplo');
        $this->actingAs($admin)->get('/panel/cursos')->assertOk()->assertSee('3ro BATAN C');
    }

    public function test_panel_del_grupo_muestra_entregante_y_devolucion_y_no_permitiese_doble_entrega(): void
    {
        $profesor = $this->usuario(User::ROL_DOCENTE);
        $primero = $this->usuario(User::ROL_ALUMNO);
        $segundo = $this->usuario(User::ROL_ALUMNO);
        foreach ([$primero, $segundo] as $alumno) {
            DB::table('grupo_user')->insert(['grupo_id' => 1, 'user_id' => $alumno->id]);
            DB::table('aula_integrantes_autorizados')->insert(['grupo_id' => 1, 'user_id' => $alumno->id, 'autorizado_por' => $profesor->id]);
        }
        $tarea = DB::table('aula_tareas')->insertGetId(['grupo_id' => 1, 'docente_id' => $profesor->id, 'titulo' => 'Proyecto', 'materia' => 'Marketing', 'consigna' => 'Entregar']);
        $this->actingAs($primero)->post('/aula/tareas/'.$tarea.'/entregas', ['archivo' => UploadedFile::fake()->create('proyecto.pdf', 10, 'application/pdf')])->assertRedirect();
        $entrega = DB::table('aula_entregas')->first();
        $this->actingAs($segundo)->get('/aula/tareas/'.$tarea)->assertOk()->assertSee('Trabajo entregado')->assertSee($primero->name)->assertDontSee('Enviar entrega');
        $this->actingAs($segundo)->post('/aula/tareas/'.$tarea.'/entregas', ['archivo' => UploadedFile::fake()->create('otro.pdf', 10, 'application/pdf')])->assertSessionHasErrors('archivo');
        $this->assertDatabaseCount('aula_entregas', 1);
        $this->actingAs($profesor)->post('/aula/entregas/'.$entrega->id.'/devoluciones', ['calificacion' => '8/10', 'errores' => 'Corregir el logo', 'comentario' => 'Buen progreso'])->assertRedirect();
        $this->actingAs($segundo)->get('/aula')->assertOk()->assertSee($primero->name)->assertSee('8/10')->assertSee('Corregir el logo')->assertSee('Buen progreso')->assertSee($profesor->name);
        $this->actingAs($profesor)->post('/aula/entregas/'.$entrega->id.'/devoluciones', [])->assertSessionHasErrors('comentario');
    }

    public function test_inicio_de_sesion_limita_intentos_repetidos(): void
    {
        for ($intento = 0; $intento < 10; $intento++) {
            $this->post('/ingresar', ['email' => 'prueba@sil.edu.py', 'password' => 'incorrecta'])->assertSessionHasErrors('email');
        }
        $this->post('/ingresar', ['email' => 'prueba@sil.edu.py', 'password' => 'incorrecta'])->assertTooManyRequests();
    }

    public function test_vista_previa_es_solo_lectura_y_filtra_como_el_alumno(): void
    {
        $docente = $this->usuario(User::ROL_DOCENTE);
        $alumno = $this->usuario(User::ROL_ALUMNO);
        $otro = $this->usuario(User::ROL_ALUMNO);
        $alumno->curso = '3ro BATAN A';
        $alumno->save();
        DB::table('grupo_user')->insert(['grupo_id' => 1, 'user_id' => $alumno->id]);
        DB::table('aula_integrantes_autorizados')->insert(['grupo_id' => 1, 'user_id' => $alumno->id, 'autorizado_por' => $docente->id]);
        $id = DB::table('aula_tareas')->insertGetId(['grupo_id' => 1, 'docente_id' => $docente->id, 'titulo' => 'Trabajo del grupo', 'materia' => 'Marketing', 'consigna' => 'Preparar logo']);
        DB::table('aula_tareas')->insert(['grupo_id' => 999, 'docente_id' => $docente->id, 'titulo' => 'Tarea de otro grupo', 'materia' => 'Business', 'consigna' => 'Otra']);
        $this->actingAs($docente)->get('/panel/vista-alumno/'.$alumno->id)->assertOk()->assertSee('Vista previa de')->assertSee('Trabajo del grupo')->assertDontSee('Tarea de otro grupo')->assertDontSee('Crear tarea y fecha');
        $this->actingAs($docente)->get('/panel/vista-alumno/'.$alumno->id.'/tareas/'.$id)->assertOk()->assertSee('formulario está desactivado')->assertDontSee('Guardar devolución');
        $this->actingAs($docente)->get('/panel/vista-alumno/'.$otro->id.'/tareas/'.$id)->assertNotFound();
        $this->assertAuthenticatedAs($docente);
        $this->actingAs($alumno)->get('/panel/vista-alumno')->assertForbidden();
        $this->actingAs($alumno)->get('/panel/vista-alumno/'.$otro->id)->assertForbidden();
    }

    public function test_docente_reabre_y_grupo_entrega_nueva_version_con_historial(): void
    {
        $profesor = $this->usuario(User::ROL_DOCENTE);
        $alumno = $this->usuario(User::ROL_ALUMNO);
        DB::table('grupo_user')->insert(['grupo_id' => 1, 'user_id' => $alumno->id]);
        DB::table('aula_integrantes_autorizados')->insert(['grupo_id' => 1, 'user_id' => $alumno->id, 'autorizado_por' => $profesor->id]);
        $tarea = DB::table('aula_tareas')->insertGetId(['grupo_id' => 1, 'docente_id' => $profesor->id, 'titulo' => 'Proyecto', 'materia' => 'Business', 'consigna' => 'Enviar']);
        $this->actingAs($alumno)->post('/aula/tareas/'.$tarea.'/entregas', ['archivo' => UploadedFile::fake()->create('primera.pdf', 10, 'application/pdf')])->assertRedirect();
        $this->actingAs($alumno)->post('/aula/tareas/'.$tarea.'/reabrir')->assertForbidden();
        $this->actingAs($profesor)->post('/aula/tareas/'.$tarea.'/reabrir')->assertRedirect();
        $this->actingAs($alumno)->get('/aula/tareas/'.$tarea)->assertSee('Enviar una nueva versión');
        $this->actingAs($alumno)->post('/aula/tareas/'.$tarea.'/entregas', ['archivo' => UploadedFile::fake()->create('corregida.pdf', 10, 'application/pdf')])->assertRedirect();
        $this->assertDatabaseCount('aula_entregas', 2);
        $this->assertDatabaseHas('aula_tareas', ['id' => $tarea, 'permite_nueva_entrega' => false]);
        $this->actingAs($alumno)->get('/aula/tareas/'.$tarea)->assertSee('primera.pdf')->assertSee('corregida.pdf')->assertDontSee('Enviar una nueva versión');
        $this->actingAs($alumno)->post('/aula/tareas/'.$tarea.'/entregas', ['archivo' => UploadedFile::fake()->create('tercera.pdf', 10, 'application/pdf')])->assertSessionHasErrors('archivo');
    }

    public function test_docente_crea_tarea_para_varios_grupos_sin_duplicar_destinatarios(): void
    {
        $profesor = $this->usuario(User::ROL_DOCENTE);
        DB::table('grupos')->insert(['id' => 2, 'curso' => '3', 'seccion' => 'B', 'especialidad' => 'BATAN', 'numero' => 1, 'anio' => 2026, 'activo' => 1]);
        $datos = ['grupos' => [1, 2], 'titulo' => 'Macro', 'materia' => 'Business', 'consigna' => 'Enviar'];
        $this->actingAs($profesor)->post('/aula/tareas', $datos)->assertRedirect('/aula');
        $this->assertDatabaseHas('aula_tareas', ['grupo_id' => 1, 'titulo' => 'Macro']);
        $this->assertDatabaseHas('aula_tareas', ['grupo_id' => 2, 'titulo' => 'Macro']);
        $this->actingAs($profesor)->post('/aula/tareas', array_merge($datos, ['grupos' => [1, 1]]))->assertSessionHasErrors('grupos.0');
        $this->assertDatabaseCount('aula_tareas', 2);
    }

    public function test_modalidad_solo_finales_oculta_parciales_y_las_conserva(): void
    {
        $admin = $this->usuario(User::ROL_ADMIN);
        $profesor = $this->usuario(User::ROL_DOCENTE);
        $alumno = $this->usuario(User::ROL_ALUMNO);
        DB::table('grupo_user')->insert(['grupo_id' => 1, 'user_id' => $alumno->id]);
        DB::table('aula_integrantes_autorizados')->insert(['grupo_id' => 1, 'user_id' => $alumno->id, 'autorizado_por' => $profesor->id]);
        $parcial = DB::table('aula_tareas')->insertGetId(['grupo_id' => 1, 'docente_id' => $profesor->id, 'titulo' => 'Boceto de marca', 'materia' => 'Marketing', 'consigna' => 'Enviar']);
        DB::table('aula_tareas')->insert(['grupo_id' => 1, 'docente_id' => $profesor->id, 'titulo' => 'Macro final', 'materia' => 'General', 'consigna' => 'Enviar']);
        $modalidades = array_fill_keys(User::CURSOS, 'todas');
        $modalidades['3ro BATAN A'] = 'solo_finales';
        $this->actingAs($profesor)->put('/panel/configuracion/entregas', ['modalidades' => $modalidades])->assertForbidden();
        $this->actingAs($admin)->put('/panel/configuracion/entregas', ['modalidades' => $modalidades])->assertRedirect();
        $this->actingAs($alumno)->get('/aula/trabajos')->assertOk()->assertSee('Macro final')->assertDontSee('Boceto de marca');
        $this->actingAs($alumno)->get('/aula/tareas/'.$parcial)->assertNotFound();
        $this->actingAs($profesor)->post('/aula/tareas', ['grupo_id' => 1, 'titulo' => 'Boceto', 'materia' => 'Marketing', 'consigna' => 'Enviar'])->assertSessionHasErrors('materia');
        $this->assertDatabaseHas('aula_tareas', ['id' => $parcial]);
        $modalidades['3ro BATAN A'] = 'todas';
        $this->actingAs($admin)->put('/panel/configuracion/entregas', ['modalidades' => $modalidades])->assertRedirect();
        $this->actingAs($alumno)->get('/aula/trabajos')->assertSee('Boceto de marca');
    }

    public function test_modalidad_se_aplica_por_curso_y_no_al_abanico_completo(): void
    {
        $admin = $this->usuario(User::ROL_ADMIN);
        $profesor = $this->usuario(User::ROL_DOCENTE);
        $a = $this->usuario(User::ROL_ALUMNO);
        $b = $this->usuario(User::ROL_ALUMNO);
        $a->curso = '3ro BATAN A';
        $a->save();
        $b->curso = '3ro BATAN B';
        $b->save();
        DB::table('grupos')->insert(['id' => 2, 'curso' => '3', 'seccion' => 'B', 'especialidad' => 'BATAN', 'numero' => 1, 'anio' => 2026, 'activo' => 1]);
        foreach ([[$a, 1], [$b, 2]] as [$alumno, $grupo]) {
            DB::table('grupo_user')->insert(['grupo_id' => $grupo, 'user_id' => $alumno->id]);
            DB::table('aula_integrantes_autorizados')->insert(['grupo_id' => $grupo, 'user_id' => $alumno->id, 'autorizado_por' => $profesor->id]);
            DB::table('aula_tareas')->insert(['grupo_id' => $grupo, 'docente_id' => $profesor->id, 'titulo' => 'Parcial del curso', 'materia' => 'Marketing', 'consigna' => 'Entregar']);
        }
        $modalidades = array_fill_keys(User::CURSOS, 'todas');
        $modalidades['3ro BATAN A'] = 'solo_finales';
        $this->actingAs($admin)->put('/panel/configuracion/entregas', ['modalidades' => $modalidades])->assertRedirect();
        $this->actingAs($a)->get('/aula/trabajos')->assertDontSee('Parcial del curso');
        $this->actingAs($b)->get('/aula/trabajos')->assertSee('Parcial del curso');
        $this->actingAs($profesor)->post('/aula/tareas', ['grupos' => [1, 2], 'titulo' => 'Actividad', 'materia' => 'Marketing', 'consigna' => 'Enviar'])->assertSessionHasErrors('materia');
        $this->actingAs($profesor)->post('/aula/tareas', ['grupos' => [2], 'titulo' => 'Actividad', 'materia' => 'Marketing', 'consigna' => 'Enviar'])->assertRedirect();
    }

    public function test_calendario_sin_tabla_muestra_instruccion_en_vez_de_error(): void
    {
        $docente = $this->usuario(User::ROL_DOCENTE);
        Schema::drop('aula_plazos');
        $this->actingAs($docente)->get('/panel/calendario')->assertOk()->assertSee('Falta la tabla del calendario');
        $this->actingAs($docente)->get('/aula')->assertOk()->assertSee('Falta importar el SQL de plazos');
        $this->actingAs($docente)->post('/aula/plazos', ['curso' => '3ro BATAN A', 'materia' => 'Marketing', 'etapa' => 'Logo', 'vence_en' => '2026-12-10T12:00'])->assertSessionHasErrors('calendario');
    }

    public function test_modalidad_general_anterior_se_conserva_hasta_configurar_cada_curso(): void
    {
        DB::table('aula_configuracion')->where('id', 1)->update(['entregas_parciales' => false]);
        $this->assertFalse(ConfiguracionAula::admiteParciales('3ro BATAN A'));
        DB::table('aula_configuracion_cursos')->insert(['curso' => '3ro BATAN A', 'entregas_parciales' => true]);
        $this->assertTrue(ConfiguracionAula::admiteParciales('3ro BATAN A'));
        $this->assertFalse(ConfiguracionAula::admiteParciales('3ro BATAN B'));
    }

    public function test_seccion_general_y_vista_previa_agrupan_trabajos(): void
    {
        $docente = $this->usuario(User::ROL_DOCENTE);
        $alumno = $this->usuario(User::ROL_ALUMNO);
        DB::table('grupo_user')->insert(['grupo_id' => 1, 'user_id' => $alumno->id]);
        DB::table('aula_integrantes_autorizados')->insert(['grupo_id' => 1, 'user_id' => $alumno->id, 'autorizado_por' => $docente->id]);
        DB::table('aula_tareas')->insert(['grupo_id' => 1, 'docente_id' => $docente->id, 'titulo' => 'Presentación final', 'materia' => 'General', 'consigna' => 'Enviar']);
        $this->actingAs($alumno)->get('/aula/trabajos')->assertOk()->assertSee('Entrega de trabajos')->assertSee('General')->assertSee('Presentación final');
        $this->actingAs($docente)->get('/panel/vista-alumno/'.$alumno->id.'/trabajos')->assertOk()->assertSee('Presentación final')->assertDontSee('Crear tarea y fecha de entrega');
    }
}
