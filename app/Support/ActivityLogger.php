<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityLogger
{
    /** Modelos cuyos eventos NO se deben registrar (ruido / loops). */
    protected static array $ignored = [
        ActivityLog::class,
        \Illuminate\Notifications\DatabaseNotification::class,
    ];

    public static function shouldSkip(?int $userId = null, ?string $roleName = null): bool
    {
        // Si el usuario tiene rol Super Admin, NO registrar.
        if ($roleName && trim($roleName) === 'Super Admin') {
            return true;
        }
        return false;
    }

    public static function record(string $event, array $attributes = []): void
    {
        try {
            $user = Auth::user();
            $roleName = $user?->role?->name;

            if (self::shouldSkip($user?->id, $roleName)) {
                return;
            }

            $request = request();

            ActivityLog::create(array_merge([
                'user_id'    => $user?->id,
                'user_name'  => $user?->name,
                'role_name'  => $roleName,
                'event'      => $event,
                'route'      => optional($request?->route())->getName(),
                'method'     => $request?->method(),
                'url'        => $request ? Str::limit($request->fullUrl(), 480, '') : null,
                'ip'         => $request?->ip(),
                'user_agent' => $request ? Str::limit((string) $request->userAgent(), 480, '') : null,
                'created_at' => now(),
            ], $attributes));
        } catch (\Throwable $e) {
            // Nunca dejar que el log rompa la aplicación.
            report($e);
        }
    }

    public static function fromModel(string $event, Model $model): void
    {
        if (self::isIgnoredModel($model)) {
            return;
        }

        $changes = null;
        if ($event === 'UPDATED') {
            $changes = [
                'before' => self::sanitize($model->getOriginal()),
                'after'  => self::sanitize($model->getChanges()),
            ];
        } elseif ($event === 'CREATED') {
            $changes = ['after' => self::sanitize($model->getAttributes())];
        } elseif ($event === 'DELETED') {
            $changes = ['before' => self::sanitize($model->getOriginal())];
        }

        $shortName = class_basename($model);
        $description = match ($event) {
            'CREATED' => "Creó {$shortName} #{$model->getKey()}",
            'UPDATED' => "Actualizó {$shortName} #{$model->getKey()}",
            'DELETED' => "Eliminó {$shortName} #{$model->getKey()}",
            default   => $event . " " . $shortName,
        };

        self::record($event, [
            'model_type'  => get_class($model),
            'model_id'    => $model->getKey(),
            'description' => $description,
            'changes'     => $changes,
        ]);
    }

    protected static function isIgnoredModel(Model $model): bool
    {
        foreach (self::$ignored as $cls) {
            if ($model instanceof $cls) return true;
        }
        return false;
    }

    /** Quita atributos sensibles antes de guardar el snapshot. */
    protected static function sanitize(array $attrs): array
    {
        $blacklist = ['password', 'remember_token', 'api_token', 'two_factor_secret', 'two_factor_recovery_codes'];
        foreach ($blacklist as $k) {
            if (array_key_exists($k, $attrs)) {
                $attrs[$k] = '***';
            }
        }
        return $attrs;
    }
}
