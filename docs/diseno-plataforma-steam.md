# Diseno completo de la plataforma STEAM

## Proposito

Construir una plataforma web para gestionar de manera integral los proyectos STEAM de distintos cursos: informacion, guias, tareas, cronogramas, entregas, evaluaciones, correcciones, aportes individuales, comunicacion y evaluacion del jurado.

La primera version parte del artefacto `formato-apa-word-sil.html`, que hoy funciona como guia interactiva y revisor local de documentos Word. Ese modulo inicial debe evolucionar hacia una plataforma con roles, grupos, entregas y evaluacion mediante API.

## Vision del sistema

La plataforma debe permitir que cada participante vea y haga solo lo que corresponde a su rol:

- Los alumnos consultan guias, descargan ejemplos, suben entregas, reciben devoluciones, corrigen archivos y registran aportes.
- Los profesores administran cursos, grupos, tareas, rubricas, entregas, correcciones, mensajes y reportes.
- Los coordinadores supervisan varios cursos, docentes, cronogramas y resultados generales.
- El jurado evalua proyectos asignados con formularios y rubricas especificas.
- Los administradores gestionan configuracion, usuarios, permisos, periodos, plantillas y modulos.

## Principios de diseno

- Modularidad: cada area debe poder crecer sin romper las demas.
- Trazabilidad: toda entrega, correccion, evaluacion y modificacion debe quedar registrada.
- Privacidad: los archivos de alumnos deben tratarse como informacion privada.
- Control docente: la IA propone evaluaciones y correcciones, pero el profesor puede revisar, ajustar y aprobar.
- Versionado: ningun archivo original se sobreescribe; toda correccion genera una nueva version.
- Rubricas explicitas: la API debe evaluar contra criterios definidos, no con prompts improvisados.
- Escalabilidad gradual: empezar con Word/APA y agregar presentaciones, sitios web, publicidades y gestion completa de proyectos.

## Stack propuesto

Backend:

- Laravel como framework principal.
- PHP 8.3 o superior.
- Base de datos relacional: PostgreSQL recomendado; MySQL tambien viable.
- Jobs/colas para evaluaciones pesadas con API.
- Storage local en desarrollo y S3-compatible en produccion.
- Sistema de permisos basado en roles y politicas.

Frontend:

- Laravel Blade + Livewire o Alpine para la primera etapa.
- Componentes reutilizables para paneles, rubricas, entregas y reportes.
- Separacion clara de CSS, JS, imagenes y contenido.

IA/API:

- Servicio interno `AiEvaluationService` con adaptadores por proveedor.
- Prompts y rubricas versionadas en base de datos o archivos controlados.
- Respuestas estructuradas en JSON para poder guardar puntajes, observaciones y acciones sugeridas.
- Jobs en segundo plano para evitar esperas largas en la interfaz.

Procesamiento de archivos:

- `.docx`: lectura de XML interno, extraccion de texto, estilos, margenes, tablas, encabezados, pies y campos.
- `.pptx`: lectura de diapositivas, texto, notas, imagenes y estructura.
- Sitios web: analisis por URL o por paquete subido.
- Publicidades: analisis de imagen/video/texto segun plataforma y rubrica.

## Modulos principales

### 1. Identidad, roles y permisos

Roles iniciales:

- `admin`: acceso total.
- `coordinador`: gestion institucional y supervision de cursos.
- `profesor`: gestion de cursos, grupos, tareas, entregas y correcciones.
- `alumno`: acceso a sus cursos, grupos, tareas y entregas.
- `jurado`: evaluacion de proyectos asignados.

Permisos clave:

- Crear cursos.
- Crear grupos.
- Crear tareas.
- Subir entregas.
- Ver entregas.
- Evaluar con API.
- Ver resultados de IA.
- Corregir manualmente.
- Aprobar correcciones automaticas.
- Descargar archivos corregidos.
- Evaluar como jurado.
- Usar chat por curso, grupo o equipo docente.

### 2. Estructura academica

Entidades:

- Periodo lectivo.
- Curso.
- Seccion o turno.
- Profesor asignado.
- Alumno matriculado.
- Grupo de proyecto.
- Proyecto STEAM.

