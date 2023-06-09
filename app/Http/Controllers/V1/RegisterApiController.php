<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RegisterApiController extends Controller
{
    // Registro de usuario
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
        $user->verification_token = substr(str_shuffle(MD5(microtime())), 0, 30);

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

            // Envío del correo de verificación
            $this->sendVerificationEmail($user);

            // Devolvemos el usuario creado
            return response()->json($data);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['mensaje' => 'Los datos introducidos no son válidos'], 400);
        }
    }

    // Enviamos el email de verificación
    private function sendVerificationEmail(User $user)
    {
        $verificationLink = route('api.verify.email', [
            'id' => $user->id,
            'token' => $user->verification_token,
        ]);

        Mail::send('emails.verify', ['verificationLink' => $verificationLink], function ($message) use ($user) {
            $message->to($user->email)->subject('Verificación de correo electrónico');
        });
    }

    // Verificamos el email cuando ha pinchado el enlace del correo
    public function verifyEmail($id, $token)
    {
        $user = User::findOrFail($id);

        if ($user->verification_token === $token) {
            $user->is_verified = true;
            $user->verification_token = null; // Establecer el campo como null en lugar de eliminarlo
            $user->save();

            return view('emails.email-verified', ['message' => 'Correo electrónico verificado correctamente.']);
        }

        return response()->json(['error' => 'El enlace de verificación no es válido.'], 400);
    }

    // Continuamos el registro
    public function continueRegister(Request $request, $id)
    {
        // Obtenemos el usuario autenticado
        $user = User::findOrFail($id);

        // Comprobamos que ya está verificado
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

            // Almacenamos los datos introducidos
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
                $validator = Validator::make($request->all(), $campo, $mensaje);

                if ($validator->fails()) {
                    return response()->json([
                        'mensaje' => 'Error en los datos proporcionados',
                        'errores' => $validator->errors()
                    ], 400);
                }

                // Modificamos la foto
                $fotoUrl = null;
                if ($user->foto) {
                    $fotoUrl = asset('storage/' . $user->foto);
                }

                // Añadimos las categorías elegidas al usuario
                $user->categorias()->attach($categoriasSeleccionadas);

                // Ponemos a true el campo del registro y guardamos
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

    // Comprobar si el número de teléfono introducido se está usando
    public function verifyPhoneNumber(Request $request)
    {
        // Obtenemos el teléfono introducido
        $telefono = $request->telefono;

        // Comprobamos si existe
        $exists = User::where('telefono', $telefono)->exists();

        if (!$exists) {
            return response()->json(['mensaje' => 'El teléfono es válido']);
        } else {
            return response()->json(['mensaje' => 'El teléfono ya existe']);
        }
    }

    // Compribar si el email está verificado
    public function isEmailVerified(Request $request)
    {
        // Obtenemos el email
        $email = $request->email;

        // Y accedemos al usuario
        $user = User::where('email', $email)->first();

        if ($user->is_verified) {
            return response()->json(['mensaje' => 'El email está verificado']);
        } else {
            return response()->json(['mensaje' => 'El email no está verificado']);
        }
    }

}
