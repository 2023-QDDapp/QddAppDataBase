<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users['users'] = User::all();

        return view('user.index', $users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateUserData($request);

        $user = new User();
        $user->nombre = $validatedData['nombre'];
        $user->telefono = $validatedData['telefono'];
        $user->email = $validatedData['email'];
        $user->password = bcrypt($validatedData['password']);
        $user->fecha_nacimiento = $validatedData['fecha_nacimiento'];
        $user->biografia = $validatedData['biografia'];
        $user->foto = $validatedData['foto'];
        $user->save();

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $this->validateUserData($request, $user->id);

        $user->nombre = $validatedData['nombre'];
        $user->telefono = $validatedData['telefono'];
        $user->email = $validatedData['email'];
        $user->fecha_nacimiento = $validatedData['fecha_nacimiento'];
        $user->biografia = $validatedData['biografia'];
        $user->foto = $validatedData['foto'];

        if (!empty($validatedData['password'])) {
            $user->password = bcrypt($validatedData['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Datos del usuario actualizados correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }


    private function validateUserData(Request $request, $userId = null)
    {
        $rules = [
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|unique:users,telefono',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
            'fecha_nacimiento' => 'required|date',
            'biografia' => 'required|string|max:500',
            'foto' => 'required|image',
        ];

        if ($userId) {
            $rules['telefono'] .= ',' . $userId;
            $rules['email'] .= ',' . $userId;
        }

        return $request->validate($rules);
    }
}