Cada proyecto pertenece a un curso y puede tener:

- Nombre.
- Problema o desafio.
- Objetivos.
- Integrantes.
- Roles internos.
- Cronograma.
- Entregas.
- Evidencias.
- Evaluaciones.
- Estado general.

### 3. Gestion de proyectos STEAM

El modulo de proyecto debe centralizar:

- Informacion del proyecto.
- Integrantes y roles.
- Tareas.
- Hitos.
- Cronograma.
- Archivos.
- Enlaces.
- Bitacora de trabajo.
- Comentarios del profesor.
- Evaluaciones parciales y finales.
- Estado de defensa.
- Evaluacion de jurado.

Estados sugeridos:

- `borrador`
- `en_proceso`
- `entrega_parcial`
- `en_revision`
- `requiere_correccion`
- `aprobado`
- `defensa_programada`
- `evaluado_por_jurado`
- `cerrado`

### 4. Guias y recursos didacticos

El sistema debe incluir guias interactivas para preparar entregas:

- Formato STEAM basado en APA.
- Presentaciones para defensa.
- Sitios web del proyecto.
- Publicidades para redes sociales.
- Uso de rubricas.
- Criterios de evaluacion.

Cada guia puede tener:

- Pantallas o pasos.
- Ejercicios.
- Checklist.
- Descargas.
- Enlaces.
- Ejemplos correctos.
- Ejemplos con errores.
- Actividades asociadas.

### 5. Entregas

Una entrega representa el material que un alumno o grupo presenta para una tarea.

Tipos iniciales:

- Documento Word (`.docx`).
- Presentacion (`.pptx` o PDF).
- Sitio web por URL.
- Sitio web por ZIP.
- Publicidad para redes: imagen, video o copy.

Cada entrega debe guardar:

- Curso.
- Grupo.
- Tarea.
- Alumno que subio.
- Archivo o URL.
- Version.
- Fecha.
- Estado.
- Resultado de evaluacion automatica.
- Correccion docente.
- Historial de cambios.

### 6. Rubricas

Las rubricas son el centro del sistema de evaluacion.

Cada rubrica debe tener:

- Nombre.
- Tipo de entrega.
- Version.
- Criterios.
- Puntajes.
- Niveles de logro.
- Reglas automaticas.
- Instrucciones para la API.
- Visibilidad para alumnos.

Ejemplos de criterios:

- Cumplimiento de formato.
- Claridad del contenido.
- Estructura.
- Uso de fuentes.
- Calidad visual.
- Coherencia con el proyecto.
- Viabilidad tecnica.
- Trabajo colaborativo.
- Preparacion para defensa.

### 7. Evaluacion automatica con API

Flujo general:

1. El alumno sube una entrega o registra una URL.
2. Laravel valida permisos, formato y tamano.
3. El archivo se guarda como version original.
4. Se crea un job de evaluacion.
5. El job extrae contenido y metadatos.
6. Se llama a la API con la rubrica correspondiente.
7. La API devuelve resultado estructurado.
8. El sistema guarda puntajes, errores, avisos y recomendaciones.
9. El alumno y el profesor ven el reporte segun permisos.
10. El profesor puede confirmar, editar o complementar la devolucion.

La respuesta ideal de la API debe incluir:

- Puntaje total.
- Puntaje por criterio.
- Errores detectados.
- Avisos.
- Fortalezas.
- Correcciones sugeridas.
- Evidencia usada para cada observacion.
- Nivel de confianza.
- Recomendacion de accion.

### 8. Modificacion asistida de archivos

La API no debe modificar el archivo original directamente.

Flujo propuesto:

1. El alumno o profesor solicita correccion asistida.
2. El sistema toma la version original o la version seleccionada.
3. La API propone cambios.
4. El sistema genera una nueva version corregida.
5. Se guarda el archivo corregido.
6. Se registra que cambios se hicieron.
7. El profesor puede aprobar o rechazar la version.
8. El alumno descarga el archivo corregido o ve instrucciones.

Tipos de salida posibles:

