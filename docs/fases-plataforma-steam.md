# Fases de desarrollo de la plataforma STEAM

## Objetivo del plan

Organizar el desarrollo por partes manejables, empezando por el modulo de Formato STEAM basado en APA y preparando la base para crecer hacia una plataforma completa de gestion de proyectos STEAM.

## Criterio general de avance

Cada fase debe entregar una parte usable, no solo codigo interno. Una fase se considera terminada cuando:

- Tiene interfaz funcional.
- Tiene permisos definidos.
- Guarda datos donde corresponde.
- Tiene validaciones basicas.
- Puede probarse con casos reales.
- Tiene documentado que queda pendiente.

## Fase 1: Formato STEAM basado en APA

Esta fase toma como punto de partida el archivo `formato-apa-word-sil.html`.

### Fase 1.1: Informacion y guia interactiva

Objetivo:

- Convertir la informacion actual del artefacto en una seccion web publica y mantenible.

Alcance:

- Migrar la guia actual a Laravel.
- Mantener navegacion por pasos.
- Mantener indice.
- Mantener barra de avance.
- Mantener checklist.
- Mantener seccion de APA puro.
- Separar CSS, JavaScript, imagenes y contenido.
- Cargar dependencias locales cuando sea posible.
- Corregir o decidir el sistema de modos: SIL, intermedio y completo.

Entregables:

- Ruta publica `/formato-steam-apa`.
- Vista principal de la guia.
- Assets separados.
- Navegacion funcional.
- Progreso guardado por navegador o por usuario si ya existe login.

Criterios de aceptacion:

- El contenido actual se puede recorrer completo.
- Los pasos visibles coinciden con el modo elegido.
- El checklist funciona.
- La guia se ve correctamente en escritorio y movil.
- No depende del entorno de artefactos de Claude.

Notas tecnicas:

- En esta subfase se puede mantener el procesamiento `.docx` en navegador si todavia no hay usuarios ni backend de entregas.
- El objetivo principal es estabilizar la experiencia actual.

### Fase 1.2: Ejemplos y archivos descargables

Objetivo:

- Agregar recursos reales para practicar el formato.

Alcance:

- Crear una seccion de recursos descargables.
- Agregar archivos Word de muestra.
- Agregar plantilla base.
- Agregar ejemplos correctos e incorrectos.
- Agregar ejercicios guiados.
- Controlar versiones de cada recurso.

Archivos sugeridos:

- `plantilla-steam-sil.dotx`
- `ejemplo-correcto-steam-sil.docx`
- `ejemplo-con-errores-steam-sil.docx`
- `practica-formato-steam-sil.docx`
- `rubrica-formato-steam-sil.pdf`
- `checklist-formato-steam-sil.pdf`

Entregables:

- Ruta `/formato-steam-apa/recursos`.
- Botones de descarga.
- Metadatos de version y fecha.
- Archivos organizados en storage o carpeta publica controlada.

Criterios de aceptacion:

- El alumno puede descargar cada archivo.
- El profesor puede identificar que archivo usar para cada ejercicio.
- Los archivos descargados abren correctamente en Word.
- Cada recurso tiene nombre, descripcion y version.

Permisos:

- Recursos publicos: visibles sin login si son guias generales.
- Recursos de curso: visibles solo a usuarios del curso.
- Recursos docentes: visibles solo a profesores, coordinadores y admin.

### Fase 1.3: Subida de trabajos y evaluacion por API

Objetivo:

- Permitir que alumnos o grupos suban un archivo Word y que una API lo evalue contra la rubrica de formato STEAM-SIL.

Alcance:

- Login basico.
- Roles iniciales: admin, profesor, alumno.
- Cursos.
- Grupos.
- Tareas.
- Entregas `.docx`.
- Rubrica de formato.
- Evaluacion automatica por API.
- Reporte para alumno.
- Panel de revision para profesor.

Flujo alumno:

1. Ingresa a su curso.
2. Entra a la tarea de formato.
3. Sube su archivo `.docx`.
4. Ve estado de evaluacion.
5. Recibe reporte con errores, avisos y recomendaciones.
6. Corrige y puede volver a entregar si la tarea lo permite.

Flujo profesor:

1. Crea o activa tarea.
2. Asigna rubrica.
3. Ve entregas por grupo o alumno.
4. Revisa reporte automatico.
5. Agrega comentarios.
6. Aprueba, pide correccion o habilita nueva entrega.

Entregables:

