<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Asume que tienes un modelo User en App\Models\User
use App\Models\Almacen; // Para obtener la lista de almacenes
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Muestra la lista de todos los usuarios.
     */


   
    public function index()
    {
        // 1. Carga los usuarios en la variable $users
        $users = User::with('almacen')->get();
        
        // 2. Pasa la variable $users a la vista con compact('users')
        // Esto hace que la variable $users esté disponible en la vista.
        return view('admin.usuarios.index', compact('users')); 
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        $almacenes = Almacen::all(); // Obtiene todos los almacenes
        return view('admin.usuarios.create', compact('almacenes'));
    }

    /**
     * Guarda un nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'user'])],
            'almacen_id' => 'nullable|exists:almacen,Id_Almacen', // Valida que exista en la tabla almacen
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'almacen_id' => $request->almacen_id,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     */
    public function edit(User $usuario)
    {
        // La ruta resource usa el nombre 'usuario' por defecto
        $almacenes = Almacen::all();
        return view('admin.usuarios.edit', compact('usuario', 'almacenes'));
    }

    /**
     * Actualiza la información de un usuario (incluyendo Rol y Almacén).
     */
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // El email debe ser único, excluyendo el email actual del usuario
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'almacen_id' => 'nullable|exists:almacen,Id_Almacen',
            'password' => 'nullable|string|min:8|confirmed', // Opción para cambiar la contraseña
        ]);

        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->role = $request->role;
        $usuario->almacen_id = $request->almacen_id;

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Elimina un usuario.
     */
    public function destroy(User $usuario)
    {
        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}