- Archivo corregido `.docx`.
- Archivo comentado.
- Reporte de cambios.
- Lista de acciones manuales.

Regla central:

- Toda correccion automatica debe ser reversible y auditable.

### 9. Correcciones docentes

Los profesores deben poder:

- Ver entregas por curso, grupo, tarea y estado.
- Leer reporte automatico.
- Agregar comentarios generales.
- Agregar comentarios por criterio.
- Cambiar puntajes.
- Pedir nueva entrega.
- Aprobar entrega.
- Aprobar o rechazar correccion automatica.
- Publicar devolucion al alumno.

### 10. Bitacora de aportes

Cada grupo debe poder registrar que hizo cada integrante.

Datos sugeridos:

- Alumno.
- Fecha.
- Tarea o hito relacionado.
- Descripcion del aporte.
- Evidencia opcional.
- Validacion del grupo.
- Revision del profesor.

Esto permite detectar participacion real y apoyar evaluaciones individuales dentro de un proyecto grupal.

### 11. Cronograma y tareas

El sistema debe permitir:

- Crear tareas por curso.
- Asociar tareas a proyectos o grupos.
- Definir fechas de inicio y entrega.
- Definir entregables.
- Asociar rubricas.
- Marcar avances.
- Notificar vencimientos.
- Ver calendario por alumno, grupo, profesor y curso.

### 12. Chat y comunicaciones

Canales sugeridos:

- Chat entre profesores.
- Chat profesor-alumno.
- Chat profesor-grupo.
- Canal de avisos por curso.
- Canal interno de coordinadores.

Reglas:

- Los alumnos no deben poder escribir a cualquier usuario fuera de sus cursos o grupos.
- Los profesores deben poder moderar comunicaciones dentro de sus cursos.
- Los mensajes relevantes deben poder vincularse a tareas o entregas.
- Las notificaciones deben respetar permisos.

### 13. Evaluacion del jurado

El jurado debe tener una experiencia separada y controlada.

Funciones:

- Acceder solo a proyectos asignados.
- Ver informacion publica del proyecto.
- Ver defensa y entregables seleccionados.
- Completar rubrica de jurado.
- Agregar comentarios.
- Enviar evaluacion final.
- No ver informacion privada no necesaria.

### 14. Reportes

Reportes necesarios:

- Reporte por alumno.
- Reporte por grupo.
- Reporte por curso.
- Reporte por proyecto.
- Reporte de evaluacion automatica.
- Reporte de correcciones docentes.
- Reporte de aportes individuales.
- Reporte de jurado.
- Reporte final del proyecto STEAM.

Exportaciones:

- PDF.
- Excel/CSV.
- Descarga de archivos finales.

## Modulo inicial: Formato STEAM basado en APA

Este modulo nace del archivo HTML actual y se divide en cuatro subfases.

### Fase 1.1: Guia informativa mejorada

Objetivo:

- Convertir la guia actual en una seccion web mantenible.

Incluye:

- Pasos actuales del formato STEAM-SIL basado en APA.
- Indice.
- Progreso del alumno.
- Checklist.
- Navegacion por pantallas.
- Seccion de APA puro.
- Revisor local o preparado para revisor servidor.

Mejoras necesarias:

- Separar HTML, CSS y JS.
- Empaquetar dependencias.
- Mejorar interfaz responsive.
- Resolver modos ocultos: SIL, intermedio y completo.
- Preparar el contenido para edicion futura.

### Fase 1.2: Ejemplos y archivos descargables

Objetivo:

- Agregar archivos reales de practica.

Descargas sugeridas:

- Plantilla Word STEAM-SIL vacia.
- Documento Word correcto de ejemplo.
- Documento Word con errores frecuentes.
- Documento para practicar correcciones.
- Checklist descargable.
- Rubrica del formato.

Funciones:

- Pagina de recursos.
- Descarga con control de version.
- Registro opcional de descargas por usuario.
- Recursos visibles por curso o rol.

### Fase 1.3: Subida y evaluacion por API

Objetivo:

- Permitir que alumnos o grupos suban un documento y reciban evaluacion automatica.