- Autenticacion.
- Panel simple por rol.
- Modelo de cursos y grupos.
- Modelo de tareas y entregas.
- Subida segura de `.docx`.
- Job de evaluacion.
- Reporte persistido.

Criterios de aceptacion:

- Un alumno solo puede ver y subir entregas de su curso/grupo.
- Un profesor solo puede ver cursos asignados.
- La clave de API nunca llega al navegador.
- La evaluacion se ejecuta en servidor o job.
- El resultado queda guardado.
- El usuario puede volver luego y ver el mismo reporte.

Resultado de API esperado:

```json
{
  "score": 82,
  "status": "requires_correction",
  "summary": "El documento cumple la mayor parte del formato, pero requiere ajustes.",
  "criteria": [
    {
      "key": "margins",
      "label": "Margenes",
      "score": 10,
      "max_score": 10,
      "status": "ok",
      "evidence": "Los cuatro margenes estan en 2,54 cm.",
      "feedback": "Correcto."
    }
  ],
  "errors": [],
  "warnings": [],
  "suggested_actions": []
}
```

Permisos:

- Alumno: crear entrega propia, ver reporte propio.
- Profesor: ver entregas de sus cursos, relanzar evaluacion, comentar.
- Admin: configurar rubrica y proveedor API.

### Fase 1.4: Modificacion asistida del archivo

Objetivo:

- Permitir que una API proponga o genere una version corregida del archivo para ajustarlo al formato adecuado.

Alcance:

- Versionado de entregas.
- Generacion de archivo corregido.
- Comparacion entre original, reporte y version corregida.
- Aprobacion docente.
- Descarga de version corregida.

Flujo:

1. Existe una entrega evaluada.
2. El alumno o profesor solicita correccion asistida.
3. El sistema crea un job de modificacion.
4. La API o motor de documentos genera cambios.
5. Se guarda una nueva version del archivo.
6. El sistema muestra resumen de cambios.
7. El profesor puede aprobar o comentar.
8. El alumno descarga la nueva version.

Reglas:

- Nunca se modifica el archivo original.
- Cada version debe tener autor, fecha y motivo.
- Las correcciones automaticas deben marcarse como propuestas.
- El profesor debe poder controlar la version final.

Entregables:

- Tabla `delivery_versions`.
- Boton "Generar version corregida".
- Estado de procesamiento.
- Descarga de archivo corregido.
- Resumen de cambios.
- Aprobacion o rechazo docente.

Criterios de aceptacion:

- El archivo original sigue disponible.
- La nueva version descarga correctamente.
- El sistema registra que cambios fueron automaticos.
- El profesor puede publicar o retener la version corregida.

## Fase 2: Presentaciones para defensa

Objetivo:

- Agregar modulo para preparar, subir y evaluar presentaciones de defensa.

Alcance:

- Guia de presentaciones.
- Plantillas descargables.
- Subida de `.pptx` o PDF.
- Evaluacion por rubrica.
- Revision de estructura, claridad, diseno, tiempo estimado y coherencia.
- Correcciones docentes.

Criterios posibles:

- Portada.
- Problema.
- Objetivos.
- Proceso.
- Producto o prototipo.
- Resultados.
- Impacto.
- Claridad visual.
- Cantidad de texto.
- Orden narrativo.
- Preparacion para defensa oral.

Entregables:

- Ruta `/presentaciones`.
- Recursos descargables.
- Tarea de presentacion.
- Evaluacion automatica.
- Reporte.

## Fase 3: Sitios web de los proyectos

Objetivo:

- Evaluar los sitios web creados por los alumnos para sus proyectos.

Alcance:

- Subida de URL o ZIP.
- Validacion de accesibilidad basica.
- Revision responsive.
- Revision de contenido.
- Revision de estructura.
- Revision de coherencia con el proyecto.
- Capturas automaticas si el entorno lo permite.

Criterios posibles:

- Identidad del proyecto.
- Informacion clara.
- Navegacion.
- Responsive.
- Accesibilidad.
- Velocidad.
- Credibilidad.
- Evidencias del proceso.
- Integracion con defensa.

Entregables:

- Ruta `/sitios-web`.
- Formulario de URL o archivo.
- Evaluacion por rubrica.
- Reporte tecnico y pedagogico.

## Fase 4: Publicidades para redes sociales

Objetivo:

- Evaluar piezas de comunicacion para redes vinculadas al proyecto STEAM.

Alcance:

- Subida de imagen, video o texto.
- Configuracion por plataforma: Instagram, TikTok, Facebook, LinkedIn u otra.
- Revision de mensaje, claridad, llamada a la accion, formato y coherencia visual.
- Recomendaciones de mejora.

