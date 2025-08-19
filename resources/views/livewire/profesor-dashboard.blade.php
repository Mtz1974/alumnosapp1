<div>
    {{-- Navbar con tabs --}}
    <div class="flex space-x-4 border-b mb-6">
        <button
            wire:click="setTab('perfil')"
            class="px-4 py-2 font-medium {{ $tab === 'perfil' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-600' }}"
        >
            Mi Perfil
        </button>

        <button
            wire:click="setTab('alumnos')"
            class="px-4 py-2 font-medium {{ $tab === 'alumnos' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-600' }}"
        >
            Alumnos
        </button>
    </div>

    {{-- Contenido dinámico --}}
    <div>
        @if($tab === 'perfil')
            @livewire('alumno-perfil', ['userId' => $userId])
        @elseif($tab === 'alumnos')
            @livewire('alumnos-index')
        @endif
    </div>
</div>
