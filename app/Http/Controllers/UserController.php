<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Categoria;
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
        //el método categorias del modelo user crea la conexión entre las categorias y los users
        $users = User::with('categorias')->get();
    
        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categorias = Categoria::all();
        return view('user.create', compact('categorias'));
        
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

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/img/user', $fileName);
            $user->foto = 'img/user/' . $fileName;
        }

        $user->save();

        $categorias = $request->input('categorias', []);
        $user->categorias()->attach($categorias);

        return redirect()->route('users.index')->with('success', 'Se creó un nuevo usuario.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user->load('categorias', 'eventosCreados', 'eventosAsistidos', 'following');
        $followingUsers = $user->following;
        $mensajesRecibidos = $user->mensajesRecibidos;

        return view('user.show', compact('user', 'followingUsers', 'mensajesRecibidos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $categorias = Categoria::all();
        return view('user.edit', compact('user', 'categorias'));
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

        if (!empty($validatedData['password'])) {
            $user->password = bcrypt($validatedData['password']);
        }

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '.' . $file->getClientOriginalExtension();

            // Eliminar foto anterior si existe
            if (!empty($user->foto)) {
                $oldFilePath = public_path('storage/' . $user->foto);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $file->storeAs('public/img/user', $fileName);
            $user->foto = 'img/user/' . $fileName;
        }

        $user->save();

        $categorias = $request->input('categorias', []);
        $user->categorias()->sync($categorias);

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
        // Eliminar foto del usuario si existe
        if (!empty($user->foto)) {
            $filePath = public_path('storage/' . $user->foto);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        //dd($filePath);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }


    private function validateUserData(Request $request, $userId = null)
    {
        $isCreating = $userId === null;

        $rules = [
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|unique:users,telefono',
            'email' => 'required|email|unique:users,email',
            'password' => $isCreating ? 'required|string|min:8|confirmed' : 'nullable|string|min:8|confirmed',
            'fecha_nacimiento' => 'required|date',
            'biografia' => 'required|string|max:500',
            'foto' => $isCreating ? 'required|image|mimes:jpeg,png,jpg,gif|max:6048' : 'nullable|image|mimes:jpeg,png,jpg,gif|max:6048',
        ];

        if ($userId) {
            $rules['telefono'] .= ',' . $userId;
            $rules['email'] .= ',' . $userId;
        }

        // Validar la foto solo si se ha enviado una nueva durante la creación
        if ($isCreating && $request->hasFile('foto')) {
            $rules['foto'] = 'required|image|mimes:jpeg,png,jpg,gif|max:6048';
        }

        return $request->validate($rules);
    }
}
