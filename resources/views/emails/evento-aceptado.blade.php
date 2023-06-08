<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuario unido a tu evento</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            text-align: center;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .logo {
            width: 400px;
            height: 200px;
            object-fit: contain;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .border-info {
            padding: 20px;
            border: 1px solid #0C5F73;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('img/logoM.png') }}" alt="Logo de la aplicación" class="logo">

        <div class="border-info">
            <p>Has sido aceptado en el evento: <strong>{{ $data['evento']->titulo }}</strong></p>
        </div>
    </div>
</body>
</html>