<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'apellido', 'email', 'rol', 'password', 'activo'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROL_ADMIN = 'admin';

    public const ROL_DOCENTE = 'docente';

    public const ROL_ALUMNO = 'alumno';

    /** Roles que se pueden asignar desde "Usuarios y roles". */
    public const ROLES = [
        self::ROL_ADMIN => 'Administrador',
        self::ROL_DOCENTE => 'Docente',
        self::ROL_ALUMNO => 'Alumno',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function esDocente(): bool
    {
        // El administrador también entra a todo lo de docente.
        return $this->rol === self::ROL_DOCENTE || $this->rol === self::ROL_ADMIN;
    }

    public function esAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    public function esAlumno(): bool
    {
        return $this->rol === self::ROL_ALUMNO;
    }

    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(Grupo::class)->withTimestamps();
    }

    /** Nombre y apellido juntos. */
    public function nombreCompleto(): string
    {
        return trim($this->name.' '.$this->apellido);
    }

    /**
     * Revisa que un alumno no quede con dos grupos del mismo año.
     * Devuelve el mensaje de error, o null si está todo bien.
     */
    public static function errorGruposAlumno(array $grupoIds): ?string
    {
        $anios = Grupo::whereIn('id', $grupoIds)->pluck('anio');

        if ($anios->count() !== $anios->unique()->count()) {
            return 'Un alumno solo puede tener un grupo por año.';
        }

        return null;
    }

    /** Nombre legible del rol (Administrador / Docente / Alumno). */
    public function etiquetaRol(): string
    {
        return self::ROLES[$this->rol] ?? ucfirst((string) $this->rol);
    }

    /**
     * El rol sale del dominio del correo institucional.
     * Devuelve null si el dominio no pertenece al colegio.
     */
    public static function rolSegunCorreo(string $email): ?string
    {
        $dominio = strtolower(substr(strrchr($email, '@') ?: '', 1));

        return match ($dominio) {
            'sil.edu.py' => self::ROL_DOCENTE,
            'alumnos.sil.edu.py' => self::ROL_ALUMNO,
            default => null,
        };
    }
}
