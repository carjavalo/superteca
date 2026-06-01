<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Nombre y apellidos en grid --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div>
                <x-input-label for="name" value="Nombre *" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="apellido1" value="Primer apellido" />
                <x-text-input id="apellido1" class="block mt-1 w-full" type="text" name="apellido1" :value="old('apellido1')" />
                <x-input-error :messages="$errors->get('apellido1')" class="mt-2" />
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:12px;">
            <div>
                <x-input-label for="apellido2" value="Segundo apellido" />
                <x-text-input id="apellido2" class="block mt-1 w-full" type="text" name="apellido2" :value="old('apellido2')" />
                <x-input-error :messages="$errors->get('apellido2')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="cedula" value="Cédula" />
                <x-text-input id="cedula" class="block mt-1 w-full" type="text" name="cedula" :value="old('cedula')" />
                <x-input-error :messages="$errors->get('cedula')" class="mt-2" />
            </div>
        </div>

        <div class="mt-3">
            <x-input-label for="contacto" value="Contacto (teléfono)" />
            <x-text-input id="contacto" class="block mt-1 w-full" type="text" name="contacto" :value="old('contacto')" />
            <x-input-error :messages="$errors->get('contacto')" class="mt-2" />
        </div>

        <div class="mt-3">
            <x-input-label for="role_id" value="Rol" />
            <select id="role_id" name="role_id"
                    style="width:100%; padding:8px 12px; border:1.5px solid #d1d5db; border-radius:6px; font-size:.9rem; color:#374151; background:#fff; outline:none; margin-top:4px; font-family:inherit;"
                    onfocus="this.style.borderColor='#2e3a75'" onblur="this.style.borderColor='#d1d5db'">
                <option value="">— Sin rol asignado —</option>
                @foreach($roles as $rol)
                    <option value="{{ $rol->id }}" {{ old('role_id') == $rol->id ? 'selected' : '' }}>{{ $rol->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
        </div>

        <div style="height:1px; background:#e5e7eb; margin:16px 0;"></div>

        <div>
            <x-input-label for="email" value="Correo electrónico *" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-3">
            <x-input-label for="password" value="Contraseña *" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-3">
            <x-input-label for="password_confirmation" value="Confirmar contraseña *" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-5">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                ¿Ya estás registrado?
            </a>
            <x-primary-button class="ms-4">
                Registrarse
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
