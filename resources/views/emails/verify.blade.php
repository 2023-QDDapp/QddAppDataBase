<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de correo electrónico</title>
</head>
<body>
    <div style="text-align: center; padding: 20px;">
        <h1 style="font-size: 24px;">Verificación de correo electrónico</h1>
        <img src="{{ $message->embed(public_path('img/logoM.png')) }}" alt="Logo de la aplicación" style="width: 200px;">
        <p style="font-size: 16px;"><strong>¡Bienvenido {{$nombre}}!</strong></p>
        <p style="font-size: 16px;">Por favor, haz clic en el siguiente enlace para verificar tu dirección de correo electrónico:</p>
        <a href="{{ $verificationLink }}" style="display: inline-block; margin: 10px 0; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; font-size: 16px; border-radius: 5px;">Verificar correo electrónico</a>
        <hr style="border: none; border-top: 1px solid #ccc; margin: 20px 0;">
        <p style="font-size: 16px;">Gracias,</p>
    </div>
</body>
</html>