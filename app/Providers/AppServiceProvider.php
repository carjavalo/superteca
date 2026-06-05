<?php

namespace App\Providers;

use App\Support\ActivityLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Directiva Blade para verificar permisos RBAC: @puede('Vista','Accion') ... @endpuede
        Blade::directive('puede', function ($expression) {
            return "<?php if (\\App\\Support\\Permisos::puede($expression)): ?>";
        });
        Blade::directive('endpuede', function () {
            return "<?php endif; ?>";
        });

        // Auditoría: eventos de autenticación
        Event::listen(Login::class, function (Login $event) {
            ActivityLogger::record('LOGIN', [
                'description' => "Inicio de sesión: " . ($event->user->name ?? $event->user->email ?? 'usuario'),
            ]);
        });
        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                ActivityLogger::record('LOGOUT', [
                    'description' => "Cierre de sesión: " . ($event->user->name ?? $event->user->email ?? 'usuario'),
                ]);
            }
        });

        // Auditoría: eventos de Eloquent (todas las tablas/modelos)
        Event::listen('eloquent.created: *', function ($eventName, $payload) {
            $m = $payload[0] ?? null;
            if ($m instanceof Model) ActivityLogger::fromModel('CREATED', $m);
        });
        Event::listen('eloquent.updated: *', function ($eventName, $payload) {
            $m = $payload[0] ?? null;
            if ($m instanceof Model) ActivityLogger::fromModel('UPDATED', $m);
        });
        Event::listen('eloquent.deleted: *', function ($eventName, $payload) {
            $m = $payload[0] ?? null;
            if ($m instanceof Model) ActivityLogger::fromModel('DELETED', $m);
        });
    }
}

