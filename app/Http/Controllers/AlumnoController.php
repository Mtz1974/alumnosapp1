<?php

namespace App\Http\Controllers;

use App\Models\AlumnosG;
use Barryvdh\DomPDF\Facade\Pdf;

class AlumnoController extends Controller
{
    public function exportarPDF()
    {
    $alumnos = AlumnosG::where('rol', 'alumno')->get();
        $pdf = Pdf::loadView('alumnos.pdf', compact('alumnos'));
        return $pdf->download('listado_alumnos.pdf');
    }
}
