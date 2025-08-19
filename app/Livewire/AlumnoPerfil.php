<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AlumnoPerfil extends Component
{
    use WithFileUploads;

    public int $userId;

    public string $email = '';
    public ?string $dni = null;
    public string $carrera = 'programacion';
    public ?string $comision = null;
    public ?string $telefono = null;
    public ?string $link_git = null;
    public ?string $link_linkedin = null;
    public ?string $link_portfolio = null;
    public string $nombresStr = '';
    public string $apellidosStr = '';
    public ?string $fecha_nacimiento = null; // Fecha de nacimiento (opcional)

    // Foto actual guardada en BD (ruta relativa en disco 'public')
    public ?string $foto_perfil = null;

    // Archivo nuevo (temporal) para Livewire
    public $nuevaFoto = null;

    // Para controlar la visibilidad del formulario de edición
    public $editMode = false;
    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';


    public function mount(int $userId)
    {
        abort_unless(
            Auth::user() &&
                Auth::user()->id === $userId &&
                (Auth::user()->esAlumno() || Auth::user()->esProfesor()),
            403
        );

        $u = User::findOrFail($userId);
        $this->userId       = $userId;
        $this->email        = $u->email;
        $this->dni          = $u->dni;
        $this->carrera      = $u->carrera ?? 'programacion';
        $this->comision     = $u->comision;
        $this->telefono     = $u->telefono;
        $this->link_git     = $u->link_git;
        $this->link_linkedin = $u->link_linkedin;
        $this->link_portfolio = $u->link_portfolio;
        $this->nombresStr   = $u->nombres_str;
        $this->apellidosStr = $u->apellidos_str;
        $this->foto_perfil  = $u->foto_perfil;
        $this->fecha_nacimiento = $u->fecha_nacimiento?->format('Y-m-d');
    }

    protected function rules()
    {
        return [
            'email'          => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userId)],
            'dni'            => ['nullable', 'max:20', Rule::unique('users', 'dni')->ignore($this->userId)],
            'carrera'        => ['required', 'string', 'max:100'],
            'comision'       => ['nullable', 'string', 'max:50'],
            'telefono'       => ['nullable', 'string', 'max:20'],
            'link_git'       => ['nullable', 'url'],
            'link_linkedin'  => ['nullable', 'url'],
            'link_portfolio' => ['nullable', 'url'],
            'nombresStr'     => ['required', 'string', 'max:255'],
            'apellidosStr'   => ['required', 'string', 'max:255'],
            'nuevaFoto'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'fecha_nacimiento' => ['nullable', 'date_format:Y-m-d'], // Formato YYYY-MM-DD
        ];
    }

    /**
     * Propiedad computada: URL de la foto de perfil
     * Prioriza la previsualización de la nueva foto subida
     */
    public function getFotoUrlProperty()
    {
        if ($this->nuevaFoto) {
            return $this->nuevaFoto->temporaryUrl();
        }

        if ($this->foto_perfil && Storage::disk('public')->exists($this->foto_perfil)) {
            return asset('storage/' . $this->foto_perfil);
        }

        return asset('images/default-avatar.png');
    }

    /**
     * Alternar modo edición
     */
    public function toggleEditMode()
    {
        $this->editMode = !$this->editMode;
    }

    /**
     * Guardar los cambios del perfil
     */
    public function save()
    {
        $data = $this->validate();

        $u = User::findOrFail($this->userId);

        // Manejo de nueva foto
        if ($this->nuevaFoto) {
            $nuevoPath = $this->nuevaFoto->store('avatars', 'public');

            // Eliminar foto anterior si existe
            if ($u->foto_perfil && Storage::disk('public')->exists($u->foto_perfil)) {
                Storage::disk('public')->delete($u->foto_perfil);
            }

            $this->foto_perfil = $nuevoPath;
        }

        // Actualizar usuario
        $u->update([
            'email'          => $data['email'],
            'dni'            => $data['dni'] ?? null,
            'carrera'        => $data['carrera'],
            'comision'       => $data['comision'] ?? null,
            'telefono'       => $data['telefono'] ?? null,
            'link_git'       => $data['link_git'] ?? null,
            'link_linkedin'  => $data['link_linkedin'] ?? null,
            'link_portfolio' => $data['link_portfolio'] ?? null,
            'nombres'        => array_values(array_filter(explode(' ', $data['nombresStr']))),
            'apellidos'      => array_values(array_filter(explode(' ', $data['apellidosStr']))),
            'foto_perfil'    => $this->foto_perfil,
            'fecha_nacimiento' => $data['fecha_nacimiento'] ? \Carbon\Carbon::createFromFormat('Y-m-d', $data['fecha_nacimiento']) : null,
        ]);

        // Limpiar archivo temporal
        $this->reset('nuevaFoto');

        // Cerrar modo edición
        $this->editMode = false;

        // Mostrar mensaje de éxito
        session()->flash('message', 'Perfil actualizado correctamente.');
    }

    /**
     * Renderizar la vista
     */
    public function render()
    {
        $profesores = User::profesores()
            ->select('id', 'email', 'telefono', 'nombres', 'apellidos', 'link_linkedin', 'link_git', 'foto_perfil')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('livewire.alumno-perfil', compact('profesores'));
    }

    public function updatePassword()
    {
        $this->validate([
            'newPassword' => ['required', 'string', 'min:8'],
            'newPasswordConfirmation' => ['required', 'same:newPassword'],
        ], [
            'newPassword.required' => 'La nueva contraseña es obligatoria.',
            'newPassword.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'newPasswordConfirmation.same' => 'Las contraseñas no coinciden.',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => bcrypt($this->newPassword),
        ]);

        $this->reset(['newPassword', 'newPasswordConfirmation']);
        session()->flash('message', 'Contraseña actualizada correctamente.');
    }
}
