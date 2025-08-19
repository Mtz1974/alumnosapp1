<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    // Campos del formulario
    public string $nombres = '';
    public string $apellidos = '';
    public string $dni = '';
    public string $carrera = 'programacion';
    public string $comision = '';
    public string $fecha_nacimiento = '';
    public string $link_git = '';
    public string $link_linkedin = '';
    public string $link_portfolio = '';
    public string $foto_perfil = '';
    public string $telefono = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'dni' => ['nullable', 'string', 'max:20', 'unique:users,dni'],
            'carrera' => ['required', 'string'],
            'comision' => ['nullable', 'string', 'max:50'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'link_git' => ['nullable', 'string', 'max:255'],
            'link_linkedin' => ['nullable', 'string', 'max:255'],
            'link_portfolio' => ['nullable', 'string', 'max:255'],
            'foto_perfil' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['nombres'] = explode(' ', $this->nombres);
        $validated['apellidos'] = explode(' ', $this->apellidos);
        $validated['rol'] = 'alumno';
        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
?>

<div>
    <form wire:submit="register">
        <h1 class="text-2xl font-bold mb-6 text-center">Registro de Alumno</h1>
        <!-- Nombres -->
        <div>
            <x-input-label for="nombres" value="Nombres" />
            <x-text-input wire:model="nombres" id="nombres" class="block mt-1 w-full" type="text" required autofocus />
            <x-input-error :messages="$errors->get('nombres')" class="mt-2" />
        </div>

        <!-- Apellidos -->
        <div class="mt-4">
            <x-input-label for="apellidos" value="Apellidos" />
            <x-text-input wire:model="apellidos" id="apellidos" class="block mt-1 w-full" type="text" required />
            <x-input-error :messages="$errors->get('apellidos')" class="mt-2" />
        </div>

        <!-- DNI -->
        <div class="mt-4">
            <x-input-label for="dni" value="DNI" />
            <x-text-input wire:model="dni" id="dni" class="block mt-1 w-full" type="text" />
            <x-input-error :messages="$errors->get('dni')" class="mt-2" />
        </div>

        <!-- Carrera -->
        <div class="mt-4">
            <x-input-label for="carrera" value="Carrera" />
            <select wire:model="carrera" id="carrera" class="block mt-1 w-full">
                <option value="programacion">Programación</option>
            </select>
            <x-input-error :messages="$errors->get('carrera')" class="mt-2" />
        </div>

        <!-- Comisión -->
        <div class="mt-4">
            <x-input-label for="comision" value="Comisión" />
            <x-text-input wire:model="comision" id="comision" class="block mt-1 w-full" type="text" />
            <x-input-error :messages="$errors->get('comision')" class="mt-2" />
        </div>

        <!-- Fecha de nacimiento -->
        <div class="mt-4">
            <x-input-label for="fecha_nacimiento" value="Fecha de Nacimiento" />
            <x-text-input wire:model="fecha_nacimiento" id="fecha_nacimiento" class="block mt-1 w-full" type="date" />
            <x-input-error :messages="$errors->get('fecha_nacimiento')" class="mt-2" />
        </div>

        <!-- Enlaces -->
        <div class="mt-4">
            <x-input-label for="link_git" value="GitHub" />
            <x-text-input wire:model="link_git" id="link_git" class="block mt-1 w-full" type="text" />
            <x-input-error :messages="$errors->get('link_git')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="link_linkedin" value="LinkedIn" />
            <x-text-input wire:model="link_linkedin" id="link_linkedin" class="block mt-1 w-full" type="text" />
            <x-input-error :messages="$errors->get('link_linkedin')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="link_portfolio" value="Portfolio" />
            <x-text-input wire:model="link_portfolio" id="link_portfolio" class="block mt-1 w-full" type="text" />
            <x-input-error :messages="$errors->get('link_portfolio')" class="mt-2" />
        </div>

     
        <!-- Teléfono -->
        <div class="mt-4">
            <x-input-label for="telefono" value="Teléfono" />
            <x-text-input wire:model="telefono" id="telefono" class="block mt-1 w-full" type="text" />
            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" value="Correo Electrónico" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar Contraseña -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar Contraseña" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Botón -->
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}" wire:navigate>
                ¿Ya tienes cuenta?
            </a>

            <x-primary-button class="ms-4">
                Registrarse
            </x-primary-button>
        </div>
    </form>
</div>