Criterios posibles:

- Objetivo de la pieza.
- Publico destinatario.
- Claridad del mensaje.
- Calidad visual.
- Uso de marca.
- Legibilidad.
- Formato por red social.
- Persuasion.
- Etica y veracidad.

Entregables:

- Ruta `/publicidades`.
- Recursos descargables.
- Evaluacion automatica.
- Reporte.

## Fase 5: Gestion completa de proyectos

Objetivo:

- Centralizar la informacion y el avance de cada proyecto STEAM.

Alcance:

- Cursos.
- Grupos.
- Proyectos.
- Objetivos.
- Roles dentro del grupo.
- Tareas.
- Hitos.
- Cronograma.
- Estado del proyecto.
- Archivos y enlaces.
- Bitacora de aportes.

Entregables:

- Panel de proyecto.
- Vista de grupo.
- Vista de tareas.
- Calendario.
- Registro de aportes.

## Fase 6: Correcciones, devoluciones y seguimiento docente

Objetivo:

- Dar al profesor un flujo completo para corregir, comentar y acompanar el proceso.

Alcance:

- Comentarios generales.
- Comentarios por criterio.
- Solicitud de reentrega.
- Historial de correcciones.
- Estados de seguimiento.
- Alertas de atraso.
- Vista comparativa entre entregas.

Entregables:

- Bandeja docente.
- Historial por grupo.
- Historial por alumno.
- Reportes de avance.

## Fase 7: Evaluacion del jurado

Objetivo:

- Permitir que jurados evaluen proyectos asignados con permisos limitados.

Alcance:

- Rol jurado.
- Asignacion de proyectos.
- Rubrica de jurado.
- Vista de defensa.
- Formulario de evaluacion.
- Comentarios.
- Cierre de evaluacion.

Entregables:

- Panel de jurado.
- Rubrica final.
- Reporte por proyecto.
- Exportacion de resultados.

## Fase 8: Chat, avisos y notificaciones

Objetivo:

- Agregar comunicacion interna con privilegios por rol.

Alcance:

- Chat profesor-profesor.
- Chat profesor-grupo.
- Chat profesor-alumno.
- Avisos por curso.
- Notificaciones de tareas, entregas y correcciones.
- Moderacion basica.

Entregables:

- Centro de mensajes.
- Canales por curso y grupo.
- Notificaciones en la plataforma.

## Fase 9: Paneles, reportes y administracion avanzada

Objetivo:

- Dar a coordinadores y administradores una vision completa del programa STEAM.

Alcance:

- Dashboard por periodo.
- Dashboard por curso.
- Dashboard por profesor.
- Dashboard por proyecto.
- Indicadores de entregas.
- Indicadores de participacion.
- Resultados de rubricas.
- Exportaciones.
- Configuracion general.

Entregables:

- Panel administrativo.
- Reportes PDF.
- Exportaciones Excel/CSV.
- Gestion avanzada de usuarios y permisos.

## Orden recomendado de implementacion

1. Crear proyecto Laravel base.
2. Migrar el HTML actual como Fase 1.1.
3. Separar y limpiar assets.
4. Agregar descargas de la Fase 1.2.
5. Agregar autenticacion y roles minimos.
6. Agregar cursos, grupos y tareas.
7. Implementar entregas Word.
8. Implementar evaluacion por API.
9. Implementar reportes.
10. Implementar version corregida.
11. Repetir patron para presentaciones.
12. Repetir patron para sitios web.
13. Repetir patron para publicidades.
14. Agregar gestion completa de proyectos.
15. Agregar jurado.
16. Agregar chat y notificaciones.
17. Consolidar dashboards y reportes.

## Riesgos principales

- Intentar construir todos los modulos al mismo tiempo.
- No definir rubricas claras antes de usar API.
- Permitir que la API modifique archivos sin versionado.
- Guardar archivos de alumnos en ubicaciones publicas.
- Mezclar permisos de profesor, alumno y jurado.
- No registrar historial de evaluaciones y correcciones.
- Depender de prompts no versionados.
- No probar con documentos reales de Word.

## Primera meta concreta

La primera meta operativa debe ser:

- Tener `/formato-steam-apa` funcionando como sitio web dentro de Laravel.
- Mantener la experiencia actual.
- Agregar una seccion de recursos descargables.
- Dejar preparada la estructura para que una tarea pueda pedir subida de `.docx`.

Con eso la plataforma empieza pequena, pero con direccion clara hacia el sistema completo.

