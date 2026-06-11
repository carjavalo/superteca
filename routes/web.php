<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Ruta temporal para ejecutar migraciones en cPanel
Route::get('/run-migrations-cpanel', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return '<pre>Migraciones ejecutadas exitosamente:' . PHP_EOL . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return '<pre>Error al migrar: ' . $e->getMessage() . '</pre>';
    }
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'permiso'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/profile/password', [ProfileController::class, 'showChangePassword'])->name('password.change');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('password.change.update');

    // Gestión de usuarios
    Route::get('/admin/usuarios', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/usuarios', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/usuarios/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/usuarios/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

    // Gestión de Laboratorios
    Route::get('/admin/laboratorios', [\App\Http\Controllers\Admin\LaboratorioController::class, 'index'])->name('admin.laboratorios.index');
    Route::post('/admin/laboratorios', [\App\Http\Controllers\Admin\LaboratorioController::class, 'store'])->name('admin.laboratorios.store');
    Route::put('/admin/laboratorios/{laboratorio}', [\App\Http\Controllers\Admin\LaboratorioController::class, 'update'])->name('admin.laboratorios.update');
    Route::delete('/admin/laboratorios/{laboratorio}', [\App\Http\Controllers\Admin\LaboratorioController::class, 'destroy'])->name('admin.laboratorios.destroy');

    // Gestión de medicamentos
    Route::get('/admin/medicamentos', [MedicamentoController::class, 'index'])->name('admin.medicamentos.index');
    Route::post('/admin/medicamentos', [MedicamentoController::class, 'store'])->name('admin.medicamentos.store');
    Route::put('/admin/medicamentos/{medicamento}', [MedicamentoController::class, 'update'])->name('admin.medicamentos.update');
    Route::delete('/admin/medicamentos/{medicamento}', [MedicamentoController::class, 'destroy'])->name('admin.medicamentos.destroy');

    // Gestión de Presentaciones
    Route::get('/admin/presentaciones', [\App\Http\Controllers\PresentacionController::class, 'index'])->name('admin.presentaciones.index');
    Route::post('/admin/presentaciones', [\App\Http\Controllers\PresentacionController::class, 'store'])->name('admin.presentaciones.store');
    Route::put('/admin/presentaciones/{presentacion}', [\App\Http\Controllers\PresentacionController::class, 'update'])->name('admin.presentaciones.update');
    Route::delete('/admin/presentaciones/{presentacion}', [\App\Http\Controllers\PresentacionController::class, 'destroy'])->name('admin.presentaciones.destroy');

    // Gestión de Proveedores
    Route::get('/admin/proveedores', [\App\Http\Controllers\Admin\ProveedorController::class, 'index'])->name('admin.proveedores.index');
    Route::get('/admin/proveedores/{proveedor}', [\App\Http\Controllers\Admin\ProveedorController::class, 'show'])->name('admin.proveedores.show');
    Route::post('/admin/proveedores', [\App\Http\Controllers\Admin\ProveedorController::class, 'store'])->name('admin.proveedores.store');
    Route::put('/admin/proveedores/{proveedor}', [\App\Http\Controllers\Admin\ProveedorController::class, 'update'])->name('admin.proveedores.update');
    Route::delete('/admin/proveedores/{proveedor}', [\App\Http\Controllers\Admin\ProveedorController::class, 'destroy'])->name('admin.proveedores.destroy');

    // Gestión de Formas Farmacéuticas
    Route::get('/admin/formas-farmaceuticas', [\App\Http\Controllers\Admin\FormaFarmaceuticaController::class, 'index'])->name('admin.formas_farmaceuticas.index');
    Route::get('/admin/formas-farmaceuticas/{formas_farmaceutica}', [\App\Http\Controllers\Admin\FormaFarmaceuticaController::class, 'show'])->name('admin.formas_farmaceuticas.show');
    Route::post('/admin/formas-farmaceuticas', [\App\Http\Controllers\Admin\FormaFarmaceuticaController::class, 'store'])->name('admin.formas_farmaceuticas.store');
    Route::put('/admin/formas-farmaceuticas/{formas_farmaceutica}', [\App\Http\Controllers\Admin\FormaFarmaceuticaController::class, 'update'])->name('admin.formas_farmaceuticas.update');
    Route::delete('/admin/formas-farmaceuticas/{formas_farmaceutica}', [\App\Http\Controllers\Admin\FormaFarmaceuticaController::class, 'destroy'])->name('admin.formas_farmaceuticas.destroy');

    // Gestión de Unidades de Medida
    Route::get('/admin/unidades-medida', [\App\Http\Controllers\Admin\UnidadMedidaController::class, 'index'])->name('admin.unidades_medida.index');
    Route::get('/admin/unidades-medida/{unidad_medida}', [\App\Http\Controllers\Admin\UnidadMedidaController::class, 'show'])->name('admin.unidades_medida.show');
    Route::post('/admin/unidades-medida', [\App\Http\Controllers\Admin\UnidadMedidaController::class, 'store'])->name('admin.unidades_medida.store');
    Route::put('/admin/unidades-medida/{unidad_medida}', [\App\Http\Controllers\Admin\UnidadMedidaController::class, 'update'])->name('admin.unidades_medida.update');
    Route::delete('/admin/unidades-medida/{unidad_medida}', [\App\Http\Controllers\Admin\UnidadMedidaController::class, 'destroy'])->name('admin.unidades_medida.destroy');

    // Gestión de Vías de Administración
    Route::get('/admin/vias-administracion', [\App\Http\Controllers\Admin\ViaAdministracionController::class, 'index'])->name('admin.vias_administracion.index');
    Route::get('/admin/vias-administracion/{vias_administracion}', [\App\Http\Controllers\Admin\ViaAdministracionController::class, 'show'])->name('admin.vias_administracion.show');
    Route::post('/admin/vias-administracion', [\App\Http\Controllers\Admin\ViaAdministracionController::class, 'store'])->name('admin.vias_administracion.store');
    Route::put('/admin/vias-administracion/{vias_administracion}', [\App\Http\Controllers\Admin\ViaAdministracionController::class, 'update'])->name('admin.vias_administracion.update');
    Route::delete('/admin/vias-administracion/{vias_administracion}', [\App\Http\Controllers\Admin\ViaAdministracionController::class, 'destroy'])->name('admin.vias_administracion.destroy');

    // Gestión de Inventario (Kardex)
    Route::get('/admin/inventario/lotes', [\App\Http\Controllers\InventarioController::class, 'lotes'])->name('admin.inventarios.lotes');

    // Entradas de Inventario
    Route::get('/admin/inventario/entradas', [\App\Http\Controllers\Admin\EntradaController::class, 'index'])->name('admin.entradas.index');
    Route::get('/admin/inventario/entradas/crear', [\App\Http\Controllers\Admin\EntradaController::class, 'create'])->name('admin.entradas.create');
    Route::get('/admin/inventario/entradas/buscar-paciente', [\App\Http\Controllers\Admin\EntradaController::class, 'buscarPaciente'])->name('admin.entradas.buscar_paciente');
    Route::post('/admin/inventario/entradas', [\App\Http\Controllers\Admin\EntradaController::class, 'store'])->name('admin.entradas.store');
    Route::get('/admin/inventario/entradas/{entrada}', [\App\Http\Controllers\Admin\EntradaController::class, 'show'])->name('admin.entradas.show');
    Route::get('/admin/inventario/entradas/{entrada}/editar', [\App\Http\Controllers\Admin\EntradaController::class, 'edit'])->name('admin.entradas.edit');
    Route::put('/admin/inventario/entradas/{entrada}', [\App\Http\Controllers\Admin\EntradaController::class, 'update'])->name('admin.entradas.update');
    Route::post('/admin/inventario/entradas/{entrada}/confirmar', [\App\Http\Controllers\Admin\EntradaController::class, 'confirm'])->name('admin.entradas.confirm');
    Route::post('/admin/inventario/entradas/{entrada}/anular', [\App\Http\Controllers\Admin\EntradaController::class, 'annul'])->name('admin.entradas.annul');
    Route::delete('/admin/inventario/entradas/{entrada}', [\App\Http\Controllers\Admin\EntradaController::class, 'destroy'])->name('admin.entradas.destroy');

    // Salidas de Inventario
    Route::get('/admin/inventario/salidas', [\App\Http\Controllers\Admin\SalidaController::class, 'index'])->name('admin.salidas.index');
    Route::get('/admin/inventario/salidas/crear', [\App\Http\Controllers\Admin\SalidaController::class, 'create'])->name('admin.salidas.create');
    Route::post('/admin/inventario/salidas', [\App\Http\Controllers\Admin\SalidaController::class, 'store'])->name('admin.salidas.store');
    Route::get('/admin/inventario/salidas/{salida}', [\App\Http\Controllers\Admin\SalidaController::class, 'show'])->name('admin.salidas.show');
    Route::get('/admin/inventario/salidas/{salida}/editar', [\App\Http\Controllers\Admin\SalidaController::class, 'edit'])->name('admin.salidas.edit');
    Route::put('/admin/inventario/salidas/{salida}', [\App\Http\Controllers\Admin\SalidaController::class, 'update'])->name('admin.salidas.update');
    Route::post('/admin/inventario/salidas/{salida}/confirmar', [\App\Http\Controllers\Admin\SalidaController::class, 'confirm'])->name('admin.salidas.confirm');
    Route::post('/admin/inventario/salidas/{salida}/anular', [\App\Http\Controllers\Admin\SalidaController::class, 'annul'])->name('admin.salidas.annul');
    Route::delete('/admin/inventario/salidas/{salida}', [\App\Http\Controllers\Admin\SalidaController::class, 'destroy'])->name('admin.salidas.destroy');

    // Ajustes de Inventario
    Route::get('/admin/inventario/ajustes', [\App\Http\Controllers\Admin\AjusteInventarioController::class, 'index'])->name('admin.ajustes.index');
    Route::get('/admin/inventario/ajustes/crear', [\App\Http\Controllers\Admin\AjusteInventarioController::class, 'create'])->name('admin.ajustes.create');
    Route::post('/admin/inventario/ajustes', [\App\Http\Controllers\Admin\AjusteInventarioController::class, 'store'])->name('admin.ajustes.store');
    Route::get('/admin/inventario/ajustes/{ajuste}', [\App\Http\Controllers\Admin\AjusteInventarioController::class, 'show'])->name('admin.ajustes.show');
    Route::post('/admin/inventario/ajustes/{ajuste}/aprobar', [\App\Http\Controllers\Admin\AjusteInventarioController::class, 'approve'])->name('admin.ajustes.approve');
    Route::post('/admin/inventario/ajustes/{ajuste}/anular', [\App\Http\Controllers\Admin\AjusteInventarioController::class, 'annul'])->name('admin.ajustes.annul');
    Route::delete('/admin/inventario/ajustes/{ajuste}', [\App\Http\Controllers\Admin\AjusteInventarioController::class, 'destroy'])->name('admin.ajustes.destroy');
    Route::post('/admin/inventario/ajustes/motivos', [\App\Http\Controllers\Admin\AjusteInventarioController::class, 'storeMotivo'])->name('admin.ajustes.motivos.store');

    // Traslados de Inventario
    Route::get('/admin/inventario/traslados', [\App\Http\Controllers\Admin\TrasladoController::class, 'index'])->name('admin.traslados.index');
    Route::get('/admin/inventario/traslados/crear', [\App\Http\Controllers\Admin\TrasladoController::class, 'create'])->name('admin.traslados.create');
    Route::post('/admin/inventario/traslados', [\App\Http\Controllers\Admin\TrasladoController::class, 'store'])->name('admin.traslados.store');
    Route::get('/admin/inventario/traslados/{traslado}', [\App\Http\Controllers\Admin\TrasladoController::class, 'show'])->name('admin.traslados.show');
    Route::patch('/admin/inventario/traslados/{traslado}/aprobar', [\App\Http\Controllers\Admin\TrasladoController::class, 'aprobar'])->name('admin.traslados.aprobar');
    Route::patch('/admin/inventario/traslados/{traslado}/despachar', [\App\Http\Controllers\Admin\TrasladoController::class, 'despachar'])->name('admin.traslados.despachar');
    Route::patch('/admin/inventario/traslados/{traslado}/recibir', [\App\Http\Controllers\Admin\TrasladoController::class, 'recibir'])->name('admin.traslados.recibir');
    Route::patch('/admin/inventario/traslados/{traslado}/rechazar', [\App\Http\Controllers\Admin\TrasladoController::class, 'rechazar'])->name('admin.traslados.rechazar');
    Route::delete('/admin/inventario/traslados/{traslado}/anular', [\App\Http\Controllers\Admin\TrasladoController::class, 'anular'])->name('admin.traslados.anular');
    Route::delete('/admin/inventario/traslados/{traslado}', [\App\Http\Controllers\Admin\TrasladoController::class, 'destroy'])->name('admin.traslados.destroy');
    Route::get('/admin/inventario/traslados/lotes-disponibles', [\App\Http\Controllers\Admin\TrasladoController::class, 'lotesDisponibles'])->name('admin.traslados.lotes');

    // Kardex - Historial de movimientos
    Route::get('/admin/inventario/kardex',           [\App\Http\Controllers\Admin\KardexController::class, 'index'])->name('admin.kardex.index');
    Route::get('/admin/inventario/kardex/lote/{lote_id?}', [\App\Http\Controllers\Admin\KardexController::class, 'porLote'])->name('admin.kardex.lote');
    Route::get('/admin/inventario/kardex/analytics', [\App\Http\Controllers\Admin\KardexController::class, 'analytics'])->name('admin.kardex.analytics');

    // Producción - Fórmulas (Recetas Maestras)
    Route::get   ('/admin/produccion/formulas',                  [\App\Http\Controllers\Admin\FormulaController::class, 'index'])->name('admin.formulas.index');
    Route::get   ('/admin/produccion/formulas/crear',            [\App\Http\Controllers\Admin\FormulaController::class, 'create'])->name('admin.formulas.create');
    Route::post  ('/admin/produccion/formulas',                  [\App\Http\Controllers\Admin\FormulaController::class, 'store'])->name('admin.formulas.store');
    Route::get   ('/admin/produccion/formulas/{formula}',        [\App\Http\Controllers\Admin\FormulaController::class, 'show'])->name('admin.formulas.show');
    Route::get   ('/admin/produccion/formulas/{formula}/simulador', [\App\Http\Controllers\Admin\FormulaController::class, 'simulador'])->name('admin.formulas.simulador');
    Route::delete('/admin/produccion/formulas/{formula}',        [\App\Http\Controllers\Admin\FormulaController::class, 'destroy'])->name('admin.formulas.destroy');

    // Producción - Mezclas (Proceso de Fabricación)
    Route::get   ('/admin/produccion/mezclas',                            [\App\Http\Controllers\Admin\MezclaController::class, 'index'])->name('admin.mezclas.index');
    Route::get   ('/admin/produccion/mezclas/crear',                      [\App\Http\Controllers\Admin\MezclaController::class, 'create'])->name('admin.mezclas.create');
    Route::post  ('/admin/produccion/mezclas',                            [\App\Http\Controllers\Admin\MezclaController::class, 'store'])->name('admin.mezclas.store');
    Route::get   ('/admin/produccion/mezclas/{mezcla}',                   [\App\Http\Controllers\Admin\MezclaController::class, 'show'])->name('admin.mezclas.show');
    Route::patch ('/admin/produccion/mezclas/{mezcla}/iniciar',           [\App\Http\Controllers\Admin\MezclaController::class, 'iniciar'])->name('admin.mezclas.iniciar');
    Route::post  ('/admin/produccion/mezclas/{mezcla}/consumo',           [\App\Http\Controllers\Admin\MezclaController::class, 'registrarConsumo'])->name('admin.mezclas.consumo');
    Route::delete('/admin/produccion/mezclas/{mezcla}/consumo/{consumo}', [\App\Http\Controllers\Admin\MezclaController::class, 'eliminarConsumo'])->name('admin.mezclas.consumo.eliminar');
    Route::patch ('/admin/produccion/mezclas/{mezcla}/enviar-control',    [\App\Http\Controllers\Admin\MezclaController::class, 'enviarControl'])->name('admin.mezclas.enviarControl');
    Route::post  ('/admin/produccion/mezclas/{mezcla}/control',           [\App\Http\Controllers\Admin\MezclaController::class, 'registrarControl'])->name('admin.mezclas.control');
    Route::patch ('/admin/produccion/mezclas/{mezcla}/liberar',           [\App\Http\Controllers\Admin\MezclaController::class, 'liberar'])->name('admin.mezclas.liberar');
    Route::patch ('/admin/produccion/mezclas/{mezcla}/cancelar',          [\App\Http\Controllers\Admin\MezclaController::class, 'cancelar'])->name('admin.mezclas.cancelar');
    Route::delete('/admin/produccion/mezclas/{mezcla}',                   [\App\Http\Controllers\Admin\MezclaController::class, 'destroy'])->name('admin.mezclas.destroy');

    // Producción - Reempaques (Conversión de presentaciones)
    Route::get   ('/admin/produccion/reempaques',                                  [\App\Http\Controllers\Admin\ReempaqueController::class, 'index'])->name('admin.reempaques.index');
    Route::get   ('/admin/produccion/reempaques/crear',                            [\App\Http\Controllers\Admin\ReempaqueController::class, 'create'])->name('admin.reempaques.create');
    Route::post  ('/admin/produccion/reempaques',                                  [\App\Http\Controllers\Admin\ReempaqueController::class, 'store'])->name('admin.reempaques.store');
    Route::get   ('/admin/produccion/reempaques/{reempaque}',                      [\App\Http\Controllers\Admin\ReempaqueController::class, 'show'])->name('admin.reempaques.show');
    Route::patch ('/admin/produccion/reempaques/{reempaque}/iniciar',              [\App\Http\Controllers\Admin\ReempaqueController::class, 'iniciar'])->name('admin.reempaques.iniciar');
    Route::post  ('/admin/produccion/reempaques/{reempaque}/consumo',              [\App\Http\Controllers\Admin\ReempaqueController::class, 'registrarConsumo'])->name('admin.reempaques.consumo');
    Route::delete('/admin/produccion/reempaques/{reempaque}/consumo/{consumo}',    [\App\Http\Controllers\Admin\ReempaqueController::class, 'eliminarConsumo'])->name('admin.reempaques.consumo.eliminar');
    Route::patch ('/admin/produccion/reempaques/{reempaque}/enviar-control',       [\App\Http\Controllers\Admin\ReempaqueController::class, 'enviarControl'])->name('admin.reempaques.enviarControl');
    Route::post  ('/admin/produccion/reempaques/{reempaque}/control',              [\App\Http\Controllers\Admin\ReempaqueController::class, 'registrarControl'])->name('admin.reempaques.control');
    Route::patch ('/admin/produccion/reempaques/{reempaque}/liberar',              [\App\Http\Controllers\Admin\ReempaqueController::class, 'liberar'])->name('admin.reempaques.liberar');
    Route::patch ('/admin/produccion/reempaques/{reempaque}/anular',               [\App\Http\Controllers\Admin\ReempaqueController::class, 'anular'])->name('admin.reempaques.anular');
    Route::delete('/admin/produccion/reempaques/{reempaque}',                      [\App\Http\Controllers\Admin\ReempaqueController::class, 'destroy'])->name('admin.reempaques.destroy');

    // Producción - Preparaciones (Central de Mezclas)
    Route::get   ('/admin/produccion/preparaciones',                           [\App\Http\Controllers\Admin\PreparacionController::class, 'index'])->name('admin.preparaciones.index');
    Route::get   ('/admin/produccion/preparaciones/crear',                     [\App\Http\Controllers\Admin\PreparacionController::class, 'create'])->name('admin.preparaciones.create');
    Route::post  ('/admin/produccion/preparaciones',                           [\App\Http\Controllers\Admin\PreparacionController::class, 'store'])->name('admin.preparaciones.store');
    Route::get   ('/admin/produccion/preparaciones/{preparacion}',             [\App\Http\Controllers\Admin\PreparacionController::class, 'show'])->name('admin.preparaciones.show');
    Route::patch ('/admin/produccion/preparaciones/{preparacion}/iniciar',     [\App\Http\Controllers\Admin\PreparacionController::class, 'iniciar'])->name('admin.preparaciones.iniciar');
    Route::post  ('/admin/produccion/preparaciones/{preparacion}/consumo',     [\App\Http\Controllers\Admin\PreparacionController::class, 'registrarConsumo'])->name('admin.preparaciones.consumo');
    Route::delete('/admin/produccion/preparaciones/{preparacion}/consumo/{consumo}', [\App\Http\Controllers\Admin\PreparacionController::class, 'eliminarConsumo'])->name('admin.preparaciones.consumo.eliminar');
    Route::patch ('/admin/produccion/preparaciones/{preparacion}/enviar-control', [\App\Http\Controllers\Admin\PreparacionController::class, 'enviarControl'])->name('admin.preparaciones.enviarControl');
    Route::post  ('/admin/produccion/preparaciones/{preparacion}/control',     [\App\Http\Controllers\Admin\PreparacionController::class, 'registrarControl'])->name('admin.preparaciones.control');
    Route::patch ('/admin/produccion/preparaciones/{preparacion}/liberar',     [\App\Http\Controllers\Admin\PreparacionController::class, 'liberar'])->name('admin.preparaciones.liberar');
    Route::post  ('/admin/produccion/preparaciones/{preparacion}/entregar',    [\App\Http\Controllers\Admin\PreparacionController::class, 'entregar'])->name('admin.preparaciones.entregar');
    Route::patch ('/admin/produccion/preparaciones/{preparacion}/anular',      [\App\Http\Controllers\Admin\PreparacionController::class, 'anular'])->name('admin.preparaciones.anular');
    Route::delete('/admin/produccion/preparaciones/{preparacion}',             [\App\Http\Controllers\Admin\PreparacionController::class, 'destroy'])->name('admin.preparaciones.destroy');

    // Reportes - Registro de actividad (solo Super Admin)
    Route::get('/admin/reportes/registros', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('admin.reportes.registros');
    Route::get('/admin/reportes/registros/{log}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('admin.reportes.registros.show');

    // Reportes - Insumos / Consumos (Data Mart de consulta)
    Route::get('/admin/reportes/insumos',          [\App\Http\Controllers\Admin\ReporteInsumosController::class, 'index'])->name('admin.reportes.insumos');
    Route::get('/admin/reportes/insumos/exportar', [\App\Http\Controllers\Admin\ReporteInsumosController::class, 'exportar'])->name('admin.reportes.insumos.exportar');

    // Reportes - Trazabilidad 360°
    Route::get('/admin/reportes/trazabilidad',                          [\App\Http\Controllers\Admin\TrazabilidadController::class, 'index'])->name('admin.reportes.trazabilidad');
    Route::get('/admin/reportes/trazabilidad/buscar',                   [\App\Http\Controllers\Admin\TrazabilidadController::class, 'buscar'])->name('admin.reportes.trazabilidad.buscar');
    Route::get('/admin/reportes/trazabilidad/autocomplete',             [\App\Http\Controllers\Admin\TrazabilidadController::class, 'autocomplete'])->name('admin.reportes.trazabilidad.autocomplete');
    Route::get('/admin/reportes/trazabilidad/recall',                   [\App\Http\Controllers\Admin\TrazabilidadController::class, 'recall'])->name('admin.reportes.trazabilidad.recall');
    Route::get('/admin/reportes/trazabilidad/lote/{id}',                [\App\Http\Controllers\Admin\TrazabilidadController::class, 'lote'])->name('admin.reportes.trazabilidad.lote');
    Route::get('/admin/reportes/trazabilidad/paciente/{id}',            [\App\Http\Controllers\Admin\TrazabilidadController::class, 'paciente'])->name('admin.reportes.trazabilidad.paciente');
    Route::get('/admin/reportes/trazabilidad/incidente/{id}',           [\App\Http\Controllers\Admin\TrazabilidadController::class, 'incidente'])->name('admin.reportes.trazabilidad.incidente');
    Route::get('/admin/reportes/trazabilidad/equipo/{id}',              [\App\Http\Controllers\Admin\TrazabilidadController::class, 'equipo'])->name('admin.reportes.trazabilidad.equipo');
    Route::get('/admin/reportes/trazabilidad/preparacion/{id}',         [\App\Http\Controllers\Admin\TrazabilidadController::class, 'preparacion'])->name('admin.reportes.trazabilidad.preparacion');
    Route::get('/admin/reportes/trazabilidad/mezcla/{id}',              [\App\Http\Controllers\Admin\TrazabilidadController::class, 'mezcla'])->name('admin.reportes.trazabilidad.mezcla');
    Route::get('/admin/reportes/trazabilidad/reempaque/{id}',           [\App\Http\Controllers\Admin\TrazabilidadController::class, 'reempaque'])->name('admin.reportes.trazabilidad.reempaque');

    // Dispensación - Entregas
    Route::get   ('/admin/dispensacion/entregas',                   [\App\Http\Controllers\Admin\DispensacionEntregaController::class, 'index'])->name('admin.dispensacion.entregas.index');
    Route::get   ('/admin/dispensacion/entregas/crear',             [\App\Http\Controllers\Admin\DispensacionEntregaController::class, 'create'])->name('admin.dispensacion.entregas.create');
    Route::post  ('/admin/dispensacion/entregas',                   [\App\Http\Controllers\Admin\DispensacionEntregaController::class, 'store'])->name('admin.dispensacion.entregas.store');
    Route::get   ('/admin/dispensacion/entregas/{entrega}',         [\App\Http\Controllers\Admin\DispensacionEntregaController::class, 'show'])->name('admin.dispensacion.entregas.show');
    Route::patch ('/admin/dispensacion/entregas/{entrega}/entregar',[\App\Http\Controllers\Admin\DispensacionEntregaController::class, 'entregar'])->name('admin.dispensacion.entregas.entregar');
    Route::post  ('/admin/dispensacion/entregas/{entrega}/recibir', [\App\Http\Controllers\Admin\DispensacionEntregaController::class, 'recibir'])->name('admin.dispensacion.entregas.recibir');
    Route::post  ('/admin/dispensacion/entregas/{entrega}/devolver',[\App\Http\Controllers\Admin\DispensacionEntregaController::class, 'devolver'])->name('admin.dispensacion.entregas.devolver');
    Route::patch ('/admin/dispensacion/entregas/{entrega}/anular',  [\App\Http\Controllers\Admin\DispensacionEntregaController::class, 'anular'])->name('admin.dispensacion.entregas.anular');
    Route::delete('/admin/dispensacion/entregas/{entrega}',         [\App\Http\Controllers\Admin\DispensacionEntregaController::class, 'destroy'])->name('admin.dispensacion.entregas.destroy');

    // Dispensación - Pacientes (ficha clínica farmacéutica)
    Route::get   ('/admin/dispensacion/pacientes',                              [\App\Http\Controllers\Admin\PacienteClinicoController::class, 'index'])->name('admin.dispensacion.pacientes.index');
    Route::get   ('/admin/dispensacion/pacientes/crear',                        [\App\Http\Controllers\Admin\PacienteClinicoController::class, 'create'])->name('admin.dispensacion.pacientes.create');
    Route::post  ('/admin/dispensacion/pacientes',                              [\App\Http\Controllers\Admin\PacienteClinicoController::class, 'store'])->name('admin.dispensacion.pacientes.store');
    Route::get   ('/admin/dispensacion/pacientes/{paciente}',                   [\App\Http\Controllers\Admin\PacienteClinicoController::class, 'show'])->name('admin.dispensacion.pacientes.show');
    Route::put   ('/admin/dispensacion/pacientes/{paciente}',                   [\App\Http\Controllers\Admin\PacienteClinicoController::class, 'update'])->name('admin.dispensacion.pacientes.update');
    Route::delete('/admin/dispensacion/pacientes/{paciente}',                   [\App\Http\Controllers\Admin\PacienteClinicoController::class, 'destroy'])->name('admin.dispensacion.pacientes.destroy');
    Route::post  ('/admin/dispensacion/pacientes/{paciente}/alergias',          [\App\Http\Controllers\Admin\PacienteClinicoController::class, 'storeAlergia'])->name('admin.dispensacion.pacientes.alergias.store');
    Route::delete('/admin/dispensacion/pacientes/{paciente}/alergias/{alergia}',[\App\Http\Controllers\Admin\PacienteClinicoController::class, 'destroyAlergia'])->name('admin.dispensacion.pacientes.alergias.destroy');
    Route::post  ('/admin/dispensacion/pacientes/{paciente}/diagnosticos',      [\App\Http\Controllers\Admin\PacienteClinicoController::class, 'storeDiagnostico'])->name('admin.dispensacion.pacientes.diagnosticos.store');
    Route::delete('/admin/dispensacion/pacientes/{paciente}/diagnosticos/{diagnostico}',[\App\Http\Controllers\Admin\PacienteClinicoController::class, 'destroyDiagnostico'])->name('admin.dispensacion.pacientes.diagnosticos.destroy');
    Route::post  ('/admin/dispensacion/pacientes/{paciente}/prescripciones',    [\App\Http\Controllers\Admin\PacienteClinicoController::class, 'storePrescripcion'])->name('admin.dispensacion.pacientes.prescripciones.store');
    Route::patch ('/admin/dispensacion/pacientes/{paciente}/prescripciones/{prescripcion}/estado', [\App\Http\Controllers\Admin\PacienteClinicoController::class, 'cambiarEstadoPrescripcion'])->name('admin.dispensacion.pacientes.prescripciones.estado');
    Route::delete('/admin/dispensacion/pacientes/{paciente}/prescripciones/{prescripcion}',[\App\Http\Controllers\Admin\PacienteClinicoController::class, 'destroyPrescripcion'])->name('admin.dispensacion.pacientes.prescripciones.destroy');

    // Dispensación - Validación farmacéutica
    Route::get   ('/admin/dispensacion/validaciones',                 [\App\Http\Controllers\Admin\ValidacionController::class, 'index'])->name('admin.dispensacion.validaciones.index');
    Route::get   ('/admin/dispensacion/validaciones/crear',           [\App\Http\Controllers\Admin\ValidacionController::class, 'create'])->name('admin.dispensacion.validaciones.create');
    Route::post  ('/admin/dispensacion/validaciones',                 [\App\Http\Controllers\Admin\ValidacionController::class, 'store'])->name('admin.dispensacion.validaciones.store');
    Route::get   ('/admin/dispensacion/validaciones/{validacion}',    [\App\Http\Controllers\Admin\ValidacionController::class, 'show'])->name('admin.dispensacion.validaciones.show');
    Route::post  ('/admin/dispensacion/validaciones/{validacion}/aprobar', [\App\Http\Controllers\Admin\ValidacionController::class, 'aprobar'])->name('admin.dispensacion.validaciones.aprobar');
    Route::patch ('/admin/dispensacion/validaciones/{validacion}/alertas/{alerta}', [\App\Http\Controllers\Admin\ValidacionController::class, 'resolverAlerta'])->name('admin.dispensacion.validaciones.alertas.toggle');
    Route::delete('/admin/dispensacion/validaciones/{validacion}',    [\App\Http\Controllers\Admin\ValidacionController::class, 'destroy'])->name('admin.dispensacion.validaciones.destroy');

    // ===================== CALIDAD - CADENA DE FRÍO =====================
    Route::prefix('admin/calidad/cadena-frio')->name('admin.calidad.cadena-frio.')->group(function () {
        $c = \App\Http\Controllers\Admin\CadenaFrioController::class;

        Route::get('/',                    [$c, 'index'])->name('index');

        // Equipos
        Route::get('/equipos',                          [$c, 'equiposIndex'])->name('equipos.index');
        Route::post('/equipos',                         [$c, 'equiposStore'])->name('equipos.store');
        Route::get('/equipos/{equipo}',                 [$c, 'equiposShow'])->name('equipos.show');
        Route::put('/equipos/{equipo}',                 [$c, 'equiposUpdate'])->name('equipos.update');
        Route::delete('/equipos/{equipo}',              [$c, 'equiposDestroy'])->name('equipos.destroy');

        // Sensores
        Route::post('/equipos/{equipo}/sensores',       [$c, 'sensoresStore'])->name('sensores.store');
        Route::delete('/sensores/{sensor}',             [$c, 'sensoresDestroy'])->name('sensores.destroy');

        // Monitoreo
        Route::get('/monitoreo',                        [$c, 'monitoreoIndex'])->name('monitoreo.index');
        Route::post('/monitoreo',                       [$c, 'monitoreoStore'])->name('monitoreo.store');

        // Alertas
        Route::get('/alertas',                          [$c, 'alertasIndex'])->name('alertas.index');
        Route::get('/alertas/{alerta}',                 [$c, 'alertasShow'])->name('alertas.show');
        Route::post('/alertas/{alerta}/cerrar',         [$c, 'alertasCerrar'])->name('alertas.cerrar');
        Route::patch('/afectaciones/{afectacion}',      [$c, 'afectacionUpdate'])->name('afectaciones.update');

        // Lotes en equipos
        Route::get('/lotes',                            [$c, 'lotesIndex'])->name('lotes.index');
        Route::post('/lotes',                           [$c, 'lotesStore'])->name('lotes.store');
        Route::post('/lotes/{lote}/salida',             [$c, 'lotesSalida'])->name('lotes.salida');
    });

    // ===================== CALIDAD - CONTROLES =====================
    Route::prefix('admin/calidad/controles')->name('admin.calidad.controles.')->group(function () {
        $c = \App\Http\Controllers\Admin\ControlCalidadController::class;

        Route::get('/',                  [$c, 'index'])->name('index');
        Route::get('/bandeja',           [$c, 'bandeja'])->name('bandeja');
        Route::get('/crear',             [$c, 'create'])->name('create');
        Route::post('/',                 [$c, 'store'])->name('store');

        // Parámetros (catálogo) - antes de /{control}
        Route::get('/parametros/listar',       [$c, 'parametrosIndex'])->name('parametros.index');
        Route::post('/parametros',             [$c, 'parametrosStore'])->name('parametros.store');
        Route::put('/parametros/{parametro}',  [$c, 'parametrosUpdate'])->name('parametros.update');
        Route::delete('/parametros/{parametro}', [$c, 'parametrosDestroy'])->name('parametros.destroy');

        // Trazabilidad por lote
        Route::get('/trazabilidad/{lote}',     [$c, 'trazabilidad'])->name('trazabilidad');

        // Acciones correctivas
        Route::patch('/acciones/{accion}',     [$c, 'accionUpdate'])->name('acciones.update');

        // Evidencias
        Route::delete('/evidencias/{evidencia}', [$c, 'evidenciaDestroy'])->name('evidencias.destroy');

        // Detalle del control - rutas con {control} al final
        Route::get('/{control}',         [$c, 'show'])->name('show');
        Route::patch('/{control}/resultado', [$c, 'actualizarResultado'])->name('resultado');
        Route::delete('/{control}',      [$c, 'destroy'])->name('destroy');
        Route::post('/{control}/acciones',     [$c, 'accionStore'])->name('acciones.store');
        Route::post('/{control}/evidencias',   [$c, 'evidenciaStore'])->name('evidencias.store');
    });

    // ===================== CALIDAD - INCIDENTES =====================
    Route::prefix('admin/calidad/incidentes')->name('admin.calidad.incidentes.')->group(function () {
        $c = \App\Http\Controllers\Admin\IncidenteController::class;

        Route::get('/',          [$c, 'index'])->name('index');
        Route::get('/bandeja',   [$c, 'bandeja'])->name('bandeja');
        Route::get('/crear',     [$c, 'create'])->name('create');
        Route::post('/',         [$c, 'store'])->name('store');

        // Acciones (kanban CAPA) - antes de {incidente}
        Route::patch('/acciones/{accion}', [$c, 'accionUpdate'])->name('acciones.update');

        // Evidencias - antes de {incidente}
        Route::delete('/evidencias/{evidencia}', [$c, 'evidenciaDestroy'])->name('evidencias.destroy');

        // Detalle y operaciones sobre el incidente
        Route::get('/{incidente}',                    [$c, 'show'])->name('show');
        Route::patch('/{incidente}/estado',           [$c, 'actualizarEstado'])->name('estado');
        Route::patch('/{incidente}/detalle',          [$c, 'detalleUpdate'])->name('detalle');
        Route::post('/{incidente}/acciones',          [$c, 'accionStore'])->name('acciones.store');
        Route::post('/{incidente}/evidencias',        [$c, 'evidenciaStore'])->name('evidencias.store');
        Route::post('/{incidente}/seguimiento',       [$c, 'seguimientoStore'])->name('seguimiento');
        Route::post('/{incidente}/bloquear-lote',     [$c, 'bloquearLote'])->name('bloquearLote');
        Route::delete('/{incidente}',                 [$c, 'destroy'])->name('destroy');
    });

    // Gestión de roles
    Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::post('/admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::put('/admin/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/admin/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');

    // Gestión de Permisos (Centro de Control de Acceso RBAC) — solo Super Admin
    Route::get('/admin/permisos',           [\App\Http\Controllers\Admin\PermisoController::class, 'index'])->name('admin.permisos.index');
    Route::put('/admin/permisos',           [\App\Http\Controllers\Admin\PermisoController::class, 'update'])->name('admin.permisos.update');
    Route::post('/admin/permisos/duplicar', [\App\Http\Controllers\Admin\PermisoController::class, 'duplicar'])->name('admin.permisos.duplicar');
    Route::get('/admin/permisos/exportar',  [\App\Http\Controllers\Admin\PermisoController::class, 'exportar'])->name('admin.permisos.exportar');

    // Gestor de Tipos de Entrada (catálogo dinámico que alimenta el select de Entradas)
    Route::get('/admin/tipos-entrada',                  [\App\Http\Controllers\Admin\TipoEntradaController::class, 'index'])->name('admin.tipos_entrada.index');
    Route::post('/admin/tipos-entrada',                 [\App\Http\Controllers\Admin\TipoEntradaController::class, 'store'])->name('admin.tipos_entrada.store');
    Route::put('/admin/tipos-entrada/{tipos_entrada}',  [\App\Http\Controllers\Admin\TipoEntradaController::class, 'update'])->name('admin.tipos_entrada.update');
    Route::delete('/admin/tipos-entrada/{tipos_entrada}',[\App\Http\Controllers\Admin\TipoEntradaController::class, 'destroy'])->name('admin.tipos_entrada.destroy');

    // Gestor de Tipos de Administración (catálogo dinámico que alimenta el select «Tipo» de Vías de Administración)
    Route::get('/admin/tipos-administracion',                         [\App\Http\Controllers\Admin\TipoAdministracionController::class, 'index'])->name('admin.tipos_administracion.index');
    Route::post('/admin/tipos-administracion',                        [\App\Http\Controllers\Admin\TipoAdministracionController::class, 'store'])->name('admin.tipos_administracion.store');
    Route::put('/admin/tipos-administracion/{tipos_administracion}',  [\App\Http\Controllers\Admin\TipoAdministracionController::class, 'update'])->name('admin.tipos_administracion.update');
    Route::delete('/admin/tipos-administracion/{tipos_administracion}',[\App\Http\Controllers\Admin\TipoAdministracionController::class, 'destroy'])->name('admin.tipos_administracion.destroy');

    // Gestor de Servicios (catálogo dinámico que alimenta el select «Servicio» de Pacientes)
    Route::get('/admin/tipos-servicios',                  [\App\Http\Controllers\Admin\TipoServiciosController::class, 'index'])->name('admin.tipos_servicios.index');
    Route::post('/admin/tipos-servicios',                 [\App\Http\Controllers\Admin\TipoServiciosController::class, 'store'])->name('admin.tipos_servicios.store');
    Route::put('/admin/tipos-servicios/{tipo_servicio}',  [\App\Http\Controllers\Admin\TipoServiciosController::class, 'update'])->name('admin.tipos_servicios.update');
    Route::delete('/admin/tipos-servicios/{tipo_servicio}',[\App\Http\Controllers\Admin\TipoServiciosController::class, 'destroy'])->name('admin.tipos_servicios.destroy');

    // Gestor de EPS (catálogo que alimenta el campo «EPS» de Pacientes y demás vistas)
    Route::get('/admin/eps',          [\App\Http\Controllers\Admin\EpsController::class, 'index'])->name('admin.eps.index');
    Route::post('/admin/eps',         [\App\Http\Controllers\Admin\EpsController::class, 'store'])->name('admin.eps.store');
    Route::put('/admin/eps/{eps}',    [\App\Http\Controllers\Admin\EpsController::class, 'update'])->name('admin.eps.update');
    Route::delete('/admin/eps/{eps}', [\App\Http\Controllers\Admin\EpsController::class, 'destroy'])->name('admin.eps.destroy');
});

require __DIR__.'/auth.php';
