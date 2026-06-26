<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 👈 1. AGREGAMOS ESTE IMPORT

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable; // 👈 2. AGREGAMOS EL TRAIT AQUÍ

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',     // 👈 3. ASEGÚRATE DE AGREGAR EL CAMPO DE ROL QUE USE TU BASE DE DATOS (ej: 'role', 'tipo', etc.)
        'estado',   // 👈 4. Y EL ESTADO PARA COMPROBAR SI ESTÁ ACTIVO/INACTIVO
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    public function hasAccessToDSS(): bool
    {
        return $this->role === 'admin'
            && $this->estado == 1;
    }
}

