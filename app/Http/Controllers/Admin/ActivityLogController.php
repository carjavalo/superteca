<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(
            optional(auth()->user()->role)->name === 'Super Admin',
            403,
            'Solo el Super Admin puede ver el registro de actividades.'
        );

        $query = ActivityLog::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('description', 'like', "%$s%")
                  ->orWhere('user_name', 'like', "%$s%")
                  ->orWhere('url', 'like', "%$s%")
                  ->orWhere('route', 'like', "%$s%");
            });
        }
        if ($request->filled('event'))     $query->where('event', $request->event);
        if ($request->filled('user_id'))   $query->where('user_id', $request->user_id);
        if ($request->filled('desde'))     $query->whereDate('created_at', '>=', $request->desde);
        if ($request->filled('hasta'))     $query->whereDate('created_at', '<=', $request->hasta);

        $logs = $query->latest('id')->paginate(25)->withQueryString();

        $stats = [
            'total'    => ActivityLog::count(),
            'hoy'      => ActivityLog::whereDate('created_at', today())->count(),
            'logins'   => ActivityLog::where('event', 'LOGIN')->whereDate('created_at', today())->count(),
            'cambios'  => ActivityLog::whereIn('event', ['CREATED','UPDATED','DELETED'])->whereDate('created_at', today())->count(),
            'usuarios' => ActivityLog::whereDate('created_at', today())->distinct('user_id')->count('user_id'),
        ];

        $usuarios = \App\Models\User::orderBy('name')->get(['id','name']);

        return view('admin.reportes.registros', compact('logs', 'stats', 'usuarios'));
    }

    public function show(ActivityLog $log)
    {
        abort_unless(
            optional(auth()->user()->role)->name === 'Super Admin',
            403
        );
        $log->load('user');
        return response()->json($log);
    }
}
