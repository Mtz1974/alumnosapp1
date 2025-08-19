{{-- resources/views/livewire/alumno-perfil.blade.php --}}
<div class="space-y-6" wire:ignore.self>
    <h3 class="text-lg font-semibold">Mi perfil</h3>

    @if (session('message'))
        <div class="p-3 rounded bg-green-100 text-green-700">{{ session('message') }}</div>
    @endif

    {{-- Perfil del Alumno (solo lectura) --}}
    <div class="bg-white p-6 rounded shadow">
        <h4 class="font-semibold mb-3">Tu perfil</h4>
        <div class="flex items-center gap-6 mb-4">
            <img src="{{ $this->fotoUrl }}" alt="Tu foto de perfil"
                class="h-16 w-16 rounded-full object-cover ring-1 ring-gray-200" />
            <div>
                <div class="font-medium text-lg">{{ $nombresStr }} {{ $apellidosStr }}</div>
                <div class="text-sm text-gray-600">{{ $email }}</div>
                @if ($dni)
                    <div class="text-sm text-gray-500">DNI: {{ $dni }}</div>
                @endif
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4 text-sm">
            {{-- fecha de nacimiento --}}
            <div>
                <span class="font-medium">Fecha de nacimiento:</span>
                <span class="text-gray-700">
                    {{ $fecha_nacimiento }}
                </span>
            </div>
            <div>
                <span class="font-medium">Carrera:</span>
                <span class="text-gray-700"> {{ ucfirst($carrera) }}</span>
            </div>
            <div>
                <span class="font-medium">Comisión:</span>
                <span class="text-gray-700"> {{ $comision ?? 'No asignada' }}</span>
            </div>

            @if ($telefono)
                <div>
                    <span class="font-medium">WhatsApp:</span>
                    <a href="https://wa.me/+549{{ preg_replace('/\D/', '', $telefono) }}" target="_blank"
                        class="underline text-blue-600 hover:text-blue-800">
                        {{ $telefono }}
                    </a>
                </div>
            @endif

            @if ($link_git)
                <div>
                    <span class="font-medium">GitHub:</span>
                    <a href="{{ $link_git }}" target="_blank" class="underline text-blue-600 hover:text-blue-800">
                        {{ $link_git }}
                    </a>
                </div>
            @endif

            @if ($link_linkedin)
                <div>
                    <span class="font-medium">LinkedIn:</span>
                    <a href="{{ $link_linkedin }}" target="_blank" class="underline text-blue-600 hover:text-blue-800">
                        {{ $link_linkedin }}
                    </a>
                </div>
            @endif

            @if ($link_portfolio)
                <div>
                    <span class="font-medium">Portfolio:</span>
                    <a href="{{ $link_portfolio }}" target="_blank"
                        class="underline text-blue-600 hover:text-blue-800">
                        {{ $link_portfolio }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Profesores (solo lectura) - solo visible si es Alumno --}}
    @if (auth()->user()->esAlumno())
        <div class="bg-white p-6 rounded shadow">
            <h4 class="font-semibold mb-3">Profesores (solo lectura)</h4>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach ($profesores as $p)
                    <div class="border rounded p-3 flex items-center gap-3">
                        @if ($p->foto_url && $p->foto_url !== asset('images/default-avatar.png'))
                            <img src="{{ $p->foto_url }}" alt="Foto de {{ $p->nombre_completo }}"
                                class="h-12 w-12 rounded-full object-cover ring-1 ring-gray-200" />
                        @else
                            <div
                                class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-500">
                                {{ strtoupper(substr($p->nombres_str, 0, 1)) }}{{ strtoupper(substr($p->apellidos_str, 0, 1)) }}
                            </div>
                        @endif
                        {{-- Información del profesor --}}
                        <div>
                            <div class="font-medium">{{ $p->nombres_str }} {{ $p->apellidos_str }}</div>
                            <div class="text-sm text-gray-600">{{ $p->email }}</div>
                            {{-- fecha de nacimiento --}}
                            @if ($p->fecha_nacimiento)
                                <div class="text-sm text-gray-500">Fecha de nacimiento:
                                    {{ $p->fecha_nacimiento->format('d/m/Y') }}</div>
                            @endif
                            @if ($p->telefono)
                                <div class="text-sm">
                                    <a class="underline"
                                        href="https://wa.me/+549{{ preg_replace('/\D/', '', $p->telefono) }}"
                                        target="_blank">WhatsApp</a>
                                </div>
                            @endif
                            <div class="flex gap-3 mt-2 text-sm">
                                @if ($p->link_linkedin)
                                    <a class="underline" target="_blank" href="{{ $p->link_linkedin }}">LinkedIn</a>
                                @endif
                                @if ($p->link_git)
                                    <a class="underline" target="_blank" href="{{ $p->link_git }}">GitHub</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Botón para activar la edición -->
    <button wire:click="toggleEditMode"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm font-medium">
        {{ $editMode ? 'Ocultar edición' : 'Editar mi perfil' }}
    </button>

    <!-- Secciones de edición (solo visibles si editMode está activado) -->
    @if ($editMode)
        {{-- AVATAR + CAMBIO DE FOTO --}}
        <div class="bg-white p-6 rounded shadow flex items-center gap-6">
            <img src="{{ $this->fotoUrl }}" alt="Foto de perfil"
                class="h-24 w-24 rounded-full object-cover ring-1 ring-gray-200" />

            <div class="flex-1">
                <x-input-label value="Cambiar foto de perfil" />
                <input type="file" accept="image/*" wire:model="nuevaFoto" class="mt-1 block">
                <x-input-error :messages="$errors->get('nuevaFoto')" />
                <div wire:loading wire:target="nuevaFoto" class="text-sm text-gray-500 mt-1">Subiendo imagen...</div>
                <p class="text-xs text-gray-500 mt-1">Formatos: JPG/PNG/WEBP. Máx 2MB.</p>
            </div>
        </div>

        {{-- RESTO DEL FORMULARIO --}}
        <div class="bg-white p-6 rounded shadow grid md:grid-cols-2 gap-4">
            <div>
                <x-input-label value="Nombres" />
                <x-text-input wire:model="nombresStr" class="w-full" />
                <x-input-error :messages="$errors->get('nombresStr')" />
            </div>
            <div>
                <x-input-label value="Apellidos" />
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
                <x-input-label value="Fecha de nacimiento (opcional)" />
                <x-text-input type="date" wire:model="fecha_nacimiento" class="w-full" />
                <x-input-error :messages="$errors->get('fecha_nacimiento')" />
            </div> <!-- ✅ Cierre del div que faltaba -->
            <div>
                <x-input-label value="Carrera" />
                <x-text-input wire:model="carrera" class="w-full bg-gray-100 text-gray-500 cursor-not-allowed"
                    disabled />
            </div>
            <div>
                <x-input-label value="Comisión" />
                <x-text-input wire:model="comision" class="w-full" />
            </div>
            <div>
                <x-input-label value="Teléfono (WhatsApp) sin el 0 ej: 3704xxxxxx" />
                <x-text-input wire:model="telefono" class="w-full" />
            </div>
            <div>
                <x-input-label value="GitHub" />
                <x-text-input wire:model="link_git" class="w-full" />
            </div>
            <div>
                <x-input-label value="LinkedIn" />
                <x-text-input wire:model="link_linkedin" class="w-full" />
            </div>
            <div>
                <x-input-label value="Portfolio" />
                <x-text-input wire:model="link_portfolio" class="w-full" />
            </div>
            {{-- CAMBIO DE CONTRASEÑA --}}
            <div class="bg-white p-6 rounded shadow grid md:grid-cols-2 gap-4 mt-6">
                <div>
                    <x-input-label value="Nueva contraseña" />
                    <x-text-input type="password" wire:model="newPassword" class="w-full" />
                    <x-input-error :messages="$errors->get('newPassword')" />
                </div>
                <div>
                    <x-input-label value="Confirmar nueva contraseña" />
                    <x-text-input type="password" wire:model="newPasswordConfirmation" class="w-full" />
                    <x-input-error :messages="$errors->get('newPasswordConfirmation')" />
                </div>

                <div class="md:col-span-2">
                    <x-primary-button wire:click="updatePassword">Actualizar contraseña</x-primary-button>
                </div>
            </div>


            <div class="md:col-span-2">
                <x-primary-button wire:click="save">Guardar cambios</x-primary-button>
            </div>
        </div>
    @endif
</div>
