<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accion;
use App\Models\AuditoriaPermiso;
use App\Models\Permiso;
use App\Models\Role;
use App\Models\RolPermiso;
use App\Models\User;
use App\Models\UsuarioPermiso;
use App\Support\PermisosCatalogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PermisoController extends Controller
{
    /** Solo el Super Admin administra el Centro de Permisos. */
    private function autorizar(): void
    {
        abort_unless(
            optional(auth()->user()->role)->name === 'Super Admin',
            403,
            'Solo el Super Admin puede acceder a la Gestión de Permisos.'
        );
    }

    /**
     * Centro de Gestión de Permisos (RBAC).
     */
    public function index(Request $request)
    {
        $this->autorizar();

        // Auto-registración: agrega al catálogo cualquier vista/acción nueva.
        PermisosCatalogo::sync();

        $config   = config('permisos');
        $acciones = Accion::orderBy('id')->get();
        $roles    = Role::orderBy('name')->get();

        // Mapa nombre de acción -> id (para resolver las columnas de la matriz).
        $accionIdPorNombre = $acciones->pluck('id', 'nombre');

        // Rol seleccionado (por defecto el primero que no sea Super Admin).
        $rolSeleccionadoId = $request->integer('rol')
            ?: optional($roles->firstWhere('name', '!=', 'Super Admin'))->id
            ?: optional($roles->first())->id;

        $rolSeleccionado = $roles->firstWhere('id', $rolSeleccionadoId);

        // Permisos concedidos al rol seleccionado: [permiso_id][accion_id] = true
        $concedidos = [];
        if ($rolSeleccionado) {
            foreach (RolPermiso::where('rol_id', $rolSeleccionado->id)->where('permitido', 1)->get(['permiso_id', 'accion_id']) as $rp) {
                $concedidos[$rp->permiso_id][$rp->accion_id] = true;
            }
        }

        // Filtros (se aplican en el cliente; aquí solo se usan para precargar
        // los controles). La matriz siempre se construye completa para que el
        // guardado abarque todos los módulos aunque haya un filtro activo.
        $filtroModulo = $request->string('modulo')->toString();
        $busqueda     = trim($request->string('q')->toString());

        // Construcción de la matriz módulo -> vistas -> acciones.
        $permisos = Permiso::where('estado', 1)->orderBy('orden')->get()->groupBy('modulo');

        $modulos = [];
        $rolActivos = 0;     // celdas concedidas al rol
        $rolTotal   = 0;     // celdas asignables totales (universo de la matriz)

        foreach ($config['modulos'] as $moduloKey => $moduloCfg) {
            // Columnas de acción de este módulo.
            $columnas = [];
            foreach ($config['matriz'][$moduloKey] ?? ['Ver', 'Crear', 'Editar', 'Eliminar'] as $nombreAccion) {
                if (isset($accionIdPorNombre[$nombreAccion])) {
                    $columnas[] = ['id' => $accionIdPorNombre[$nombreAccion], 'nombre' => $nombreAccion];
                }
            }
            $columnaIds = array_column($columnas, 'id');

            $vistas = [];
            $modActivos = 0;

            foreach (($permisos[$moduloKey] ?? collect()) as $permiso) {
                $estados  = [];
                $activos  = 0;
                foreach ($columnaIds as $aid) {
                    $on = isset($concedidos[$permiso->id][$aid]);
                    $estados[$aid] = $on;
                    if ($on) {
                        $activos++;
                    }
                }

                $vistas[] = [
                    'id'          => $permiso->id,
                    'vista'       => $permiso->vista,
                    'descripcion' => $permiso->descripcion,
                    'estados'     => $estados,
                    'activos'     => $activos,
                    'total'       => count($columnaIds),
                ];

                $modActivos += $activos;
            }

            $rolActivos += $modActivos;
            $rolTotal   += count(($permisos[$moduloKey] ?? collect())) * count($columnaIds);

            $modulos[] = [
                'key'      => $moduloKey,
                'label'    => $moduloCfg['label'],
                'icono'    => $moduloCfg['icono'] ?? '📁',
                'acciones' => $columnas,
                'vistas'   => $vistas,
                'activos'  => $modActivos,
                'total'    => count(($permisos[$moduloKey] ?? collect())) * count($columnaIds),
            ];
        }

        // KPIs globales.
        $kpis = [
            'usuarios'      => User::count(),
            'roles'         => Role::count(),
            'vistas'        => Permiso::where('estado', 1)->count(),
            'acciones'      => $acciones->count(),
            'asignaciones'  => RolPermiso::where('permitido', 1)->count(),
            'especiales'    => UsuarioPermiso::where('permitido', 1)->count(),
        ];

        // Panel del rol seleccionado.
        $rolInfo = $rolSeleccionado ? [
            'id'       => $rolSeleccionado->id,
            'nombre'   => $rolSeleccionado->name,
            'usuarios' => User::where('role_id', $rolSeleccionado->id)->count(),
            'activos'  => $rolActivos,
            'total'    => $rolTotal,
            'super'    => $rolSeleccionado->name === 'Super Admin',
        ] : null;

        // Comparación de dos roles (opcional).
        $comparacion = null;
        if ($request->filled('cmp_a') && $request->filled('cmp_b')) {
            $comparacion = $this->construirComparacion(
                (int) $request->cmp_a,
                (int) $request->cmp_b,
                $config,
                $accionIdPorNombre
            );
        }

        return view('admin.permisos.index', [
            'modulos'         => $modulos,
            'roles'           => $roles,
            'rolSeleccionado' => $rolSeleccionado,
            'rolInfo'         => $rolInfo,
            'kpis'            => $kpis,
            'filtroModulo'    => $filtroModulo,
            'busqueda'        => $busqueda,
            'modulosCatalogo' => $config['modulos'],
            'comparacion'     => $comparacion,
        ]);
    }

    /**
     * Guarda la matriz de permisos del rol seleccionado.
     */
    public function update(Request $request)
    {
        $this->autorizar();

        $datos = $request->validate([
            'rol_id'        => ['required', 'integer', 'exists:roles,id'],
            'perm'          => ['nullable', 'array'],
        ]);

        $rolId  = (int) $datos['rol_id'];
        $config = config('permisos');

        $accionIdPorNombre = Accion::pluck('id', 'nombre');
        $permisos = Permiso::where('estado', 1)->get()->groupBy('modulo');

        // Universo de celdas administradas por la matriz: permiso_id => [accion_ids]
        $universo = [];
        foreach ($config['modulos'] as $moduloKey => $moduloCfg) {
            $columnaIds = collect($config['matriz'][$moduloKey] ?? ['Ver', 'Crear', 'Editar', 'Eliminar'])
                ->map(fn ($n) => $accionIdPorNombre[$n] ?? null)
                ->filter()->values()->all();

            foreach (($permisos[$moduloKey] ?? collect()) as $permiso) {
                $universo[$permiso->id] = $columnaIds;
            }
        }

        // Marcados por el usuario: perm[permisoId][accionId] = "1"
        $marcados = $request->input('perm', []);

        $insertar  = [];
        $ahora     = now();
        $totalOn   = 0;
        foreach ($universo as $permisoId => $accionIds) {
            foreach ($accionIds as $accionId) {
                if (! empty($marcados[$permisoId][$accionId])) {
                    $insertar[] = [
                        'rol_id'     => $rolId,
                        'permiso_id' => $permisoId,
                        'accion_id'  => $accionId,
                        'permitido'  => 1,
                        'created_at' => $ahora,
                    ];
                    $totalOn++;
                }
            }
        }

        DB::transaction(function () use ($universo, $rolId, $insertar) {
            // Borra solo las celdas administradas por la matriz (preserva otras).
            foreach ($universo as $permisoId => $accionIds) {
                if (! empty($accionIds)) {
                    RolPermiso::where('rol_id', $rolId)
                        ->where('permiso_id', $permisoId)
                        ->whereIn('accion_id', $accionIds)
                        ->delete();
                }
            }

            foreach (array_chunk($insertar, 500) as $lote) {
                RolPermiso::insert($lote);
            }
        });

        $rol = Role::find($rolId);
        $this->auditar('ACTUALIZAR_PERMISOS', $rolId,
            "Actualizó permisos del rol «{$rol?->name}»: {$totalOn} acciones permitidas.");

        return redirect()
            ->route('admin.permisos.index', ['rol' => $rolId])
            ->with('success', "Permisos del rol «{$rol?->name}» guardados correctamente.");
    }

    /**
     * Clona los permisos de un rol origen hacia un rol destino.
     */
    public function duplicar(Request $request)
    {
        $this->autorizar();

        $datos = $request->validate([
            'origen_id'  => ['required', 'integer', 'exists:roles,id'],
            'destino_id' => ['required', 'integer', 'exists:roles,id', 'different:origen_id'],
        ]);

        $origenId  = (int) $datos['origen_id'];
        $destinoId = (int) $datos['destino_id'];
        $ahora     = now();

        $copias = RolPermiso::where('rol_id', $origenId)->where('permitido', 1)
            ->get(['permiso_id', 'accion_id'])
            ->map(fn ($r) => [
                'rol_id'     => $destinoId,
                'permiso_id' => $r->permiso_id,
                'accion_id'  => $r->accion_id,
                'permitido'  => 1,
                'created_at' => $ahora,
            ])->all();

        DB::transaction(function () use ($destinoId, $copias) {
            RolPermiso::where('rol_id', $destinoId)->delete();
            foreach (array_chunk($copias, 500) as $lote) {
                RolPermiso::insert($lote);
            }
        });

        $origen  = Role::find($origenId);
        $destino = Role::find($destinoId);
        $this->auditar('DUPLICAR_PERMISOS', $destinoId,
            "Clonó permisos de «{$origen?->name}» hacia «{$destino?->name}» (".count($copias)." acciones).");

        return redirect()
            ->route('admin.permisos.index', ['rol' => $destinoId])
            ->with('success', "Permisos clonados de «{$origen?->name}» a «{$destino?->name}».");
    }

    /**
     * Exporta a CSV los permisos concedidos de un rol.
     */
    public function exportar(Request $request): StreamedResponse
    {
        $this->autorizar();

        $rolId = $request->integer('rol');
        $rol   = Role::findOrFail($rolId);

        $filas = DB::table('rol_permisos as rp')
            ->join('permisos as p', 'p.id', '=', 'rp.permiso_id')
            ->join('acciones as a', 'a.id', '=', 'rp.accion_id')
            ->where('rp.rol_id', $rolId)
            ->where('rp.permitido', 1)
            ->orderBy('p.modulo')->orderBy('p.orden')->orderBy('a.id')
            ->get(['p.modulo', 'p.vista', 'a.nombre as accion']);

        $nombre = 'permisos_'.Str::slug($rol->name).'_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($rol, $filas) {
            $out = fopen('php://output', 'w');
            fprintf($out, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel
            fputcsv($out, ['Rol', 'Módulo', 'Vista', 'Acción permitida']);
            foreach ($filas as $f) {
                fputcsv($out, [$rol->name, ucfirst($f->modulo), $f->vista, $f->accion]);
            }
            fclose($out);
        }, $nombre, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Construye la tabla comparativa entre dos roles.
     */
    private function construirComparacion(int $aId, int $bId, array $config, $accionIdPorNombre): ?array
    {
        $rolA = Role::find($aId);
        $rolB = Role::find($bId);
        if (! $rolA || ! $rolB) {
            return null;
        }

        $cargar = function (int $rolId) {
            $set = [];
            foreach (RolPermiso::where('rol_id', $rolId)->where('permitido', 1)->get(['permiso_id', 'accion_id']) as $rp) {
                $set[$rp->permiso_id][$rp->accion_id] = true;
            }
            return $set;
        };
        $setA = $cargar($aId);
        $setB = $cargar($bId);

        $permisos = Permiso::where('estado', 1)->orderBy('orden')->get()->groupBy('modulo');

        $filas = [];
        foreach ($config['modulos'] as $moduloKey => $moduloCfg) {
            $columnaIds = collect($config['matriz'][$moduloKey] ?? ['Ver', 'Crear', 'Editar', 'Eliminar'])
                ->map(fn ($n) => $accionIdPorNombre[$n] ?? null)->filter()->values()->all();
            $totalCols = count($columnaIds);

            foreach (($permisos[$moduloKey] ?? collect()) as $permiso) {
                $a = 0; $b = 0;
                foreach ($columnaIds as $aid) {
                    if (isset($setA[$permiso->id][$aid])) $a++;
                    if (isset($setB[$permiso->id][$aid])) $b++;
                }
                if ($a === 0 && $b === 0) {
                    continue; // omitir vistas sin permisos en ninguno de los dos
                }
                $filas[] = [
                    'modulo' => $moduloCfg['label'],
                    'vista'  => $permiso->vista,
                    'a'      => $a,
                    'b'      => $b,
                    'total'  => $totalCols,
                    'difiere' => $a !== $b,
                ];
            }
        }

        return [
            'rolA'  => $rolA,
            'rolB'  => $rolB,
            'filas' => $filas,
        ];
    }

    /** Registra un evento en auditoria_permisos. */
    private function auditar(string $accion, ?int $rolId, string $descripcion): void
    {
        try {
            AuditoriaPermiso::create([
                'usuario_id'  => auth()->id(),
                'rol_id'      => $rolId,
                'accion'      => $accion,
                'descripcion' => $descripcion,
                'fecha'       => now(),
                'ip'          => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
