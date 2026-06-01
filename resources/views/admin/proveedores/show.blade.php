<x-app-layout>
    <x-slot name="header">
        <h2>Proveedor · {{ $proveedor->nombre_comercial ?: $proveedor->razon_social }}</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }
        .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px; }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; padding:9px 16px; border-radius:8px; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
        .btn-outline:hover { background:#f9fafb; }

        .hero { background:#fff; border-radius:16px; padding:24px; box-shadow:0 6px 16px rgba(0,0,0,.05); display:flex; gap:22px; align-items:center; margin-bottom:22px; }
        .hero-logo { width:90px; height:90px; border-radius:18px; background:#eef0f7; color:var(--inst); display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:800; flex-shrink:0; overflow:hidden; }
        .hero-logo img { width:100%; height:100%; object-fit:cover; }
        .hero-info h1 { margin:0; font-size:1.6rem; color:var(--inst); font-weight:800; }
        .hero-info .meta { color:#6b7280; margin-top:6px; font-size:.95rem; }
        .hero-badges { display:flex; gap:6px; flex-wrap:wrap; margin-top:10px; }
        .badge { display:inline-flex; align-items:center; padding:4px 10px; border-radius:20px; font-size:.75rem; font-weight:700; border:1px solid transparent; }
        .badge-active   { background:#d1fae5; color:#065f46; border-color:#a7f3d0; }
        .badge-inactive { background:#fee2e2; color:#991b1b; border-color:#fecaca; }
        .badge-frio     { background:#eff6ff; color:#1e40af; border-color:#bfdbfe; }
        .badge-tipo     { background:#f3f4f6; color:#374151; border-color:#e5e7eb; }

        .grid-2 { display:grid; grid-template-columns:2fr 1fr; gap:22px; align-items:start; }
        @media (max-width:1000px) { .grid-2 { grid-template-columns:1fr; } }

        .panel { background:#fff; border-radius:14px; padding:22px; box-shadow:0 6px 14px rgba(0,0,0,.04); margin-bottom:20px; }
        .panel h3 { margin:0 0 16px 0; font-size:1rem; color:var(--inst); border-bottom:2px solid #f3f4f6; padding-bottom:8px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }

        .kv { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px,1fr)); gap:12px 22px; }
        .kv .item .l { color:#6b7280; font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; font-weight:600; }
        .kv .item .v { color:#1f2937; font-size:.95rem; font-weight:600; margin-top:2px; word-break:break-word; }

        table { width:100%; border-collapse:collapse; font-size:.9rem; }
        table th { text-align:left; padding:10px 12px; background:#f9fafb; color:#374151; font-weight:700; font-size:.8rem; text-transform:uppercase; letter-spacing:.04em; }
        table td { padding:10px 12px; border-top:1px solid #f3f4f6; color:#4b5563; }

        .semaforo { display:flex; gap:12px; align-items:center; }
        .semaforo .dot { width:14px; height:14px; border-radius:50%; display:inline-block; }
        .sem-g { background:#10b981; } .sem-y { background:#f59e0b; } .sem-r { background:#ef4444; }

        .indicators { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
        .indicators .ind { background:#f9fafb; border-radius:10px; padding:12px; text-align:center; }
        .indicators .ind .n { font-size:1.4rem; font-weight:800; color:var(--inst); }
        .indicators .ind .t { color:#6b7280; font-size:.78rem; text-transform:uppercase; font-weight:600; margin-top:2px; }

        .timeline { list-style:none; padding:0; margin:0; }
        .timeline li { position:relative; padding:10px 0 10px 22px; border-left:2px solid #e5e7eb; margin-left:6px; }
        .timeline li::before { content:''; position:absolute; left:-7px; top:14px; width:12px; height:12px; border-radius:50%; background:var(--inst); }
        .timeline li .d { font-size:.78rem; color:#6b7280; font-weight:600; }
        .timeline li .t { color:#1f2937; font-size:.9rem; }
    </style>

    <div class="top-bar">
        <a href="{{ route('admin.proveedores.index') }}" class="btn-outline">← Volver al listado</a>
    </div>

    <div class="hero">
        <div class="hero-logo">
            @if($proveedor->logo)
                <img src="{{ asset('storage/'.$proveedor->logo) }}" alt="logo">
            @else
                {{ strtoupper(substr($proveedor->razon_social, 0, 2)) }}
            @endif
        </div>
        <div class="hero-info" style="flex:1;">
            <h1>{{ $proveedor->nombre_comercial ?: $proveedor->razon_social }}</h1>
            <div class="meta">
                {{ $proveedor->razon_social }} · NIT {{ $proveedor->nit_completo }}
                @if($proveedor->codigo) · Código <strong>{{ $proveedor->codigo }}</strong> @endif
            </div>
            <div class="hero-badges">
                @if($proveedor->tipo_proveedor)
                    <span class="badge badge-tipo">{{ $proveedor->tipo_label }}</span>
                @endif
                @if($proveedor->estado)
                    <span class="badge badge-active">● Activo</span>
                @else
                    <span class="badge badge-inactive">● Inactivo</span>
                @endif
                @if($proveedor->maneja_cadena_frio)
                    <span class="badge badge-frio">❄ Cadena de frío</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div>
            {{-- Información general --}}
            <div class="panel">
                <h3>Información general</h3>
                <div class="kv">
                    <div class="item"><div class="l">Registro INVIMA</div><div class="v">{{ $proveedor->registro_invima ?: '—' }}</div></div>
                    <div class="item"><div class="l">Habilitación Salud</div><div class="v">{{ $proveedor->habilitacion_salud ?: '—' }}</div></div>
                    <div class="item"><div class="l">Dirección</div><div class="v">{{ $proveedor->direccion ?: '—' }}</div></div>
                    <div class="item"><div class="l">Ciudad / Departamento</div><div class="v">{{ trim(($proveedor->ciudad ?: '—') . ' / ' . ($proveedor->departamento ?: '—'), ' /') }}</div></div>
                    <div class="item"><div class="l">País</div><div class="v">{{ $proveedor->pais ?: '—' }}</div></div>
                    <div class="item"><div class="l">Teléfono</div><div class="v">{{ $proveedor->telefono ?: '—' }}</div></div>
                    <div class="item"><div class="l">Celular</div><div class="v">{{ $proveedor->celular ?: '—' }}</div></div>
                    <div class="item"><div class="l">Email</div><div class="v">{{ $proveedor->email ?: '—' }}</div></div>
                    <div class="item"><div class="l">Sitio web</div><div class="v">{{ $proveedor->sitio_web ?: '—' }}</div></div>
                </div>
            </div>

            {{-- Contactos --}}
            <div class="panel">
                <h3>Contactos</h3>
                <div class="kv">
                    <div class="item"><div class="l">Comercial</div><div class="v">{{ $proveedor->contacto_comercial ?: '—' }}</div></div>
                    <div class="item"><div class="l">Tel. contacto</div><div class="v">{{ $proveedor->telefono_contacto ?: '—' }}</div></div>
                    <div class="item"><div class="l">Email contacto</div><div class="v">{{ $proveedor->email_contacto ?: '—' }}</div></div>
                    <div class="item"><div class="l">Farmacovigilancia</div><div class="v">{{ $proveedor->contacto_farmacovigilancia ?: '—' }}</div></div>
                    <div class="item"><div class="l">Logística</div><div class="v">{{ $proveedor->contacto_logistica ?: '—' }}</div></div>
                </div>
            </div>

            {{-- Comercial / Logística --}}
            <div class="panel">
                <h3>Condiciones comerciales y logística</h3>
                <div class="kv">
                    <div class="item"><div class="l">Condiciones de pago</div><div class="v">{{ $proveedor->condiciones_pago ?: '—' }}</div></div>
                    <div class="item"><div class="l">Días de crédito</div><div class="v">{{ $proveedor->dias_credito ?? '—' }}</div></div>
                    <div class="item"><div class="l">Tiempo de entrega</div><div class="v">{{ $proveedor->tiempo_entrega_horas ? $proveedor->tiempo_entrega_horas.' h' : '—' }}</div></div>
                    <div class="item"><div class="l">Horario de entrega</div><div class="v">{{ $proveedor->horario_entrega ?: '—' }}</div></div>
                    <div class="item"><div class="l">Cadena de frío</div><div class="v">{{ $proveedor->maneja_cadena_frio ? 'Sí' : 'No' }}</div></div>
                    <div class="item"><div class="l">Rango temperatura</div><div class="v">
                        @if(!is_null($proveedor->temperatura_min) || !is_null($proveedor->temperatura_max))
                            {{ $proveedor->temperatura_min ?? '—' }} °C / {{ $proveedor->temperatura_max ?? '—' }} °C
                        @else — @endif
                    </div></div>
                </div>
            </div>

            {{-- Productos suministrados --}}
            <div class="panel">
                <h3>Productos suministrados</h3>
                @if($proveedor->inventarioLotes->isEmpty())
                    <p style="color:#6b7280;">Este proveedor aún no tiene lotes registrados en inventario.</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Medicamento</th>
                                <th>Presentación</th>
                                <th>Lote</th>
                                <th>Vence</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proveedor->inventarioLotes->take(15) as $l)
                                <tr>
                                    <td>{{ $l->medicamento->nombre ?? '—' }}</td>
                                    <td>{{ $l->presentacion->nombre ?? '—' }}</td>
                                    <td>{{ $l->lote }}</td>
                                    <td>{{ optional($l->fecha_vencimiento)->format('d/m/Y') ?? '—' }}</td>
                                    <td>{{ $l->cantidad_actual }} {{ $l->unidad_medida }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if($proveedor->certificaciones || $proveedor->observaciones)
                <div class="panel">
                    <h3>Certificaciones y observaciones</h3>
                    @if($proveedor->certificaciones)
                        <div style="margin-bottom:10px;"><strong>Certificaciones:</strong><br>{{ $proveedor->certificaciones }}</div>
                    @endif
                    @if($proveedor->observaciones)
                        <div><strong>Observaciones:</strong><br>{{ $proveedor->observaciones }}</div>
                    @endif
                </div>
            @endif
        </div>

        <div>
            {{-- Semáforo --}}
            <div class="panel">
                <h3>Semáforo de cumplimiento</h3>
                <div class="semaforo" style="flex-direction:column; align-items:flex-start; gap:8px;">
                    <div><span class="dot sem-g"></span> <strong>Cumple</strong> — sin incidencias</div>
                    <div><span class="dot sem-y"></span> Retrasos puntuales</div>
                    <div><span class="dot sem-r"></span> Incumplimientos</div>
                </div>
            </div>

            {{-- Indicadores --}}
            <div class="panel">
                <h3>Indicadores</h3>
                <div class="indicators">
                    <div class="ind"><div class="n">{{ $proveedor->inventarioLotes->count() }}</div><div class="t">Lotes activos</div></div>
                    <div class="ind"><div class="n">—</div><div class="t">Cumplimiento</div></div>
                    <div class="ind"><div class="n">—</div><div class="t">Devoluciones</div></div>
                    <div class="ind"><div class="n">—</div><div class="t">Incidentes</div></div>
                </div>
            </div>

            {{-- Historial --}}
            <div class="panel">
                <h3>Historial</h3>
                <ul class="timeline">
                    <li>
                        <div class="d">{{ $proveedor->created_at?->format('Y-m-d') }}</div>
                        <div class="t">Proveedor registrado en el sistema</div>
                    </li>
                    @if($proveedor->updated_at && $proveedor->updated_at->ne($proveedor->created_at))
                        <li>
                            <div class="d">{{ $proveedor->updated_at->format('Y-m-d') }}</div>
                            <div class="t">Última actualización de ficha</div>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Documentos (placeholder) --}}
            <div class="panel">
                <h3>Documentos</h3>
                <p style="color:#6b7280; font-size:.88rem; margin:0;">
                    Próximamente: RUT, Cámara de comercio, INVIMA, ISO, contratos.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
