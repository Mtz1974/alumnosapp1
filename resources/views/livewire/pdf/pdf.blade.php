<!-- filepath: resources/views/alumnos/pdf.blade.php -->
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Listado de Alumnos</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #eee;
        }
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
            @foreach ($alumnos as $alumno)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $alumno->nombre_completo }}</td>
                    <td>{{ $alumno->email }}</td>
                    <td>{{ $alumno->dni }}</td>
                    <td>{{ $alumno->carrera }}</td>
                    <td>{{ $alumno->comision }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
