<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de correo electrónico</title>
</head>
<body>
    <h1>Verificación de correo electrónico</h1>
    <p>Hola,</p>
    <p>Por favor, haz clic en el siguiente enlace para verificar tu dirección de correo electrónico:</p>
    <p><a href="{{ $verificationLink }}">{{ $verificationLink }}</a></p>
    <p>Gracias,</p>
    <p>Tu aplicación</p>
</body>
</html>