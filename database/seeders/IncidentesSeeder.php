<?php

namespace Database\Seeders;

use App\Models\Incidente;
use App\Models\IncidenteAccion;
use App\Models\IncidenteAfectacion;
use App\Models\IncidenteDetalle;
use App\Models\IncidenteSeguimiento;
use App\Models\InventarioLote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class IncidentesSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $userId = $user?->id;

        $loteIds = InventarioLote::limit(5)->pluck('id')->all();

        $demos = [
            [
                'tipo' => 'CADENA_FRIO', 'clas' => 'DESVIACION', 'sev' => 'ALTA', 'estado' => 'INVESTIGACION',
                'desc' => 'Temperatura de la nevera 2 fuera de rango (12°C) durante 4 horas. Se detectó al inicio del turno de la mañana.',
                'causa'=> 'Falla del compresor + corte eléctrico breve sin respaldo de UPS.',
                'imp'  => 'Posible afectación de estabilidad en lotes refrigerados.',
            ],
            [
                'tipo' => 'INVENTARIO', 'clas' => 'NO_CONFORMIDAD', 'sev' => 'MEDIA', 'estado' => 'ACCION_CORRECTIVA',
                'desc' => 'Lote vencido encontrado en estantería de stock activo durante auditoría interna.',
                'causa'=> 'Falla en rutina FEFO al reorganizar el almacén.',
                'imp'  => 'Riesgo de dispensación de producto vencido.',
            ],
            [
                'tipo' => 'PREPARACION', 'clas' => 'EVENTO_ADVERSO', 'sev' => 'CRITICA', 'estado' => 'ABIERTO',
                'desc' => 'Error de dosificación detectado en preparación P-2026-15 antes de su entrega.',
                'causa'=> null, 'imp'  => null,
            ],
            [
                'tipo' => 'DISPENSACION', 'clas' => 'HALLAZGO', 'sev' => 'BAJA', 'estado' => 'CERRADO',
                'desc' => 'Etiqueta de paciente impresa con tipografía borrosa.',
                'causa'=> 'Cartucho de impresora con bajo nivel de tóner.',
                'imp'  => 'Sin impacto clínico, riesgo de confusión.',
            ],
            [
                'tipo' => 'AUDITORIA', 'clas' => 'HALLAZGO', 'sev' => 'MEDIA', 'estado' => 'INVESTIGACION',
                'desc' => 'Auditoría INVIMA detectó ausencia de registro de temperatura por 1 día.',
                'causa'=> 'Sensor desconectado por mantenimiento sin registro manual.',
                'imp'  => 'Hallazgo documentable.',
            ],
        ];

        $i = 0;
        foreach ($demos as $d) {
            $i++;
            $fecha = Carbon::now()->subDays($i*2)->subHours(rand(1,12));

            $inc = Incidente::create([
                'codigo'             => 'INC-'.$fecha->format('Ymd').'-'.str_pad((string)$i, 3, '0', STR_PAD_LEFT),
                'fecha_incidente'    => $fecha,
                'tipo_incidente'     => $d['tipo'],
                'clasificacion'      => $d['clas'],
                'severidad'          => $d['sev'],
                'descripcion'        => $d['desc'],
                'usuario_reporta_id' => $userId,
                'fecha_reporte'      => $fecha->copy()->addMinutes(15),
                'estado'             => $d['estado'],
            ]);

            IncidenteDetalle::create([
                'incidente_id'                 => $inc->id,
                'causa_raiz'                   => $d['causa'],
                'impacto'                      => $d['imp'],
                'conclusion'                   => $d['estado']==='CERRADO' ? 'Se ejecutaron las acciones correctivas y se verificó cumplimiento.' : null,
                'responsable_investigacion_id' => $userId,
                'fecha_cierre'                 => $d['estado']==='CERRADO' ? $fecha->copy()->addDays(2) : null,
            ]);

            // afectación a un lote (si hay)
            if (!empty($loteIds)) {
                $loteId = $loteIds[($i-1) % count($loteIds)];
                IncidenteAfectacion::create([
                    'incidente_id'       => $inc->id,
                    'inventario_lote_id' => $loteId,
                    'observaciones'      => 'Lote bajo evaluación por incidente.',
                ]);

                if (in_array($d['sev'], ['ALTA','CRITICA'])) {
                    InventarioLote::where('id', $loteId)->update([
                        'bloqueado_incidente' => true,
                        'incidente_id'        => $inc->id,
                        'estado_calidad'      => 'BLOQUEADO',
                    ]);
                }
            }

            // acciones CAPA
            IncidenteAccion::create([
                'incidente_id'     => $inc->id,
                'tipo_accion'      => 'CORRECTIVA',
                'descripcion'      => 'Acción correctiva inmediata sobre la causa identificada.',
                'responsable_id'   => $userId,
                'fecha_compromiso' => $fecha->copy()->addDays(3)->toDateString(),
                'fecha_ejecucion'  => $d['estado']==='CERRADO' ? $fecha->copy()->addDays(2)->toDateString() : null,
                'estado'           => $d['estado']==='CERRADO' ? 'CERRADA' : 'EN_PROCESO',
            ]);
            IncidenteAccion::create([
                'incidente_id'     => $inc->id,
                'tipo_accion'      => 'PREVENTIVA',
                'descripcion'      => 'Acción preventiva para evitar recurrencia.',
                'responsable_id'   => $userId,
                'fecha_compromiso' => $fecha->copy()->addDays(15)->toDateString(),
                'estado'           => 'PENDIENTE',
            ]);

            // bitácora
            IncidenteSeguimiento::create([
                'incidente_id' => $inc->id,
                'fecha'        => $fecha->copy()->addMinutes(15),
                'comentario'   => 'Incidente reportado en el sistema.',
                'usuario_id'   => $userId,
            ]);
            if ($d['estado'] !== 'ABIERTO') {
                IncidenteSeguimiento::create([
                    'incidente_id' => $inc->id,
                    'fecha'        => $fecha->copy()->addHours(2),
                    'comentario'   => 'Inicia investigación; se asigna responsable.',
                    'usuario_id'   => $userId,
                ]);
            }
            if ($d['estado'] === 'CERRADO') {
                IncidenteSeguimiento::create([
                    'incidente_id' => $inc->id,
                    'fecha'        => $fecha->copy()->addDays(2),
                    'comentario'   => 'Incidente cerrado tras verificación de eficacia.',
                    'usuario_id'   => $userId,
                ]);
            }
        }
    }
}
