{{--
    Vista: página amigable de error de base de datos.
    Se muestra cuando el manejador global captura una QueryException
    (ver bootstrap/app.php). No usa la sesión ni la BD para renderizar.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Servicio no disponible</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f0f2f5;
            color: #212529;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .card {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 480px;
            text-align: center;
        }
        h1 {
            font-size: 22px;
            margin-top: 0;
            color: #343a40;
        }
        p {
            font-size: 15px;
            line-height: 1.5;
            color: #495057;
        }
        a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 18px;
            background: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
        a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Servicio temporalmente no disponible</h1>
        <p>
            No se pudo conectar con la base de datos. Por favor, intenta nuevamente
            dentro de unos minutos.
        </p>
        <p>Si el problema continúa, comunícate con el encargado del laboratorio.</p>
        <a href="/">Volver al inicio</a>
    </div>
</body>
</html>
