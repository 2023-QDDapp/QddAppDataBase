<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Categoria;
use App\Models\EventoUser;
use Carbon\Carbon;

class EventoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Evento::with('categoria', 'creador')->get();

        return view('event.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Evento $event)
    {
        $categorias = Categoria::all();
        $users = User::all();
        return view('event.create' , compact('event','categorias', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateEventData($request);

        $evento = new Evento();
        $evento->fill($validatedData);
        //$evento->n_participantes = 1; asignación por defecto de participantes porque el organizador se considera un participante
        
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/img/event', $fileName);
            $evento->imagen = 'img/event/' . $fileName;
        }

        $evento->save();

        // Agregar usuarios relacionados al evento con el estado por defecto "false"
        $usuariosSeleccionados = $request->input('usuarios', []);
        $usuarios = User::whereIn('id', $usuariosSeleccionados)->get();
        
        foreach ($usuarios as $usuario) {
            $evento->usuariosAsistentes()->attach($usuario, ['estado' => false]);
        }

        return redirect()->route('events.index')->with('success', 'Se creó un nuevo evento.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Evento $event)
    {

        $event::with('categoria', 'creador', 'usuariosAsistentes')->get();
        
        return view('event.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = Evento::with('user', 'categoria')->findOrFail($id);
        $users = User::where('id', '!=', $event->user_id)
                    ->whereDoesntHave('eventosAsistidos', function ($query) use ($event) {
                        $query->where('evento_id', $event->id)
                            ->where('estado', true);
                    })
                    ->get();
        $categorias = Categoria::all();

        return view('event.edit', compact('event', 'users', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $validatedData = $this->validateEventData($request);

        $evento = Evento::findOrFail($id);

        $evento->fill($validatedData);

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $fileName = time() . '.' . $file->getClientOriginalExtension();

            // Eliminar imagen anterior si existe
            if (!empty($evento->imagen)) {
                $oldFilePath = public_path('storage/' . $evento->imagen);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            $file->storeAs('public/img/event', $fileName);
            $evento->imagen = 'img/event/' . $fileName;
        }

        $evento->save();

        // Actualizar usuarios relacionados al evento con el estado existente
        $usuariosSeleccionados = $request->input('usuarios', []);
        $usuariosAntiguos = $evento->usuariosAsistentes()->pluck('user_id')->toArray();

        $usuariosEliminar = array_diff($usuariosAntiguos, $usuariosSeleccionados);
        $usuariosAgregar = array_diff($usuariosSeleccionados, $usuariosAntiguos);

        // Eliminar usuarios deseleccionados
        $evento->usuariosAsistentes()->detach($usuariosEliminar);

        // Agregar nuevos usuarios y actualizar el estado existente
        foreach ($usuariosAgregar as $usuarioAgregar) {
            $evento->usuariosAsistentes()->attach($usuarioAgregar, ['estado' => false]);
        }

        return redirect()->route('events.index')->with('success', 'Datos del evento actualizados correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);

        if (!empty($evento->imagen)) {
            $filePath = public_path('storage/' . $evento->imagen);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Eliminar el evento de la base de datos
        $evento->delete();

        // Redireccionar a la página principal de eventos o a donde consideres apropiado
        return redirect()->route('events.index')->with('success', 'Evento eliminado exitosamente.');
    }

    private function validateEventData(Request $request, $eventId = null)
    {
        $isCreating = $eventId === null;

        $rules = [
            'user_id' => 'required|exists:users,id',
            'categoria_id' => 'required|exists:categorias,id',
            'titulo' => 'required|string|max:255',
            'fecha_hora_inicio' => 'required|date_format:Y-m-d\TH:i',
            'fecha_hora_fin' => 'required|date_format:Y-m-d\TH:i|after:fecha_hora_inicio',
            'descripcion' => 'required|string|max:500',
            'tipo' => 'required|string',
            'imagen' => 'nullable|string',
            'location' => 'required|string',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'n_participantes' => 'nullable|integer',
        ];

        if ($eventId) {
            $rules['user_id'] .= ',' . $eventId;
        }

        // Validar la imagen solo si se ha enviado una nueva durante la creación
        if ($isCreating && $request->hasFile('imagen')) {
            $rules['imagen'] = 'required|image|mimes:jpeg,png,jpg,gif|max:6048';
        }

        return $request->validate($rules);
    }
}
