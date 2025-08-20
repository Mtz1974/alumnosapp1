<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Alumnos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Listado de Alumnos</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>DNI</th>
                <th>Carrera</th>
                <th>Comisión</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alumnos as $alumno)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    @php
                        // Si los campos son JSON, decodificarlos y unirlos
                        $nombres = $alumno->nombres ?? null;
                        $apellidos = $alumno->apellidos ?? null;
                        if (is_string($nombres)) $nombres = json_decode($nombres, true);
                        if (is_string($apellidos)) $apellidos = json_decode($apellidos, true);
                    @endphp
                    @if(!empty($nombres) && is_array($nombres))
                        {{ implode(' ', $nombres) }}
                    @elseif(!empty($alumno->nombres_str))
                        {{ $alumno->nombres_str }}
                    @elseif(!empty($alumno->nombre))
                        {{ $alumno->nombre }}
                    @elseif(!empty($alumno->name))
                        {{ $alumno->name }}
                    @endif
                    @if((!empty($nombres) && is_array($nombres)) || !empty($alumno->nombres_str) || !empty($alumno->nombre) || !empty($alumno->name))
                        &nbsp;
                    @endif
                    @if(!empty($apellidos) && is_array($apellidos))
                        {{ implode(' ', $apellidos) }}
                    @elseif(!empty($alumno->apellidos_str))
                        {{ $alumno->apellidos_str }}
                    @elseif(!empty($alumno->apellidos))
                        {{ $alumno->apellidos }}
                    @endif
                    @if(empty($nombres) && empty($alumno->nombres_str) && empty($alumno->nombre) && empty($alumno->name) && empty($apellidos) && empty($alumno->apellidos_str) && empty($alumno->apellidos))
                        -
                    @endif
                </td>
                <td>{{ $alumno->email }}</td>
                <td>{{ $alumno->dni ?? '' }}</td>
                <td>{{ $alumno->carrera ?? '' }}</td>
                <td>{{ $alumno->comision ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
