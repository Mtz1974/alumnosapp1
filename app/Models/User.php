<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// app/Models/User.php
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'email',
        'password',
        'nombres',
        'apellidos',
        'dni',
        'carrera',
        'comision',
        'fecha_nacimiento',
        'link_git',
        'link_linkedin',
        'link_portfolio',
        'foto_perfil',
        'telefono',
        'rol',
    ];

    /**
     * Atributos que deben ocultarse en arrays o JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atributos con casting automático.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'nombres' => 'array',   // Se almacenan como JSON
        'apellidos' => 'array', // Se almacenan como JSON
        'fecha_nacimiento' => 'date',
        'password' => 'hashed',
    ];

    /**
     * Verifica si el usuario es alumno.
     */
    public function esAlumno()
    {
        return $this->rol === 'alumno';
    }

    /**
     * Verifica si el usuario es profesor.
     */
    public function esProfesor()
    {
        return $this->rol === 'profesor';
    }
    // estos accessors te simplifican búsqueda/visualización sin pelearte con JSON en Blade/Livewire.
    public function scopeAlumnos($q)
    {
        return $q->where('rol', 'alumno');
    }
    public function scopeProfesores($q)
    {
        return $q->where('rol', 'profesor');
    }

    public function getNombresStrAttribute()
    {
        return is_array($this->nombres) ? implode(' ', array_filter($this->nombres)) : (string) $this->nombres;
    }

    public function getApellidosStrAttribute()
    {
        return is_array($this->apellidos) ? implode(' ', array_filter($this->apellidos)) : (string) $this->apellidos;
    }

    public function getNombreCompletoAttribute()
    {
        return trim($this->nombres_str . ' ' . $this->apellidos_str);
    }

    public function getFotoUrlAttribute()
    {
        $path = $this->foto_perfil;
        if ($path && Storage::disk('public')->exists($path)) {
            // ej: storage/app/public/avatars/abc.jpg -> /storage/avatars/abc.jpg
            return asset('storage/' . $path); // uso de asset('storage/...') 
        }
        return asset('images/default-avatar.png');
    }
}
