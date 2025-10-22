<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles; // <-- Importación de Spatie

class User extends Authenticatable
{
    
    use HasFactory, Notifiable, HasRoles; 
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id', 
        'role', // Clave para la asignación de roles
        'almacen_id', // Clave para la asignación de almacén
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast. (Usando la sintaxis moderna)
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

    /**
     * Define la relación: Un usuario pertenece a un Almacén.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id', 'Id_Almacen');
    }

    /**
     * Método para verificar si el usuario es administrador (Antiguo).
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        // NOTA: Para Spatie, usa hasRole: return $this->hasRole('admin');
        return $this->role === 'admin';
    }

    /**
     * Método para verificar si el usuario es un usuario general (Antiguo).
     *
     * @return bool
     */
    public function isUser(): bool
    {
        // NOTA: Para Spatie, usa hasRole: return $this->hasRole('user');
        return $this->role === 'user';
    }
}
