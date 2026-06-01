<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaboratoriosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('laboratorios')->insert([
            [
                'codigo' => 'LAB-001',
                'nombre' => 'Genfar S.A.',
                'nit' => '800123456-1',
                'registro_invima' => 'INV-GEN-2000',
                'pais_origen' => 'Colombia',
                'ciudad' => 'Cali',
                'direccion' => 'Calle 10 # 5-20 Zona Industrial',
                'telefono' => '6025551234',
                'email' => 'contacto@genfar.com.co',
                'sitio_web' => 'https://www.genfar.com.co',
                'contacto_comercial' => 'Carlos Perez',
                'contacto_farmacovigilancia' => 'farmacovigilancia@genfar.com.co',
                'requiere_cadena_frio' => 0,
                'semaforo_sanitario' => 'verde',
                'estado' => 1,
                'observaciones' => 'Proveedor principal de medicamentos genéricos.',
                'logo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => 'LAB-002',
                'nombre' => 'Bayer de Colombia S.A.',
                'nit' => '860001234-1',
                'registro_invima' => 'INV-BAYER-2023',
                'pais_origen' => 'Alemania',
                'ciudad' => 'Bogotá',
                'direccion' => 'Calle 100 # 50-20 Edificio Bayer',
                'telefono' => '6012345678',
                'email' => 'ventas@bayer.com.co',
                'sitio_web' => 'https://www.bayer.com.co',
                'contacto_comercial' => 'Ana Martinez',
                'contacto_farmacovigilancia' => 'fv.colombia@bayer.com',
                'requiere_cadena_frio' => 0,
                'semaforo_sanitario' => 'verde',
                'estado' => 1,
                'observaciones' => 'Laboratorio multinacional, medicamentos de marca, innovación.',
                'logo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => 'LAB-003',
                'nombre' => 'Sanofi-Aventis de Colombia',
                'nit' => '860503112-9',
                'registro_invima' => 'INV-SAN-1999',
                'pais_origen' => 'Francia',
                'ciudad' => 'Bogotá',
                'direccion' => 'Cra 9 # 100-09',
                'telefono' => '6016200000',
                'email' => 'comercial@sanofi.com.co',
                'sitio_web' => 'https://www.sanofi.com.co',
                'contacto_comercial' => 'Roberto Gómez',
                'contacto_farmacovigilancia' => 'farmacovigilancia.colombia@sanofi.com',
                'requiere_cadena_frio' => 1,
                'semaforo_sanitario' => 'verde',
                'estado' => 1,
                'observaciones' => 'Principal proveedor de insulinas (Glargina, Lantus).',
                'logo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => 'LAB-004',
                'nombre' => 'Roche S.A.',
                'nit' => '860002135-4',
                'registro_invima' => 'INV-ROC-2015',
                'pais_origen' => 'Suiza',
                'ciudad' => 'Medellín',
                'direccion' => 'Cra. 48 # 20-34',
                'telefono' => '6043209999',
                'email' => 'info@roche.com.co',
                'sitio_web' => 'https://www.roche.com.co',
                'contacto_comercial' => 'Santiago Rueda',
                'contacto_farmacovigilancia' => 'medellin.fv@roche.com',
                'requiere_cadena_frio' => 0,
                'semaforo_sanitario' => 'amarillo',
                'estado' => 1,
                'observaciones' => 'Medicamentos especializados. Semáforo amarillo por próxima renovación de registros INVIMA.',
                'logo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => 'LAB-005',
                'nombre' => 'Tecnoquímicas S.A. (MK)',
                'nit' => '890300123-5',
                'registro_invima' => 'INV-TQ-1980',
                'pais_origen' => 'Colombia',
                'ciudad' => 'Cali',
                'direccion' => 'Calle 23 # 7-39',
                'telefono' => '6028823232',
                'email' => 'ventas@tecnoquimicas.com',
                'sitio_web' => 'https://www.tecnoquimicas.com',
                'contacto_comercial' => 'Maria Silva',
                'contacto_farmacovigilancia' => 'alertas.salud@tecnoquimicas.com',
                'requiere_cadena_frio' => 0,
                'semaforo_sanitario' => 'rojo',
                'estado' => 0,
                'observaciones' => 'Suspendido temporalmente. Semáforo rojo por alertas y retiros preventivos del INVIMA en un lote específico.',
                'logo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}