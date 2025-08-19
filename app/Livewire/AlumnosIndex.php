<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class AlumnosIndex extends Component
{
    use WithPagination;

    // Búsqueda y paginación
    public string $search = '';
    public int $perPage = 10;

    // Para crear/editar
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

    // Para mostrar detalle
    public ?int $showingDetailId = null;
    public $currentAlumno = null;

    protected function rules()
    {
        $uniqueEmail = Rule::unique('users', 'email')->ignore($this->editingId);
        $uniqueDni   = Rule::unique('users', 'dni')->ignore($this->editingId);

        return [
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
            
        ];
    }

    /**
     * Reinicia la paginación cuando cambia la búsqueda
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Renderiza la vista con los alumnos filtrados
     */
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
                    // Búsqueda en nombres y apellidos (JSON)
                    ->orWhere('nombres', 'like', $s)        // busca en el array JSON
                    ->orWhere('apellidos', 'like', $s)      // busca en el array JSON
                    ->orWhereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", [$s])
                    ->orWhereRaw("CONCAT(apellidos, ' ', nombres) LIKE ?", [$s]);
            });
        })
        ->orderBy('id', 'desc');

    return view('livewire.alumnos-index', [
        'alumnos' => $query->paginate($this->perPage),
    ]);
}

    /**
     * Prepara el formulario para crear un nuevo alumno
     */
    public function create()
    {
        $this->resetForm();
        $this->editingId = 0;
    }

    /**
     * Carga los datos del alumno para editar
     */
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
    }

    /**
     * Guarda un alumno nuevo o actualiza uno existente
     */
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
            'rol'            => 'alumno',
        ];

        if ($this->editingId === 0) {
            $payload['password'] = 'changeme123'; // Contraseña temporal
            User::create($payload);
            session()->flash('message', 'Alumno creado con éxito.');
        } else {
            $u = User::alumnos()->findOrFail($this->editingId);
            $u->update($payload);
            session()->flash('message', 'Alumno actualizado.');
        }

        $this->resetForm();
    }

    /**
     * Elimina un alumno
     */
    public function delete(int $id)
    {
        $u = User::alumnos()->findOrFail($id);
        $u->delete();
        session()->flash('message', 'Alumno eliminado.');
        $this->resetForm();
    }

    /**
     * Resetea el formulario y limpia variables
     */
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
            'currentAlumno',
        ]);
        $this->perPage = 10;
    }

    /**
     * Muestra el detalle del alumno
     */
    public function showDetail(int $id)
    {
        $this->showingDetailId = $id;
        $this->currentAlumno = User::alumnos()->findOrFail($id);
    }

    /**
     * Cierra la vista de detalle
     */
    public function closeDetail()
    {
        $this->showingDetailId = null;
        $this->currentAlumno = null;
    }
}