<x-app-layout>
    <x-slot name="header">
        <h2>Mi cuenta</h2>
    </x-slot>

    <div style="max-width:860px; margin:0 auto; display:flex; flex-direction:column; gap:24px;">

        @if (session('status') === 'profile-updated')
            <div style="background:#d1fae5; color:#065f46; padding:10px 16px; border-radius:8px; border-left:4px solid #059669;">
                ✓ Perfil actualizado correctamente.
            </div>
        @endif

        {{-- ── Datos personales ── --}}
        <div style="background:#fff; padding:28px; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,0.07);">
            <h3 style="color:#2e3a75; margin-top:0; border-bottom:2px solid #e5e7eb; padding-bottom:10px;">Información personal</h3>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                {{-- Avatar --}}
                <div style="display:flex; align-items:center; gap:18px; margin-bottom:22px;">
                    <div style="width:88px; height:88px; border-radius:50%; overflow:hidden; background:#2e3a75; color:#fff; display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:700; border:3px solid #2e3a75; flex-shrink:0;">
                        @if(Auth::user()->profile_image)
                            <img src="{{ asset('storage/'.Auth::user()->profile_image) }}" alt="avatar" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                        @endif
                    </div>
                    <div>
                        <x-input-label for="profile_image" value="Foto de perfil" />
                        <input id="profile_image" name="profile_image" type="file" accept="image/*" class="block mt-1 text-sm text-gray-600">
                        <x-input-error :messages="$errors->get('profile_image')" class="mt-1" />
                    </div>
                </div>

                {{-- Nombre y apellidos --}}
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
                    <div>
                        <x-input-label for="name" value="Nombre *" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full"
                            :value="old('name', $user->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="apellido1" value="Primer apellido" />
                        <x-text-input id="apellido1" name="apellido1" type="text" class="block mt-1 w-full"
                            :value="old('apellido1', $user->apellido1)" />
                        <x-input-error :messages="$errors->get('apellido1')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="apellido2" value="Segundo apellido" />
                        <x-text-input id="apellido2" name="apellido2" type="text" class="block mt-1 w-full"
                            :value="old('apellido2', $user->apellido2)" />
                        <x-input-error :messages="$errors->get('apellido2')" class="mt-1" />
                    </div>
                </div>

                {{-- Cédula y contacto --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-top:14px;">
                    <div>
                        <x-input-label for="cedula" value="Cédula" />
                        <x-text-input id="cedula" name="cedula" type="text" class="block mt-1 w-full"
                            :value="old('cedula', $user->cedula)" />
                        <x-input-error :messages="$errors->get('cedula')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="contacto" value="Contacto (teléfono)" />
                        <x-text-input id="contacto" name="contacto" type="text" class="block mt-1 w-full"
                            :value="old('contacto', $user->contacto)" />
                        <x-input-error :messages="$errors->get('contacto')" class="mt-1" />
                    </div>
                </div>

                <div style="height:1px; background:#e5e7eb; margin:18px 0;"></div>

                {{-- Correo --}}
                <div>
                    <x-input-label for="email" value="Correo electrónico *" />
                    <x-text-input id="email" name="email" type="email" class="block mt-1 w-full"
                        :value="old('email', $user->email)" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div class="mt-5 flex justify-end">
                    <x-primary-button>Guardar cambios</x-primary-button>
                </div>
            </form>
        </div>

        {{-- ── Seguridad ── --}}
        <div style="background:#fff; padding:28px; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,0.07);">
            <h3 style="color:#2e3a75; margin-top:0; border-bottom:2px solid #e5e7eb; padding-bottom:10px;">Seguridad</h3>
            <p style="color:#6b7280; font-size:.9rem; margin-top:0;">Cambia tu contraseña para proteger tu cuenta.</p>
            <a href="{{ route('password.change') }}"
               style="display:inline-block; background:#2e3a75; color:#fff; padding:9px 20px; border-radius:6px; text-decoration:none; font-weight:600; font-size:.9rem;">
                🔒 Cambiar contraseña
            </a>
        </div>

        {{-- ── Eliminar cuenta ── --}}
        <div style="background:#fff; padding:28px; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,0.07); border-left:4px solid #dc2626;">
            <h3 style="color:#dc2626; margin-top:0; border-bottom:2px solid #fee2e2; padding-bottom:10px;">Zona de peligro</h3>
            <p style="color:#6b7280; font-size:.9rem; margin-top:0;">Una vez eliminada tu cuenta, todos los datos serán borrados de forma permanente.</p>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="password" id="delete-password-input">
                <button type="button"
                    onclick="var p=prompt('Ingresa tu contraseña para confirmar la eliminación:'); if(p){document.getElementById('delete-password-input').value=p; this.form.submit();}"
                    style="background:#dc2626; color:#fff; border:none; padding:9px 20px; border-radius:6px; font-weight:600; cursor:pointer; font-size:.9rem; font-family:inherit;">
                    🗑 Eliminar mi cuenta
                </button>
                @if($errors->userDeletion->any())
                    <div style="color:#dc2626; margin-top:10px; font-size:.85rem;">{{ $errors->userDeletion->first() }}</div>
                @endif
            </form>
        </div>

    </div>
</x-app-layout>
