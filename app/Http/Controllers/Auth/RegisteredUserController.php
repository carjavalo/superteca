<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        return view('auth.register', compact('roles'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'apellido1' => ['nullable', 'string', 'max:100'],
            'apellido2' => ['nullable', 'string', 'max:100'],
            'cedula'    => ['nullable', 'string', 'max:30', 'unique:users,cedula'],
            'contacto'  => ['nullable', 'string', 'max:30'],
            'email'     => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id'   => ['nullable', 'exists:roles,id'],
        ]);

        $user = User::create([
            'name'      => $request->name,
            'apellido1' => $request->apellido1,
            'apellido2' => $request->apellido2,
            'cedula'    => $request->cedula,
            'contacto'  => $request->contacto,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role_id'   => $request->role_id ?: null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
