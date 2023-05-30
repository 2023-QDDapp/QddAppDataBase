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
        
        $request->validate([
            'nombre' => 'required|string',
            'telefono' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'biografia' => 'required|string|max:500',
            'foto' => 'required|string',
        ]);

        // Decodificar y guardar la foto
        if ($request->has('foto')) {
            $base64Image = $request->input('foto');
            list($type, $data) = explode(';', $base64Image);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $fileName = time() . '.jpg';
            $filePath = 'public/img/user/' . $fileName;
            Storage::put($filePath, $data);
        }

        $user = User::create([
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'biografia' => $request->biografia,
            'foto' => 'img/user/' . $fileName,
            'verification_token' => Str::random(60),
        ]);

        // Envío del correo de verificación
        $this->sendVerificationEmail($user);

        return response()->json(['message' => 'Usuario registrado exitosamente. Por favor, verifique su correo electrónico.'], 201);
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
            $user->verification_token = null;
            $user->save();

            return response()->json(['message' => 'Correo electrónico verificado correctamente.'], 200);
        }

        return response()->json(['error' => 'El enlace de verificación no es válido.'], 400);
    }
}