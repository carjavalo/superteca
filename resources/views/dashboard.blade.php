<x-app-layout>
    <x-slot name="header">
        <h2>Inicio</h2>
    </x-slot>

    @php $user = Auth::user(); @endphp

    {{-- Tarjetas de bienvenida --}}
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:18px; margin-bottom:24px;">
        <div style="background:#fff; border-left:5px solid #2e3a75; padding:18px 20px; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <div style="color:#6b7280; font-size:.8rem; text-transform:uppercase; letter-spacing:.05em;">Nombre completo</div>
            <div style="color:#2e3a75; font-size:1.1rem; font-weight:700; margin-top:4px;">
                {{ trim($user->name . ' ' . $user->apellido1 . ' ' . $user->apellido2) }}
            </div>
        </div>
        <div style="background:#fff; border-left:5px solid #2e3a75; padding:18px 20px; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <div style="color:#6b7280; font-size:.8rem; text-transform:uppercase; letter-spacing:.05em;">Cédula</div>
            <div style="color:#1f2937; font-size:1rem; font-weight:600; margin-top:4px;">
                {{ $user->cedula ?: '—' }}
            </div>
        </div>
        <div style="background:#fff; border-left:5px solid #2e3a75; padding:18px 20px; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <div style="color:#6b7280; font-size:.8rem; text-transform:uppercase; letter-spacing:.05em;">Contacto</div>
            <div style="color:#1f2937; font-size:1rem; font-weight:600; margin-top:4px;">
                {{ $user->contacto ?: '—' }}
            </div>
        </div>
        <div style="background:#fff; border-left:5px solid #2e3a75; padding:18px 20px; border-radius:8px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <div style="color:#6b7280; font-size:.8rem; text-transform:uppercase; letter-spacing:.05em;">Miembro desde</div>
            <div style="color:#1f2937; font-size:1rem; font-weight:600; margin-top:4px;">
                {{ $user->created_at->format('d/m/Y') }}
            </div>
        </div>
    </div>

    <div style="background:#fff; padding:28px; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <h3 style="color:#2e3a75; margin-top:0;">¡Bienvenido al sistema Superteca!</h3>
        <p style="color:#4b5563; margin:0;">
            Usa el menú lateral para navegar por las secciones del sistema.
            Desde el menú de usuario (esquina superior derecha) puedes actualizar tu perfil, cambiar tu contraseña o cerrar sesión.
        </p>
    </div>
</x-app-layout>
