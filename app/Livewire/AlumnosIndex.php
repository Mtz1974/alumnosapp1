<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AlumnosIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public int $perPage = 10;

    public ?int $editingId = null;
    public string $email = '';
    public string $dni = '';
    public string $carrera = 'programacion';
    public ?string $comision = null;
    public ?string $telefono = null;
    public ?string $link_git = null;
    public ?string $link_linkedin = null;
    public ?string $link_portfolio = null;
    public string $nombresStr = '';
    public string $apellidosStr = '';
    public ?string $fecha_nacimiento = null;

    // Foto actual y nueva
    public ?string $foto_perfil = null;
    public $nuevaFoto = null;

    // Passwords
    public $password = '';
    public $passwordConfirmation = '';
    public $newPassword = '';
    public $newPasswordConfirmation = '';

    // Detalle expandido
    public ?int $showingDetailId = null;


    protected function rules()
    {
        $uniqueEmail = Rule::unique('users', 'email')->ignore($this->editingId);
        $uniqueDni   = Rule::unique('users', 'dni')->ignore($this->editingId);

        $rules = [
            'email'          => ['required', 'email', 'max:255', $uniqueEmail],
            'dni'            => ['nullable', 'max:20', $uniqueDni],
            'carrera'        => ['required', 'string', 'max:100'],
            'comision'       => ['nullable', 'string', 'max:50'],
            'telefono'       => ['nullable', 'string', 'max:20'],
            'link_git'       => ['nullable', 'url'],
            'link_linkedin'  => ['nullable', 'url'],
            'link_portfolio' => ['nullable', 'url'],
            'nombresStr'     => ['required', 'string', 'max:255'],
            'apellidosStr'   => ['required', 'string', 'max:255'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'nuevaFoto'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        if ($this->editingId === 0) {
            $rules['password'] = ['required', 'string', Password::defaults()];
            $rules['passwordConfirmation'] = ['required', 'same:password'];
        }

        if ($this->editingId > 0 && $this->newPassword) {
            $rules['newPassword'] = ['string', Password::defaults()];
            $rules['newPasswordConfirmation'] = ['required', 'same:newPassword'];
        }

        return $rules;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::alumnos()
            ->when($this->search !== '', function ($q) {
                $s = "%{$this->search}%";
                $q->where(function ($w) use ($s) {
                    $w->where('email', 'like', $s)
                        ->orWhere('dni', 'like', $s)
                        ->orWhere('carrera', 'like', $s)
                        ->orWhere('comision', 'like', $s)
                        ->orWhere('nombres', 'like', $s)
                        ->orWhere('apellidos', 'like', $s)
                        ->orWhereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", [$s])
                        ->orWhereRaw("CONCAT(apellidos, ' ', nombres) LIKE ?", [$s]);
                });
            })
            ->orderBy('id', 'desc');

        return view('livewire.alumnos-index', [
            'alumnos' => $query->paginate($this->perPage),
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->editingId = 0;
    }

    public function edit(int $id)
    {
        $u = User::alumnos()->findOrFail($id);
        $this->editingId = $u->id;
        $this->email = $u->email ?? '';
        $this->dni = $u->dni ?? '';
        $this->carrera = $u->carrera ?? 'programacion';
        $this->comision = $u->comision;
        $this->telefono = $u->telefono;
        $this->link_git = $u->link_git;
        $this->link_linkedin = $u->link_linkedin;
        $this->link_portfolio = $u->link_portfolio;
        $this->nombresStr = $u->nombres_str;
        $this->apellidosStr = $u->apellidos_str;
        $this->fecha_nacimiento = $u->fecha_nacimiento ? $u->fecha_nacimiento->format('Y-m-d') : null;
        $this->foto_perfil = $u->foto_perfil;
    }

    public function save()
    {
        $data = $this->validate();

        $payload = [
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
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
            'rol'            => 'alumno',
        ];

        // Manejo de nueva foto
        if ($this->nuevaFoto) {
            $nuevoPath = $this->nuevaFoto->store('avatars', 'public');

            // Eliminar anterior
            if ($this->foto_perfil && Storage::disk('public')->exists($this->foto_perfil)) {
                Storage::disk('public')->delete($this->foto_perfil);
            }

            $this->foto_perfil = $nuevoPath;
            $payload['foto_perfil'] = $nuevoPath;
        }

        if ($this->editingId === 0) {
            $payload['password'] = Hash::make($this->password);
            User::create($payload);
            session()->flash('message', 'Alumno creado con éxito.');
        } else {
            $u = User::alumnos()->findOrFail($this->editingId);
            $u->update($payload);

            if ($this->newPassword) {
                $u->update([
                    'password' => Hash::make($this->newPassword),
                ]);
            }

            session()->flash('message', 'Alumno actualizado con éxito.');
        }

        $this->resetForm();
    }

    public function delete(int $id)
    {
        $u = User::alumnos()->findOrFail($id);

        // Borrar foto si existe
        if ($u->foto_perfil && Storage::disk('public')->exists($u->foto_perfil)) {
            Storage::disk('public')->delete($u->foto_perfil);
        }

        $u->delete();
        session()->flash('message', 'Alumno eliminado con éxito.');
    }

    public function showDetail(int $id)
    {
        $this->showingDetailId = $id;
    }

    public function closeDetail()
    {
        $this->showingDetailId = null;
    }

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

private function resetForm()
{
    $this->reset([
        'editingId',
        'email',
        'dni',
        'carrera',
        'comision',
        'telefono',
        'link_git',
        'link_linkedin',
        'link_portfolio',
        'nombresStr',
        'apellidosStr',
        'fecha_nacimiento',
        'foto_perfil',
        'nuevaFoto',
        'password',
        'passwordConfirmation',
        'newPassword',
        'newPasswordConfirmation',
        'showingDetailId',
    ]);

 
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

