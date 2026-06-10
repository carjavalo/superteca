<?php

/*
|--------------------------------------------------------------------------
| Catálogo RBAC — Centro de Gestión de Permisos
|--------------------------------------------------------------------------
| Fuente única de verdad de todos los módulos, vistas y acciones del
| aplicativo. El controlador sincroniza (de forma ADITIVA, sin destruir
| datos) este catálogo contra las tablas `permisos` y `acciones` cada vez
| que se abre la vista de Gestión de Permisos.
|
| ➜ Para registrar una nueva vista basta con agregarla aquí: aparecerá
|   automáticamente en Gestión de Permisos sin tener que programar nada más.
*/

return [

    // Acciones globales disponibles en el sistema (tabla `acciones`).
    'acciones' => [
        'Ver', 'Crear', 'Editar', 'Eliminar', 'Aprobar', 'Cerrar', 'Validar',
        'Imprimir', 'Exportar', 'Anular', 'Exportar PDF', 'Exportar Excel',
        'Exportar CSV', 'Investigar', 'Programar', 'Cargar', 'Ejecutar',
        'Activar', 'Descargar',
    ],

    // Qué columnas de acción se muestran en la matriz para cada módulo.
    // Los nombres deben existir en la lista `acciones` de arriba.
    'matriz' => [
        'maestros'       => ['Ver', 'Crear', 'Editar', 'Eliminar', 'Imprimir', 'Exportar'],
        'inventario'     => ['Ver', 'Crear', 'Editar', 'Eliminar', 'Aprobar', 'Anular', 'Imprimir', 'Exportar'],
        'produccion'     => ['Ver', 'Crear', 'Editar', 'Eliminar', 'Aprobar', 'Anular', 'Cerrar', 'Imprimir'],
        'dispensacion'   => ['Ver', 'Crear', 'Editar', 'Eliminar', 'Aprobar', 'Anular', 'Imprimir'],
        'calidad'        => ['Ver', 'Crear', 'Editar', 'Eliminar', 'Aprobar', 'Cerrar', 'Investigar', 'Exportar'],
        'reportes'       => ['Ver', 'Imprimir', 'Exportar', 'Exportar PDF', 'Exportar Excel', 'Exportar CSV', 'Programar'],
        'configuracion'  => ['Ver', 'Crear', 'Editar', 'Eliminar'],
        'administracion' => ['Ver', 'Crear', 'Editar', 'Eliminar', 'Activar', 'Descargar'],
    ],

    /*
    | Mapa de RUTAS → VISTA. El middleware `permiso` exige que el rol tenga la
    | acción «Ver» sobre la vista indicada para poder acceder a cualquier ruta
    | cuyo nombre empiece por el prefijo. Se aplica el prefijo más largo que
    | coincida. Las rutas que no aparezcan aquí quedan permitidas (ej. perfil).
    */
    'rutas' => [
        'admin.reportes.insumos'          => 'Consumos generales',
        'admin.reportes.trazabilidad'     => 'Trazabilidad de lotes',
        'admin.medicamentos'              => 'Medicamentos',
        'admin.presentaciones'            => 'Presentaciones',
        'admin.laboratorios'              => 'Laboratorios',
        'admin.proveedores'               => 'Proveedores',
        'admin.formas_farmaceuticas'      => 'Formas farmacéuticas',
        'admin.vias_administracion'       => 'Vías de administración',
        'admin.unidades_medida'           => 'Unidades de medida',
        'admin.entradas'                  => 'Entradas',
        'admin.salidas'                   => 'Salidas',
        'admin.inventarios'               => 'Inventario por lotes',
        'admin.ajustes'                   => 'Ajustes',
        'admin.traslados'                 => 'Traslados',
        'admin.kardex'                    => 'Kardex',
        'admin.preparaciones'             => 'Preparaciones',
        'admin.formulas'                  => 'Fórmulas magistrales',
        'admin.mezclas'                   => 'Mezclas IV',
        'admin.reempaques'                => 'Reempaques',
        'admin.dispensacion.entregas'     => 'Entregas',
        'admin.dispensacion.pacientes'    => 'Historial paciente',
        'admin.dispensacion.validaciones' => 'Validación farmacéutica',
        'admin.calidad.cadena-frio'       => 'Cadena de frío',
        'admin.calidad.controles'         => 'Controles de calidad',
        'admin.calidad.incidentes'        => 'Incidentes',
        'admin.users'                     => 'Gestión de usuarios',
        'admin.roles'                     => 'Gestión de roles',
        'admin.permisos'                  => 'Gestión de permisos',
        'admin.tipos_entrada'             => 'Gestor Tipos de Entrada',
    ],

    /*
    | Acción exigida según el ÚLTIMO segmento del nombre de la ruta. El middleware
    | deriva la acción (Crear, Editar, Eliminar, Exportar, Aprobar, Anular…) y exige
    | ese permiso específico sobre la vista. Cualquier sufijo no listado se trata
    | como «Ver» (no bloquea de más). Si la acción no es configurable para el módulo
    | de la vista, también se degrada a «Ver» (evita bloqueos imposibles de conceder).
    */
    'acciones_ruta' => [
        // Ver (lectura / navegación)
        'index' => 'Ver', 'show' => 'Ver', 'lotes' => 'Ver', 'lote' => 'Ver',
        'analytics' => 'Ver', 'bandeja' => 'Ver', 'buscar' => 'Ver', 'autocomplete' => 'Ver',
        'recall' => 'Ver', 'simulador' => 'Ver', 'trazabilidad' => 'Ver', 'paciente' => 'Ver',
        'incidente' => 'Ver', 'equipo' => 'Ver', 'preparacion' => 'Ver', 'mezcla' => 'Ver',
        'reempaque' => 'Ver',
        // Crear (Nuevo)
        'create' => 'Crear', 'store' => 'Crear',
        // Editar
        'edit' => 'Editar', 'update' => 'Editar', 'estado' => 'Editar', 'detalle' => 'Editar',
        'resultado' => 'Editar', 'toggle' => 'Editar',
        // Eliminar
        'destroy' => 'Eliminar', 'eliminar' => 'Eliminar',
        // Exportar
        'exportar' => 'Exportar', 'export' => 'Exportar',
        // Aprobar
        'confirm' => 'Aprobar', 'aprobar' => 'Aprobar', 'approve' => 'Aprobar', 'liberar' => 'Aprobar',
        // Anular
        'annul' => 'Anular', 'anular' => 'Anular', 'rechazar' => 'Anular', 'cancelar' => 'Anular',
        // Cerrar / Validar
        'cerrar' => 'Cerrar', 'validar' => 'Validar',
    ],

    // Módulos y sus vistas (tabla `permisos`). El orden define la presentación.
    'modulos' => [
        'maestros' => [
            'label' => 'Maestros',
            'icono' => '🗂️',
            'vistas' => [
                'Medicamentos', 'Principios activos', 'Presentaciones', 'Laboratorios',
                'Proveedores', 'Pacientes', 'Servicios', 'Médicos', 'Fórmulas maestras',
                'Equipos cadena frío', 'Ubicaciones', 'Parámetros',
                'Formas farmacéuticas', 'Vías de administración', 'Unidades de medida',
            ],
        ],
        'inventario' => [
            'label' => 'Inventario',
            'icono' => '📦',
            'vistas' => [
                'Inventario por lotes', 'Entradas', 'Salidas', 'Ajustes', 'Traslados',
                'Kardex', 'Movimientos', 'Cadena de frío', 'Alertas cadena frío',
            ],
        ],
        'produccion' => [
            'label' => 'Producción',
            'icono' => '⚗️',
            'vistas' => [
                'Mezclas IV', 'Preparaciones', 'Reempaques', 'Nutrición parenteral',
                'Fórmulas magistrales', 'Lote de producción', 'Control de proceso',
            ],
        ],
        'dispensacion' => [
            'label' => 'Dispensación',
            'icono' => '💊',
            'vistas' => [
                'Órdenes médicas', 'Entregas', 'Devoluciones', 'Historial paciente',
                'Carro de medicación', 'Validación farmacéutica',
            ],
        ],
        'calidad' => [
            'label' => 'Calidad',
            'icono' => '✅',
            'vistas' => [
                'Incidentes', 'Acciones correctivas', 'Acciones preventivas',
                'Controles de calidad', 'No conformidades', 'Auditorías internas',
                'BPE — Buenas prácticas', 'Evidencias',
            ],
        ],
        'reportes' => [
            'label' => 'Reportes',
            'icono' => '📊',
            'vistas' => [
                'Consumos generales', 'Consumos por medicamento', 'Consumos por servicio',
                'Consumos por paciente', 'Consumos por lote', 'Consumos por laboratorio',
                'Costos de consumo', 'Consumos producción', 'Trazabilidad de lotes',
                'Trazabilidad paciente', 'Trazabilidad producción', 'Trazabilidad calidad',
                'Trazabilidad cadena frío', 'Trazabilidad completa', 'Recall INVIMA',
                'Proyección de consumo', 'Reporte financiero',
            ],
        ],
        'configuracion' => [
            'label' => 'Configuración',
            'icono' => '⚙️',
            'vistas' => [
                'Gestión de usuarios', 'Gestión de roles', 'Gestión de permisos',
                'Gestor Tipos de Entrada',
                'Configuración general', 'Parámetros generales', 'Integraciones',
                'Copias de seguridad', 'Auditoría sistema',
            ],
        ],
        'administracion' => [
            'label' => 'Administración',
            'icono' => '🛡️',
            'vistas' => [
                'Panel principal', 'Indicadores sistema', 'Logs de acceso', 'Sesiones activas',
            ],
        ],
    ],
];
