<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ProfesorDashboard extends Component
{
    public string $tab = 'perfil';

    public function setTab($tab)
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.profesor-dashboard', [
            'userId' => Auth::id(),
        ]);
    }
}
