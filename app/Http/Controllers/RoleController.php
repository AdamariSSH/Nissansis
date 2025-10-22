<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource (Muestra la lista de roles).
     */
    public function index()
    {
        // Obtiene todos los roles con sus permisos asociados para la vista de listado
        $roles = Role::with('permissions')->get();
        // La vista es resources/views/admin/roles/index.blade.php
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource (Muestra el formulario para crear un rol).
     */
    public function create()
    {
        // Obtenemos todos los permisos disponibles para mostrarlos en el formulario
        $permissions = Permission::all();
        // La vista debe ser resources/views/admin/roles/create.blade.php
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage (Guarda un nuevo rol).
     */
    public function store(Request $request)
    {
        // 1. Validación de la solicitud
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array', // Esperamos un array de IDs de permisos
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Este nombre de rol ya existe.',
        ]);

        try {
            // Utilizamos una transacción para asegurar que la creación de rol y permisos sea atómica
            DB::transaction(function () use ($request) {
                // 2. Creación del Rol
                $role = Role::create(['name' => $request->name]);
                
                // 3. Asignación de Permisos
                if ($request->has('permissions')) {
                    // Sincroniza los permisos (adjunta los IDs seleccionados)
                    $role->syncPermissions($request->permissions);
                }
            });

            return redirect()->route('admin.roles.index')
                             ->with('success', "Rol '{$request->name}' creado exitosamente y permisos asignados.");
        
        } catch (\Exception $e) {
            Log::error("Error al crear el rol: " . $e->getMessage());
            return back()->withInput()->with('error', 'Ocurrió un error al crear el rol. Por favor, intenta de nuevo.');
        }
    }

    /**
     * Show the form for editing the specified resource (Muestra el formulario de edición).
     */
    public function edit(Role $role)
    {
        // 1. Obtener todos los permisos disponibles
        $permissions = Permission::all();
        
        // 2. Obtener los IDs de los permisos que ya tiene este rol
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        // La vista debe ser resources/views/admin/roles/edit.blade.php
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage (Actualiza el rol).
     */
    public function update(Request $request, Role $role)
    {
        // 1. Validación de la solicitud
        $request->validate([
            // La validación unique debe ignorar el nombre del rol actual
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Este nombre de rol ya existe.',
        ]);

        try {
            DB::transaction(function () use ($request, $role) {
                // 2. Actualización del nombre del Rol
                $role->update(['name' => $request->name]);

                // 3. Sincronización de Permisos
                $role->syncPermissions($request->permissions ?? []);
            });
            
            return redirect()->route('admin.roles.index')
                             ->with('success', "Rol '{$role->name}' actualizado exitosamente.");
                             
        } catch (\Exception $e) {
            Log::error("Error al actualizar el rol: " . $e->getMessage());
            return back()->withInput()->with('error', 'Ocurrió un error al actualizar el rol. Por favor, intenta de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage (Elimina el rol).
     */
    public function destroy(Role $role)
    {
        // No permitir eliminar roles críticos (admin) o roles con usuarios asociados.
        // Aquí podrías añadir una validación para evitar eliminar el rol 'admin' o 'super-admin'
        if ($role->name === 'admin' || $role->name === 'super-admin') {
             return back()->with('error', "El rol '{$role->name}' no puede ser eliminado.");
        }

        try {
            // El método delete() de Spatie maneja la limpieza de la tabla pivote (model_has_roles)
            $role->delete();

            return redirect()->route('admin.roles.index')
                             ->with('success', "Rol '{$role->name}' eliminado exitosamente.");

        } catch (\Exception $e) {
            Log::error("Error al eliminar el rol: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al eliminar el rol. Asegúrate de que no tenga usuarios asociados.');
        }
    }

    // El método editPermissions que tenías antes ya no es necesario, ya que
    // la lógica de permisos está integrada en edit() y update().
}