Flujo:

1. Profesor crea tarea de formato.
2. Grupo o alumno sube `.docx`.
3. Sistema valida permisos y archivo.
4. Se crea entrega.
5. Se ejecuta evaluacion automatica.
6. Alumno ve reporte.
7. Profesor ve reporte y puede corregir o aprobar.

Permisos:

- Alumno: subir y ver sus resultados.
- Profesor: ver entregas de sus cursos y lanzar o relanzar evaluacion.
- Coordinador: ver resultados agregados.
- Admin: configurar rubricas y API.

### Fase 1.4: Modificacion asistida del archivo

Objetivo:

- Permitir que la API genere una version corregida o guiada del documento.

Flujo:

1. Se parte de una entrega evaluada.
2. El usuario solicita correccion asistida.
3. El sistema genera una nueva version.
4. La version corregida queda marcada como propuesta.
5. El profesor puede aprobarla, comentarla o pedir cambios.
6. El alumno descarga la version corregida.

Reglas:

- No se sobreescribe el original.
- Se registra cada cambio.
- El profesor conserva control final.
- El alumno puede comparar original, reporte y version corregida.

## Modelo de datos inicial

Tablas base:

- `users`
- `roles`
- `permissions`
- `courses`
- `course_user`
- `project_groups`
- `project_group_user`
- `steam_projects`
- `tasks`
- `deliveries`
- `delivery_versions`
- `rubrics`
- `rubric_criteria`
- `evaluations`
- `evaluation_items`
- `teacher_feedback`
- `jury_assignments`
- `jury_evaluations`
- `work_logs`
- `messages`
- `files`
- `ai_jobs`

## Permisos por rol

| Funcion | Admin | Coordinador | Profesor | Alumno | Jurado |
| --- | --- | --- | --- | --- | --- |
| Configurar sistema | Si | Parcial | No | No | No |
| Crear cursos | Si | Si | No | No | No |
| Crear tareas | Si | Si | Si | No | No |
| Ver cursos asignados | Si | Si | Si | Si | No |
| Crear grupos | Si | Si | Si | Parcial | No |
| Subir entregas | Si | Si | Si | Si | No |
| Evaluar con API | Si | Si | Si | Segun tarea | No |
| Ver reportes de grupo | Si | Si | Si | Solo propio | No |
| Corregir entregas | Si | Si | Si | No | No |
| Aprobar correccion automatica | Si | Si | Si | No | No |
| Evaluar como jurado | No | Opcional | Opcional | No | Si |
| Usar chat docente | Si | Si | Si | No | No |
| Usar chat de grupo | Si | Si | Si | Si | No |

## Seguridad y privacidad

Medidas minimas:

- Autenticacion obligatoria para entregas y reportes.
- Autorizacion por curso, grupo y rol.
- Validacion estricta de archivos.
- Limites de tamano.
- Escaneo basico de tipo MIME.
- Storage privado para archivos de alumnos.
- URLs firmadas para descargas.
- Logs de acceso a archivos.
- Claves de API solo en servidor.
- Jobs con reintentos controlados.
- Auditoria de evaluaciones y correcciones.

## Decisiones pendientes

- Base de datos: PostgreSQL o MySQL.
- Hosting: servidor PHP tradicional, VPS, Laravel Cloud u otra opcion.
- Proveedor de API para evaluacion.
- Nivel de analisis local vs analisis en servidor.
- Si el frontend sera Blade/Livewire o Inertia/Vue.
- Si el chat se implementara desde el inicio o en una fase avanzada.
- Si habra acceso de padres o tutores en una etapa posterior.

## Camino recomendado

El desarrollo debe comenzar con una base Laravel pequena pero bien organizada:

1. Montar proyecto Laravel.
2. Migrar la guia HTML como modulo inicial.
3. Separar assets y dependencias.
4. Agregar descargas.
5. Agregar usuarios, cursos, grupos y roles.
6. Agregar entregas Word.
7. Agregar evaluacion por API.
8. Agregar version corregida.
9. Repetir el mismo patron para presentaciones, sitios web y publicidades.

