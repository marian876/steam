
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;
SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '8f004824-4576-11f1-a05f-28a9d53174b4:1-5334';
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_13_180938_agregar_rol_a_users',1),(5,'2026_09_13_180939_crear_tabla_prompts',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
DROP TABLE IF EXISTS `prompts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prompts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `texto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int unsigned NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `prompts` DISABLE KEYS */;
INSERT INTO `prompts` VALUES (1,'Revisar mi documento contra los 15 puntos de evaluación','Subís este prompt junto con tu archivo de Word a una IA (ChatGPT, Claude, Gemini...) y te devuelve, punto por punto, si cumplís cada uno de los 15 criterios de evaluación y qué modificar exactamente para corregir lo que falte.','Actuás como corrector del Formato Steam del colegio (basado en APA 7.ª edición). Te voy a adjuntar el archivo de Word de mi proyecto.\n\nMuy importante: revisá la CONFIGURACIÓN REAL del documento, no solo su apariencia. En Word se puede lograr que un texto SE VEA como un título en negrita y centrado sin tener aplicado el estilo Título 1 — eso NO cumple el formato, aunque a simple vista parezca correcto. Para cada elemento, fijate qué estilo tiene aplicado el párrafo (Normal, Título 1, Título 2, Título 3, Sin espaciado, Descripción, etc.) y no solo cómo se ve. Excepción: la carátula y el encabezado \"Índice\" de la tabla de contenido usan formato directo a propósito, no estilos de título — así no aparecen como entradas en la tabla de contenido. No marques eso como incumplimiento.\n\nEstas son las especificaciones exactas que tiene que cumplir el documento:\n\n1. LA PÁGINA\n- Tamaño de papel: A4 vertical (21 x 29,7 cm). Horizontal solo en las páginas con tablas anchas.\n- Márgenes: 2,54 cm en los cuatro lados, en todas las páginas.\n\n2. LOS ESTILOS\nFuente base: Times New Roman 12 pt (estándar institucional). También se admite, como alternativa, una sola de estas en todo el documento: Georgia 11 pt, Calibri 11 pt, Arial 11 pt o Lucida Sans Unicode 10 pt — nunca mezclar fuentes distintas entre secciones. Los tamaños que siguen abajo están dados para Times New Roman; si el proyecto usa una fuente alternativa, evaluá que se mantenga la misma proporción (títulos más grandes o destacados que el cuerpo) en vez de exigir esos puntos exactos. Interlineado general: 1,5 líneas.\n\nEstilo Normal (cuerpo del texto):\n- Times New Roman 12 pt, regular (sin negrita ni cursiva)\n- Alineación izquierda, sin justificar\n- Sangría de primera línea: 1,27 cm\n- Interlineado 1,5 líneas, 0 pto antes/después\n\nTítulo 1:\n- Times New Roman 14 pt, negrita, centrado\n- Sin sangría, interlineado 1,5, 0/0 pto\n- Salto de página anterior (cada Título 1 empieza en hoja nueva)\n- Mayúscula en cada palabra principal (excepto artículos, preposiciones y conjunciones cortas)\n- Sin punto final, sin MAYÚSCULAS SOSTENIDAS, sin numeración manual (1., 1.1., etc.)\n\nTítulo 2:\n- Times New Roman 12 pt, negrita, alineado a la izquierda\n- Sin sangría, interlineado 1,5, 0/0 pto\n- Misma capitalización que Título 1, sin punto final\n\nTítulo 3:\n- Times New Roman 12 pt, negrita y cursiva, alineado a la izquierda\n- Sin sangría, interlineado 1,5, 0/0 pto\n- Formato tipo oración (solo la primera letra en mayúscula), sin punto final\n\nTexto de las tablas:\n- Estilo Sin espaciado para encabezado y celdas: Times New Roman 12 pt (11 pt si es extensa), sin sangría, interlineado sencillo, 0/0 pto\n- Encabezado y primera columna en negrita y centrado horizontal: puede lograrse con las propiedades de primera fila/primera columna del estilo de tabla (TablaSteam), o agregando negrita y centrado como formato directo sobre Sin espaciado si el estilo no llega a aplicarlo bien — cualquiera de las dos formas es válida. Marcalo como no cumplido solo si NINGUNA de las dos está aplicada.\n- Alineación vertical de la celda: centrado\n- Alineación de la tabla en la página: izquierda (o Ajustar a la ventana si es ancha, nunca centrada)\n\nEstilo de bordes de la tabla:\n- Estilo de tabla llamado TablaSteam, basado en Tabla normal\n- Bordes horizontales simples de ½ pto (superior de la tabla, debajo del encabezado, inferior de la tabla), los tres iguales\n- Sin bordes interiores entre filas ni verticales\n\nNúmero, título y nota de tablas y figuras:\n- Rótulo (\"Tabla 1\" / \"Figura 1\"): estilo Descripción, negrita, sin sangría, interlineado 1,5, 0/0 pto\n- El rótulo de las figuras debe decir \"Figura\" (o \"Figure\" si el proyecto está en inglés) — Word no lo trae por defecto, hay que crearlo con \"Nuevo rótulo\"; el rótulo por defecto \"Ilustración\" no es válido. El de las tablas debe decir \"Tabla\" o \"Table\" según el idioma del proyecto.\n- Título descriptivo: en el MISMO párrafo que el rótulo (separado con Shift+Enter, no con Enter — si quedan en párrafos distintos, la lista de tablas/ilustraciones sale incompleta), mismo estilo Descripción pero sin negrita y con cursiva\n- Nota debajo (empieza con \"Nota.\"): mismo estilo que el título, sin sangría y en cursiva\n\n3. EL DOCUMENTO\nCarátula (página 1, sin encabezado ni número visible, con salto de sección después, con formato directo — no con estilos de título):\n- Logo del colegio, centrado\n- Título del proyecto en negrita, centrado\n- Logo de la empresa si corresponde, centrado\n- \"Miembros\" en negrita (izquierda) + lista de integrantes con sangría\n- \"Profesores\" en negrita (izquierda) + lista de docentes con sangría\n- Curso, centrado — Ciudad y país, centrado — Período o fecha al final, centrado\n\nSaltos de sección (no de página) para separar: carátula, cada capítulo de Título 1, bibliografía, apéndices y páginas horizontales.\n\nEncabezado, pie y numeración:\n- Encabezado izquierdo: logo de la empresa/proyecto, 1 cm de alto\n- Encabezado derecho: escudo del colegio, 1 cm de alto, sin texto\n- Pie de página derecho: número de página arábigo\n- Sin encabezado ni número en la carátula, visible desde la página 2\n\nTabla de contenido: página 2, en sección aparte, Título 1/2/3 en Times New Roman 12 pt e interlineado 1,5, generada como campo automático de Word (no tipeada a mano) y actualizada antes de entregar. El encabezado \"Índice\" de esta tabla también va con formato directo, no con estilo de título.\n\nTabla de tablas y tabla de ilustraciones: optativas (solo si el trabajo tiene tablas y/o figuras), van después de la tabla de contenido, generadas con Referencias → Insertar tabla de ilustraciones (campos reales, no texto tipeado a mano).\n\n4. CITAS Y REFERENCIAS\nCitas textuales: siempre entre comillas integradas al párrafo (nunca en bloque aparte, sin importar la extensión), seguidas de (Apellido, Año), con el punto después del paréntesis.\n\nCita de contenido generado por IA: si la IA generó el contenido usado, se cita (Compañía, Año) en el texto. En la bibliografía puede figurar de dos formas válidas según el caso: como chat específico (Compañía. (Fecha). Título del chat [Generative AI chat]. Herramienta. URL del chat) cuando se cita una conversación puntual y compartible, o como herramienta en general (Compañía. (Año). Nombre de la herramienta [Large language model]. URL de la herramienta) cuando la IA solo ayudó a redactar, traducir o corregir el propio texto. Los corchetes [Generative AI chat] y [Large language model] van literalmente en inglés, tal como los especifica APA — no los marques como error ni pidas traducirlos aunque el resto de la referencia esté en español.\n\nBibliografía:\n- Página nueva, después del cuerpo del trabajo, encabezado \"Bibliografía\" en Título 1\n- Orden alfabético por apellido de autor\n- Times New Roman 12 pt, interlineado 1,5\n- Sangría francesa de 1,27 cm, 0 pto antes/después entre entradas\n- Generada preferentemente con el gestor de citas de Word (estilo APA 7th edition instalado)\n- Si cada referencia individual está bien en formato APA 7 (sangría francesa, fuente, etc.) pero el orden alfabético general no es exacto, puede deberse a un problema conocido de Word para Mac al generar la bibliografía — señalalo como posible causa (sugerí revisar Administrar fuentes, actualizar el campo, o generarla desde otra computadora) en vez de marcarlo solo como un error del alumno.\n\n5. APÉNDICES\n- Después de la bibliografía, cada uno en página nueva\n- Uno solo: \"Apéndice\", sin letra. Varios: \"Apéndice A\", \"Apéndice B\"...\n- Título del apéndice debajo, en negrita y centrado\n- Mencionado en el cuerpo del texto\n\nCon estas especificaciones, evaluá los siguientes 15 criterios uno por uno, indicando ✔ Cumple, ✘ No cumple o ⚠ Cumple parcialmente. Basate en lo que está REALMENTE configurado en el archivo (estilos aplicados, propiedades de página, campos de Word) y no en cómo se ve a simple vista ni en suposiciones. Cuando un punto no se cumpla del todo, explicá con precisión qué modificar y cómo hacerlo en Word (el menú, botón o atajo concreto):\n\n1. Configura correctamente el tamaño y la orientación de las páginas.\n2. Configura correctamente los márgenes del documento.\n3. Configura correctamente los estilos para textos y títulos.\n4. Configura correctamente los estilos para textos y títulos de tablas.\n5. Todo el texto del documento tiene asignado el estilo correspondiente (no formato manual/directo, salvo la carátula y el encabezado \"Índice\", que van con formato directo a propósito).\n6. Presenta correctamente la portada según los elementos y distribución establecidos.\n7. Configura correctamente el encabezado de página.\n8. Configura correctamente la numeración de las páginas.\n9. Genera correctamente la tabla de contenido a partir de los estilos de títulos.\n10. Genera los índices de tablas e ilustraciones.\n11. Presenta las tablas con numeración, título, formato y estructura correctamente configurados.\n12. Presenta las figuras e ilustraciones con numeración, título y formato correctamente configurados.\n13. Incluye correctamente las notas y fuentes correspondientes a tablas e ilustraciones.\n14. Presenta correctamente las citas textuales y referencias dentro del documento.\n15. Presenta la bibliografía con el formato, orden y sangría establecidos.\n\nAl final agregá:\n- El puntaje total sobre 15.\n- Una lista, ordenada de más a menos urgente, con las correcciones que tengo que hacer antes de entregar.',1,1,'2026-09-13 18:20:43','2026-09-13 18:20:43');
/*!40000 ALTER TABLE `prompts` ENABLE KEYS */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'alumno',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

