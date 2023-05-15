<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->is_super_admin) {
            $admins['admins'] = Admin::all();
        } else {
            $admins['admins'] = Admin::where('id', Auth::id())->get();
        }

        return view('admin.index', $admins);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateAdminData($request);

        $admin = new Admin();
        $admin->name = $validatedData['name'];
        $admin->email = $validatedData['email'];
        $admin->password = bcrypt($validatedData['password']);
        $admin->is_super_admin = $request->input('is_super_admin') ? true : false;
        $admin->save();

        return redirect()->route('admins.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        return view('admin.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $admin)
    {
        return view('admin.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $admin)
    {
        $validatedData = $this->validateAdminData($request, $admin->id);

        $admin->name = $validatedData['name'];
        $admin->email = $validatedData['email'];

        if (!empty($validatedData['password'])) {
            $admin->password = bcrypt($validatedData['password']);
        }

        if (array_key_exists('is_super_admin', $validatedData)) {
            //$admin->is_super_admin = $validatedData['is_super_admin'] ? true : false;
            $admin->is_super_admin = $validatedData['is_super_admin'] ?? false;
        }

        $admin->save();

        return redirect()->route('admins.index')->with('success', 'Datos del administrador actualizados correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();

        return redirect()->route('admins.index')->with('success', 'Administrador eliminado.');
    }

     /**
     * Validate the admin data from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $adminId
     * @return array
     */
    private function validateAdminData(Request $request, $adminId = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'is_super_admin' => 'nullable|boolean',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        if ($adminId) {
            $rules['email'] .= ',' . $adminId;
        }

        return $request->validate($rules);
    }
}
