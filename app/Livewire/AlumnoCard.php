<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AlumnoCard extends Component
{
    public $foto;
    public $nombreCompleto;

    public function mount()
    {
        $user = Auth::user();

        // Si no tiene foto en BD, usamos un avatar por defecto
        $this->foto = $user->foto_perfil 
            ? asset('storage/' . $user->foto_perfil) 
            : asset('images/default-avatar.png');

        // Como nombres y apellidos están en JSON (array), los unimos
        $nombres = is_array($user->nombres) ? implode(' ', $user->nombres) : $user->nombres;
        $apellidos = is_array($user->apellidos) ? implode(' ', $user->apellidos) : $user->apellidos;

        $this->nombreCompleto = trim($nombres . ' ' . $apellidos);
    }

    public function render()
    {
        return view('livewire.alumno-card');
    }
}
