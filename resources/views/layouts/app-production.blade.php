<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Audio Note AI Transcriber') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if(app()->environment('production'))
            @if(file_exists(public_path('build/manifest.json')))
                @vite(['resources/css/app.css', 'resources/js/app.js'])
            @else
                <!-- Fallback for production without manifest -->
                <script>
                    console.warn('Vite manifest not found, loading fallback assets');
                </script>
                <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
                <script src="{{ asset('build/assets/app.js') }}" defer></script>
            @endif
        @else
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">
            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
