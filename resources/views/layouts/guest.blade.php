<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-4 py-10">
            <div class="w-full max-w-md">
                <div class="flex items-center justify-center">
                    <a href="/" class="inline-flex items-center gap-3">
                        <x-application-logo class="w-12 h-12 fill-current text-gray-900" />
                        <span class="text-lg font-bold tracking-tight text-gray-900">{{ config('app.name', 'PtitForum') }}</span>
                    </a>
                </div>

                <div class="mt-6 rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 px-6 py-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
