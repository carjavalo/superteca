<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $isSuperAdmin = optional(auth()->user()->role)->name === 'Super Admin';

        $users = User::with('role')
            ->when(!$isSuperAdmin, function ($q) {
                // Ocultar usuarios con rol "Super Admin"
                $q->whereDoesntHave('role', function ($r) {
                    $r->where('name', 'Super Admin');
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%$search%")
                       ->orWhere('apellido1', 'like', "%$search%")
                       ->orWhere('apellido2', 'like', "%$search%")
                       ->orWhere('email', 'like', "%$search%")
                       ->orWhere('cedula', 'like', "%$search%")
                       ->orWhere('contacto', 'like', "%$search%");
                });
            })
            ->latest()->paginate(10)->withQueryString();

        $roles = Role::orderBy('name')->get();
        return view('admin.users.index', compact('users', 'search', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'apellido1' => ['nullable', 'string', 'max:100'],
            'apellido2' => ['nullable', 'string', 'max:100'],
            'cedula'    => ['nullable', 'string', 'max:30', 'unique:users,cedula'],
            'contacto'  => ['nullable', 'string', 'max:30'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', Password::defaults(), 'confirmed'],
            'role_id'   => ['nullable', 'exists:roles,id'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = [
            'name'      => $request->name,
            'apellido1' => $request->apellido1,
            'apellido2' => $request->apellido2,
            'cedula'    => $request->cedula,
            'contacto'  => $request->contacto,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role_id'   => $request->role_id ?: null,
        ];

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('avatars', 'public');
        }

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'apellido1' => ['nullable', 'string', 'max:100'],
            'apellido2' => ['nullable', 'string', 'max:100'],
            'cedula'    => ['nullable', 'string', 'max:30', Rule::unique('users', 'cedula')->ignore($user->id)],
            'contacto'  => ['nullable', 'string', 'max:30'],
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password'  => ['nullable', Password::defaults(), 'confirmed'],
            'role_id'   => ['nullable', 'exists:roles,id'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only(['name', 'apellido1', 'apellido2', 'cedula', 'contacto', 'email']);
        $data['role_id'] = $request->role_id ?: null;
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'No puedes eliminar tu propia cuenta desde aquí.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado.');
    }
}
