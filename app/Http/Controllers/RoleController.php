<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $roles = Role::when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('descripcion', 'like', "%$search%");
        })->latest()->paginate(10)->withQueryString();

        return view('admin.roles.index', compact('roles', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:60', 'unique:roles,name'],
            'descripcion' => ['nullable', 'string', 'max:100'],
        ]);

        Role::create([
            'name'        => $request->name,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado exitosamente.');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:60', Rule::unique('roles', 'name')->ignore($role->id)],
            'descripcion' => ['nullable', 'string', 'max:100'],
        ]);

        $role->update([
            'name'        => $request->name,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Rol eliminado.');
    }
}
