<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sermon Transcriber') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if(app()->environment(['local', 'development']))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <link rel="stylesheet" href="{{ asset('build/assets/' . App\Helpers\AssetHelper::getAssetFilename('resources/css/app.css')) }}">
            <script src="{{ asset('build/assets/' . App\Helpers\AssetHelper::getAssetFilename('resources/js/app.js')) }}" defer></script>
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-50">
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
