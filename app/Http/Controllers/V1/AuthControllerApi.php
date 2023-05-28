<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;
use App\Models\User;

class AuthControllerApi extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $credentials = $request->only('email', 'password');

        // Verificar si el correo electrónico está verificado
        $user = User::where('email', $credentials['email'])->first();

        if ($user && !$user->is_verified) {
            return response()->json(['error' => 'email_not_verified'], 401);
        }
        //dd($user->id, $user->password);
        
        try {
            if (! $token = Auth::guard('api')->attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'telefono' => $user->telefono,
                'fecha_nacimiento' => $user->fecha_nacimiento,
                'biografia' => $user->biografia,
                'foto' => asset('storage/' . $user->foto),
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        JWTAuth::invalidate($token);
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(Request $request)
    {
        $token = $request->header('Authorization');
        $newToken = JWTAuth::refresh($token);
        return response()->json(compact('newToken'));
    }
}
