<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventarioLote;
use App\Models\Medicamento;
use Carbon\Carbon;

class InventarioController extends Controller
{
    public function lotes(Request $request)
    {
        $search = $request->get('search');
        $estado = $request->get('estado'); // activo, vencido, proximo
        
        $query = InventarioLote::with('medicamento');

        if ($search) {
            $query->whereHas('medicamento', function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
            })->orWhere('lote', 'like', "%{$search}%");
        }

        if ($estado) {
            $hoy = Carbon::now();
            if ($estado == 'vencido') {
                $query->where('fecha_vencimiento', '<', $hoy);
            } elseif ($estado == 'proximo') {
                $query->whereBetween('fecha_vencimiento', [$hoy, $hoy->copy()->addDays(30)]);
            } elseif ($estado == 'activo') {
                $query->where('fecha_vencimiento', '>=', $hoy->copy()->addDays(30));
            }
        }

        $lotes = $query->orderBy('fecha_vencimiento', 'asc')->paginate(15)->withQueryString();

        // Stats para el Dashboard
        $stats = [
            'total_lotes' => InventarioLote::count(),
            'vencidos' => InventarioLote::where('fecha_vencimiento', '<', Carbon::now())->count(),
            'proximos' => InventarioLote::whereBetween('fecha_vencimiento', [Carbon::now(), Carbon::now()->addDays(30)])->count(),
            'bajo_stock' => InventarioLote::whereRaw('cantidad_actual < (cantidad_inicial * 0.2)')->count(), // Menos del 20%
        ];

        return view('admin.inventarios.lotes', compact('lotes', 'search', 'estado', 'stats'));
    }
}
