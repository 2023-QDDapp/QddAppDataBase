<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de correo electrónico</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }

        .text-center {
            text-align: center;
        }

        .logo {
            width: 200px;
        }

        .btn-primary {
            display: inline-block;
            margin: 10px 0;
            padding: 10px 20px;
            border-radius: 5px;
        }

    </style>
</head>
<body>
    <div class="text-center">
        <h1 class="font-size-24"><strong>¡Bienvenido!</strong></h1>
        <img src="{{ $message->embed(public_path('img/logoM.png')) }}" alt="Logo de la aplicación" class="logo">
        <p class="font-size-16">Por favor, pulsa en el siguiente botón para verificar tu dirección de correo electrónico:</p>
        <a href="{{ $verificationLink }}" style="display: inline-block; margin: 10px 0; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; font-size: 16px; border-radius: 5px; border: none;">Verificar correo electrónico</a>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
