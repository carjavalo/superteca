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
        'dispensacion'   => ['Ver', 'Crear', 'Editar', 'Eliminar', 'Anular', 'Imprimir'],
        'calidad'        => ['Ver', 'Crear', 'Editar', 'Eliminar', 'Aprobar', 'Cerrar', 'Investigar', 'Exportar'],
        'reportes'       => ['Ver', 'Imprimir', 'Exportar', 'Exportar PDF', 'Exportar Excel', 'Exportar CSV', 'Programar'],
        'configuracion'  => ['Ver', 'Crear', 'Editar', 'Eliminar'],
        'administracion' => ['Ver', 'Crear', 'Editar', 'Eliminar', 'Activar', 'Descargar'],
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
                'Carro de medicación',
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
