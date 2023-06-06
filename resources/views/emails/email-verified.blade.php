<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de correo electrónico</title>
    <style>
        body {
            background-image: url({{ asset('img/fondo.png') }});
            background-repeat: no-repeat;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }

        .container {
            text-align: center;
        }

        .logo {
            width: 200px;
        }

        p.message {
            font-size: 32px;
            color: #0C5F73;
        }
    </style>
</head>
<body>
    <div class="container">
        <img class="logo" src="{{ asset('img/logoM.png') }}" alt="Logo de la aplicación">
        <p class="message">{{ $message }}</p>
    </div>
</body>
</html>