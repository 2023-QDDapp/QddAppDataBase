<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Usuario unido a tu evento</title>
</head>
<body>
    

    @if ($data['evento']->tipo)
        <h1>Alguien se ha unido a tu evento</h1>
        <p>El usuario {{ $data['user']->nombre }} se ha unido a tu evento "{{ $data['evento']->titulo }}".</p>
    @else
        <p>El usuario {{ $data['user']->nombre }} ha solicitado unirse a tu evento "{{ $data['evento']->titulo }}".</p>
    @endif

    <p>Puedes contactar al usuario a través de su correo electrónico: {{ $data['user']->email }}</p>
</body>
</html>