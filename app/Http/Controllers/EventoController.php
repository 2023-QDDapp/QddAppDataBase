<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Categoria;
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
        $evento->user_id = $validatedData['user_id'];
        $evento->categoria_id = $validatedData['categoria_id'];
        $evento->titulo = $validatedData['titulo'];
        $evento->fecha_hora_inicio = $validatedData['fecha_hora_inicio'];
        $evento->fecha_hora_fin = $validatedData['fecha_hora_fin'];
        $evento->descripcion = $validatedData['descripcion'];
        $evento->tipo = $validatedData['tipo'];
        $evento->location = $validatedData['location'];
        $evento->latitud = $validatedData['latitud'];
        $evento->longitud = $validatedData['longitud'];
        //$evento->n_participantes = $validatedData['n_participantes'];

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/img/event', $fileName);
            $evento->imagen = 'img/event/' . $fileName;
        }

        $evento->save();

        return redirect()->route('events.index')->with('success', 'se creo un nuevo evento.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Evento $event)
    {

        $event::with('categoria', 'creador')->get();
        
        return view('event.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Evento $event)
    {
        $categorias = Categoria::all();
        $users = User::all();

        return view('event.edit', compact('event', 'categorias', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Evento $evento)
    {
        $validatedData = $this->validateEventData($request);

        $evento->user_id = $validatedData['user_id'];
        $evento->categoria_id = $validatedData['categoria_id'];
        $evento->titulo = $validatedData['titulo'];
        $evento->fecha_hora_inicio = $validatedData['fecha_hora_inicio'];
        $evento->fecha_hora_fin = $validatedData['fecha_hora_fin'];
        $evento->descripcion = $validatedData['descripcion'];
        $evento->tipo = $validatedData['tipo'];
        $evento->location = $validatedData['location'];
        $evento->latitud = $validatedData['latitud'];
        $evento->longitud = $validatedData['longitud'];
        //$evento->n_participantes = $validatedData['n_participantes'];

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

        return redirect()->route('events.index')->with('success', 'Datos del evento actualizados correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Evento $evento)
    {
        $evento->delete();

        return redirect()->route('events.index')->with('success', 'Evento eliminado.');
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
            //'n_participantes' => 'nullable|integer',
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
