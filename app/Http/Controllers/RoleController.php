<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $isSuperAdmin = optional(auth()->user()->role)->name === 'Super Admin';

        $search = $request->get('search', '');
        $roles = Role::when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('descripcion', 'like', "%$search%");
        })->when(! $isSuperAdmin, function ($q) {
            $q->where('name', '!=', 'Super Admin');
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
        $this->bloquearSuperAdmin($role);

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
        $this->bloquearSuperAdmin($role);

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Rol eliminado.');
    }

    /**
     * Impide que un usuario que no sea Super Admin acceda/manipule el rol Super Admin.
     */
    private function bloquearSuperAdmin(Role $role): void
    {
        $isSuperAdmin = optional(auth()->user()->role)->name === 'Super Admin';

        if (! $isSuperAdmin && $role->name === 'Super Admin') {
            abort(403, 'No tiene permisos para gestionar el rol Super Admin.');
        }
    }
}
