<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

/**
 * HU-01 / HU-02 / HU-03
 *
 * Entidad User — Capa de Seguridad y Acceso (RBAC).
 * Métodos de negocio según el Diagrama de Clases UML (Etapa B, Sección 4.1):
 * - isAdmin()
 * - hasAccessToDSS()
 * - getSales()
 * - getFullName()
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atributos asignables en masa.
     * NOTA: 'estado' se gestiona explícitamente, no se expone a mass-assignment
     * desde el registro público (ver RegisterUserRequest / AuthController).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Atributos ocultos en las respuestas JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts de atributos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    /**
     * Relación: un usuario (vendedor) registra 0..* ventas.
     * Se deja declarada para HU-07 (Registro de venta), que es quien la consume.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Alias de negocio sobre la relación sales(), tal como se define
     * en el diagrama de clases: + getSales() : List<Sale>
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * + isAdmin() : boolean
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * + hasAccessToDSS() : boolean
     * Regla de negocio central de HU-03: solo el rol 'admin' tiene
     * acceso al módulo analítico DSS / Dashboard y debe estar activo.
     */
    public function hasAccessToDSS(): bool
    {
        return $this->isAdmin() && $this->estado === true;
    }

    /**
     * + getFullName() : String
     * Por ahora 'name' almacena el nombre completo (ver tabla users),
     * se deja el método para no romper el contrato del diagrama de clases
     * si en el futuro se separan nombre/apellido.
     */
    public function getFullName(): string
    {
        return $this->name;
    }

    /**
     * Scope de conveniencia: solo usuarios activos (estado = true).
     * Usado por HU-02 (login) para impedir el acceso de cuentas inactivas.
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}