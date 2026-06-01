<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Superteca') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root { --institucional: #2e3a75; }
            html, body { margin:0; padding:0; height:100%; font-family: 'Figtree', sans-serif; }
            .auth-bg {
                position: fixed;
                inset: 0;
                background-image: url('{{ asset("img/nuevologo.jpg") }}');
                background-size: cover;
                background-position: center top;
                z-index: 0;
            }
            .auth-bg::after {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(
                    160deg,
                    rgba(46, 58, 117, 0.82) 0%,
                    rgba(20, 30, 70, 0.70) 50%,
                    rgba(46, 58, 117, 0.88) 100%
                );
                backdrop-filter: blur(2px);
            }
            .auth-container {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                position: relative;
                z-index: 1;
            }
            .auth-card {
                background: #fff;
                width: 100%;
                max-width: 440px;
                border-radius: 14px;
                padding: 38px 36px;
                box-shadow: 0 20px 50px rgba(0,0,0,0.25);
            }
            .auth-brand {
                text-align: center;
                margin-bottom: 26px;
            }
            .auth-brand .logo {
                width: 70px; height: 70px; border-radius: 50%;
                background: var(--institucional); color: #fff;
                margin: 0 auto 12px;
                display:flex; align-items:center; justify-content:center;
                font-size: 1.8rem; font-weight: 700;
            }
            .auth-brand h1 {
                color: var(--institucional);
                font-size: 1.5rem;
                font-weight: 700;
                margin: 0;
            }
            .auth-brand p { color: #6b7280; font-size: .9rem; margin: 4px 0 0; }
        </style>
    </head>
    <body>
        <div class="auth-bg"></div>
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-brand">
                    <div class="logo">{{ strtoupper(substr(config('app.name','S'),0,1)) }}</div>
                    <h1>{{ config('app.name', 'Superteca') }}</h1>
                    <p>Sistema institucional</p>
                </div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
