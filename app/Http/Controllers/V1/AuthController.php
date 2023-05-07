<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //Función que utilizaremos para hacer login
    public function authenticate(Request $request){
        //Indicamos los parámetros que queremos recibir de la request
        $credentials = $request->only('email', 'password');


        //Validación
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);


        //Si la validación falla, devolvemos un error
        if($validator->fails()){
            return response()->json(['error' => $validator->messages()], 400);
        }


        //Intentamos hacer login
        try{
            if(!$token = JWTAuth::attempt($credentials)){
                //Credenciales incorrectas
                return response()->json(['error' => 'Login failed'], 401);
            }
        }catch (JWTException $e){
            //Error chungo
            return response()->json([
                'message' => 'Error',
            ], 500);
        }


        //Devolver el token
        return response()->json([
            'token' => $token,
            'user' => Auth::user()
        ]);
    }

    //Funcion que elimina el token y desconecta al usuario
    public function logout(Request $request){
        //Valida que nos envía el token
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);


        //Fallo de validación


        //Si el token es válido eliminamos el token desconectando al usuario
    
    }

}
