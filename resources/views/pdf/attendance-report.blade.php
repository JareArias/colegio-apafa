<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencia APAFA</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #3b82f6; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; color: #1e3a8a; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #666; }
        .meta { margin-bottom: 15px; }
        .meta table { width: 100%; }
        .meta td { padding: 4px 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f3f4f6; color: #111827; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #888; }
        .badge { font-weight: bold; padding: 2px 5px; border-radius: 3px; font-size: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>ASOCIACIÓN DE PADRES DE FAMILIA (APAFA)</h1>
        <p>Reporte Oficial de Asistencia a Asamblea / Reunión</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td><strong>Reunión:</strong> {{ $meeting->title }}</td>
                <td><strong>Fecha de Evento:</strong> {{ $meeting->meeting_date }}</td>
            </tr>
            <tr>
                <td><strong>Total Asistentes:</strong> {{ $attendances->count() }}</td>
                <td><strong>Fecha de Reporte:</strong> {{ $date }}</td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 45%;">Padre / Apoderado</th>
                <th style="width: 20%;">DNI</th>
                <th style="width: 15%;">Hora Ingreso</th>
                <th style="width: 15%;">Método</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->user->name }}</td>
                    <td>{{ $item->user->dni }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->scanned_at)->format('H:i:s') }}</td>
                    <td>{{ strtoupper($item->registered_by) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Documento generado automáticamente por el Sistema Control APAFA.</p>
    </div>

</body>
</html>