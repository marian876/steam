<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\PromptsSitioWeb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ModulosTest extends TestCase
{
    use RefreshDatabase;

    public function test_cada_modulo_expone_sus_prompts_y_ediciones(): void
    {
        foreach (['contabilidad' => 1, 'marketing' => 10, 'business-administration' => 12, 'economics' => 12] as $pagina => $total) {
            $this->assertCount($total, PromptsSitioWeb::de($pagina)->originales());
        }
        $docente = User::factory()->create(['rol' => User::ROL_DOCENTE]);
        $guia = PromptsSitioWeb::de('marketing');
        $clave = array_key_first($guia->originales());
        $this->actingAs($docente)->put('/panel/marketing/prompts/'.$clave, ['titulo' => 'Título editado', 'descripcion' => 'Descripción editada', 'texto' => 'Prompt editado por docente'])->assertRedirect('/panel/modulos/marketing');
        $this->get('/marketing')->assertOk()->assertSee('Prompt editado por docente')->assertSee('Título editado');
        $this->get('/economics')->assertDontSee('Prompt editado por docente');
    }

    public function test_archivo_visible_solo_en_modulo_elegido_y_oculto_no_descarga(): void
    {
        Storage::fake('local');
        $docente = User::factory()->create(['rol' => User::ROL_DOCENTE]);
        $alumno = User::factory()->create(['rol' => User::ROL_ALUMNO]);
        $datos = ['titulo' => 'Plantilla nueva', 'archivo' => UploadedFile::fake()->create('modelo.pdf', 10, 'application/pdf')];
        $this->actingAs($alumno)->post('/panel/modulos/marketing/archivos', $datos)->assertForbidden();
        $this->actingAs($docente)->post('/panel/modulos/marketing/archivos', $datos)->assertRedirect();
        $id = DB::table('archivos_modulos')->value('id');
        $this->get('/marketing')->assertSee('Plantilla nueva');
        $this->get('/economics')->assertDontSee('Plantilla nueva');
        $this->get('/materiales/'.$id)->assertOk();
        $this->actingAs($docente)->put('/panel/modulos/marketing/archivos/'.$id)->assertRedirect();
        $this->get('/materiales/'.$id)->assertNotFound();
        $this->get('/marketing')->assertDontSee('Plantilla nueva');
    }
}
