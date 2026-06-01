<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\MedicamentoController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
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

    // Reportes - Registro de actividad (solo Super Admin)
    Route::get('/admin/reportes/registros', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('admin.reportes.registros');
    Route::get('/admin/reportes/registros/{log}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('admin.reportes.registros.show');

    // Gestión de roles
    Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::post('/admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::put('/admin/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/admin/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
});

require __DIR__.'/auth.php';
