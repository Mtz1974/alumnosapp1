{{-- resources/views/livewire/alumnos-index.blade.php --}}
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <input 
            type="text" 
            wire:model.live="search" 
            placeholder="Buscar por email, dni, carrera, comisión" 
            class="border rounded px-3 py-2 w-1/2" 
        />
        <div class="flex items-center gap-3">
            <select wire:model="perPage" class="border rounded px-2 py-1">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <x-primary-button wire:click="create">Nuevo alumno</x-primary-button>
        </div>
    </div>

    @if (session('message'))
        <div class="p-3 rounded bg-green-100 text-green-700">{{ session('message') }}</div>
    @endif

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left p-3">Foto</th>
                    <th class="text-left p-3">Nombre</th>
                    <th class="text-left p-3">Email</th>
                    <th class="text-left p-3">DNI</th>
                    <th class="text-left p-3">Carrera</th>
                    <th class="text-left p-3">Comisión</th>
                    <th class="text-right p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($alumnos as $a)
                    <tr class="border-t">
                        <td class="p-3">
                            @if ($a->foto_perfil && $a->foto_url !== asset('images/default-avatar.png'))
                                <img 
                                    src="{{ $a->foto_url }}" 
                                    alt="Foto de {{ $a->nombre_completo }}"
                                    class="w-10 h-10 rounded-full object-cover border-2 border-gray-200"
                                >
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-xs font-medium text-gray-500">
                                    {{ substr($a->nombres_str, 0, 1) }}{{ substr($a->apellidos_str, 0, 1) }}
                                </div>
                            @endif
                        </td>
                        <td class="p-3">{{ $a->nombre_completo }}</td>
                        <td class="p-3">{{ $a->email }}</td>
                        <td class="p-3">{{ $a->dni }}</td>
                        <td class="p-3">{{ $a->carrera }}</td>
                        <td class="p-3">{{ $a->comision }}</td>
                        <td class="p-3 text-right space-y-1">
                            <x-secondary-button wire:click="edit({{ $a->id }})">Editar</x-secondary-button>
                            <x-secondary-button wire:click="showDetail({{ $a->id }})">Ver +</x-secondary-button>

                            <x-danger-button 
                                class="ml-2" 
                                x-data
                                x-on:click.prevent="if(confirm('¿Eliminar alumno?')) $wire.delete({{ $a->id }})">
                                Eliminar
                            </x-danger-button>
                        </td>
                    </tr>

                    {{-- Fila de detalle (solo visible si está seleccionado) --}}
                    @if ($showingDetailId == $a->id)
                        <tr class="bg-gray-50 border-t">
                            <td colspan="7" class="p-6">
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div class="flex items-start gap-4">
                                        <img 
                                            src="{{ $a->foto_url }}" 
                                            alt="Foto de {{ $a->nombre_completo }}"
                                            class="w-20 h-20 rounded-full object-cover ring-1 ring-gray-200"
                                        >
                                        <div>
                                            <h4 class="font-semibold text-lg">{{ $a->nombre_completo }}</h4>
                                            <div class="text-sm text-gray-600">{{ $a->email }}</div>
                                            @if ($a->dni)
                                                <div class="text-sm"><strong>DNI:</strong> {{ $a->dni }}</div>
                                            @endif
                                            @if ($a->telefono)
                                                <div class="text-sm">
                                                    <strong>WhatsApp:</strong>
                                                    <a href="https://wa.me/+549{{ preg_replace('/\D/', '', $a->telefono) }}"
                                                       target="_blank" class="underline text-blue-600">
                                                        {{ $a->telefono }}
                                                    </a>
                                                </div>
                                            @endif
                                            @if ($a->fecha_nacimiento)
                                                <div class="text-sm">
                                                    <strong>Fecha de Nacimiento:</strong>
                                                    {{ $a->fecha_nacimiento->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="space-y-3 text-sm">
                                        <div>
                                            <strong>Carrera:</strong>
                                            <span class="text-gray-700"> {{ ucfirst($a->carrera) }}</span>
                                        </div>
                                        <div>
                                            <strong>Comisión:</strong>
                                            <span class="text-gray-700"> {{ $a->comision ?? 'No asignada' }}</span>
                                        </div>
                                        @if ($a->link_git)
                                            <div>
                                                <strong>GitHub:</strong>
                                                <a href="{{ $a->link_git }}" target="_blank" class="underline text-blue-600 hover:text-blue-800">
                                                    {{ $a->link_git }}
                                                </a>
                                            </div>
                                        @endif
                                        @if ($a->link_linkedin)
                                            <div>
                                                <strong>LinkedIn:</strong>
                                                <a href="{{ $a->link_linkedin }}" target="_blank" class="underline text-blue-600 hover:text-blue-800">
                                                    {{ $a->link_linkedin }}
                                                </a>
                                            </div>
                                        @endif
                                        @if ($a->link_portfolio)
                                            <div>
                                                <strong>Portfolio:</strong>
                                                <a href="{{ $a->link_portfolio }}" target="_blank" class="underline text-blue-600 hover:text-blue-800">
                                                    {{ $a->link_portfolio }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-4 text-right">
                                    <x-secondary-button wire:click="closeDetail">Cerrar</x-secondary-button>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td class="p-3" colspan="7">Sin resultados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $alumnos->links() }}</div>

    {{-- Formulario crear/editar --}}
    @if (!is_null($editingId))
        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-semibold mb-4">
                {{ $editingId === 0 ? 'Crear alumno' : 'Editar alumno' }}
            </h3>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-input-label value="Foto del alumno" />
                    <input 
                        type="file" 
                        wire:model="foto"
                        class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100"
                        accept="image/*"
                    >
                    @error('foto') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <x-input-label value="Nombres (separados por espacio)" />
                    <x-text-input wire:model="nombresStr" class="w-full" />
                    <x-input-error :messages="$errors->get('nombresStr')" />
                </div>
                <div>
                    <x-input-label value="Apellidos (separados por espacio)" />
                    <x-text-input wire:model="apellidosStr" class="w-full" />
                    <x-input-error :messages="$errors->get('apellidosStr')" />
                </div>
                <div>
                    <x-input-label value="Email" />
                    <x-text-input type="email" wire:model="email" class="w-full" />
                    <x-input-error :messages="$errors->get('email')" />
                </div>
                <div>
                    <x-input-label value="DNI" />
                    <x-text-input wire:model="dni" class="w-full" />
                    <x-input-error :messages="$errors->get('dni')" />
                </div>
                <div>
                    <x-input-label value="Fecha de Nacimiento" />
                    <x-text-input type="date" wire:model="fecha_nacimiento" class="w-full" />
                    <x-input-error :messages="$errors->get('fecha_nacimiento')" />
                </div>
                <div>
                    <x-input-label value="Carrera" />
                    <x-text-input wire:model="carrera" class="w-full" />
                    <x-input-error :messages="$errors->get('carrera')" />
                </div>
                <div>
                    <x-input-label value="Comisión" />
                    <x-text-input wire:model="comision" class="w-full" />
                    <x-input-error :messages="$errors->get('comision')" />
                </div>
                <div>
                    <x-input-label value="Teléfono" />
                    <x-text-input wire:model="telefono" class="w-full" />
                </div>
                <div>
                    <x-input-label value="GitHub (URL)" />
                    <x-text-input wire:model="link_git" class="w-full" />
                </div>
                <div>
                    <x-input-label value="LinkedIn (URL)" />
                    <x-text-input wire:model="link_linkedin" class="w-full" />
                </div>
                <div>
                    <x-input-label value="Portfolio (URL)" />
                    <x-text-input wire:model="link_portfolio" class="w-full" />
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <x-primary-button wire:click="save">Guardar</x-primary-button>
                <x-secondary-button wire:click="$set('editingId', null)">Cancelar</x-secondary-button>
            </div>
        </div>
    @endif
</div>