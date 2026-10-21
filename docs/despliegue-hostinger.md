# Guía de despliegue en Hostinger (Laravel 13)

Proyecto: **Steam** · Plan: **Business Web Hosting (con SSH)** · PHP **8.3+** · BD **MySQL/SQLite**
Subdominio: **steam.proyectosmeg.es** · Document root anclado a **`public_html`**

> **Este proyecto usa el layout "una sola carpeta":** ya está preparado para que
> copies **todo el contenido de la carpeta `Steam`** dentro de `public_html`, edites
> el `.env` y funcione. No usa la estructura Laravel estándar (`public/` aparte).

---

## Cómo está montado (ya hecho en el repo)

- `index.php` está en la **raíz** (no en `public/`) y apunta a `./vendor` y `./bootstrap`.
- Los assets web (`build/`, `favicon.ico`, `robots.txt`, `formato-apa-word-sil.html`)
  están en la raíz.
- `.htaccess` en la raíz enruta todo a `index.php` **y bloquea con 403** el acceso web
  a `.env`, `vendor/`, `app/`, `config/`, `storage/`, etc. → el código y las
  credenciales quedan protegidos aunque estén dentro de `public_html`.
- `.env` viene en **producción** y **sin base de datos** (sesión y caché por archivo),
  así que el documento se muestra sin configurar MySQL.

---

## Pasos para subir

### 1. hPanel: PHP 8.3+
hPanel → **Avanzado → Configuración de PHP** → selecciona **8.3** o superior.

### 2. Subir los archivos
Copia **todo el contenido de la carpeta `Steam`** dentro de:
```
/home/u788392792/domains/steam.proyectosmeg.es/public_html/
```
Debe quedar así (los archivos sueltos en `public_html`, no dentro de otra subcarpeta):
```
public_html/
├── index.php   .htaccess   .env
├── build/   formato-apa-word-sil.html   favicon.ico   robots.txt
├── app/  bootstrap/  config/  database/  resources/  routes/  storage/  vendor/
└── artisan  composer.json ...
```
> **No hace falta subir `node_modules/`** (son ~82 MB y no se usan en el servidor;
> los assets ya están compilados en `build/`). Puedes omitirla o borrarla.

### 3. Editar el `.env` (en `public_html/.env`)
- `APP_URL=https://steam.proyectosmeg.es` (ya viene puesto).
- Para usar MySQL: pon `DB_CONNECTION=mysql` y **descomenta/completa** las líneas
  `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
  (Si dejas SQLite, funciona sin tocar nada.)

### 4. Permisos y caché (por SSH)
```bash
cd /home/u788392792/domains/steam.proyectosmeg.es/public_html
chmod -R 775 storage bootstrap/cache
php artisan config:cache
php artisan migrate --force     # solo si usas MySQL/BD
```

### 5. SSL
hPanel → **Seguridad → SSL** → activa el certificado gratis y fuerza HTTPS.

---

## Verificar

- `https://steam.proyectosmeg.es/` → se ve el documento "Formato APA en Word". ✅
- `https://steam.proyectosmeg.es/formato-apa-word-sil.html` → lo mismo (estático). ✅
- `https://steam.proyectosmeg.es/.env` → **403 Forbidden** (protegido). 🔒
- `https://steam.proyectosmeg.es/vendor/autoload.php` → **403 Forbidden**. 🔒

Si alguno de los dos últimos **no** da 403, avísame: significa que el `.htaccess`
no se está aplicando y hay que revisarlo antes de seguir.

---

## Al actualizar el código más adelante

Vuelve a subir los archivos cambiados y recachea:
```bash
php artisan optimize:clear
php artisan config:cache
```
Si cambiaste el frontend, recompila local (`npm run build`) y sube la carpeta `build/`.
