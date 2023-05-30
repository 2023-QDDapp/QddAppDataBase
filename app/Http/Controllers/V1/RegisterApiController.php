<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RegisterApiController extends Controller
{
    public function register(Request $request)
    {
        // Escribimos los campos que se van a validar
        $campo = [
            'email' => 'required|email|unique:users',
            'password' => 'nullable|string|min:6'
        ];

        // Con el mensaje de error correspondiente
        $mensaje = [
            'email.required' => 'El email es obligatorio',
            'email.unique' => 'El email ya está en uso',
            'email.email' => 'Introduce un email válido',
            'password.min' => 'La contraseña no puede ser menor de 6 caracteres'
        ];

        // Almacenamos los datos
        $user = new User;
        $user->email = $request->email;
        $user->password = $request->password;

        // Validamos y guardamos
        try {
            $this->validate($request, $campo, $mensaje);
            $user->save();

            $data = [
                'mensaje' => 'El usuario ha sido registrado correctamente. Por favor, verifique su correo electrónico.',
                'id' => $user->id,
                'email' => $user->email,
                'password' => $user->password
            ];

            // Devolvemos el usuario creado
            return response()->json($data);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['mensaje' => 'Los datos introducidos no son válidos']);
        }

        // Envío del correo de verificación
        $this->sendVerificationEmail($user);
    }

    private function sendVerificationEmail(User $user)
    {
        $verificationLink = route('api.verify.email', [
            'id' => $user->id,
            'token' => $user->verification_token,
        ]);

        Mail::send('emails.verify', ['verificationLink' => $verificationLink, 'nombre' => $user->nombre], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verificación de correo electrónico');
        });
    }

    public function verifyEmail(Request $request, $id, $token)
    {
        $user = User::findOrFail($id);

        if ($user->verification_token === $token) {
            $user->is_verified = true;
            $user->verification_token = null; // Establecer el campo como null en lugar de eliminarlo
            $user->save();

            return response()->json(['message' => 'Correo electrónico verificado correctamente.'], 200);
        }

        return response()->json(['error' => 'El enlace de verificación no es válido.'], 400);
    }

    public function continueRegister(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->is_verified) {
            // Escribimos los campos que se van a validar
            $campo = [
                'nombre' => 'required|string|max:255',
                'telefono' => 'required|string|max:9|unique:users,telefono',
                'fecha_nacimiento' => 'required|date',
                'biografia' => 'required|string|max:500',
                'foto' => 'required|string',
                'categorias' => 'required|array|size:3'
            ];

            // Con el mensaje de error correspondiente
            $mensaje = [
                'required' => 'El campo :attribute es obligatorio',
                'max' => 'El campo :attribute no puede ser mayor de :max caracteres',
                'size' => 'No puedes elegir más de tres categorías'
            ];

            $user->nombre = $request->nombre;
            $user->telefono = $request->telefono;
            $user->fecha_nacimiento = $request->fecha_nacimiento;
            $user->biografia = $request->biografia;
            $categoriasSeleccionadas = $request->input('categorias');

            // Guardamos la foto
            if ($request->has('foto')) {
                $base64Image = $request->input('foto');
                list($type, $data) = explode(';', $base64Image);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $fileName = time() . '.jpg'; // Nombre del archivo
                $filePath = 'public/img/user/' . $fileName; // Ruta donde se guarda la foto
                Storage::put($filePath, $data);
                $user->foto = 'img/user/' . $fileName;
            }

            // Validamos y guardamos
            try {
                $this->validate($request, $campo, $mensaje);
                $user->save();

                // Modificamos la foto
                $fotoUrl = null;
                if ($user->foto) {
                    $fotoUrl = asset('storage/' . $user->foto);
                }

                // Añadimos las categorías elegidas al usuario
                $user->categorias()->attach($categoriasSeleccionadas);

                $user->is_registered = true;
                $user->save();

                $data = [
                    'mensaje' => 'Registro completado correctamente.',
                    'id' => $user->id,
                    'nombre' => $user->nombre,
                    'foto' => $fotoUrl,
                    'email' => $user->email,
                    'password' => $user->password,
                    'fecha_nacimiento' => $user->fecha_nacimiento,
                    'biografia' => $user->biografia,
                    'intereses' => $categoriasSeleccionadas
                ];

                return response()->json($data, 200);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json(['mensaje' => 'Los datos introducidos no son válidos'], 400);
            }
        } else {
            return response()->json([
                'mensaje' => 'Verifique su email.'
            ], 400);
        }
    }
}
