<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EquipoCadenaFrio;
use App\Models\MonitoreoTemperatura;
use App\Models\AlertaCadenaFrio;
use App\Models\LoteCadenaFrio;
use App\Models\AfectacionLote;
use App\Models\InventarioLote;
use App\Models\User;

class CadenaFrioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        // 1. Equipos
        $nevera1 = EquipoCadenaFrio::create([
            'codigo' => 'N-001',
            'nombre' => 'Nevera Principal Almacén',
            'tipo' => 'NEVERA',
            'ubicacion' => 'Almacén Central',
            'temperatura_min' => 2.0,
            'temperatura_max' => 8.0,
            'fabricante' => 'Thermo Scientific',
            'estado' => true,
        ]);

        $nevera2 = EquipoCadenaFrio::create([
            'codigo' => 'N-002',
            'nombre' => 'Nevera Oncológicos',
            'tipo' => 'NEVERA',
            'ubicacion' => 'Área de Mezclas',
            'temperatura_min' => 2.0,
            'temperatura_max' => 8.0,
            'estado' => true,
        ]);

        $congelador = EquipoCadenaFrio::create([
            'codigo' => 'C-001',
            'nombre' => 'Congelador Biológicos',
            'tipo' => 'CONGELADOR',
            'ubicacion' => 'Almacén Central',
            'temperatura_min' => -25.0,
            'temperatura_max' => -15.0,
            'estado' => true,
        ]);

        // 2. Sensores
        $nevera1->sensores()->create(['codigo_sensor' => 'SENS-N001-A', 'marca' => 'Testo', 'estado' => true]);
        $nevera2->sensores()->create(['codigo_sensor' => 'SENS-N002-A', 'marca' => 'Testo', 'estado' => true]);

        // 3. Monitoreos Normales (Nevera 1)
        for ($i = 6; $i >= 0; $i--) {
            MonitoreoTemperatura::create([
                'equipo_id' => $nevera1->id,
                'fecha_hora' => now()->subHours($i),
                'temperatura' => rand(40, 60) / 10,
                'usuario_id' => $user ? $user->id : null,
                'origen' => 'AUTOMATICO',
                'fuera_rango' => false,
            ]);
        }
        
        // Monitoreo Congelador
        MonitoreoTemperatura::create([
            'equipo_id' => $congelador->id,
            'fecha_hora' => now()->subMinutes(10),
            'temperatura' => -18.5,
            'usuario_id' => $user ? $user->id : null,
            'origen' => 'MANUAL',
            'fuera_rango' => false,
        ]);

        // 4. Asignar Lotes a Nevera 2 (para poder afectar)
        $lotes = InventarioLote::take(2)->get();
        foreach ($lotes as $lote) {
            LoteCadenaFrio::create([
                'inventario_lote_id' => $lote->id,
                'equipo_id' => $nevera2->id,
                'fecha_ingreso' => now()->subDays(2),
            ]);
            $lote->update(['equipo_cadena_frio_id' => $nevera2->id, 'estado_calidad' => 'LIBERADO']);
        }

        // 5. Generar Alerta (Nevera 2 se salió de rango)
        MonitoreoTemperatura::create([
            'equipo_id' => $nevera2->id,
            'fecha_hora' => now()->subHours(2),
            'temperatura' => 5.5,
            'usuario_id' => $user ? $user->id : null,
            'origen' => 'AUTOMATICO',
            'fuera_rango' => false,
        ]);
        
        // Monitoreo Fuera de rango
        $tempMala = 9.5;
        $mon = MonitoreoTemperatura::create([
            'equipo_id' => $nevera2->id,
            'fecha_hora' => now()->subMinutes(30),
            'temperatura' => $tempMala,
            'usuario_id' => $user ? $user->id : null,
            'origen' => 'MANUAL',
            'fuera_rango' => true,
            'observaciones' => 'Puerta quedó semiabierta'
        ]);

        // Crear alerta
        $alerta = AlertaCadenaFrio::create([
            'equipo_id' => $nevera2->id,
            'fecha_inicio' => $mon->fecha_hora,
            'temperatura_registrada' => $tempMala,
            'temperatura_permitida_min' => $nevera2->temperatura_min,
            'temperatura_permitida_max' => $nevera2->temperatura_max,
            'severidad' => 'MEDIA',
            'estado' => 'ABIERTA',
            'usuario_id' => $user ? $user->id : null,
        ]);

        // Afectar lotes
        foreach ($lotes as $lote) {
            AfectacionLote::create([
                'alerta_id' => $alerta->id,
                'inventario_lote_id' => $lote->id,
                'estado' => 'PENDIENTE_EVALUACION',
                'usuario_id' => $user ? $user->id : null,
            ]);
            $lote->update(['estado_calidad' => 'CUARENTENA']);
        }
    }
}
