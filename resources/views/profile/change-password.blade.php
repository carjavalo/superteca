<x-app-layout>
    <x-slot name="header">
        <h2>Cambiar contraseña</h2>
    </x-slot>

    <div style="max-width:600px; margin:0 auto;">
        <div style="background:#fff; padding:28px; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <h3 style="color:#2e3a75; margin-top:0;">Actualizar contraseña</h3>
            <p style="color:#6b7280; font-size:.9rem;">Asegúrate de utilizar una contraseña larga y aleatoria para mantener la seguridad de tu cuenta.</p>

            @if (session('status') === 'password-updated')
                <div style="background:#d1fae5; color:#065f46; padding:10px 14px; border-radius:6px; margin-bottom:16px;">
                    Contraseña actualizada correctamente.
                </div>
            @endif

            <form method="POST" action="{{ route('password.change.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="current_password" value="Contraseña actual" />
                    <x-text-input id="current_password" name="current_password" type="password" class="block mt-1 w-full" autocomplete="current-password" />
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password" value="Nueva contraseña" />
                    <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" autocomplete="new-password" />
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password_confirmation" value="Confirmar nueva contraseña" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" autocomplete="new-password" />
                </div>

                <div class="mt-6 flex justify-end">
                    <x-primary-button>Guardar</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
